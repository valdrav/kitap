<?php

/**
 * https://kitap.kurtulum.com/show-error.php
 * 500 nedenini gösterir. İş bitince SİL.
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

echo "=== 500 HATA TEŞHİSİ ===\n\n";

$roots = [
    dirname(__DIR__),
    __DIR__,
    realpath(__DIR__.'/..') ?: '',
    realpath(__DIR__.'/../..') ?: '',
];

$root = null;
foreach ($roots as $dir) {
    if ($dir && is_file($dir.'/vendor/autoload.php') && is_file($dir.'/bootstrap/app.php')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "vendor/bootstrap bulunamadı\n";
    exit;
}

echo "Root: {$root}\n";

$envFile = $root.'/.env';
echo ".env: ".(is_file($envFile) ? 'VAR' : 'YOK')."\n";

if (is_file($envFile)) {
    $env = file_get_contents($envFile);
    foreach (['APP_KEY', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'] as $key) {
        if (preg_match('/^'.$key.'=(.*)$/m', $env, $m)) {
            $val = trim($m[1], " \t\"'");
            if ($key === 'APP_KEY') {
                echo "{$key}: ".(strlen($val) > 10 ? 'DOLU ('.strlen($val).' karakter)' : 'BOŞ/EKSİK')."\n";
            } elseif ($key === 'DB_PASSWORD') {
                echo "{$key}: ".(strlen($val) ? 'DOLU' : 'BOŞ')."\n";
            } else {
                echo "{$key}: {$val}\n";
            }
        } else {
            echo "{$key}: TANIMLI DEĞİL\n";
        }
    }
}

echo "\nstorage writable: ".(is_writable($root.'/storage') ? 'EVET' : 'HAYIR')."\n";
echo "bootstrap/cache writable: ".(is_writable($root.'/bootstrap/cache') ? 'EVET' : 'HAYIR')."\n";

$log = $root.'/storage/logs/laravel.log';
if (is_file($log)) {
    echo "\n--- laravel.log (son 40 satır) ---\n";
    $lines = file($log);
    echo implode('', array_slice($lines, -40));
} else {
    echo "\nlaravel.log yok\n";
}

echo "\n--- Laravel boot denemesi ---\n";
try {
    require $root.'/vendor/autoload.php';
    $app = require $root.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/login', 'GET');
    $response = $kernel->handle($request);
    echo 'HTTP status: '.$response->getStatusCode()."\n";
    $content = $response->getContent();
    if ($response->getStatusCode() >= 400) {
        echo "Body (ilk 1500 karakter):\n";
        echo substr(strip_tags($content), 0, 1500)."\n";
    } else {
        echo "OK — /login çalışıyor gibi.\n";
    }
} catch (Throwable $e) {
    echo 'EXCEPTION: '.get_class($e)."\n";
    echo 'Mesaj: '.$e->getMessage()."\n";
    echo 'Dosya: '.$e->getFile().':'.$e->getLine()."\n";
    echo "\nStack (ilk 15):\n";
    $trace = explode("\n", $e->getTraceAsString());
    echo implode("\n", array_slice($trace, 0, 15))."\n";
}

echo "\nBu dosyayı SİL: show-error.php\n";
