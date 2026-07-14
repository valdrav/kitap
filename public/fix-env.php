<?php

/**
 * /fix-env.php — .env düzelt + migrate. İş bitince SİL.
 */
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING);
header('Content-Type: text/plain; charset=utf-8');

function findPhpCli(): string
{
    foreach (['/opt/plesk/php/8.3/bin/php', '/opt/plesk/php/8.2/bin/php', 'php'] as $bin) {
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
    echo "vendor yok\n";
    exit(1);
}

echo "Root: {$root}\n";
chdir($root);

$envPath = $root.'/.env';
if (! is_file($envPath)) {
    copy($root.'/.env.example', $envPath);
    echo ".env oluşturuldu\n";
}

$env = file_get_contents($envPath);

$set = function (string $key, string $value) use (&$env): void {
    $line = $key.'='.$value;
    if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $env)) {
        $env = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $env);
    } else {
        $env .= "\n".$line."\n";
    }
};

$set('APP_NAME', '"Dernek Kitap"');
$set('APP_ENV', 'production');
$set('APP_DEBUG', 'true'); // geçici — giriş açılınca false yap
$set('APP_URL', 'https://kitap.kurtulum.com');
$set('DB_CONNECTION', 'mysql');
$set('DB_HOST', '127.0.0.1');
$set('DB_PORT', '3306');
$set('DB_DATABASE', 'kitap_db');
$set('DB_USERNAME', 'kitap_user');
$set('DB_PASSWORD', 'Valdrav115');
$set('SESSION_DRIVER', 'file');
$set('CACHE_STORE', 'file');
$set('QUEUE_CONNECTION', 'sync');
$set('FILESYSTEM_DISK', 'public');

file_put_contents($envPath, $env);
echo ".env güncellendi (DB + APP_DEBUG=true)\n";

// storage izinleri
@mkdir($root.'/storage/framework/sessions', 0775, true);
@mkdir($root.'/storage/framework/views', 0775, true);
@mkdir($root.'/storage/framework/cache', 0775, true);
@mkdir($root.'/storage/logs', 0775, true);
@mkdir($root.'/bootstrap/cache', 0775, true);
@chmod($root.'/storage', 0775);
@chmod($root.'/bootstrap/cache', 0775);

$php = findPhpCli();
echo "PHP: {$php}\n\n";

foreach ([
    'key:generate --force',
    'migrate --force',
    'db:seed --force',
    'storage:link',
    'optimize:clear',
] as $cmd) {
    echo "> artisan {$cmd}\n";
    passthru(escapeshellarg($php).' '.escapeshellarg($root.'/artisan').' '.$cmd.' 2>&1', $code);
    echo "kod: {$code}\n\n";
}

echo "Bitti. Şimdi /login dene.\n";
echo "Çalışınca .env içinde APP_DEBUG=false yap ve fix-env.php / show-error.php SİL.\n";
