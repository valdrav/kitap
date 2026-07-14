<?php

/**
 * Plesk: https://kitap.kurtulum.com/setup-vendor.php
 * composer install çalıştırır. İş bitince SİL.
 */
set_time_limit(0);
ini_set('memory_limit', '512M');
header('Content-Type: text/plain; charset=utf-8');

echo "=== Dernek Kitap — composer install ===\n\n";

// Proje kökünü bul
$candidates = [
    dirname(__DIR__),                   // public/setup -> project root
    __DIR__,                            // yanlışlıkla kökteyse
    dirname(__DIR__, 1),
    dirname(__DIR__, 2),
];

$root = null;
foreach ($candidates as $dir) {
    if (is_file($dir.'/composer.json') && is_file($dir.'/artisan')) {
        $root = $dir;
        break;
    }
}

// Nested: .../public/public/setup-vendor.php
if ($root === null && is_file(__DIR__.'/../composer.json')) {
    $root = realpath(__DIR__.'/..');
}
if ($root === null && is_file(__DIR__.'/../../composer.json')) {
    $root = realpath(__DIR__.'/../..');
}
if ($root === null && is_file(__DIR__.'/composer.json')) {
    $root = __DIR__;
}

if ($root === null) {
    echo "HATA: composer.json / artisan bulunamadı.\n";
    echo "Çalışılan dizin: ".__DIR__."\n";
    exit(1);
}

echo "Proje kökü: {$root}\n";
chdir($root);

$php = PHP_BINARY ?: 'php';
echo "PHP: {$php} (".PHP_VERSION.")\n\n";

// Composer phar
$composerPhar = $root.'/composer.phar';
if (! is_file($composerPhar)) {
    echo "composer.phar indiriliyor...\n";
    $installer = file_get_contents('https://getcomposer.org/installer');
    if ($installer === false) {
        echo "HATA: Composer installer indirilemedi (allow_url_fopen kapalı olabilir).\n";
        exit(1);
    }
    file_put_contents($root.'/composer-setup.php', $installer);
    passthru(escapeshellarg($php).' '.escapeshellarg($root.'/composer-setup.php').' --install-dir='.escapeshellarg($root).' --filename=composer.phar', $code);
    @unlink($root.'/composer-setup.php');
    if (! is_file($composerPhar)) {
        echo "HATA: composer.phar oluşturulamadı. Kod: {$code}\n";
        exit(1);
    }
}

echo "\ncomposer install başlıyor...\n\n";
passthru(escapeshellarg($php).' '.escapeshellarg($composerPhar).' install --no-dev --optimize-autoloader --no-interaction 2>&1', $installCode);

echo "\nÇıkış kodu: {$installCode}\n";

if (is_file($root.'/vendor/autoload.php')) {
    echo "\nOK: vendor/autoload.php oluştu.\n";
    echo "Şimdi sırayla:\n";
    echo "1) /setup-env.php  (yoksa .env oluştur)\n";
    echo "2) Document root = .../public/public  VEYA doğru public klasörü\n";
    echo "3) /setup-artisan.php\n";
    echo "4) Bu dosyayı SİL: setup-vendor.php\n";
    echo "5) Aç: /login\n";
} else {
    echo "\nHATA: vendor hâlâ yok. Plesk'te SSH Terminal ile kur.\n";
    echo "cd {$root}\n";
    echo "php composer.phar install --no-dev\n";
}
