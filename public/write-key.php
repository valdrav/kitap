<?php

/**
 * /write-key.php — .env'ye APP_KEY + DB_PASSWORD yazar.
 * Sonuçta başarılı/başarısız açıkça söyler. İş bitince SİL.
 */
header('Content-Type: text/plain; charset=utf-8');

$root = null;
foreach ([dirname(__DIR__), __DIR__, realpath(__DIR__.'/..') ?: ''] as $dir) {
    if ($dir && is_file($dir.'/composer.json')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "Root bulunamadı\n";
    exit(1);
}

$path = $root.'/.env';
echo "Root: {$root}\n";
echo ".env path: {$path}\n";
echo "exists: ".(is_file($path) ? 'yes' : 'no')."\n";
echo "writable: ".(is_writable($path) || (! is_file($path) && is_writable($root)) ? 'yes' : 'NO')."\n";
echo "owner check: ".(function_exists('posix_getpwuid') && file_exists($path)
    ? (posix_getpwuid(fileowner($path))['name'] ?? '?')
    : 'n/a')."\n\n";

if (! is_file($path)) {
    if (is_file($root.'/.env.example')) {
        $copied = @copy($root.'/.env.example', $path);
        echo "copy .env.example: ".($copied ? 'OK' : 'FAIL')."\n";
    }
}

if (! is_file($path)) {
    echo "HATA: .env yok ve oluşturulamıyor.\n";
    echo "Plesk File Manager ile .env oluştur / izin ver (664).\n";
    exit(1);
}

$env = file_get_contents($path);
if ($env === false) {
    echo "HATA: .env okunamadı\n";
    exit(1);
}

$key = 'base64:jvSp37FBKpbTfpNAlsWdalWo0MZz8qk/JU2dXjroK+U=';

$replacements = [
    'APP_KEY' => $key,
    'APP_DEBUG' => 'true',
    'APP_URL' => 'https://kitap.kurtulum.com',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'kitap_db',
    'DB_USERNAME' => 'kitap_user',
    'DB_PASSWORD' => 'Valdrav115',
    'SESSION_DRIVER' => 'file',
    'CACHE_STORE' => 'file',
    'QUEUE_CONNECTION' => 'sync',
];

foreach ($replacements as $k => $v) {
    if (preg_match('/^'.preg_quote($k, '/').'=/m', $env)) {
        $env = preg_replace('/^'.preg_quote($k, '/').'=.*/m', $k.'='.$v, $env);
    } else {
        $env .= "\n{$k}={$v}\n";
    }
}

$result = @file_put_contents($path, $env);
if ($result === false) {
    echo "HATA: .env yazılamadı (Permission denied).\n\n";
    echo "Plesk File Manager:\n";
    echo "1) .env dosyasına sağ tık → Change Permissions → 644 veya 664\n";
    echo "2) Owner: domain kullanıcısı (psacln / kitap.kurtulum.com)\n";
    echo "3) .env içine şu satırları yapıştır:\n\n";
    echo "APP_KEY={$key}\n";
    echo "DB_PASSWORD=Valdrav115\n";
    echo "DB_DATABASE=kitap_db\n";
    echo "DB_USERNAME=kitap_user\n";
    exit(1);
}

echo "OK: .env yazıldı ({$result} byte)\n\n";

// Doğrula
$check = file_get_contents($path);
foreach (['APP_KEY', 'DB_PASSWORD', 'DB_DATABASE'] as $k) {
    preg_match('/^'.$k.'=(.*)$/m', $check, $m);
    $val = isset($m[1]) ? trim($m[1]) : '';
    if ($k === 'DB_PASSWORD') {
        echo "{$k}: ".(strlen($val) ? 'DOLU' : 'BOŞ')."\n";
    } elseif ($k === 'APP_KEY') {
        echo "{$k}: ".(strlen($val) > 10 ? 'DOLU' : 'BOŞ')."\n";
    } else {
        echo "{$k}: {$val}\n";
    }
}

echo "\nŞimdi aç: /run-migrate.php\n";
echo "Sonra write-key.php SİL\n";
