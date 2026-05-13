# Arsitektur Sistem Akhir

Dokumen ini merangkum integrasi, stabilisasi, dan kesiapan produksi Lost & Found Campus Platform.

## 1. Validasi Integrasi Sistem

Backend, web admin, dan mobile harus berjalan sebagai satu sistem dengan satu sumber kebenaran.

Proses validasi:

- Route backend dicek dengan `php artisan route:list --path=api/v1`.
- Alur web admin dicakup oleh feature test.
- Source mobile diperiksa dengan `flutter analyze --no-pub`.
- Mobile shell test memverifikasi jalur bootstrap auth.
- Status bersama divalidasi melalui enum, migrasi, factory, dan test.

Keputusan stabilisasi:

- Klaim dibuat melalui `POST /api/v1/claims`.
- Upload laporan memakai multipart field `image`.
- Mobile memakai field `report_type`, `location_text`, `latitude`, dan `longitude`.
- Moderasi web dan API sama-sama memanggil `ReportService` dan `ClaimService`.

Checklist integrasi:

- Backend ke web admin: lulus.
- Backend ke kontrak API mobile: lulus.
- Autentikasi token Sanctum: lulus.
- Proteksi role admin: lulus.
- Batasan role user: lulus.
- Alur status laporan: lulus.
- Alur status klaim: lulus.
- Alur notifikasi: lulus.

Peta interaksi platform:

```text
Flutter Mobile
  -> Dio REST calls
  -> Laravel API /api/v1
  -> Sanctum bearer token
  -> Services / Repositories / Models
  -> MySQL

Laravel Web Admin
  -> Blade routes /admin
  -> Session auth + middleware role admin
  -> Services / Repositories / Models yang sama
  -> MySQL
```

## 2. Stabilisasi API

Client harus dapat membaca response dengan pola yang sama untuk sukses, validasi, otorisasi, not found, dan pagination.

Yang distandarkan:

- Success response di `ApiResponse`.
- Error API di `bootstrap/app.php`.
- Pagination envelope berisi `data`, `meta`, dan `links`.
- `error.code` tetap tersedia untuk kompatibilitas.

Envelope sukses:

```json
{
  "success": true,
  "message": "Permintaan berhasil diproses.",
  "data": {},
  "errors": null,
  "meta": {}
}
```

Envelope error:

```json
{
  "success": false,
  "message": "Data yang dikirim tidak valid.",
  "data": null,
  "errors": {},
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Data yang dikirim tidak valid.",
    "details": {}
  },
  "meta": {}
}
```

Checklist:

- Konsistensi penamaan: lulus.
- Konsistensi response: lulus.
- Konsistensi error: lulus.
- Konsistensi pagination: lulus.
- Konsistensi otorisasi: lulus.

## 3. Pengerasan Error dan Debugging

Failure state harus membantu pengguna, bukan menyebabkan crash atau perilaku ambigu.

Keputusan:

- Validasi gagal mengembalikan `errors` per field.
- Token kedaluwarsa membersihkan secure storage mobile dan mengarahkan pengguna ke login.
- Error validasi web ditampilkan di area alert layout admin.
- Penolakan kamera/GPS menampilkan snackbar dan tetap menyediakan input manual.
- Upload tetap divalidasi server-side.

## 4. Stabilisasi UX dan UI

Kualitas presentasi bergantung pada hierarki, spacing, dan kejelasan aksi.

Keputusan:

- Design system Blade dan Flutter dipertahankan.
- Teks dan artefak encoding yang tampak diperbaiki.
- Web admin tetap padat dan berorientasi tugas.
- Mobile tetap bertumpuk, ramah sentuhan, dan berorientasi pengguna.
- Badge/chip status menjadi penanda utama lifecycle.

## 5. Pengerasan Mobile

Fitur mobile harus terasa native dan pulih dengan baik saat permission atau jaringan bermasalah.

Keputusan:

- Kamera dan GPS diakses melalui service.
- Provider bertanggung jawab atas async state.
- Widget tetap tipis dan reusable.
- Gagal kamera menampilkan error ramah pengguna.
- GPS ditolak tetap memungkinkan input lokasi manual.
- Pembuatan laporan menampilkan preview gambar dan koordinat sebelum upload.
- Bottom navigation menjaga alur mobile yang natural.

## 6. Pengerasan Web Platform

Admin membutuhkan moderasi yang andal, triage yang jelas, dan form yang dapat diprediksi.

Keputusan:

- Business logic tetap di service bersama.
- Form admin divalidasi melalui form request.
- Preview upload hanya membantu pengguna; validasi utama tetap di backend.
- Drag and drop upload tetap dibatasi validasi gambar.
- PWA hanya menangani fallback offline navigasi sederhana.
- Workflow admin tetap efisien melalui tabel, filter, dan aksi moderasi langsung.

## 7. Review Database dan Performa

Project harus tetap scalable untuk demo akademik tanpa redesign berlebihan.

Keputusan:

- Schema saat ini dipertahankan.
- Index role/status/filter tetap digunakan.
- Query repository tetap paginated.
- Laporan melakukan eager load `user` dan `category`.
- Klaim melakukan eager load untuk report, claimant, dan reviewer.
- Notifikasi melakukan eager load untuk report/claim terkait.
- Foreign key memakai cascade atau null behavior sesuai kepemilikan data.

## 8. Review Keamanan

Kesiapan produksi membutuhkan dasar keamanan untuk auth, role, upload, dan token.

Keputusan:

- Route admin dilindungi session auth dan `role:admin`.
- Route API dilindungi Sanctum kecuali auth publik dan listing kategori.
- Kata sandi di-hash oleh model cast dan service.
- Token mobile disimpan di secure storage.
- API/admin moderation dan kategori admin dilindungi role.
- Upload dibatasi ke JPG, JPEG, PNG, WEBP maksimal 4 MB.
- Mass assignment memakai `fillable` eksplisit.

## 9. Cleanup dan Maintainability

Review akhir tidak boleh menampilkan starter text, catatan mati, atau format tidak konsisten.

Keputusan:

- README Laravel bawaan diganti.
- README root diperbarui dengan setup dan command validasi.
- Dokumentasi API, web, mobile, deployment, dan pengumpulan disusun per topik.
- Demo seeding dibuat deterministik.
- Kompatibilitas response dijaga.
- Tidak ada redesign arsitektur yang tidak perlu.

## 10. Checklist Pengujian

Backend:

- Registrasi/masuk/me/keluar.
- Error role dan otorisasi API.
- Pembuatan laporan dengan gambar.
- Filter listing laporan.
- Setujui/tolak laporan.
- Pengajuan dan approval klaim.
- Tandai notifikasi sebagai dibaca.
- Masuk dan moderasi web admin.

Web:

- Masuk/keluar admin.
- Statistik dashboard.
- Filter dan detail laporan.
- Setujui/tolak laporan.
- Upload drag and drop.
- Approval/rejection klaim.
- CRUD kategori.
- Dropdown/list/read state notifikasi.
- Manifest PWA, service worker, dan offline fallback.

Mobile:

- Masuk dan persistensi token.
- Cari/filter/pagination laporan.
- Detail laporan.
- Pembuatan laporan dengan atau tanpa gambar.
- Penolakan permission kamera.
- Penolakan permission GPS.
- Validasi pengajuan klaim.
- Alur notifikasi belum dibaca/dibaca.

Integrasi:

- Laporan dari mobile muncul di moderasi admin.
- Approval admin membuat laporan terlihat dan dapat diklaim.
- Klaim masuk ke antrean admin.
- Approval klaim mengubah status laporan menjadi `claimed`.
- Notifikasi muncul untuk pengguna terkait.

## 11. Persiapan Demo

Akun demo stabil:

```text
admin@example.com / password123
test@example.com / password123
claimant@example.com / password123
```

Alur demo:

1. Admin login ke `/admin`.
2. Admin membuka dashboard dan aktivitas pending.
3. Pengguna mobile login.
4. Pengguna membuat laporan dengan kamera dan GPS.
5. Admin menyetujui laporan.
6. Pengguna lain mengajukan klaim.
7. Admin menyetujui klaim.
8. Pengguna melihat notifikasi dan status klaim.

Asset cadangan:

- Screenshot dashboard.
- Screenshot detail laporan.
- Screenshot daftar laporan mobile.
- Screenshot pembuatan laporan dengan kamera/GPS.
- Screenshot flow notifikasi.
- Rekaman demo pendek.

## 12. Finalisasi Dokumentasi

Reviewer harus dapat memahami project tanpa membaca semua source code.

Dokumen utama:

- `docs/mobile-platform.md`: arsitektur dan fitur mobile.
- `docs/web-platform.md`: arsitektur admin, PWA, dan upload.
- `docs/api-*.md`: kontrak API.
- `docs/deployment-guide.md`: panduan produksi.
- `docs/academic-presentation-guide.md`: panduan presentasi.
- `docs/final-system-architecture.md`: ringkasan integrasi akhir.

## 13. Kesiapan Produksi

Environment:

- Set `APP_ENV=production`.
- Set `APP_DEBUG=false`.
- Buat `APP_KEY` asli.
- Set `APP_URL` ke URL backend produksi.
- Set credential database produksi.
- Set `SESSION_DOMAIN` dan `SANCTUM_STATEFUL_DOMAINS` bila memakai domain khusus.
- Jalankan `php artisan storage:link`.
- Pastikan `storage/` dan `bootstrap/cache/` writable.
- Konfigurasi HTTPS di server atau reverse proxy.

Build backend:

- `composer install --no-dev --optimize-autoloader`
- `npm ci`
- `npm run build`
- `php artisan migrate --force`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

Build mobile:

- Set base URL produksi dengan `--dart-define=API_BASE_URL=...`.
- Pastikan deskripsi permission Android/iOS lengkap.
- Jalankan `flutter analyze`.
- Jalankan `flutter test`.
- Build APK/AAB Android atau archive iOS sesuai kebutuhan.

Checklist deployment:

- Backup database sebelum migrasi.
- Verifikasi nilai `.env`.
- Verifikasi storage symlink.
- Verifikasi data demo dimatikan atau memang disengaja.
- Verifikasi kebijakan akun admin produksi.
- Verifikasi log writable.
- Verifikasi health route `/up`.

## 14. Validasi Engineering Akhir

Status stabilisasi:

- Arsitektur: stabil.
- Konsistensi API: stabil.
- UX web admin: stabil.
- UX mobile: stabil.
- Baseline keamanan: stabil.
- Kesiapan demo: siap.
- Kesiapan produksi: disiapkan, belum deploy.

Target validasi:

- `php artisan test`
- `php artisan route:list --path=api/v1`
- `npm run build`
- `flutter analyze --no-pub`
- `flutter test --no-pub`
