<?php

/**
 * Hızlı düzeltme: routes/web.php içindeki / → /login yönlendirmesini kaldırır
 * (ERR_TOO_MANY_REDIRECTS). Bir kez açıp silin.
 */
header('Content-Type: text/plain; charset=utf-8');

$root = null;
foreach ([dirname(__DIR__), __DIR__] as $dir) {
    if (is_file($dir.'/routes/web.php')) {
        $root = $dir;
        break;
    }
}

if (! $root) {
    echo "routes/web.php bulunamadı\n";
    exit;
}

$path = $root.'/routes/web.php';
$fixed = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

/*
| Filament panel path '' — / paneli kapsar.
| / → /login yönlendirmesi YOK (giriş sonrası redirect döngüsü önlenir).
*/

Route::view('/welcome', 'welcome');

PHP;

if (@file_put_contents($path, $fixed) === false) {
    echo "Yazılamadı: {$path}\nPlesk ile git pull yapın.\n";
    exit(1);
}

echo "OK: {$path} düzeltildi\n";

// route / config cache sil
foreach (glob($root.'/bootstrap/cache/*.php') ?: [] as $f) {
    if (is_file($f) && basename($f) !== '.gitignore') {
        @unlink($f);
        echo "cache sil: ".basename($f)."\n";
    }
}

echo "\nTarayıcıda kitap.kurtulum.com çerezlerini silin, sonra /login açın.\n";
echo "Bu dosyayı SİLİN: break-redirect.php\n";

// kendini silmeyi dene
@unlink(__FILE__);
if (dirname(__FILE__) !== $root) {
    $twin = $root.'/break-redirect.php';
    if (is_file($twin)) {
        @unlink($twin);
    }
}
