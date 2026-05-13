# Pengujian dan Validasi Fase 2

## Perintah Migrasi dan Setup

Jalankan dari `backend/`.

```bash
composer install
php artisan migrate:fresh --seed
php artisan storage:link
php artisan route:list --path=api/v1
php artisan test
```

Akun seed:

| Peran | Email | Kata Sandi |
| --- | --- | --- |
| Admin | `admin@example.com` | `password123` |
| Pengguna | `test@example.com` | `password123` |
| Pengklaim | `claimant@example.com` | `password123` |

Kategori seed:

- Elektronik
- Dokumen
- Tas
- Kunci
- Pakaian

## Alur Pengujian API

1. Registrasi atau masuk pengguna dengan `/api/v1/auth/login`.
2. Masuk sebagai admin dengan `admin@example.com`.
3. Lihat kategori dengan `GET /api/v1/categories`.
4. Buat laporan sebagai pengguna dengan `POST /api/v1/reports`.
5. Setujui atau tolak laporan sebagai admin.
6. Pastikan pemilik laporan menerima notifikasi.
7. Buat pengguna kedua dan ajukan klaim pada laporan yang disetujui.
8. Setujui atau tolak klaim sebagai admin.
9. Pastikan persetujuan klaim mengubah status laporan menjadi `claimed`.
10. Pastikan pengaju klaim menerima notifikasi.
11. Tandai notifikasi sebagai dibaca.
12. Uji filter laporan dan klaim.

## Struktur Collection Postman

Variable yang disarankan:

| Variable | Contoh |
| --- | --- |
| `base_url` | `http://localhost:8000/api/v1` |
| `user_token` | Bearer token dari login user |
| `admin_token` | Bearer token dari login admin |
| `report_id` | ID laporan yang dibuat |
| `claim_id` | ID klaim yang dibuat |
| `notification_id` | ID notifikasi yang dibuat |

Folder:

- Autentikasi
  - Registrasi
  - Masuk Pengguna
  - Masuk Admin
  - Me
  - Keluar
- Kategori
  - Daftar Kategori
  - Admin Membuat Kategori
  - Admin Memperbarui Kategori
  - Admin Menghapus Kategori
- Laporan
  - Daftar Laporan
  - Buat Laporan
  - Detail Laporan
  - Perbarui Laporan
  - Hapus Laporan
  - Admin Menyetujui Laporan
  - Admin Menolak Laporan
- Klaim
  - Buat Klaim
  - Daftar Klaim
  - Detail Klaim
  - Admin Menyetujui Klaim
  - Admin Menolak Klaim
- Notifikasi
  - Daftar Notifikasi
  - Tandai Notifikasi Dibaca

## Checklist Validasi

- Endpoint yang perlu autentikasi menolak token kosong dengan `UNAUTHENTICATED`.
- Mutasi kategori oleh non-admin mengembalikan `FORBIDDEN`.
- Non-pemilik tidak dapat memperbarui atau menghapus laporan pengguna lain.
- Pengguna tidak dapat mengklaim laporannya sendiri.
- Klaim wajib memiliki bukti kepemilikan minimal 20 karakter.
- Laporan `pending` atau `rejected` tidak dapat diklaim.
- Validasi gambar menerima `jpg`, `jpeg`, `png`, dan `webp` maksimal 4 MB.
- Penggantian gambar laporan menghapus file lama.
- Penghapusan laporan menghapus file gambar yang tersimpan.
- Persetujuan laporan admin membuat notifikasi belum dibaca untuk pemilik.
- Penolakan laporan admin membuat notifikasi belum dibaca untuk pemilik.
- Persetujuan klaim admin membuat notifikasi belum dibaca untuk pengaju klaim.
- Penolakan klaim admin membuat notifikasi belum dibaca untuk pengaju klaim.
- Pengguna tidak dapat menandai notifikasi pengguna lain sebagai dibaca.
- Filter laporan berfungsi untuk keyword, kategori, jenis, status, dan pagination.
