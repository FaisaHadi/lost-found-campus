# API Klaim

URL dasar: `/api/v1`

Semua endpoint klaim memerlukan autentikasi bearer Sanctum.

## Aturan Bisnis

- Pengguna tidak dapat mengklaim laporan miliknya sendiri.
- Klaim wajib memiliki `proof_text`.
- Klaim hanya dapat diajukan untuk laporan dengan `status=approved` dan `moderation_status=approved`.
- Satu laporan dapat memiliki beberapa klaim dari pengguna berbeda.
- Satu pengguna hanya dapat mengirim satu klaim aktif untuk satu laporan.
- Admin meninjau klaim.
- Persetujuan klaim mengubah klaim menjadi `approved`, mengubah laporan menjadi `claimed`, dan menolak klaim lain yang masih `pending`.
- Penolakan klaim mengubah klaim menjadi `rejected`.
- Persetujuan dan penolakan klaim membuat notifikasi belum dibaca untuk pengaju klaim.
- Admin dapat melihat semua klaim.
- Pengguna biasa dapat melihat klaim yang ia ajukan dan klaim pada laporan miliknya.

## Buat Klaim

`POST /api/v1/claims`

Autentikasi: wajib

Permintaan:

```json
{
  "report_id": 10,
  "proof_text": "Saya bisa menyebutkan nomor seri barang dan menjelaskan stiker pada casing."
}
```

Respons:

```json
{
  "success": true,
  "message": "Klaim berhasil dikirim dan menunggu tinjauan admin.",
  "data": {
    "id": 4,
    "report_id": 10,
    "status": "pending",
    "proof_text": "Saya bisa menyebutkan nomor seri barang dan menjelaskan stiker pada casing."
  }
}
```

Contoh error validasi:

```json
{
  "success": false,
  "message": "Pengguna tidak dapat mengklaim laporan miliknya sendiri.",
  "data": null,
  "errors": {
    "report_id": [
      "Pengguna tidak dapat mengklaim laporan miliknya sendiri."
    ]
  },
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Pengguna tidak dapat mengklaim laporan miliknya sendiri.",
    "details": {
      "report_id": [
        "Pengguna tidak dapat mengklaim laporan miliknya sendiri."
      ]
    }
  },
  "meta": {}
}
```

## Daftar Klaim

`GET /api/v1/claims`

Autentikasi: wajib

Query parameter:

| Nama | Deskripsi |
| --- | --- |
| `status` | `pending`, `approved`, atau `rejected` |
| `report_id` | Filter berdasarkan laporan |
| `claimant_id` | Filter khusus admin |
| `sort_by` | `created_at`, `updated_at`, `status` |
| `sort_dir` | `asc` atau `desc` |
| `per_page` | 1 sampai 100 |

Contoh:

```http
GET /api/v1/claims?status=pending&per_page=10
Authorization: Bearer <token>
```

Respons:

```json
{
  "success": true,
  "message": "Klaim berhasil diambil.",
  "data": [
    {
      "id": 4,
      "report_id": 10,
      "claimant_id": 8,
      "status": "pending",
      "report": {
        "id": 10,
        "title": "Ditemukan ransel"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

## Detail Klaim

`GET /api/v1/claims/{id}`

Autentikasi: admin, pengaju klaim, atau pemilik laporan

Respons:

```json
{
  "success": true,
  "message": "Klaim berhasil diambil.",
  "data": {
    "id": 4,
    "status": "pending",
    "claimant": {
      "id": 8,
      "name": "Pengguna Mahasiswa"
    }
  }
}
```

## Review Klaim Admin

### Setujui Klaim

`PATCH /api/v1/admin/claims/{id}/approve`

Autentikasi: admin

```json
{
  "success": true,
  "message": "Klaim berhasil disetujui.",
  "data": {
    "id": 4,
    "status": "approved",
    "reviewed_by": 1
  }
}
```

### Tolak Klaim

`PATCH /api/v1/admin/claims/{id}/reject`

Autentikasi: admin

```json
{
  "success": true,
  "message": "Klaim berhasil ditolak.",
  "data": {
    "id": 4,
    "status": "rejected",
    "reviewed_by": 1
  }
}
```
