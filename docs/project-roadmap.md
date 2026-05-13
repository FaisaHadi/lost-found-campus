# Roadmap Project

Roadmap ini menjaga Platform Lost & Found Campus tetap terarah, bertahap, dan stabil.

## Fase Project

### Phase 0: Fondasi Engineering

- Inisialisasi struktur repository.
- Inisialisasi Git dan aturan ignore dasar.
- Membuat dokumentasi engineering.
- Membuat fondasi kontrak API.
- Inisialisasi backend Laravel.
- Inisialisasi aplikasi mobile Flutter.
- Menyiapkan struktur folder berlapis.

### Phase 1: Fondasi Backend Core

- Konfigurasi environment dan koneksi MySQL.
- Konfigurasi Laravel Sanctum.
- Membuat helper response API bersama.
- Menentukan rencana migrasi database.
- Menyiapkan route autentikasi.
- Menambahkan baseline test backend.

### Phase 2: Domain Lost and Found

- Implementasi model laporan.
- Implementasi model klaim.
- Implementasi fondasi notifikasi.
- Menambahkan validasi dan policy.
- Menambahkan endpoint REST sesuai kontrak API.

### Phase 3: Platform Administrasi Web

- Menyiapkan layout Blade dan aset Tailwind.
- Implementasi alur autentikasi admin.
- Implementasi halaman manajemen laporan.
- Implementasi upload drag and drop.
- Menyiapkan baseline PWA.

### Phase 4: Aplikasi Mobile Flutter

- Konfigurasi service layer API.
- Implementasi alur autentikasi.
- Implementasi alur pengiriman laporan.
- Menambahkan integrasi kamera.
- Menambahkan integrasi lokasi GPS.

### Fase 5: Pengujian, Pengerasan, dan Persiapan Rilis

- Menambahkan feature dan integration test.
- Memverifikasi kompatibilitas kontrak API.
- Menguji workflow web dan mobile.
- Meninjau aturan keamanan dan otorisasi.
- Menyiapkan dokumentasi deployment akhir.

## Roadmap Backend

- Inisialisasi Laravel 12.
- Setup autentikasi Sanctum.
- Konfigurasi MySQL.
- Struktur folder arsitektur berlapis.
- Versioning API di `/api/v1/`.
- Format response API bersama.
- Service dan repository domain.
- Policy untuk resource terlindungi.
- Notifikasi untuk pembaruan pengguna.

## Roadmap Web

- Interface administrasi Laravel Blade.
- Sistem layout berbasis Tailwind.
- Shell dasbor admin.
- Interface moderasi laporan.
- Interface review klaim.
- Dukungan upload drag and drop.
- Setup PWA.

## Roadmap Mobile

- Inisialisasi project Flutter.
- Struktur bersih di bawah `lib/`.
- Service API client.
- Provider untuk state autentikasi.
- Layar pengiriman laporan.
- Integrasi kamera.
- Integrasi lokasi GPS.
- Tampilan notifikasi ramah mobile.

## Roadmap Pengujian

- Unit test backend untuk service.
- Feature test backend untuk endpoint API.
- Test autentikasi dan otorisasi.
- Test integrasi repository jika perilaku database penting.
- Widget test Flutter untuk layar kritis.
- Checklist verifikasi manual untuk fitur khusus platform.

## Roadmap Dokumentasi

- Aturan engineering.
- Kontrak API.
- Panduan setup backend.
- Panduan setup mobile.
- Dokumentasi skema database.
- Panduan testing.
- Catatan deployment.
- Dokumentasi pendukung laporan akhir.
