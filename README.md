# Platform Lost & Found Campus

Lost & Found Campus adalah project akademik multiplatform untuk mengelola laporan barang hilang dan ditemukan di lingkungan kampus. Sistem ini memakai satu backend Laravel REST API, dashboard admin Laravel Blade, aplikasi mobile Flutter, dan satu database MySQL terpusat.

Implementasi saat ini mencakup backend API, platform admin web, dan aplikasi mobile pengguna. Tahap akhir project berfokus pada stabilisasi, validasi integrasi, dan kesiapan presentasi.

## Gambaran Arsitektur

```text
Client Platform
  - Platform Admin Web
  - Aplikasi Mobile Flutter

        |
        v

REST API
  - Laravel 12
  - Autentikasi Laravel Sanctum

        |
        v

Backend Aplikasi
  - Services
  - Repositories
  - Models
  - Policies
  - Notifikasi

        |
        v

Database MySQL
```

Backend menjadi sumber data utama. Web dan mobile harus berkomunikasi melalui REST API yang terdokumentasi dan tidak boleh melewati validasi, otorisasi, atau aturan akses data backend.

## Platform Yang Dipakai

- Web: Laravel Blade dengan Tailwind CSS
- Mobile: Flutter
- Backend API: Laravel 12 REST API
- Autentikasi: Laravel Sanctum
- Database: MySQL

## Stack Teknologi

- PHP dan Laravel 12
- Laravel Sanctum
- MySQL
- Blade template
- Tailwind CSS
- Flutter dan Dart
- Git
- Kontrak API berbasis Markdown

## Fitur Per Platform

Fitur mobile:

- Integrasi kamera
- Lokasi GPS
- Pembuatan laporan, penelusuran laporan, klaim, dan notifikasi

Fitur web:

- Unggah gambar dengan drag and drop
- Dukungan Progressive Web App
- Dasbor admin, moderasi, manajemen kategori, dan pelacakan notifikasi

## Struktur Repository

```text
lost-found-campus/
  backend/       Backend Laravel, REST API, dan platform admin web
  mobile/        Aplikasi mobile Flutter
  docs/          Aturan engineering, roadmap, dan dokumentasi project
  api-contract/  Fondasi kontrak REST API
  ui-design/     Referensi UI dan aset perencanaan desain
  README.md      Ringkasan project
```

## Alur Kerja Engineering

Pengembangan mengikuti pendekatan API-first dan stability-first:

1. Definisikan atau perbarui kontrak API sebelum mengubah perilaku client atau backend.
2. Jaga logika backend tetap berlapis melalui controller, service, repository, model, policy, dan notification.
3. Simpan perilaku khusus platform di folder platform masing-masing.
4. Hindari pekerjaan fitur tanpa scope yang terdokumentasi.
5. Tinjau perubahan dari sisi maintainability, konsistensi, dan kompatibilitas API sebelum digabungkan.

## Setup Lokal

Backend:

```bash
cd backend
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

Mobile:

```bash
cd mobile
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

Di Windows, aktifkan Developer Mode sebelum menjalankan `flutter pub get` jika dukungan symlink plugin belum aktif.

## Perintah Validasi

```bash
cd backend
php artisan test
npm run build

cd ../mobile
flutter analyze --no-pub
flutter test --no-pub
```

## Dokumen Delivery Akhir

- `docs/final-system-architecture.md`
- `docs/deployment-guide.md`
- `docs/academic-presentation-guide.md`
- `docs/visual-assets.md`
- `docs/final-submission-checklist.md`
- `docs/final-engineering-evaluation.md`
- `docs/future-roadmap.md`
- `docs/defense-preparation.md`
- `docs/final-closure-report.md`
