# Backend Lost & Found Campus

Folder ini berisi backend Laravel, REST API, dan platform administrasi Blade untuk sistem Lost & Found Campus.

## Tanggung Jawab

- REST API di bawah `/api/v1`
- Autentikasi token dengan Laravel Sanctum
- Platform admin web di `/admin`
- Alur laporan, klaim, kategori, dan notifikasi
- Penyimpanan dan pembersihan gambar
- Otorisasi berbasis role dan policy
- Fondasi PWA untuk platform admin

## Setup Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

Akun demo setelah seeding:

```text
admin@example.com / password123
test@example.com / password123
claimant@example.com / password123
```

## Validasi

```bash
php artisan config:clear
php artisan route:list --path=api/v1
php artisan test
npm run build
```

## Arsitektur

```text
routes
-> controllers
-> form requests
-> policies / middleware
-> services
-> repositories
-> Eloquent models
```

Controller web dan API berbagi service layer agar moderasi, penanganan gambar, notifikasi, dan transisi status tetap konsisten.
