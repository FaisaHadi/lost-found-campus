# Panduan Persiapan Sidang / Defense

Dokumen ini membantu tim menjelaskan, mendemokan, mempertahankan, dan memulihkan demo Lost & Found Campus Platform jika terjadi masalah saat presentasi.

Lingkup sistem:

- Backend dan REST API: Laravel 12, Sanctum, MySQL, service, repository, policy, dan notifikasi.
- Web admin: Laravel Blade, dashboard admin, moderasi, upload drag and drop, dan dasar PWA.
- Mobile: Flutter, Provider, Dio, secure storage, kamera, dan GPS.

## 1. Checklist Validasi Demo

Demo defense harus membuktikan flow produk penuh, bukan hanya layar terpisah. Demo paling kuat mengikuti satu laporan dari pembuatan mobile, penyimpanan backend, moderasi web, notifikasi, hingga klaim.

Persiapan:

1. Siapkan database dengan akun dan record demo dari seeder.
2. Nyalakan backend Laravel dan pastikan `/api/v1` dapat diakses.
3. Pastikan login web admin di `/admin/login`.
4. Pastikan build mobile mengarah ke API base URL yang sama.
5. Siapkan screenshot dan rekaman cadangan sebelum live demo.

Strategi:

- Presentasikan sistem sebagai satu platform terintegrasi dengan dua client.
- Jelaskan bahwa service backend memegang validasi, perubahan status, otorisasi, dan notifikasi.
- Gunakan demo untuk menunjukkan adaptasi platform: mobile membuat laporan, web melakukan moderasi.

Checklist:

| Area | Aksi Demo | Hasil yang Diharapkan | Status |
| --- | --- | --- | --- |
| Autentikasi mobile | Masuk sebagai `test@example.com` | Token Sanctum tersimpan aman dan feed terbuka | Siap |
| Pembuatan laporan | Membuat laporan lost/found | Form memvalidasi judul, kategori, tipe, deskripsi, dan lokasi | Siap |
| Kamera | Ambil gambar | Preview gambar muncul sebelum submit | Siap |
| GPS | Ambil lokasi saat ini | Latitude/longitude terisi, input manual tetap tersedia | Siap |
| API | Kirim laporan | API menyimpan laporan sebagai pending | Siap |
| Database | Cek record laporan | Data, image path, koordinat, status, dan relasi user tersimpan | Siap |
| Web admin | Masuk sebagai `admin@example.com` | Dasbor dan moderasi dapat diakses | Siap |
| Moderasi | Setujui/tolak laporan | Status berubah melalui service bersama | Siap |
| Notifikasi | Kembali ke mobile | Pengguna melihat notifikasi/perubahan status | Siap |
| Klaim | Kirim klaim | Bukti kepemilikan tervalidasi dan status dapat dilacak | Siap |
| Moderasi klaim | Setujui klaim | Status laporan menjadi claimed dan pengklaim diberi notifikasi | Siap |

Kalimat penjelasan:

"Demo ini memvalidasi lifecycle penuh. Aplikasi mobile mengirim data laporan, bukti gambar, dan koordinat opsional ke API Laravel. Backend memvalidasi dan menyimpan laporan, lalu web admin memakai service yang sama untuk menyetujui atau menolak. Notifikasi menutup loop status ke pengguna."

## 2. Struktur Presentasi

Urutan presentasi:

| Bagian | Tujuan | Fokus |
| --- | --- | --- |
| 1. Latar Belakang | Menjelaskan masalah lost and found kampus | Laporan manual, visibilitas rendah, status tidak jelas |
| 2. Solusi | Memperkenalkan sistem | Mobile user app, web admin, backend bersama |
| 3. Arsitektur | Menunjukkan struktur | Laravel API, MySQL, Flutter, Blade admin |
| 4. Backend | Menjelaskan core engine | Autentikasi, validasi, service, repository, policy |
| 5. Web | Menjelaskan role admin | Moderasi, dashboard, drag and drop, PWA |
| 6. Mobile | Menjelaskan role pengguna | Laporan, klaim, notifikasi, kamera, GPS |
| 7. Fitur Platform | Menunjukkan adaptasi | Kamera, GPS, drag and drop, PWA |
| 8. Integrasi | Menghubungkan semua bagian | Lifecycle laporan dan klaim |
| 9. Demo | Membuktikan implementasi | Mobile -> API -> web admin -> notifikasi |
| 10. Kesimpulan | Merangkum kualitas | Maintainability, stabilitas, lingkup akademik |

Strategi:

- Mulai dari masalah dan flow, baru masuk detail teknis.
- Jangan menyajikan web dan mobile sebagai project terpisah; keduanya client dari backend yang sama.
- Gunakan istilah konsisten: laporan, klaim, moderasi, notifikasi, status.

## 3. Catatan Defense Keputusan Engineering

| Keputusan | Jawaban Defense |
| --- | --- |
| Mengapa Laravel? | Laravel menyediakan routing, validasi, migrasi, auth, file handling, policy, dan Blade dalam satu framework stabil. Cocok untuk delivery akademik yang tetap rapi. |
| Mengapa Flutter? | Flutter mendukung UI mobile, plugin kamera/GPS, secure storage, dan satu codebase Android-focused. Cocok untuk pelaporan lapangan. |
| Mengapa REST API? | REST sederhana, mudah diprediksi, mudah dikonsumsi Flutter, dan mudah didokumentasikan. |
| Mengapa backend terpusat? | Validasi, otorisasi, status, dan notifikasi tidak diduplikasi di web dan mobile. |
| Mengapa kamera dan GPS? | Laporan barang hilang/temuan membutuhkan bukti visual dan lokasi yang akurat. |
| Mengapa drag and drop di web? | Admin bekerja dari desktop/laptop sehingga drag and drop mempercepat pengelolaan data. |
| Mengapa PWA? | PWA membuat admin web lebih seperti aplikasi tanpa membuat aplikasi desktop baru. |
| Mengapa arsitektur berlapis? | Controller tipis, service mengelola workflow, repository mengelola query, policy melindungi akses. |
| Mengapa Provider? | Provider ringan, stabil, dan cukup untuk state auth, laporan, klaim, dan notifikasi. |

Jawab dari kebutuhan project, bukan popularitas teknologi. Sebutkan tradeoff dengan jujur.

## 4. Persiapan Pertanyaan Teknis

| Topik | Pertanyaan | Jawaban Ideal |
| --- | --- | --- |
| Arsitektur | Mengapa backend, web admin, dan mobile dipisahkan? | Setiap platform punya tanggung jawab berbeda. Backend menyimpan aturan dan data, web admin untuk moderasi, mobile untuk pelaporan dan pelacakan. |
| Arsitektur | Apakah ini microservices? | Tidak. Ini backend monolith modular dengan client terpisah, lebih realistis untuk skala project. |
| API | Bagaimana client mengambil data? | Mobile memakai Dio ke endpoint `/api/v1`; web admin memakai route Laravel dan service bersama. |
| API | Mengapa response distandarkan? | Agar success, error validasi, error otorisasi, dan pagination mudah diparse. |
| Autentikasi | Bagaimana login bekerja? | API memakai token Sanctum untuk mobile; web admin memakai session Laravel. |
| Otorisasi | Bagaimana user biasa tidak bisa moderasi? | Route admin dilindungi role middleware, dan policy/service menjaga ownership. |
| Database | Entitas utama apa saja? | Users, categories, reports, claims, dan notifications. |
| Status | Bagaimana status laporan berubah? | Mulai dari pending, lalu admin approve/reject. Laporan approved dapat diklaim. Klaim approved membuat laporan claimed. |
| Upload | Bagaimana gambar diproses? | Gambar divalidasi backend dan dikirim multipart. Mobile menampilkan preview sebelum submit. |
| GPS | Apa yang terjadi jika permission ditolak? | Aplikasi menampilkan error dan tetap menyediakan input lokasi manual. |
| Skalabilitas | Apakah dapat mendukung lebih banyak user? | Untuk skala kampus, ya. Peningkatan berikutnya bisa menambah caching, queue, cloud storage, dan indexing. |
| Limitasi | Apa yang belum ada? | Push notification nyata, draft offline, analytics lanjutan, CI/CD, dan cloud storage. |

## 5. Review Stabilitas Demo Langsung

Akun demo:

- Admin: `admin@example.com` / `password123`
- Reporter: `test@example.com` / `password123`
- Pengklaim: `claimant@example.com` / `password123`

Data dummy:

- Minimal satu laporan pending untuk moderasi.
- Minimal satu laporan approved untuk klaim.
- Minimal satu notifikasi untuk pelacakan status.
- Kategori umum untuk tipe laporan.

Checklist sebelum demo:

- Jalankan migrasi dan seeder.
- Jalankan `php artisan storage:link`.
- Jalankan `npm run build`.
- Nyalakan Laravel server.
- Pastikan `API_BASE_URL` mobile benar.
- Pastikan permission kamera dan lokasi Android.
- Buka web admin sebelum presentasi.
- Buka mobile app dan uji login sebelum presentasi.
- Siapkan screenshot dan video cadangan.

Rencana pemulihan:

- Kamera gagal: tunjukkan permission handling dan gunakan screenshot/galeri.
- GPS gagal: jelaskan denial flow dan pakai lokasi manual.
- API server gagal: gunakan data seeded, screenshot, atau video.
- Perangkat mobile gagal: lanjutkan dengan arsitektur dan demo web admin.

## 6. Defense Fitur Khusus Platform

| Fitur | Platform | Alasan | Defense Teknis |
| --- | --- | --- | --- |
| Kamera | Mobile | Pengguna melapor langsung dari lokasi kampus. | Implementasi melalui capture/pick image, preview state, permission handling, dan multipart upload. |
| GPS | Mobile | Laporan membutuhkan akurasi lokasi. | Permission check, capture koordinat, auto-fill, dan fallback manual. |
| Drag and drop | Web admin | Admin desktop lebih cepat mengelola file. | Preview UI membantu, tetapi validasi tetap backend. |
| PWA | Web admin | Admin bisa mengakses web seperti aplikasi ringan. | Manifest dan service worker disiapkan untuk fallback presentasi. |

Tekankan bahwa fitur platform bukan dekorasi, tetapi mendukung domain lost and found.

## 7. Panduan Defense Arsitektur

High-level architecture:

```text
Flutter Mobile App
  -> Dio API Client
  -> Laravel REST API
  -> Services / Repositories / Policies
  -> MySQL Database

Laravel Web Admin
  -> Blade Admin Routes
  -> Shared Services / Repositories / Policies
  -> MySQL Database
```

Struktur backend:

- Routes mendefinisikan entry point.
- Controllers menerima request dan mengembalikan response.
- Form requests memvalidasi input.
- Services mengatur workflow.
- Repositories mengorganisasi query.
- Models mendefinisikan relasi.
- Policies dan middleware melindungi aksi.
- Notifikasi mengomunikasikan perubahan lifecycle.

Relasi database:

- Pengguna memiliki banyak laporan.
- Pengguna memiliki banyak klaim.
- Category memiliki banyak reports.
- Laporan dimiliki oleh user dan kategori.
- Laporan memiliki banyak klaim.
- Claim belongs to report dan claimant.
- Notification belongs to user.

Alur API:

```text
Mobile request
  -> Bearer token authentication
  -> Validation
  -> Service workflow
  -> Database read/write
  -> Standard JSON response
  -> Provider state update
  -> Mobile UI refresh
```

## 8. Review Limitasi Sistem

Limitasi saat ini:

- Push notification belum berupa device push real-time.
- Draft laporan offline belum tersedia.
- Image storage masih local/public storage.
- CI/CD belum menjadi bagian delivery.
- Analytics masih dasar.
- Pengujian perangkat nyata perlu diulang pada device demo.
- Sistem dirancang untuk skala kampus, bukan skala nasional/enterprise.

Pengembangan lanjutan:

- Push notification service.
- Draft offline di mobile.
- Cloud object storage.
- Queue untuk notifikasi dan proses berat.
- CI/CD untuk build dan deployment.
- Analytics admin.
- Rate limiting dan audit log.

Jangan mengklaim "unlimited users" atau "enterprise ready". Jelaskan bahwa sistem production-prepared untuk skala akademik dan kampus kecil.

## 9. Aset Presentasi Akhir

Screenshot wajib:

- Mobile login.
- Mobile report feed.
- Mobile create report.
- Camera preview.
- GPS capture.
- Detail laporan.
- Claim form.
- Notifikasi.
- Web admin dashboard.
- Web moderation.
- Web claim review.
- Bukti PWA.

Diagram wajib:

- Arsitektur overview.
- Workflow lifecycle laporan.
- Workflow lifecycle klaim.
- Alur request/response API.
- ERD atau diagram relasi.
- Peta tanggung jawab platform.

## 10. Simulasi Defense

Urutan latihan:

1. Buka dengan masalah lost and found kampus.
2. Perkenalkan solusi multiplatform.
3. Tampilkan arsitektur.
4. Jelaskan backend sebagai single source of truth.
5. Jelaskan web admin sebagai platform moderasi.
6. Jelaskan mobile sebagai platform pelaporan dan tracking.
7. Demo login dan pembuatan laporan mobile.
8. Tampilkan bukti gambar dan GPS.
9. Kirim laporan.
10. Beralih ke web admin.
11. Setujui/tolak laporan.
12. Kembali ke notifikasi/status mobile.
13. Demo klaim bila waktu cukup.
14. Tutup dengan testing, deployment readiness, dan roadmap.

Pertanyaan kemungkinan:

- "Di mana business logic?" Jawab: "Di service dan policy backend."
- "Bagaimana mobile request diamankan?" Jawab: "API memakai Sanctum bearer token dan mobile menyimpannya di secure storage."
- "Bagaimana jika GPS ditolak?" Jawab: "Aplikasi menangani denial dan menyediakan input manual."
- "Mengapa tidak semua web?" Jawab: "Kamera dan GPS lebih natural di mobile, sedangkan moderasi lebih efisien di web."
- "Apakah bisa scale?" Jawab: "Untuk kampus, ya. Berikutnya dapat ditambah caching, queue, cloud storage, dan CI/CD."

## 11. Evaluasi Kesiapan Defense

| Area | Kesiapan | Catatan |
| --- | --- | --- |
| Kejelasan presentasi | Siap | Alur dari masalah ke bukti implementasi jelas. |
| Penjelasan arsitektur | Siap | Tanggung jawab backend, web, mobile, dan database dapat dijelaskan. |
| Keputusan engineering | Siap | Teknologi dikaitkan dengan scope project. |
| Reliabilitas demo | Siap dengan persiapan | Perlu cek environment sebelum defense. |
| Q&A teknis | Siap | Jawaban API, auth, database, security, dan scalability tersedia. |
| Aset visual | Perlu capture final | Screenshot dan video cadangan perlu dibuat dari environment akhir. |
| Kesiapan tim | Siap dengan latihan | Setiap anggota sebaiknya memegang satu area penjelasan. |

Pembagian bicara:

- Anggota 1: masalah, solusi, arsitektur overview.
- Anggota 2: backend, API, database, auth, security.
- Anggota 3: web admin, moderasi, PWA, drag and drop.
- Anggota 4: mobile, Provider, kamera, GPS, notifikasi, klaim.
- Bersama: demo dan Q&A.

Kesimpulan:

Lost & Found Campus Platform siap dipertahankan sebagai project engineering akademik multiplatform. Sisa persiapan adalah operasional: capture screenshot akhir, latihan transisi, verifikasi akun demo, dan uji perangkat/jaringan yang akan dipakai.
