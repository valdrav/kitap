<?php

/**
 * /run-migrate.php — key sonrası migrate+seed
 */
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING);
header('Content-Type: text/plain; charset=utf-8');

function findPhpCli(): string
{
    foreach (['/opt/plesk/php/8.3/bin/php', '/opt/plesk/php/8.2/bin/php'] as $bin) {
        $out = [];
        $code = 1;
        @exec(escapeshellarg($bin).' -r "echo PHP_MAJOR_VERSION;" 2>/dev/null', $out, $code);
        if ($code === 0 && isset($out[0]) && (int) $out[0] >= 8) {
            return $bin;
        }
    }

    return '/opt/plesk/php/8.3/bin/php';
}

$root = null;
foreach ([dirname(__DIR__), __DIR__, realpath(__DIR__.'/..') ?: ''] as $dir) {
    if ($dir && is_file($dir.'/artisan') && is_file($dir.'/vendor/autoload.php')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "artisan/vendor yok\n";
    exit(1);
}

$php = findPhpCli();
echo "Root: {$root}\nPHP: {$php}\n\n";
chdir($root);

foreach ([
    'config:clear',
    'cache:clear',
    'migrate --force',
    'db:seed --force',
    'storage:link',
    'optimize:clear',
] as $cmd) {
    echo "> artisan {$cmd}\n";
    passthru(escapeshellarg($php).' '.escapeshellarg($root.'/artisan').' '.$cmd.' 2>&1', $code);
    echo "kod: {$code}\n\n";
}

echo "Bitti. /login dene.\n";
echo "run-migrate.php ve write-key.php SİL. APP_DEBUG=false yap.\n";
