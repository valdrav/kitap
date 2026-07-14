<?php

/**
 * Tek seferlik: kurulum / teşhis scriptlerini siler, sonra kendini de siler.
 * Kullanım: /cleanup-helpers.php
 */
header('Content-Type: text/plain; charset=utf-8');

$roots = array_unique(array_filter([
    dirname(__DIR__),
    __DIR__,
    realpath(__DIR__.'/..') ?: null,
]));

$names = [
    'show-error.php',
    'force-env.php',
    'fix-env.php',
    'write-key.php',
    'diagnose.php',
    'setup-artisan.php',
    'setup-vendor.php',
    'run-migrate.php',
    'peek-env.php',
    'cleanup-helpers.php',
];

$deleted = [];
$failed = [];

foreach ($roots as $root) {
    foreach ($names as $name) {
        $path = $root.DIRECTORY_SEPARATOR.$name;
        if (! is_file($path)) {
            continue;
        }
        if (@unlink($path)) {
            $deleted[] = $path;
        } else {
            $failed[] = $path;
        }
    }
}

echo "Silinen (".count($deleted)."):\n";
foreach ($deleted as $p) {
    echo "  OK  {$p}\n";
}

if ($failed) {
    echo "\nSilinemedi (".count($failed)."):\n";
    foreach ($failed as $p) {
        echo "  FAIL {$p}\n";
    }
    echo "\nPlesk Dosya Yöneticisi ile elle silin.\n";
} else {
    echo "\nTamam. Bu adres artık 404 olmalı.\n";
}
