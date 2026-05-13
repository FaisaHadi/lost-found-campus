# Aset Visual

Dokumen ini menyiapkan diagram dan target screenshot yang siap dipakai untuk presentasi.

## Diagram Arsitektur Sistem

```mermaid
flowchart TD
    Mobile[Platform Pengguna Mobile Flutter]
    Web[Admin Web Laravel Blade]
    API[Laravel REST API /api/v1]
    Services[Service dan Repository]
    DB[(Database MySQL)]
    Storage[(Penyimpanan Publik Gambar Laporan)]

    Mobile --> API
    Web --> Services
    API --> Services
    Services --> DB
    Services --> Storage
```

## Diagram Autentikasi

```mermaid
sequenceDiagram
    participant Pengguna
    participant Mobile
    participant API
    participant Sanctum
    participant DB

    Pengguna->>Mobile: Memasukkan email dan kata sandi
    Mobile->>API: POST /auth/login
    API->>DB: Memverifikasi pengguna
    API->>Sanctum: Membuat token
    API-->>Mobile: Mengirim token dan data pengguna
    Mobile->>Mobile: Menyimpan token secara aman
```

## Diagram Siklus Laporan

```mermaid
stateDiagram-v2
    [*] --> Pending: Pengguna membuat laporan
    Pending --> Approved: Admin menyetujui laporan
    Pending --> Rejected: Admin menolak laporan
    Approved --> Claimed: Admin menyetujui klaim
    Claimed --> Completed: Pemilik/admin menyelesaikan proses
```

## Diagram Alur Klaim

```mermaid
sequenceDiagram
    participant Pengklaim
    participant Mobile
    participant API
    participant Admin
    participant Web
    participant DB

    Pengklaim->>Mobile: Mengirim bukti kepemilikan
    Mobile->>API: POST /claims
    API->>DB: Menyimpan klaim dengan status pending
    Admin->>Web: Meninjau klaim
    Web->>DB: Menyetujui atau menolak melalui service
    DB-->>API: Data notifikasi tersedia
    Mobile->>API: GET /notifications
```

## Struktur API

```text
/api/v1
  /auth
    POST /register
    POST /login
    GET  /me
    POST /logout
  /categories
    GET /categories
    POST /categories
    PUT /categories/{category}
    DELETE /categories/{category}
  /reports
    GET /reports
    POST /reports
    GET /reports/{report}
    PUT/PATCH /reports/{report}
    DELETE /reports/{report}
  /claims
    GET /claims
    POST /claims
    GET /claims/{claim}
  /notifications
    GET /notifications
    PATCH /notifications/{notification}/read
  /admin
    PATCH /admin/reports/{report}/approve
    PATCH /admin/reports/{report}/reject
    PATCH /admin/claims/{claim}/approve
    PATCH /admin/claims/{claim}/reject
```

## Checklist Screenshot

Screenshot web:

- Masuk admin.
- Ringkasan dashboard.
- Daftar manajemen laporan.
- Detail laporan dengan upload gambar.
- Detail klaim.
- Daftar notifikasi.
- Bukti PWA, seperti manifest atau prompt install.
- Halaman fallback offline.

Screenshot mobile:

- Halaman login.
- Feed laporan.
- Detail laporan.
- Form pembuatan laporan.
- Pratinjau kamera.
- Pratinjau GPS.
- Pengajuan klaim.
- Daftar dan detail notifikasi.

Screenshot dokumentasi:

- Output daftar route.
- Output test.
- Output build produksi.
- Slide diagram arsitektur.

## Panduan Pratinjau UI

- Gunakan data demo dari seeder agar teks konsisten.
- Jaga zoom browser di 100 persen.
- Gunakan kondisi database yang bersih.
- Jangan menampilkan secret atau nilai `.env` asli.
- Potong screenshot hanya pada area UI yang relevan.
- Gunakan satu ukuran perangkat yang konsisten untuk screenshot mobile.
