# API Notifikasi

URL dasar: `/api/v1`

Semua endpoint notifikasi memerlukan autentikasi bearer Sanctum.

## Aturan Bisnis

- Notifikasi disimpan di database.
- Pengguna hanya dapat melihat dan memperbarui notifikasinya sendiri.
- Notifikasi dimulai dengan `status=unread`.
- Menandai notifikasi sebagai dibaca mengubah `status=read` dan mengisi `read_at`.
- Pemicu notifikasi:
  - Laporan disetujui
  - Laporan ditolak
  - Klaim disetujui
  - Klaim ditolak

## Daftar Notifikasi

`GET /api/v1/notifications`

Autentikasi: wajib

Query parameter:

| Nama | Deskripsi |
| --- | --- |
| `status` | `unread` atau `read` |
| `sort_by` | `created_at`, `updated_at`, `status` |
| `sort_dir` | `asc` atau `desc` |
| `per_page` | 1 sampai 100 |

Contoh:

```http
GET /api/v1/notifications?status=unread
Authorization: Bearer <token>
```

Respons:

```json
{
  "success": true,
  "message": "Notifikasi berhasil diambil.",
  "data": [
    {
      "id": 12,
      "title": "Laporan disetujui",
      "message": "Laporan Anda \"Ponsel hilang dekat perpustakaan\" telah disetujui.",
      "status": "unread",
      "read_at": null,
      "report_id": 3,
      "claim_id": null
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

## Tandai Notifikasi Sebagai Dibaca

`PATCH /api/v1/notifications/{id}/read`

Autentikasi: pemilik notifikasi

Respons:

```json
{
  "success": true,
  "message": "Notifikasi ditandai sebagai dibaca.",
  "data": {
    "id": 12,
    "title": "Laporan disetujui",
    "status": "read",
    "read_at": "2026-05-13T10:00:00.000000Z"
  }
}
```

Respons dilarang:

```json
{
  "success": false,
  "message": "Anda tidak diizinkan melakukan aksi ini.",
  "data": null,
  "errors": null,
  "error": {
    "code": "FORBIDDEN",
    "message": "Anda tidak diizinkan melakukan aksi ini."
  },
  "meta": {}
}
```
