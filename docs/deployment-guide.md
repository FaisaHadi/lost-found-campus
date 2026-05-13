# Panduan Deploy

Fase ini menyiapkan deployment tanpa memasukkan secret ke repository dan tanpa mengubah arsitektur.

## 1. Environment Produksi

Produksi harus mematikan debug, memakai credential database asli, membuka storage publik dengan benar, dan menjaga perilaku Sanctum tetap dapat diprediksi.

Langkah deployment:

1. Salin `backend/.env.production.example` menjadi `.env` di server.
2. Set `APP_ENV=production`.
3. Set `APP_DEBUG=false`.
4. Buat `APP_KEY` asli dengan `php artisan key:generate`.
5. Set `APP_URL` ke URL HTTPS publik.
6. Isi credential database MySQL.
7. Set `FILESYSTEM_DISK=public`.
8. Set `SESSION_ENCRYPT=true`.
9. Set `SANCTUM_STATEFUL_DOMAINS` ke domain web yang digunakan.
10. Jangan commit `.env` produksi yang sudah berisi nilai asli.

Validasi:

- `APP_ENV` bernilai `production`.
- `APP_DEBUG` bernilai `false`.
- `DB_CONNECTION=mysql`.
- `APP_URL` memakai HTTPS.
- `public/storage` mengarah ke `storage/app/public`.
- Bearer token Sanctum berjalan untuk aplikasi Flutter.
- Masuk session admin berjalan untuk `/admin`.

Struktur dokumentasi:

- `backend/.env.example`: baseline development lokal.
- `backend/.env.production.example`: template produksi dengan placeholder aman.
- `docs/deployment-guide.md`: proses deployment dan validasi.

Strategi presentasi:

- Jelaskan bahwa secret tidak dimasukkan ke repository.
- Tampilkan `.env.production.example` sebagai bukti perencanaan produksi.
- Tekankan `APP_DEBUG=false`, token Sanctum, dan storage link.

## 2. Build Produksi Laravel

Laravel produksi perlu dependency optimal, konfigurasi cache, route cache, view cache, dan aset web hasil build.

Perintah setup produksi:

```bash
cd backend
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Checklist build:

- Dependency Composer terpasang tanpa paket dev.
- Dependency NPM terpasang bersih.
- Aset Vite dibuat di `public/build`.
- Migrasi dijalankan dengan `--force`.
- Storage link dibuat.
- Config, route, dan view cache dibuat.
- Folder `storage/` dan `bootstrap/cache/` writable.
- Endpoint `/up` merespons.

Kesiapan migrasi:

- Migrasi memiliki timestamp dan urutan jelas.
- Foreign key dideklarasikan.
- Index tersedia untuk filter status dan kepemilikan.
- Tidak ada migrasi destruktif yang dibutuhkan untuk pengumpulan akhir.

## 3. Deploy Platform Web

Web admin adalah permukaan operasional untuk moderasi. Proses deploy harus menjaga aset, upload, dan PWA tetap berjalan.

Checklist:

- Jalankan `npm run build`.
- Pastikan `public/build/manifest.json` ada.
- Pastikan `/manifest.json`, `/service-worker.js`, `/offline.html`, dan `/pwa/icon.svg` dapat diakses.
- Pastikan upload drag and drop masih menampilkan preview JPG, PNG, dan WEBP.
- Pastikan server menolak upload tidak valid atau di atas 4 MB.
- Pastikan dashboard memuat data demo tanpa beban query berlebihan.
- Pastikan halaman admin bekerja di desktop dan layar sempit.

Checklist aset produksi:

- `public/build` ada di server.
- Web server mengarah ke folder `public/`.
- Service worker dapat diakses.
- Manifest PWA dapat diakses.
- CSS dan JS hasil Vite mendapat respons 200.

Strategi presentasi:

- Tunjukkan prompt install jika browser mendukung.
- Jelaskan bahwa fallback offline hanya untuk halaman offline, bukan moderasi offline penuh.

## 4. Build Mobile Flutter

Build release membutuhkan API URL yang dapat dijangkau, signing Android, dan permission kamera/GPS yang benar.

Persiapan Android release:

1. Buat keystore di luar Git.
2. Salin `mobile/android/key.properties.example` ke `mobile/android/key.properties`.
3. Isi nilai signing asli.
4. Jangan commit `key.properties`, `.jks`, atau `.keystore`.
5. Build dengan URL API produksi.

Perintah build APK:

```bash
cd mobile
flutter build apk --release --dart-define=API_BASE_URL=https://your-domain.example/api/v1
```

Output yang diharapkan:

```text
mobile/build/app/outputs/flutter-apk/app-release.apk
```

Sebelum pengumpulan akhir, lakukan rebuild bersih dengan URL backend asli:

```bash
flutter clean
flutter pub get
flutter build apk --release --dart-define=API_BASE_URL=https://your-domain.example/api/v1
```

Checklist validasi release:

- Package id Android adalah `id.ac.campus.lostfound`.
- Label aplikasi adalah `Lost Found Campus`.
- Permission kamera dideklarasikan.
- Permission lokasi fine dan coarse dideklarasikan.
- Deskripsi permission iOS untuk kamera, lokasi, dan foto tersedia.
- URL dasar API produksi mengarah ke `/api/v1`.
- Masuk berhasil terhadap backend deploy.
- Kamera berjalan pada perangkat Android nyata.
- GPS berjalan pada perangkat Android nyata.

## 5. Strategi Deploy

Deploy harus realistis untuk project mahasiswa dan tetap mudah dirawat setelah pengumpulan.

Strategi shared hosting:

- Gunakan hosting PHP 8.2+ dengan MySQL.
- Arahkan document root ke `backend/public`.
- Upload source backend tanpa `.env`, `vendor`, `node_modules`, dan cache lokal.
- Jika hosting tidak punya SSH, jalankan Composer lokal lalu upload `vendor`.
- Jalankan migrasi lewat SSH atau panel hosting jika tersedia.
- Jalankan `php artisan storage:link` atau buat symlink ekuivalen secara manual.

Strategi VPS:

- Install PHP 8.2+, Composer, Node.js, MySQL, dan Nginx atau Apache.
- Clone repository.
- Konfigurasi `.env`.
- Jalankan perintah build produksi.
- Arahkan document root web server ke `backend/public`.
- Gunakan HTTPS melalui provider server atau reverse proxy.
- Jadwalkan backup MySQL dan gambar upload.

Deploy MySQL:

- Buat database `lost_found_campus`.
- Buat user database khusus.
- Berikan privilege hanya untuk database tersebut.
- Jalankan `php artisan migrate --force`.
- Jalankan `php artisan db:seed --class=DatabaseSeeder` hanya jika data demo dibutuhkan.

Storage:

- Simpan gambar laporan di `storage/app/public/reports`.
- Pastikan symlink `public/storage` ada.
- Backup `storage/app/public` bersama database.

Distribusi APK:

- Untuk pengumpulan akademik, APK dapat dibagikan langsung.
- Sertakan kebutuhan URL backend pada catatan pengumpulan.
- Untuk rilis luas, gunakan signed AAB/APK melalui Google Play atau distribusi internal.

## 6. Validasi Database Akhir

Data produksi harus dimulai dari migrasi bersih dan optional seed yang dapat diprediksi.

Checklist migrasi:

- `php artisan migrate:fresh --seed` berjalan lokal.
- `php artisan migrate --force` berjalan di produksi.
- Tabel user, category, report, claim, notification, dan token ada.
- Foreign key aktif.
- Soft delete tersedia untuk record domain utama.

Checklist data seed:

- Akun admin demo ada.
- Pengguna test untuk demo mobile ada.
- Pengguna claimant untuk demo klaim ada.
- Kategori ada.
- Laporan demo ada.
- Klaim demo ada.
- Notifikasi demo ada.

Keamanan produksi:

- Jangan menjalankan `migrate:fresh` pada produksi yang berisi data nyata.
- Data seed demo hanya untuk lingkungan akademik/demo.

## 7. QA Akhir

Validasi akhir perlu mencakup command line dan end-to-end manual.

Backend:

- Registrasi pengguna.
- Masuk pengguna.
- Ambil profil terautentikasi.
- Keluar pengguna.
- Buat laporan.
- Daftar laporan.
- Detail laporan.
- Kirim klaim.
- Daftar notifikasi.

Web:

- Masuk admin.
- Kartu dasbor.
- Moderasi laporan.
- Moderasi klaim.
- CRUD kategori.
- Upload drag and drop.
- Manifest PWA dan fallback offline.

Mobile:

- Masuk.
- Persistensi token.
- Jelajahi laporan.
- Buat laporan.
- Ambil gambar kamera.
- Ambil koordinat GPS.
- Kirim klaim.
- Alur notifikasi dibaca.

Checklist demo:

- Server backend menyala.
- Web admin dapat dibuka.
- URL API mobile dapat dijangkau emulator/perangkat.
- Storage link ada.
- Akun demo berjalan.
- Siklus laporan dapat ditunjukkan kurang dari 5 menit.

## 8. Paket Dokumentasi Akhir

Reviewer akademik membutuhkan kedalaman engineering sekaligus instruksi eksekusi yang jelas.

Struktur dokumentasi:

- `README.md`: ringkasan project dan setup cepat.
- `backend/README.md`: setup backend/web dan validasi.
- `mobile/README.md`: setup mobile dan persiapan release.
- `docs/final-system-architecture.md`: arsitektur terintegrasi dan stabilisasi.
- `docs/deployment-guide.md`: panduan produksi dan deployment.
- `docs/academic-presentation-guide.md`: panduan demo dan presentasi.
- `docs/visual-assets.md`: diagram dan daftar screenshot.
- `docs/final-submission-checklist.md`: checklist paket.
- `docs/final-engineering-evaluation.md`: evaluasi akademik akhir.
- `docs/api-*.md`: dokumentasi API.
- `docs/web-platform.md`: dokumentasi web.
- `docs/mobile-platform.md`: dokumentasi mobile.

Strategi presentasi:

- Mulai dari arsitektur.
- Tunjukkan platform yang sudah diimplementasikan.
- Jelaskan backend bersama.
- Demonstrasikan lifecycle status.
- Tutup dengan pengujian dan kesiapan produksi.

## 9. Presentasi, Aset Visual, Review, dan Paket Akhir

Dokumen terkait:

- `docs/academic-presentation-guide.md`
- `docs/visual-assets.md`
- `docs/final-engineering-evaluation.md`
- `docs/final-submission-checklist.md`
