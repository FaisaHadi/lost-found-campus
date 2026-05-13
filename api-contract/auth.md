# Kontrak API Autentikasi

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

Autentikasi memakai Laravel Sanctum. Endpoint publik tidak memerlukan token. Endpoint terlindungi memerlukan header:

```http
Authorization: Bearer <token>
```

## Tabel Endpoint

| Method | Endpoint | Deskripsi | Autentikasi Wajib |
| --- | --- | --- | --- |
| POST | `/api/v1/auth/register` | Mendaftarkan akun pengguna baru | Tidak |
| POST | `/api/v1/auth/login` | Mengautentikasi pengguna dan mengembalikan token | Tidak |
| GET | `/api/v1/auth/me` | Mengambil profil pengguna yang sedang masuk | Ya |
| POST | `/api/v1/auth/logout` | Mencabut token akses saat ini | Ya |

## Contoh Permintaan

```json
{
  "name": "Pengguna Mahasiswa",
  "email": "student@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

## Contoh Respons

```json
{
  "success": true,
  "message": "Autentikasi berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Pengguna Mahasiswa",
      "email": "student@example.com"
    },
    "token": "plain-text-token-placeholder"
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
    "email": [
      "Email wajib diisi."
    ]
  },
  "meta": {}
}
```
