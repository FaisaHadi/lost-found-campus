# Platform Mobile

Phase 4 menambahkan aplikasi Flutter untuk pengguna kampus agar dapat melapor, menelusuri, mengklaim, dan memantau barang hilang/ditemukan.

## Arsitektur

Aplikasi mobile berada di `mobile/lib` dengan pembagian fitur yang jelas:

- `constants`: base URL API, warna, spacing, typography, dan theme.
- `models`: model data untuk user, kategori, laporan, klaim, notifikasi, session auth, dan halaman API.
- `services`: Dio API client, secure token storage, service domain, akses kamera, dan akses GPS.
- `providers`: state container untuk auth, laporan, klaim, dan notifikasi.
- `screens`: layar auth, tab home, laporan, pembuatan laporan, klaim, dan notifikasi.
- `widgets`: komponen UI reusable seperti button, card, input, status chip, loading, empty, dan error state.
- `routes`: named route dan penanganan argumen bertipe.
- `utils`: validasi, format tanggal, snackbar, helper URL, dan enum view state.

Default API:

```bash
--dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

Gunakan nilai berbeda untuk perangkat fisik atau backend production.

## State Management

Provider dipakai untuk seluruh state aplikasi:

- `AuthProvider`: login, registrasi, logout, bootstrap token, dan session expired.
- `ReportsProvider`: daftar laporan, kategori, filter, pencarian, pagination, detail, dan pembuatan laporan.
- `ClaimsProvider`: pengiriman klaim dan pemantauan status klaim.
- `NotificationsProvider`: daftar notifikasi, jumlah belum dibaca, filter, dan mark-as-read.

Setiap provider mengekspos state loading, empty, success, dan error agar layar tetap fokus pada presentasi.

## Integrasi API

Aplikasi memakai REST API Laravel yang sudah ada:

- `POST /auth/login`
- `POST /auth/register`
- `GET /auth/me`
- `POST /auth/logout`
- `GET /categories`
- `GET /reports`
- `POST /reports`
- `GET /reports/{id}`
- `GET /claims`
- `POST /claims`
- `GET /notifications`
- `PATCH /notifications/{id}/read`

Dio menyisipkan bearer token Sanctum dari `flutter_secure_storage`. Respons `401` membersihkan token dan mengembalikan aplikasi ke state belum masuk.

## Fitur Kamera

Alur buat laporan memiliki bagian foto:

- Membuka kamera perangkat melalui `image_picker`.
- Menyimpan pratinjau lokal sebelum upload.
- Mengirim gambar sebagai multipart field `image`.
- Menangani error kamera dan penolakan izin dengan snackbar.
- Menyediakan pilihan galeri jika pengguna sudah memiliki foto barang.

Permission Android berada di `AndroidManifest.xml`; deskripsi penggunaan iOS berada di `Info.plist`.

## Fitur GPS

Alur buat laporan memiliki pengambilan GPS eksplisit:

- Memeriksa layanan lokasi aktif.
- Meminta izin lokasi.
- Mengambil latitude dan longitude akurasi tinggi.
- Menampilkan koordinat dan akurasi.
- Mengizinkan penghapusan atau pembaruan koordinat.
- Tetap menyediakan catatan lokasi manual jika izin ditolak.

Error GPS tidak menghalangi pembuatan laporan.

## Strategi UX Mobile

Aplikasi mobile adalah alat pelaporan dan pelacakan pengguna, bukan dashboard admin:

- Bottom navigation untuk Laporan, Klaim, dan Pemberitahuan.
- Target sentuh besar dan aksi utama full-width.
- Daftar laporan, klaim, dan notifikasi berbasis card.
- Pull-to-refresh dan infinite scroll.
- Dukungan safe area.
- Keyboard dismiss saat scroll form.
- Pratinjau langsung untuk gambar dan GPS.
- Hirarki status jelas dengan chip reusable.

## Checklist Debugging

- Pastikan backend berjalan dan dapat dijangkau perangkat.
- Pastikan `API_BASE_URL` mengarah ke `/api/v1`.
- Android emulator memakai `http://10.0.2.2:8000/api/v1`.
- Perangkat fisik memakai IP LAN komputer dan backend menerima host tersebut.
- Pastikan masuk/registrasi mengembalikan token Sanctum.
- Pastikan request terlindungi mengirim `Authorization: Bearer <token>`.
- Jika gambar laporan tidak muncul, jalankan `php artisan storage:link`.
- Jika `flutter pub get` gagal di Windows karena symlink plugin, aktifkan Windows Developer Mode.

## Checklist Pengujian Mobile

- Masuk dengan kredensial valid.
- Masuk dengan kredensial salah dan cek tampilan error.
- Tutup dan buka aplikasi, lalu cek token tetap dipulihkan melalui `/auth/me`.
- Keluar dan pastikan token aman dibersihkan.
- Telusuri laporan, cari, filter Hilang/Ditemukan, dan filter kategori.
- Scroll ke bawah dan pastikan halaman berikutnya dimuat.
- Buka detail laporan dan cek gambar, status, lokasi, dan tombol klaim.
- Buat laporan tanpa gambar.
- Buat laporan dengan kamera atau galeri.
- Tolak izin kamera dan pastikan aplikasi tidak crash.
- Ambil koordinat GPS dan pastikan terkirim.
- Tolak izin lokasi dan pastikan lokasi manual tetap bisa diisi.
- Kirim klaim dengan bukti valid.
- Kirim bukti klaim terlalu pendek dan pastikan validasi tampil.
- Buka notifikasi dan cek indikator belum dibaca.
- Tap notifikasi dan pastikan status berubah menjadi dibaca.
