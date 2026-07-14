<?php

/**
 * Plesk teşhis: https://kitap.kurtulum.com/diagnose.php
 * İş bitince bu dosyayı sil.
 */
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);
if (basename(__DIR__) !== 'public') {
    // Document root proje köküyse
    $root = __DIR__;
    $public = __DIR__.'/public';
} else {
    $public = __DIR__;
}

echo "=== DERNEK KITAP TEŞHİS ===\n";
echo 'Zaman: '.date('c')."\n";
echo 'PHP: '.PHP_VERSION."\n";
echo 'Bu dosya: '.__FILE__."\n";
echo 'Root aday: '.$root."\n\n";

$checks = [
    'composer.json' => $root.'/composer.json',
    'vendor/' => $root.'/vendor',
    'vendor/filament' => $root.'/vendor/filament',
    'app/Filament' => $root.'/app/Filament',
    'routes/web.php' => $root.'/routes/web.php',
    '.env' => $root.'/.env',
    'public/index.php' => $public.'/index.php',
    'public/kitap-ok.txt' => $public.'/kitap-ok.txt',
];

foreach ($checks as $label => $path) {
    $ok = file_exists($path) || is_dir($path);
    echo ($ok ? '[OK] ' : '[YOK] ').$label."\n";
}

echo "\n--- routes/web.php (ilk satırlar) ---\n";
$web = $root.'/routes/web.php';
if (is_file($web)) {
    echo substr(file_get_contents($web), 0, 400)."\n";
} else {
    echo "web.php bulunamadı\n";
}

echo "\n--- composer laravel/filament ---\n";
$composer = $root.'/composer.json';
if (is_file($composer)) {
    $json = json_decode(file_get_contents($composer), true);
    echo 'laravel/framework: '.($json['require']['laravel/framework'] ?? '?')."\n";
    echo 'filament/filament: '.($json['require']['filament/filament'] ?? 'YOK')."\n";
}

echo "\nBitti. Bu sayfayı görüyorsan DOĞRU public klasöründesin.\n";
echo "Sonra /login açılmalı (Filament + composer install şart).\n";
