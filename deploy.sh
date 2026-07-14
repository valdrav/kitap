#!/bin/bash
set -e

echo "==> Dernek Kitap deploy"
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link || true
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:assets

echo "==> Tamam. Giriş: /admin/login"
echo "    E-posta: admin@kurtulum.com"
