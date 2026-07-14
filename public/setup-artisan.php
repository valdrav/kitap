<?php

/**
 * https://kitap.kurtulum.com/setup-artisan.php
 * key:generate, migrate --seed, cache. İş bitince SİL.
 */
set_time_limit(0);
header('Content-Type: text/plain; charset=utf-8');

$root = null;
foreach ([dirname(__DIR__), __DIR__, realpath(__DIR__.'/..'), realpath(__DIR__.'/../..')] as $dir) {
    if ($dir && is_file($dir.'/artisan') && is_file($dir.'/vendor/autoload.php')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "HATA: artisan veya vendor yok. Önce /setup-vendor.php çalıştır.\n";
    exit(1);
}

echo "Proje: {$root}\n\n";
chdir($root);
$php = PHP_BINARY ?: 'php';
$artisan = $root.'/artisan';

if (! is_file($root.'/.env')) {
    if (is_file($root.'/.env.example')) {
        copy($root.'/.env.example', $root.'/.env');
        echo ".env örneğinden kopyalandı. DB şifresini kontrol et!\n";
    }
}

$commands = [
    'key:generate --force',
    'migrate --seed --force',
    'storage:link',
    'optimize:clear',
    'config:cache',
    'route:cache',
    'filament:assets',
];

foreach ($commands as $cmd) {
    echo "\n> php artisan {$cmd}\n";
    passthru(escapeshellarg($php).' '.escapeshellarg($artisan).' '.$cmd.' 2>&1', $code);
    echo "kod: {$code}\n";
}

echo "\nBitti. /login adresini dene.\n";
echo "setup-vendor.php ve setup-artisan.php dosyalarını SİL.\n";
