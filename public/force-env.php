<?php

/**
 * /force-env.php
 * .env dosyasını tamamen yeniden yazar + config cache siler.
 * İş bitince SİL.
 */
header('Content-Type: text/plain; charset=utf-8');

$root = null;
foreach ([dirname(__DIR__), __DIR__, realpath(__DIR__.'/..') ?: ''] as $dir) {
    if ($dir && is_file($dir.'/artisan')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "artisan bulunamadı\n";
    exit(1);
}

echo "Root: {$root}\n";

// Config cache temizle (eski boş APP_KEY burada kalmış olabilir)
$cacheFiles = glob($root.'/bootstrap/cache/*.php') ?: [];
foreach ($cacheFiles as $f) {
    if (basename($f) === '.gitignore') {
        continue;
    }
    $ok = @unlink($f);
    echo 'cache sil: '.basename($f).' → '.($ok ? 'OK' : 'FAIL')."\n";
}

$key = 'base64:jvSp37FBKpbTfpNAlsWdalWo0MZz8qk/JU2dXjroK+U=';

$content = <<<ENV
APP_NAME="Dernek Kitap"
APP_ENV=production
APP_KEY={$key}
APP_DEBUG=true
APP_URL=https://kitap.kurtulum.com

APP_LOCALE=tr
APP_FALLBACK_LOCALE=tr
APP_FAKER_LOCALE=tr_TR

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kitap_db
DB_USERNAME=kitap_user
DB_PASSWORD=Valdrav115

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply\@kurtulum.com"
MAIL_FROM_NAME="\${APP_NAME}"

VITE_APP_NAME="\${APP_NAME}"
ENV;

// heredoc may have escaped wrong - fix mail lines simply
$content = str_replace(
    ['noreply\@kurtulum.com', '"\${APP_NAME}"'],
    ['noreply@kurtulum.com', '"${APP_NAME}"'],
    $content
);

// Better write clean content without escape issues
$content = 'APP_NAME="Dernek Kitap"'."\n"
    .'APP_ENV=production'."\n"
    .'APP_KEY='.$key."\n"
    .'APP_DEBUG=true'."\n"
    .'APP_URL=https://kitap.kurtulum.com'."\n\n"
    .'APP_LOCALE=tr'."\n"
    .'APP_FALLBACK_LOCALE=tr'."\n\n"
    .'LOG_CHANNEL=stack'."\n"
    .'LOG_LEVEL=debug'."\n\n"
    .'DB_CONNECTION=mysql'."\n"
    .'DB_HOST=127.0.0.1'."\n"
    .'DB_PORT=3306'."\n"
    .'DB_DATABASE=kitap_db'."\n"
    .'DB_USERNAME=kitap_user'."\n"
    .'DB_PASSWORD=Valdrav115'."\n\n"
    .'SESSION_DRIVER=file'."\n"
    .'SESSION_LIFETIME=120'."\n"
    .'QUEUE_CONNECTION=sync'."\n"
    .'CACHE_STORE=file'."\n"
    .'FILESYSTEM_DISK=public'."\n";

$envPath = $root.'/.env';
$tmpPath = $root.'/.env.new';

$w = @file_put_contents($tmpPath, $content);
echo "tmp yaz: ".($w === false ? 'FAIL' : $w." byte")."\n";

if ($w === false) {
    echo "\nHATA: Yazma izni yok.\n";
    echo "File Manager → klasör izinleri:\n";
    echo "  {$root} → 755\n";
    echo "  .env → 666 (geçici)\n\n";
    echo "VEYA File Manager ile .env içeriğini tamamen silip şunu yapıştır:\n\n";
    echo $content;
    exit(1);
}

// Eski .env'i yedekle
if (is_file($envPath)) {
    @rename($envPath, $root.'/.env.bak-'.date('His'));
}

$ok = @rename($tmpPath, $envPath);
if (! $ok) {
    $ok = @copy($tmpPath, $envPath) && @unlink($tmpPath);
}

echo "env taşı: ".($ok ? 'OK' : 'FAIL')."\n";

if (! $ok) {
    echo "tmp dosya: {$tmpPath} — File Manager ile .env olarak yeniden adlandır.\n";
    exit(1);
}

@chmod($envPath, 0664);

// Doğrula
$read = file_get_contents($envPath);
preg_match('/^APP_KEY=(.*)$/m', $read, $m1);
preg_match('/^DB_PASSWORD=(.*)$/m', $read, $m2);
echo 'APP_KEY: '.(strlen(trim($m1[1] ?? '')) > 10 ? 'DOLU' : 'BOŞ')."\n";
echo 'DB_PASSWORD: '.(strlen(trim($m2[1] ?? '')) ? 'DOLU' : 'BOŞ')."\n";

echo "\nŞimdi: /run-migrate.php\n";
echo "Sonra: /login\n";
echo "force-env.php SİL\n";
