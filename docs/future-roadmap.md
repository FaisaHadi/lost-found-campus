# Roadmap Pengembangan Lanjutan

Roadmap ini adalah rencana evolusi, bukan fase implementasi langsung. Sistem saat ini tetap menjadi dasar: backend Laravel, web admin Laravel Blade, aplikasi mobile Flutter, database MySQL, autentikasi Sanctum, dan aturan bisnis di service layer.

## 1. Review Arsitektur Masa Depan

Peningkatan berikutnya sebaiknya membangun di atas struktur yang sudah ada, bukan menggantinya. Arsitektur sekarang cocok untuk skala akademik dan kampus kecil karena aturan bisnis terpusat dan tanggung jawab platform terpisah.

Prinsip roadmap:

- Pertahankan arsitektur saat ini.
- Tingkatkan kualitas operasional sebelum menambah fitur kompleks.
- Tambahkan fitur hanya jika memiliki workflow pengguna atau admin yang jelas.
- Pilih evolusi bertahap Laravel dan Flutter daripada rewrite besar.

Kekuatan:

- Backend Laravel adalah sumber kebenaran utama.
- Web admin dan mobile memakai pola UX yang berbeda sesuai pengguna.
- Service dan repository mengurangi duplikasi aturan bisnis.
- Policy dan middleware memberi batas otorisasi yang jelas.
- Respons API dan alur status sudah distandarkan.
- Mobile memakai Provider dan service, bukan API call langsung di widget.

Batasan jangka panjang:

- Notifikasi masih berbasis database dan pull-driven.
- Gambar masih disimpan di public storage lokal.
- Pencarian masih keyword/filter, belum full-text search.
- Mode offline mobile belum menyimpan draft.
- Analytics admin masih dasar.
- Deploy sudah disiapkan, tetapi CI/CD belum otomatis.

Yang sebaiknya tetap stabil:

- Laravel monolith sebagai backend dan host web admin.
- REST API versi `/api/v1`.
- Lapisan service/repository bersama.
- Sanctum untuk token API mobile.
- Session auth untuk web admin.
- MySQL sebagai database relasional utama.

Yang dapat berkembang:

- API versioning saat kontrak response berubah.
- Queue untuk notifikasi dan email.
- Cloud object storage untuk gambar laporan.
- Full-text search untuk dataset laporan yang lebih besar.
- CI/CD untuk test dan build.
- Penyimpanan draft offline di mobile.

Keputusan engineering:

- Jangan memakai microservices kecuali ada banyak tim independen atau traffic berat.
- Jangan menambah real-time infrastructure sebelum push notification dan queue benar-benar dibutuhkan.
- Jangan mengganti Blade admin menjadi SPA kecuali kebutuhan admin menjadi sangat interaktif.

## 2. Roadmap Fitur

### Fase A - Perbaikan Jangka Dekat

Perbaikan berisiko rendah yang sesuai dengan arsitektur saat ini:

- Verifikasi email untuk user baru.
- Alur reset password.
- Profile screen mobile yang lebih lengkap.
- Draft laporan sebelum submit di mobile.
- Unread count notifikasi yang lebih baik.
- Export CSV laporan dan klaim untuk admin.
- Kompresi gambar sebelum upload.
- Test API tambahan untuk edge case validasi.
- Empty state yang lebih informatif.
- Audit aksesibilitas untuk kontras, label, dan target sentuh.

Keputusan: fitur ini memakai pola auth, service, provider, dan komponen yang sudah ada.

### Fase B - Perbaikan Jangka Menengah

Fitur yang membutuhkan dukungan backend atau platform lebih besar:

- Push notification melalui Firebase Cloud Messaging.
- Queue untuk proses notifikasi.
- Full-text search laporan.
- Dasbor analytics untuk tren laporan dan hasil klaim.
- Cloud image storage berbasis S3-compatible storage.
- Audit log admin untuk aksi moderasi.
- Manajemen device/session untuk token mobile.
- Restore soft delete untuk admin.
- Catatan review klaim yang lebih detail.
- Mode draft offline mobile dengan local persistence.

Keputusan: implementasikan setelah sistem inti mendapat data penggunaan nyata.

### Fase C - Kemungkinan Jangka Panjang

Pertimbangkan hanya jika project tumbuh melampaui demo kelas:

- Dukungan multi-kampus.
- Peran tambahan seperti petugas keamanan atau admin departemen.
- Mode kiosk untuk loket lost and found.
- Antrian moderasi dengan assignment.
- Expiry atau arsip laporan terjadwal.
- Laporan SLA dan performa staf.
- CDN untuk distribusi gambar.

Fitur yang sebaiknya dihindari saat ini:

- Microservices.
- Sistem AI matching yang kompleks.
- Chat real-time.
- Blockchain ownership proof.
- Recommendation engine yang terlalu rumit.

## 3. Strategi Skalabilitas

Scaling harus mengikuti bottleneck nyata. Untuk skala mahasiswa dan kampus kecil, praktik Laravel dan MySQL normal sudah cukup.

API:

- Pertahankan pagination di semua endpoint list.
- Tambahkan rate limit untuk auth dan upload.
- Cache daftar kategori yang jarang berubah.
- Pindahkan pekerjaan lambat seperti email/notifikasi ke queue.
- Tambahkan monitoring error rate dan latency jika sistem dipakai nyata.

Database:

- Pertahankan index pada status, user, kategori, dan tipe.
- Gunakan eager loading untuk user/category/claim.
- Tambahkan full-text index pada title, description, dan location jika dataset membesar.
- Tambahkan backup otomatis.
- Pertimbangkan read replica hanya jika query laporan sangat berat.

Penyimpanan gambar:

- Gunakan disk `public` lokal untuk deployment sederhana.
- Validasi tipe dan ukuran gambar.
- Kompres gambar mobile sebelum upload.
- Pindahkan ke S3-compatible storage jika upload bertambah.
- Buat thumbnail untuk list view bila dibutuhkan.

Notifikasi:

- Pertahankan database notifications untuk saat ini.
- Tambahkan index unread/read.
- Tambahkan queue job untuk notifikasi dan email/push.
- Tambahkan tabel device token FCM.
- Tambahkan preferensi notifikasi bila pengguna aktif bertambah.

Concurrent user:

- Gunakan konfigurasi produksi PHP-FPM/Nginx atau Apache.
- Cache config, route, dan view Laravel.
- Tambahkan queue worker dan cache database/Redis jika hosting mendukung.
- Load balancing baru diperlukan setelah VPS tunggal tidak cukup.

## 4. Evolusi Mobile

Peningkatan mobile sebaiknya mengurangi friksi pengguna saat melapor dan melacak barang.

- Biometric login: membuka aplikasi lokal setelah token tersimpan, tanpa mengganti Sanctum.
- Push notification: upgrade mobile paling bernilai, tetapi membutuhkan FCM, device token, dan queue.
- Offline draft mode: simpan form laporan yang belum terkirim, lalu kirim ulang saat jaringan kembali.
- Caching: cache kategori dan halaman feed terakhir.
- Aksesibilitas: tambahkan semantic label, cek text scaling, kontras, dan target sentuh.

Prioritas mobile yang disarankan:

1. Aksesibilitas dan cache kategori.
2. Draft laporan sederhana.
3. Push notification.
4. Offline retry yang lebih lengkap.
5. Biometric unlock.

## 5. Evolusi Web Admin

Web admin sebaiknya berkembang menuju moderasi yang lebih cepat dan pengawasan operasional yang lebih jelas.

Kemungkinan fitur:

- Bulk approve/reject untuk laporan terpilih.
- Catatan moderasi.
- Preset filter laporan pending.
- Analytics laporan per kategori, tren lost/found, approval rate, dan waktu moderasi.
- Activity monitoring untuk aksi admin dan perubahan status.
- Export CSV berdasarkan tanggal dan status.
- Saved filters dan quick links untuk antrian pending.

Keputusan: semua aksi massal tetap harus memakai service agar notifikasi dan aturan status tidak dilewati.

## 6. Evolusi Backend

Backend perlu diperkuat dari sisi reliabilitas, observability, dan integrasi tanpa mengganti monolith.

API versioning:

- Pertahankan `/api/v1` stabil.
- Dokumentasikan perubahan response sebelum implementasi.
- Buat `/api/v2` hanya untuk breaking change.

Caching:

- Cache daftar kategori.
- Cache count dashboard singkat jika data tumbuh.
- Tambahkan invalidation di service saat data berubah.

Queue:

- Pertahankan queue `sync` untuk deployment sederhana.
- Pindahkan notifikasi dan email ke database queue saat traffic bertambah.
- Gunakan Redis queue jika performa membutuhkan.

Cloud storage:

- Pertahankan public disk lokal untuk awal.
- Tambahkan konfigurasi disk S3-compatible saat backup lokal mulai berisiko.

Logging:

- Pertahankan log Laravel.
- Dokumentasikan proses review log.
- Tambahkan audit log untuk aksi moderasi dan kegagalan upload/API.

## 7. Peningkatan Keamanan

Keamanan perlu berkembang bertahap berdasarkan risiko nyata: penyalahgunaan auth, akuntabilitas admin, token, dan upload.

Prioritas:

- Rate limiting untuk login/register dan upload.
- Validasi lebih kuat untuk pola upload mencurigakan.
- Audit log aksi moderasi admin.
- Two-factor authentication untuk akun admin.
- Device management untuk mencabut token mobile.

Keputusan: rate limiting dan audit log adalah prioritas jangka dekat/menengah yang paling realistis.

## 8. Strategi Deploy Masa Depan

Opsi deployment:

- VPS: rekomendasi utama untuk produksi nyata karena mendukung PHP, MySQL, Node build, storage symlink, queue, dan backup.
- Shared hosting: cukup untuk demo jika mendukung SSH dan PHP 8.2+.
- Docker: berguna untuk deployment reproducible setelah tim memahami deployment manual.
- CI/CD: mulai dari test dan build otomatis sebelum automated deployment.
- Cloud hosting: dipertimbangkan setelah evaluasi akademik atau pilot publik.

Rencana bertahap:

1. Deploy manual ke VPS atau shared hosting yang layak.
2. Backup database dan upload.
3. Queue worker untuk notifikasi async.
4. CI untuk backend test, web build, dan mobile analyzer.
5. Docker jika dibutuhkan oleh tim maintenance.

## 9. Evaluasi Kematangan Engineering

Arsitektur: kuat untuk lingkup saat ini.

- Backend berlapis jelas.
- Pemisahan platform jelas.
- Integrasi API-first jelas.

Maintainability: kuat.

- Service memusatkan aturan bisnis.
- Provider memusatkan state mobile.
- Dokumentasi menjelaskan tanggung jawab platform.

Integrasi: stabil.

- Web dan mobile memakai perilaku backend yang sama.
- Respons API distandarkan.
- Autentikasi dan peran konsisten.

Skalabilitas: sesuai skala akademik dan kampus kecil.

- Pagination dan index tersedia.
- Gambar tidak disimpan langsung di row database.
- Local storage sederhana tetapi memiliki jalur evolusi ke object storage.

Disiplin engineering: tinggi untuk project akademik.

- Requirement dikerjakan bertahap.
- Fitur utama distabilkan sebelum perencanaan masa depan.
- Dokumentasi deployment dan pengumpulan tersedia.

## 10. Ringkasan Roadmap

Urutan pekerjaan berikutnya yang disarankan:

1. Verifikasi email dan reset password.
2. Rate limiting untuk auth dan upload.
3. Aksesibilitas mobile dan cache kategori.
4. Export CSV admin dan filter moderasi yang lebih baik.
5. Queue-backed notifications.
6. Push notifications.
7. Audit logs.
8. Cloud image storage.
9. Analytics dashboard.
10. Pipeline CI test/build.

Rencana maintainability:

- Pertahankan service sebagai pemilik workflow domain.
- Tambahkan test untuk setiap fitur baru.
- Perbarui dokumentasi API sebelum kontrak berubah.
- Hindari perubahan infrastruktur tanpa kebutuhan terukur.

Keputusan akhir:

Arsitektur saat ini tetap menjadi fondasi. Pengembangan berikutnya harus incremental, terdokumentasi, diuji, dan didorong oleh kebutuhan nyata workflow kampus.
