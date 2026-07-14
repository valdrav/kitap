<?php

/**
 * Plesk: /setup-vendor.php
 * open_basedir yüzünden is_file(/opt/plesk/...) patlar; yine de exec dene.
 * Web PHP 8.3 iken PATH'teki php 7.4 olabiliyor.
 */
set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('display_errors', '1');
error_reporting(E_ALL & ~E_WARNING);
header('Content-Type: text/plain; charset=utf-8');

echo "=== Dernek Kitap — composer install ===\n\n";

function phpWorks(string $bin): bool
{
    $out = [];
    $code = 1;
    @exec(escapeshellarg($bin).' -r "echo PHP_MAJOR_VERSION;" 2>/dev/null', $out, $code);

    return $code === 0 && isset($out[0]) && (int) $out[0] >= 8;
}

function findPhpCli(): string
{
    // open_basedir is_file engeller — kontrol etme, doğrudan dene
    $candidates = [
        '/opt/plesk/php/8.3/bin/php',
        '/opt/plesk/php/8.2/bin/php',
        '/opt/plesk/php/8.4/bin/php',
        '/opt/plesk/php/8.1/bin/php',
        'php',
    ];

    foreach ($candidates as $bin) {
        if (phpWorks($bin)) {
            return $bin;
        }
    }

    // 8.x bulunamadıysa yine plesk 8.3'ü zorla (exec bazen yine çalışır)
    return '/opt/plesk/php/8.3/bin/php';
}

function findProjectRoot(): ?string
{
    foreach ([dirname(__DIR__), __DIR__, realpath(__DIR__.'/..') ?: '', realpath(__DIR__.'/../..') ?: ''] as $dir) {
        if ($dir && is_file($dir.'/composer.json') && is_file($dir.'/artisan')) {
            return $dir;
        }
    }

    return null;
}

$root = findProjectRoot();
if ($root === null) {
    echo "HATA: composer.json bulunamadı.\n";
    exit(1);
}

echo "Proje kökü: {$root}\n";
chdir($root);

$php = findPhpCli();
echo "PHP CLI: {$php}\n";

$verOut = [];
@exec(escapeshellarg($php).' -v 2>&1', $verOut, $verCode);
echo trim(implode("\n", $verOut))."\n\n";

$phpMajor = 0;
@exec(escapeshellarg($php).' -r "echo PHP_MAJOR_VERSION;" 2>/dev/null', $majOut, $majCode);
if ($majCode === 0 && isset($majOut[0])) {
    $phpMajor = (int) $majOut[0];
}

$ignorePlatform = $phpMajor < 8;
if ($ignorePlatform) {
    echo "UYARI: CLI PHP < 8. Composer --ignore-platform-reqs ile kurulacak.\n";
    echo "Site zaten PHP 8.3 ile çalışıyorsa bu sorun değil.\n";
    echo "Kalıcı çözüm: Plesk → PHP Settings → 8.3 + SSH'da /opt/plesk/php/8.3/bin/php kullan.\n\n";
}

$composerPhar = $root.'/composer.phar';
if (! is_file($composerPhar)) {
    echo "composer.phar indiriliyor...\n";
    $setup = $root.'/composer-setup.php';
    $ok = false;

    $installer = @file_get_contents('https://getcomposer.org/installer');
    if ($installer !== false) {
        file_put_contents($setup, $installer);
        $ok = true;
    } else {
        @exec('curl -sS https://getcomposer.org/installer -o '.escapeshellarg($setup).' 2>&1', $cOut, $cCode);
        $ok = is_file($setup) && filesize($setup) > 100;
        if (! empty($cOut)) {
            echo implode("\n", $cOut)."\n";
        }
    }

    if (! $ok) {
        echo "HATA: installer indirilemedi.\n";
        exit(1);
    }

    $cmd = escapeshellarg($php).' '.escapeshellarg($setup)
        .' --install-dir='.escapeshellarg($root)
        .' --filename=composer.phar';
    echo "> {$cmd}\n";
    passthru($cmd.' 2>&1', $setupCode);
    @unlink($setup);

    if (! is_file($composerPhar)) {
        echo "HATA: composer.phar yok. Kod: {$setupCode}\n";
        exit(1);
    }
    echo "composer.phar OK\n\n";
}

$flags = 'install --no-dev --optimize-autoloader --no-interaction';
if ($ignorePlatform) {
    $flags .= ' --ignore-platform-reqs';
}

$installCmd = escapeshellarg($php).' '.escapeshellarg($composerPhar).' '.$flags;
echo "composer install...\n> {$installCmd}\n\n";
passthru($installCmd.' 2>&1', $installCode);
echo "\nÇıkış kodu: {$installCode}\n";

if (is_file($root.'/vendor/autoload.php')) {
    echo "\nOK: vendor hazır.\n";
    echo "Şimdi: /setup-artisan.php\n";
    echo "Sonra setup-*.php dosyalarını SİL.\n";
    exit(0);
}

echo "\nHATA: vendor yok.\n";
echo "Plesk SSH Terminal (PHP 8.3 ile):\n\n";
echo "cd {$root}\n";
echo "/opt/plesk/php/8.3/bin/php composer.phar install --no-dev --optimize-autoloader\n";
exit(1);
