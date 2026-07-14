# Dernek Kitap Yönetim Sistemi

Laravel 12 + Filament 3 ile geliştirilmiş, tam Türkçe dernek kitap satış / gelir-gider takip paneli.

Canlı adres: `https://kitap.kurtulum.com`

## Sunucu Kurulumu

```bash
git clone <repo-url> kitap
cd kitap
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

`.env` içinde veritabanı bilgilerini doldurun:

```env
APP_URL=https://kitap.kurtulum.com
DB_CONNECTION=mysql
DB_DATABASE=kitap
DB_USERNAME=...
DB_PASSWORD=...
```

Ardından:

```bash
php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Web sunucusunun `public/` klasörünü document root olarak ayarlayın. Domain: `kitap.kurtulum.com`

## Giriş Bilgileri

| | |
|---|---|
| Adres | `https://kitap.kurtulum.com/login` |
| E-posta | `admin@kurtulum.com` |
| İlk şifre | `Kurtulum2026!` |

Şifreyi **Ayarlar** sayfasından değiştirebilirsiniz (`/settings`).

## Yerel Geliştirme

```bash
composer install
cp .env.example .env
php artisan key:generate
# SQLite için: DB_CONNECTION=sqlite ve database/database.sqlite oluşturun
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Özellikler

- Bölge bazlı kitap satışı kaydı
- Kargo durum takibi
- Kitap ekleme / fiyat / stok / kapak görseli
- Gelir, gider ve net kar analizi
- Dashboard grafikleri ve detaylı raporlar
- Ayarlar: e-posta ve şifre değiştirme
- Karanlık / aydınlık tema
- Tamamen Türkçe arayüz
