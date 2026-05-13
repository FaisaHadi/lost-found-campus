# Checklist Pengumpulan Akhir

## Struktur Paket

Paket wajib:

- Source code root project.
- `backend/` untuk Laravel backend dan web admin.
- `mobile/` untuk aplikasi Flutter.
- `docs/` untuk dokumentasi.
- `api-contract/` untuk referensi kontrak API.
- Migrasi dan seeder backend.
- Contoh environment produksi.
- APK atau instruksi build APK.
- README dan instruksi setup.

Layout paket yang disarankan:

```text
lost-found-campus-final/
  README.md
  backend/
  mobile/
  docs/
  api-contract/
  delivery/
    README.md
    apk/
      lost-found-campus-release.apk
    sql/
      optional-database-export.sql
```

## Checklist Source Backend

- `composer.json` dan `composer.lock` disertakan.
- `package.json` dan `package-lock.json` disertakan.
- `app/`, `routes/`, `database/`, `resources/`, `config/`, dan `public/` disertakan.
- `.env` tidak disertakan.
- `.env.example` disertakan.
- `.env.production.example` disertakan.
- `vendor/` tidak disertakan kecuali diwajibkan aturan hosting/pengumpulan.
- `node_modules/` tidak disertakan.

## Checklist Source Mobile

- `pubspec.yaml` dan `pubspec.lock` disertakan.
- `lib/` disertakan.
- `android/` disertakan.
- `ios/` disertakan jika dibutuhkan.
- `build/` tidak disertakan.
- `.dart_tool/` tidak disertakan.
- `android/key.properties` tidak disertakan.
- File keystore tidak disertakan.
- `android/key.properties.example` disertakan.

## Checklist SQL dan Migrasi

- Migrasi Laravel tersedia di `backend/database/migrations`.
- Seeder tersedia di `backend/database/seeders`.
- Export SQL bersifat opsional dan hanya dibuat jika diminta dosen.
- Jika export SQL disertakan, jangan masukkan secret atau data pribadi nyata.

Perintah setup database bersih:

```bash
cd backend
php artisan migrate:fresh --seed
```

## Checklist APK

- Perintah build terdokumentasi.
- APK release disimpan di `apk/` jika sudah dibuat.
- Path APK hasil build adalah `mobile/build/app/outputs/flutter-apk/app-release.apk`.
- Nama file APK memakai nama project dan versi.
- URL backend API yang dipakai APK terdokumentasi.
- Permission perangkat sudah diuji.

## Checklist Dokumentasi

- README root selesai.
- README backend selesai.
- README mobile selesai.
- Dokumentasi API selesai.
- Dokumentasi platform web selesai.
- Dokumentasi platform mobile selesai.
- Panduan deployment selesai.
- Panduan presentasi selesai.
- Panduan aset visual selesai.
- Evaluasi engineering akhir selesai.

## Checklist QA Akhir

- Backend test lulus.
- Build produksi web lulus.
- Mobile analyzer lulus.
- Mobile test lulus.
- Daftar route diverifikasi.
- Akun demo dapat digunakan.
- Fallback kamera diuji.
- Fallback GPS diuji.
- Moderasi admin berjalan.
- Notifikasi dapat ditandai dibaca.

## Catatan Pengumpulan Akademik

- Jelaskan bahwa deployment sudah disiapkan, tetapi perlu credential hosting asli.
- Jelaskan bahwa secret dan signing key sengaja tidak disertakan.
- Jelaskan bahwa APK release produksi membutuhkan keystore asli.
- Jelaskan bahwa akun seeded hanya untuk demo akademik.
