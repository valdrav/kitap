<?php

/**
 * Plesk bridge: document root = proje kökü (.../kitap.kurtulum.com/public)
 */
$vendor = __DIR__.'/vendor/autoload.php';
$laravelPublic = __DIR__.'/public/index.php';

if (! is_file($vendor)) {
    if (is_file(__DIR__.'/setup-vendor.php')) {
        require __DIR__.'/setup-vendor.php';
        exit;
    }
    if (is_file(__DIR__.'/public/setup-vendor.php')) {
        require __DIR__.'/public/setup-vendor.php';
        exit;
    }
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>vendor yok</h1>';
    echo '<p>Aç: <a href="/setup-vendor.php">/setup-vendor.php</a></p>';
    exit;
}

if (! is_file($laravelPublic)) {
    http_response_code(500);
    echo 'public/index.php yok';
    exit;
}

require $laravelPublic;
