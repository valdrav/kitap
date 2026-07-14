# Canlı Kurulum (kitap.kurtulum.com)

## Sorun

Sitede "Let's get started / Laravel v13" görünüyorsa **yanlış proje** çalışıyordur.
Bizim panel: Laravel 12 + Filament. Giriş adresi: `/admin/login`

## Plesk / SSH ile doğru kurulum

### 1) Eski (yanlış) Laravel klasörünü yedekle

```bash
cd /var/www/vhosts/kurtulum.com/kitap.kurtulum.com
# Yol sende farklı olabilir; domain'in document root üstüne çık

mkdir -p ../_yedek
mv httpdocs ../_yedek/httpdocs-laravel13-$(date +%Y%m%d) 2>/dev/null || true
mv public_html ../_yedek/public_html-laravel13-$(date +%Y%m%d) 2>/dev/null || true
```

### 2) Doğru projeyi çek

```bash
cd /var/www/vhosts/kurtulum.com/kitap.kurtulum.com
git clone https://github.com/valdrav/kitap.git app
cd app
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

### 3) `.env` ayarla

```env
APP_NAME="Dernek Kitap"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kitap.kurtulum.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kitap
DB_USERNAME=VERITABANI_KULLANICI
DB_PASSWORD=VERITABANI_SIFRE
```

### 4) Veritabanı + cache

```bash
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:assets
```

### 5) Document Root (ÇOK ÖNEMLİ)

Plesk → Domain `kitap.kurtulum.com` → Hosting Ayarları → Document root:

```
.../kitap.kurtulum.com/app/public
```

`app` değil, **`app/public`** olmalı.

Nginx/Apache yeniden yükle.

### 6) Kontrol

Tarayıcıda aç:

- https://kitap.kurtulum.com/  → `/admin/login` yönlendirmesi
- https://kitap.kurtulum.com/admin/login → giriş formu

Giriş:

- E-posta: `admin@kurtulum.com`
- Şifre: `Kurtulum2026!`

---

Hâlâ "Let's get started" görürsen document root yanlış klasöre bakıyordur.
