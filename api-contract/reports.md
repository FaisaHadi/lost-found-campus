# Kontrak API Laporan

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

Endpoint laporan menggunakan autentikasi Laravel Sanctum, kecuali ada endpoint yang secara eksplisit dibuat publik pada fase berikutnya.

```http
Authorization: Bearer <token>
```

## Tabel Endpoint

| Method | Endpoint | Deskripsi | Autentikasi Wajib |
| --- | --- | --- | --- |
| GET | `/api/v1/reports` | Menampilkan daftar laporan hilang dan ditemukan | Ya |
| POST | `/api/v1/reports` | Membuat laporan barang hilang atau ditemukan | Ya |
| GET | `/api/v1/reports/{report}` | Menampilkan detail laporan | Ya |
| PUT | `/api/v1/reports/{report}` | Memperbarui detail laporan | Ya |
| DELETE | `/api/v1/reports/{report}` | Menghapus atau mengarsipkan laporan | Ya |

## Contoh Permintaan

```json
{
  "type": "lost",
  "title": "Ransel Hitam",
  "description": "Ransel hitam berisi buku catatan kampus.",
  "location_name": "Perpustakaan Utama",
  "latitude": -6.200000,
  "longitude": 106.816666,
  "reported_at": "2026-05-13T09:00:00Z"
}
```

## Contoh Respons

```json
{
  "success": true,
  "message": "Laporan berhasil dibuat.",
  "data": {
    "report": {
      "id": 1,
      "type": "lost",
      "title": "Ransel Hitam",
      "status": "open"
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
    "title": [
      "Judul wajib diisi."
    ],
    "type": [
      "Jenis yang dipilih tidak valid."
    ]
  },
  "meta": {}
}
```
