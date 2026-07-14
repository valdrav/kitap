<?php

/**
 * /setup-artisan.php — iş bitince SİL
 */
set_time_limit(0);
ini_set('display_errors', '1');
error_reporting(E_ALL & ~E_WARNING);
header('Content-Type: text/plain; charset=utf-8');

function phpWorks(string $bin): bool
{
    $out = [];
    $code = 1;
    @exec(escapeshellarg($bin).' -r "echo PHP_MAJOR_VERSION;" 2>/dev/null', $out, $code);

    return $code === 0 && isset($out[0]) && (int) $out[0] >= 8;
}

function findPhpCli(): string
{
    foreach ([
        '/opt/plesk/php/8.3/bin/php',
        '/opt/plesk/php/8.2/bin/php',
        '/opt/plesk/php/8.4/bin/php',
        'php',
    ] as $bin) {
        if (phpWorks($bin)) {
            return $bin;
        }
    }

    return '/opt/plesk/php/8.3/bin/php';
}

$root = null;
foreach ([dirname(__DIR__), __DIR__, realpath(__DIR__.'/..') ?: '', realpath(__DIR__.'/../..') ?: ''] as $dir) {
    if ($dir && is_file($dir.'/artisan') && is_file($dir.'/vendor/autoload.php')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "HATA: vendor yok. Önce /setup-vendor.php\n";
    exit(1);
}

$php = findPhpCli();
echo "Proje: {$root}\nPHP CLI: {$php}\n\n";
chdir($root);

if (! is_file($root.'/.env') && is_file($root.'/.env.example')) {
    copy($root.'/.env.example', $root.'/.env');
    echo ".env kopyalandı — DB: kitap_db / kitap_user kontrol et\n\n";
}

$artisan = $root.'/artisan';
foreach ([
    'key:generate --force',
    'migrate --seed --force',
    'storage:link',
    'optimize:clear',
    'config:cache',
    'route:cache',
    'filament:assets',
] as $cmd) {
    echo "\n> artisan {$cmd}\n";
    passthru(escapeshellarg($php).' '.escapeshellarg($artisan).' '.$cmd.' 2>&1', $code);
    echo "kod: {$code}\n";
}

echo "\nBitti → /login\n";
echo "setup-*.php SİL\n";
