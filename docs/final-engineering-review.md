# Laporan Review Engineering Akhir

## Kekuatan

- Pemisahan backend API, web admin, dan platform mobile sudah jelas.
- Service Laravel memusatkan aturan bisnis untuk laporan, klaim, notifikasi, dan gambar.
- Web admin dan API memakai service yang sama sehingga perilaku tidak mudah menyimpang.
- Aplikasi Flutter memakai Provider, Dio, secure storage, dan abstraksi service.
- Kamera dan GPS menjadi kemampuan mobile khusus dengan fallback yang wajar.
- Respons sukses dan error API memakai envelope yang konsisten.
- Feature test mencakup workflow backend dan admin web utama.
- Dokumentasi akhir menjelaskan arsitektur, workflow, pengujian, demo, dan kesiapan produksi.

## Titik Lemah

- Validasi mobile masih bersifat statis di level widget; pengujian perangkat nyata tetap diperlukan untuk kamera, GPS, dan jaringan fisik.
- Project belum memiliki automated browser visual test untuk UI admin.
- PWA admin sengaja ringan dan belum mendukung moderasi offline.
- Aset media demo dan video cadangan masih perlu disiapkan manual.

## Status Stabilitas

- Backend test: lulus.
- Build aset web produksi: lulus.
- Mobile analyzer: lulus.
- Mobile widget test: lulus.
- Daftar route API: terverifikasi.
- Deploy produksi: disiapkan, belum dieksekusi.

## Kualitas Arsitektur

Arsitektur sudah sesuai untuk platform akademik dengan orientasi produksi skala kecil:

- Controller tetap tipis.
- Form request menangani validasi.
- Policy dan middleware menangani otorisasi.
- Service menangani keputusan bisnis.
- Repository menangani query.
- Model mendefinisikan relasi dan cast.
- Provider mobile menangani state async.
- Komponen web dan mobile yang reusable menjaga konsistensi UI.

Tidak ada redesign berisiko selama stabilisasi.

## Kesiapan Presentasi

Urutan demo yang disarankan:

1. Tampilkan diagram arsitektur dari `docs/final-system-architecture.md`.
2. Masuk sebagai admin dan tampilkan dasbor.
3. Masuk di mobile sebagai `test@example.com`.
4. Buat laporan dengan foto dan GPS.
5. Setujui laporan di admin.
6. Kirim klaim dari `claimant@example.com`.
7. Setujui klaim di admin.
8. Tampilkan status klaim dan notifikasi di mobile.

Rencana cadangan:

- Gunakan data seeded jika pembuatan laporan langsung lambat.
- Gunakan screenshot untuk kamera/GPS jika permission atau emulator bermasalah.
- Gunakan rekaman cadangan jika akses jaringan lokal ke backend gagal.

## Penilaian Akhir

Lost & Found Campus Platform sudah konsisten, maintainable, dapat dijelaskan, siap dipresentasikan, dan stabil untuk demo akademik akhir. Sisa pekerjaan bersifat operasional: deployment nyata dan verifikasi perangkat nyata.
