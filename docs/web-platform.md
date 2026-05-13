# Platform Admin Web

Phase 3 menambahkan platform administrasi dan moderasi berbasis Laravel Blade di dalam aplikasi backend yang sudah ada. Platform ini memakai sistem autentikasi, model, policy, dan service yang sama dengan API.

## Arsitektur

Alur request web:

```text
Blade route
-> Web controller
-> Form request validation
-> Policy / middleware authorization
-> Service
-> Repository / Model
-> Blade response
```

Direktori penting:

| Area | Path |
| --- | --- |
| Web routes | `backend/routes/web.php` |
| Web controllers | `backend/app/Http/Controllers/Web` |
| Web form requests | `backend/app/Http/Requests/Web/Admin` |
| Layout | `backend/resources/views/layouts` |
| Komponen UI reusable | `backend/resources/views/components` |
| Halaman admin | `backend/resources/views/admin` |
| File PWA | `backend/public/manifest.json`, `backend/public/service-worker.js`, `backend/public/offline.html` |

Platform web tidak menggandakan logika laporan, klaim, notifikasi, upload gambar, atau kategori. Semua alur bisnis tetap memakai service yang sama dengan API.

## Struktur UI

- Sidebar desktop tetap untuk menu admin.
- Drawer sidebar untuk tablet dan mobile.
- Topbar sticky dengan dropdown notifikasi.
- Heading halaman dan slot aksi.
- State sukses, status, dan error validasi.
- Area konten responsif dengan card, tabel, form, dan empty state.

Komponen Blade reusable:

- `x-ui.button`
- `x-ui.badge`
- `x-ui.card`
- `x-ui.alert`
- `x-ui.empty-state`
- `x-admin.sidebar`
- `x-admin.topbar`

## Autentikasi

Route:

| Method | Route | Tujuan |
| --- | --- | --- |
| `GET` | `/admin/login` | Halaman login admin |
| `POST` | `/admin/login` | Masuk session admin |
| `POST` | `/admin/logout` | Keluar admin |
| `GET` | `/admin` | Dasbor |

Hanya pengguna dengan `role=admin` yang dapat membuka `/admin/*`. Pengguna non-admin tidak dapat masuk ke platform admin.

## Dasbor

Kartu dasbor menampilkan:

- Total laporan
- Laporan menunggu
- Klaim menunggu
- Laporan disetujui
- Jumlah kategori
- Jumlah pengguna terdaftar
- Distribusi status laporan

Bagian aktivitas menampilkan laporan terbaru, klaim terbaru, dan notifikasi belum dibaca.

## Workflow Moderasi

Moderasi laporan:

1. Admin membuka `Manajemen Laporan`.
2. Admin memfilter berdasarkan keyword, kategori, jenis, status, atau status moderasi.
3. Admin membuka detail laporan.
4. Admin menyetujui, menolak, atau mengedit metadata/status laporan.
5. Pemilik laporan menerima notifikasi saat laporan disetujui atau ditolak.

Moderasi klaim:

1. Admin membuka `Manajemen Klaim`.
2. Admin membaca bukti kepemilikan.
3. Admin menyetujui atau menolak klaim.
4. Klaim yang disetujui menandai laporan sebagai `claimed`.
5. Klaim pending lain ditolak oleh service backend.
6. Pengaju klaim menerima notifikasi.

Manajemen kategori:

1. Admin membuat, mengedit, mengaktifkan, menonaktifkan, atau menghapus kategori.
2. Kategori dipakai bersama oleh API client dan platform web.

Notifikasi:

1. Admin membuka dropdown atau daftar notifikasi.
2. Admin meninjau pesan belum dibaca atau sudah dibaca.
3. Admin menandai notifikasi sebagai dibaca.

## Fitur Khusus Web

### Upload Drag and Drop

Diimplementasikan pada form edit detail laporan.

Kemampuan:

- Drag gambar ke area upload.
- Klik untuk memilih file.
- Pratinjau gambar sebelum submit.
- Validasi browser untuk JPG, PNG, WEBP.
- Validasi ukuran maksimal 4 MB.
- Error file tampil inline.
- Validasi server tetap menjadi sumber kebenaran.
- Pembersihan gambar lama tetap ditangani `ImageStorageService`.

### Fondasi PWA

File yang diimplementasikan:

- `public/manifest.json`
- `public/service-worker.js`
- `public/offline.html`
- `public/pwa/icon.svg`

PWA menyediakan installability dan fallback offline kecil. Aksi moderasi tetap membutuhkan akses backend aktif.

## Perintah

Jalankan dari `backend/`.

```bash
composer install
npm install
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan test
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000/admin/login
```

Akun admin seed:

```text
admin@example.com
password123
```

## Checklist Pengujian

- Admin dapat login dan logout.
- Non-admin tidak dapat masuk ke `/admin`.
- Dasbor memuat statistik dan aktivitas terbaru.
- Filter laporan bekerja berdasarkan keyword, kategori, jenis, dan status.
- Detail laporan memuat metadata, gambar, dan klaim.
- Admin dapat menyetujui dan menolak laporan.
- Upload drag and drop menampilkan pratinjau gambar valid.
- File tidak valid dan file lebih dari 4 MB ditolak.
- Admin dapat menyetujui dan menolak klaim.
- CRUD kategori bekerja.
- Dropdown dan daftar notifikasi bekerja.
- PWA manifest, service worker, dan offline page tersedia.
- Halaman tetap dapat dipakai di desktop, tablet, dan mobile browser.
