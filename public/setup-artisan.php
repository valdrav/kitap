<?php

/**
 * https://kitap.kurtulum.com/setup-artisan.php
 * İş bitince SİL.
 */
set_time_limit(0);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

function findPhpCli(): string
{
    foreach ([
        '/opt/plesk/php/8.3/bin/php',
        '/opt/plesk/php/8.2/bin/php',
        '/opt/plesk/php/8.1/bin/php',
        '/usr/bin/php',
    ] as $bin) {
        if (is_file($bin) && is_executable($bin)) {
            return $bin;
        }
    }
    foreach (glob('/opt/plesk/php/*/bin/php') ?: [] as $bin) {
        if (is_executable($bin)) {
            return $bin;
        }
    }

    return 'php';
}

$root = null;
foreach ([dirname(__DIR__), __DIR__, realpath(__DIR__.'/..') ?: '', realpath(__DIR__.'/../..') ?: ''] as $dir) {
    if ($dir && is_file($dir.'/artisan') && is_file($dir.'/vendor/autoload.php')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "HATA: artisan veya vendor yok. Önce /setup-vendor.php\n";
    exit(1);
}

echo "Proje: {$root}\n";
$php = findPhpCli();
echo "PHP CLI: {$php}\n\n";
chdir($root);

if (! is_file($root.'/.env') && is_file($root.'/.env.example')) {
    copy($root.'/.env.example', $root.'/.env');
    echo ".env kopyalandı — DB bilgilerini kontrol et!\n\n";
}

$artisan = $root.'/artisan';
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

echo "\nBitti. /login dene.\n";
echo "setup-*.php dosyalarını SİL.\n";
