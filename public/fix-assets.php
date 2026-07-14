<?php

/**
 * Plesk çift public: docroot proje kökü, asset'ler public/ içinde.
 * css/js/images vb. için symlink (veya kopya) oluşturur.
 *
 * Kullanım: /fix-assets.php  → sonra bu dosyayı silin
 */
header('Content-Type: text/plain; charset=utf-8');

$root = null;
foreach ([dirname(__DIR__), __DIR__] as $dir) {
    if (is_file($dir.'/artisan') && is_dir($dir.'/public')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    http_response_code(500);
    echo "Laravel kökü bulunamadı.\n";
    exit;
}

echo "Root: {$root}\n\n";

$links = ['css', 'js', 'images', 'fonts', 'build', 'vendor', 'storage'];
$ok = 0;
$fail = 0;

foreach ($links as $name) {
    $target = $root.'/public/'.$name;
    $link = $root.'/'.$name;

    if (! file_exists($target) && ! is_link($target)) {
        echo "ATLA  {$name} (kaynak yok)\n";
        continue;
    }

    if (is_link($link) || is_dir($link) || is_file($link)) {
        // zaten doğru symlink mi?
        if (is_link($link) && realpath($link) === realpath($target)) {
            echo "OK    {$name} (zaten symlink)\n";
            $ok++;
            continue;
        }
        // yanlış/eski ise kaldır (sadece symlink veya boş dizin değilse dikkat)
        if (is_link($link)) {
            @unlink($link);
        } elseif (is_dir($link)) {
            echo "VAR   {$name}/ (dizin mevcut, dokunulmadı)\n";
            continue;
        } else {
            @unlink($link);
        }
    }

    if (@symlink($target, $link)) {
        echo "LINK  {$name} → public/{$name}\n";
        $ok++;
        continue;
    }

    // symlink yoksa kopyala
    if (is_dir($target)) {
        $copied = copyTree($target, $link);
        echo $copied ? "COPY  {$name}/\n" : "FAIL  {$name}\n";
        $copied ? $ok++ : $fail++;
    } else {
        $copied = @copy($target, $link);
        echo $copied ? "COPY  {$name}\n" : "FAIL  {$name}\n";
        $copied ? $ok++ : $fail++;
    }
}

// .env ASSET_URL yedek (symlink çalışmasa diye)
$envPath = $root.'/.env';
if (is_file($envPath) && is_writable($envPath)) {
    $env = file_get_contents($envPath);
    if (! preg_match('/^ASSET_URL=.+/m', $env)) {
        // Symlink varsa ASSET_URL gerekmez; boş bırak
        echo "\nASSET_URL: gerekmiyor (symlink tercih)\n";
    }
}

// config cache temizle
$php83 = '/opt/plesk/php/8.3/bin/php';
$php = is_file($php83) ? $php83 : 'php';
if (is_file($root.'/artisan')) {
    foreach (['config:clear', 'view:clear', 'cache:clear'] as $cmd) {
        $out = [];
        $code = 0;
        exec(escapeshellarg($php).' '.escapeshellarg($root.'/artisan').' '.$cmd.' 2>&1', $out, $code);
        echo "artisan {$cmd}: kod={$code}\n";
    }
}

echo "\nÖzet: ok={$ok} fail={$fail}\n";
echo "Test: https://kitap.kurtulum.com/css/filament/filament/app.css\n";
echo "Login: https://kitap.kurtulum.com/login\n";
echo "Sonra bu dosyayı SİLİN: fix-assets.php\n";

function copyTree(string $src, string $dst): bool
{
    if (! is_dir($dst) && ! @mkdir($dst, 0755, true)) {
        return false;
    }
    $items = scandir($src);
    if ($items === false) {
        return false;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $from = $src.'/'.$item;
        $to = $dst.'/'.$item;
        if (is_dir($from)) {
            if (! copyTree($from, $to)) {
                return false;
            }
        } else {
            if (! @copy($from, $to)) {
                return false;
            }
        }
    }

    return true;
}
