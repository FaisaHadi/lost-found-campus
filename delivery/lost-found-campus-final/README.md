# Arsip Akhir Lost Found Campus

Folder ini adalah panduan staging arsip akhir. Isinya mendokumentasikan struktur pengumpulan akademik tanpa menggandakan seluruh source tree di repository.

Arsip akhir yang disarankan:

```text
lost-found-campus-final/
  backend/
  mobile/
  docs/
  presentation/
  database/
  screenshots/
  apk/
  README.md
```

Aturan arsip:

- Salin `backend/` dari root, tanpa `vendor/`, `node_modules/`, `.env`, log, cache, dan file lokal generated.
- Salin `mobile/` dari root, tanpa `.dart_tool/`, `build/`, dan secret signing lokal.
- Salin `docs/` dari root.
- Salin `README.md` dari root.
- Sertakan `delivery/apk/lost-found-campus-release.apk` jika APK sudah dibuat.
- Sertakan screenshot, slide, dan export SQL opsional setelah review akhir.
- Jangan sertakan secret asli, keystore, atau data lokal privat.

Perintah rebuild utama:

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

cd ../mobile
flutter pub get
flutter build apk --release --dart-define=API_BASE_URL=https://your-domain.example/api/v1
```
