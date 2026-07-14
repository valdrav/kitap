<?php

/**
 * Plesk: https://kitap.kurtulum.com/setup-vendor.php
 * composer install. İş bitince SİL.
 */
set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

echo "=== Dernek Kitap — composer install ===\n\n";

function findPhpCli(): string
{
    $candidates = [
        '/opt/plesk/php/8.3/bin/php',
        '/opt/plesk/php/8.2/bin/php',
        '/opt/plesk/php/8.1/bin/php',
        '/usr/bin/php',
        '/usr/local/bin/php',
    ];

    // PHP_BINARY çoğu zaman php-fpm olur; onu kullanma
    foreach ($candidates as $bin) {
        if (is_file($bin) && is_executable($bin)) {
            return $bin;
        }
    }

    // Son çare: version ile hangi php varsa
    foreach (glob('/opt/plesk/php/*/bin/php') ?: [] as $bin) {
        if (is_executable($bin)) {
            return $bin;
        }
    }

    return 'php';
}

function findProjectRoot(): ?string
{
    $dirs = [
        dirname(__DIR__),
        __DIR__,
        realpath(__DIR__.'/..') ?: '',
        realpath(__DIR__.'/../..') ?: '',
    ];

    foreach ($dirs as $dir) {
        if ($dir && is_file($dir.'/composer.json') && is_file($dir.'/artisan')) {
            return $dir;
        }
    }

    return null;
}

$root = findProjectRoot();
if ($root === null) {
    echo "HATA: composer.json / artisan bulunamadı.\n";
    echo "Çalışılan dizin: ".__DIR__."\n";
    exit(1);
}

echo "Proje kökü: {$root}\n";
chdir($root);

$php = findPhpCli();
echo "PHP CLI: {$php}\n";

$verOut = [];
exec(escapeshellarg($php).' -v 2>&1', $verOut, $verCode);
echo trim(implode("\n", $verOut))."\n\n";

if ($verCode !== 0 || stripos(implode("\n", $verOut), 'PHP') === false) {
    echo "HATA: PHP CLI çalışmadı. Plesk SSH ile şunu dene:\n";
    echo "cd {$root}\n";
    echo "/opt/plesk/php/8.3/bin/php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\"\n";
    echo "/opt/plesk/php/8.3/bin/php composer-setup.php\n";
    echo "/opt/plesk/php/8.3/bin/php composer.phar install --no-dev --optimize-autoloader\n";
    exit(1);
}

$composerPhar = $root.'/composer.phar';

if (! is_file($composerPhar)) {
    echo "composer.phar indiriliyor...\n";

    $installerUrl = 'https://getcomposer.org/installer';
    $installer = @file_get_contents($installerUrl);

    if ($installer === false) {
        // curl fallback
        $tmp = $root.'/composer-setup.php';
        $cmd = 'curl -sS '.escapeshellarg($installerUrl).' -o '.escapeshellarg($tmp);
        echo "file_get_contents başarısız, curl deneniyor...\n";
        passthru($cmd." 2>&1", $curlCode);
        if (! is_file($tmp) || filesize($tmp) < 100) {
            echo "HATA: Composer installer indirilemedi.\n";
            exit(1);
        }
    } else {
        file_put_contents($root.'/composer-setup.php', $installer);
    }

    $setup = $root.'/composer-setup.php';
    $cmd = escapeshellarg($php).' '.escapeshellarg($setup)
        .' --install-dir='.escapeshellarg($root)
        .' --filename=composer.phar';

    echo "> {$cmd}\n";
    passthru($cmd.' 2>&1', $setupCode);
    @unlink($setup);

    if (! is_file($composerPhar)) {
        echo "HATA: composer.phar oluşturulamadı. Kod: {$setupCode}\n";
        echo "SSH ile kur:\n";
        echo "cd {$root}\n";
        echo "{$php} -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\"\n";
        echo "{$php} composer-setup.php\n";
        echo "{$php} composer.phar install --no-dev --optimize-autoloader\n";
        exit(1);
    }
    echo "composer.phar OK\n\n";
}

echo "composer install başlıyor (uzun sürebilir)...\n\n";
$installCmd = escapeshellarg($php).' '.escapeshellarg($composerPhar)
    .' install --no-dev --optimize-autoloader --no-interaction';
echo "> {$installCmd}\n\n";
passthru($installCmd.' 2>&1', $installCode);

echo "\nÇıkış kodu: {$installCode}\n";

if (is_file($root.'/vendor/autoload.php')) {
    echo "\nOK: vendor/autoload.php oluştu.\n";
    echo "Şimdi aç: /setup-artisan.php\n";
    echo "Sonra bu dosyaları SİL: setup-vendor.php, setup-artisan.php\n";
    exit(0);
}

echo "\nHATA: vendor hâlâ yok.\n";
echo "Plesk → Tools → SSH Terminal:\n\n";
echo "cd {$root}\n";
echo "{$php} composer.phar install --no-dev --optimize-autoloader\n";
exit(1);
