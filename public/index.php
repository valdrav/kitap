<?php

/**
 * Laravel giriş noktası — Plesk iç içe public/public yapısına uyumlu.
 */
define('LARAVEL_START', microtime(true));

$autoloadCandidates = [
    __DIR__.'/../vendor/autoload.php',
    __DIR__.'/../../vendor/autoload.php',
    __DIR__.'/vendor/autoload.php',
];

$autoload = null;
foreach ($autoloadCandidates as $candidate) {
    if (is_file($candidate)) {
        $autoload = $candidate;
        break;
    }
}

if ($autoload === null) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Kurulum</title></head><body style="font-family:sans-serif;max-width:720px;margin:40px auto;padding:0 16px">';
    echo '<h1>vendor klasörü yok</h1>';
    echo '<p>Composer paketleri kurulmamış. Tarayıcıda aç:</p>';
    echo '<p><a href="/setup-vendor.php"><strong>/setup-vendor.php</strong></a></p>';
    echo '<p>veya: <a href="/public/setup-vendor.php">/public/setup-vendor.php</a></p>';
    echo '<p>Aranan yollar:</p><ul>';
    foreach ($autoloadCandidates as $c) {
        echo '<li><code>'.htmlspecialchars($c).'</code></li>';
    }
    echo '</ul></body></html>';
    exit(1);
}

require $autoload;

// vendor/autoload.php -> project root = iki üst
$projectRoot = dirname($autoload, 2);

if (is_file($maintenance = $projectRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

$bootstrap = $projectRoot.'/bootstrap/app.php';
if (! is_file($bootstrap)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "bootstrap/app.php bulunamadı: {$bootstrap}\n";
    exit(1);
}

/** @var Illuminate\Foundation\Application $app */
$app = require_once $bootstrap;

$app->handleRequest(Illuminate\Http\Request::capture());
