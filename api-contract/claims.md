# Kontrak API Klaim

Path dasar: `/api/v1`

## Format JSON Standar

```json
{
  "success": true,
  "message": "Permintaan berhasil diproses.",
  "data": {},
  "errors": null,
  "meta": {}
}
```

## Kebutuhan Autentikasi

Endpoint klaim memerlukan autentikasi Laravel Sanctum.

```http
Authorization: Bearer <token>
```

## Tabel Endpoint

| Method | Endpoint | Deskripsi | Autentikasi Wajib |
| --- | --- | --- | --- |
| GET | `/api/v1/claims` | Menampilkan daftar klaim yang dikirim | Ya |
| POST | `/api/v1/reports/{report}/claims` | Mengajukan klaim untuk sebuah laporan | Ya |
| GET | `/api/v1/claims/{claim}` | Menampilkan detail klaim | Ya |
| PUT | `/api/v1/claims/{claim}` | Memperbarui informasi klaim | Ya |
| POST | `/api/v1/claims/{claim}/review` | Meninjau status klaim | Ya |

## Contoh Permintaan

```json
{
  "claim_message": "Barang ini milik saya dan saya bisa menjelaskan isinya.",
  "proof_description": "Ransel berisi buku catatan biru dan kartu mahasiswa."
}
```

## Contoh Respons

```json
{
  "success": true,
  "message": "Klaim berhasil dikirim.",
  "data": {
    "claim": {
      "id": 1,
      "report_id": 1,
      "status": "pending"
    }
  },
  "errors": null,
  "meta": {}
}
```

## Contoh Error

```json
{
  "success": false,
  "message": "Validasi gagal.",
  "data": null,
  "errors": {
    "claim_message": [
      "Pesan klaim wajib diisi."
    ]
  },
  "meta": {}
}
```
