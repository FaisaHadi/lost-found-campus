# Panduan Presentasi Akademik

## Alur Presentasi

Presentasi sebaiknya bergerak dari masalah, arsitektur, perilaku sistem, lalu validasi.

Urutan yang disarankan:

1. Pernyataan masalah: proses lost and found kampus membutuhkan pelacakan, moderasi, dan pelaporan mobile.
2. Gambaran sistem: backend Laravel, web admin Laravel, aplikasi mobile Flutter, dan database MySQL.
3. Arsitektur: backend API-first dengan lapisan service dan repository.
4. Backend: Sanctum, policy, validasi, service, dan migrasi.
5. Web admin: dashboard, moderasi, upload drag and drop, dan dasar PWA.
6. Mobile: Provider, Dio API client, secure storage, kamera, dan GPS.
7. Demo langsung: laporan dibuat dari mobile, dimoderasi admin, diklaim, lalu menghasilkan notifikasi.
8. Pengujian: backend test, web build, mobile analyzer, dan mobile test.
9. Kesiapan produksi: template environment, checklist deployment, dan strategi APK.
10. Penutup: maintainability dan batasan lingkup akademik.

## Panduan Penjelasan Teknis

Poin arsitektur:

- Backend adalah sumber kebenaran utama.
- Web admin dan mobile tidak menduplikasi aturan bisnis.
- Controller dibuat tipis dan meneruskan proses ke service.
- Service mengoordinasikan workflow bisnis.
- Repository menjaga query tetap rapi.
- Policy dan middleware melindungi role dan kepemilikan data.
- Respons API memakai envelope yang stabil.

Poin per platform:

- Web admin dibuat untuk moderasi yang efisien.
- Mobile dibuat untuk pelaporan, klaim, dan pelacakan oleh pengguna.
- Kamera meningkatkan kualitas bukti laporan.
- GPS meningkatkan akurasi lokasi dan tetap menyediakan input manual.
- PWA membuat admin web dapat diinstal dan memiliki fallback offline sederhana.

Poin pengujian:

- `php artisan test` memverifikasi flow backend dan web utama.
- `npm run build` memverifikasi kesiapan aset web produksi.
- `flutter analyze --no-pub` memeriksa kesehatan source Dart.
- `flutter test --no-pub` memverifikasi perilaku dasar aplikasi mobile.

## Checklist Demo Langsung

Sebelum demo:

- Jalankan `php artisan migrate:fresh --seed`.
- Jalankan `php artisan storage:link`.
- Jalankan `npm run build`.
- Nyalakan server backend.
- Pastikan `API_BASE_URL` mobile benar.
- Pastikan akun demo tersedia.
- Siapkan screenshot cadangan.

Urutan demo:

1. Buka `/admin/login`.
2. Masuk sebagai `admin@example.com`.
3. Tampilkan statistik dashboard dan aktivitas terbaru.
4. Buka aplikasi Flutter.
5. Masuk sebagai `test@example.com`.
6. Buat laporan dengan judul, kategori, gambar kamera, GPS, dan catatan lokasi.
7. Kembali ke admin web dan setujui laporan.
8. Gunakan flow `claimant@example.com`.
9. Kirim bukti klaim.
10. Setujui klaim di admin web.
11. Tampilkan notifikasi dan status klaim.

Rencana cadangan:

- Jika kamera gagal, gunakan screenshot atau galeri.
- Jika GPS gagal, tunjukkan fallback izin dan lokasi manual.
- Jika jaringan lokal gagal, gunakan data seeded.
- Jika perangkat mobile gagal, gunakan screen capture yang sudah disiapkan.

## Poin Bicara Presentasi

Versi singkat:

- "Sistem ini dibangun dengan satu backend Laravel dan dua client: web admin dan aplikasi mobile."
- "Backend mengelola validasi, otorisasi, perubahan status, dan notifikasi."
- "Web admin berfokus pada efisiensi moderasi."
- "Aplikasi mobile berfokus pada pelaporan pengguna, bukti kamera, lokasi GPS, dan pelacakan status."
- "Stabilisasi akhir mencakup konsistensi API, pengujian, rencana deployment, dan paket dokumentasi akademik."

Versi panjang:

- "Laporan dimulai dari status pending. Admin dapat menyetujui atau menolak. Jika laporan disetujui, pengguna lain dapat mengajukan klaim. Klaim yang disetujui akan menandai laporan sebagai claimed dan mengirim notifikasi kepada pengklaim."
- "Setiap flow penting dilindungi oleh role atau aturan kepemilikan, sehingga pengguna tidak dapat mengklaim laporannya sendiri dan pengguna non-admin tidak dapat melakukan moderasi."
- "Deploy disiapkan melalui template environment produksi, perintah optimasi Laravel, instruksi migrasi MySQL, dan panduan build APK."
