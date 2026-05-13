# Kontrak API Notifikasi

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

Endpoint notifikasi memerlukan autentikasi Laravel Sanctum.

```http
Authorization: Bearer <token>
```

## Tabel Endpoint

| Method | Endpoint | Deskripsi | Autentikasi Wajib |
| --- | --- | --- | --- |
| GET | `/api/v1/notifications` | Menampilkan notifikasi pengguna | Ya |
| GET | `/api/v1/notifications/{notification}` | Menampilkan detail notifikasi | Ya |
| POST | `/api/v1/notifications/{notification}/read` | Menandai notifikasi sebagai dibaca | Ya |
| POST | `/api/v1/notifications/read-all` | Menandai semua notifikasi sebagai dibaca | Ya |

## Contoh Permintaan

```json
{
  "read": true
}
```

## Contoh Respons

```json
{
  "success": true,
  "message": "Notifikasi berhasil diperbarui.",
  "data": {
    "notification": {
      "id": "notification-id-placeholder",
      "read_at": "2026-05-13T10:00:00Z"
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
  "message": "Notifikasi tidak ditemukan.",
  "data": null,
  "errors": {
    "notification": [
      "Notifikasi yang diminta tidak ditemukan."
    ]
  },
  "meta": {}
}
```
