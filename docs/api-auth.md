# Dokumentasi API Autentikasi

Direktori backend:

```text
D:\PABP\lost-found-campus\backend
```

URL dasar untuk development lokal:

```text
http://localhost:8000/api/v1
```

Autentikasi memakai token bearer Laravel Sanctum. Client API wajib mengirim token pada header `Authorization` untuk endpoint yang dilindungi.

```http
Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json
```

## Respons Standar

Sukses:

```json
{
  "success": true,
  "message": "Permintaan berhasil diproses.",
  "data": {}
}
```

Error:

```json
{
  "success": false,
  "message": "Pesan error.",
  "data": null,
  "errors": null,
  "error": {
    "code": "ERROR_CODE",
    "message": "Pesan error."
  },
  "meta": {}
}
```

Error validasi memakai envelope yang sama dan menaruh pesan per field di `errors`. Field `error.details` tetap disediakan agar client lama yang membaca `error.code` masih kompatibel.

## Endpoint

| Method | Endpoint | Autentikasi | Deskripsi |
| --- | --- | --- | --- |
| POST | `/api/v1/auth/register` | Publik | Mendaftarkan pengguna dan membuat token API |
| POST | `/api/v1/auth/login` | Publik | Mengautentikasi pengguna dan membuat token API |
| POST | `/api/v1/auth/logout` | Bearer token | Mencabut token API saat ini |
| GET | `/api/v1/auth/me` | Bearer token | Mengambil data pengguna yang sedang masuk |

## Registrasi

```http
POST /api/v1/auth/register
```

Permintaan:

```json
{
  "name": "Pengguna Mahasiswa",
  "email": "student@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

Respons:

```json
{
  "success": true,
  "message": "Registrasi berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Pengguna Mahasiswa",
      "email": "student@example.com",
      "role": "user"
    },
    "token": "1|plain-text-token",
    "token_type": "Bearer"
  }
}
```

## Masuk

```http
POST /api/v1/auth/login
```

Permintaan:

```json
{
  "email": "student@example.com",
  "password": "password123"
}
```

Respons:

```json
{
  "success": true,
  "message": "Masuk berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Pengguna Mahasiswa",
      "email": "student@example.com",
      "role": "user"
    },
    "token": "1|plain-text-token",
    "token_type": "Bearer"
  }
}
```

Kredensial tidak valid:

```json
{
  "success": false,
  "message": "Email atau kata sandi tidak valid.",
  "data": null,
  "errors": {
    "email": [
      "Email atau kata sandi tidak valid."
    ]
  },
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Email atau kata sandi tidak valid.",
    "details": {
      "email": [
        "Email atau kata sandi tidak valid."
      ]
    }
  },
  "meta": {}
}
```

## Me

```http
GET /api/v1/auth/me
```

Header:

```http
Authorization: Bearer <token>
```

Respons:

```json
{
  "success": true,
  "message": "Data pengguna yang masuk berhasil diambil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Pengguna Mahasiswa",
      "email": "student@example.com",
      "role": "user"
    }
  }
}
```

## Keluar

```http
POST /api/v1/auth/logout
```

Header:

```http
Authorization: Bearer <token>
```

Respons:

```json
{
  "success": true,
  "message": "Keluar berhasil.",
  "data": []
}
```

Respons tidak terautentikasi:

```json
{
  "success": false,
  "message": "Token autentikasi tidak ada atau tidak valid.",
  "data": null,
  "errors": null,
  "error": {
    "code": "UNAUTHENTICATED",
    "message": "Token autentikasi tidak ada atau tidak valid."
  },
  "meta": {}
}
```

## Perintah Migrasi

Jalankan dari direktori backend:

```powershell
cd D:\PABP\lost-found-campus\backend
mysql -u root -e "CREATE DATABASE IF NOT EXISTS lost_found_campus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
```

Untuk membangun ulang database lokal saat development:

```powershell
php artisan migrate:fresh
```

## Perintah Validasi Artisan

```powershell
php artisan config:clear
php artisan route:list --path=api/v1
php artisan test
```

Server API lokal opsional:

```powershell
php artisan serve
```

## Alur Pengujian Postman

1. Buat environment dengan `base_url` bernilai `http://localhost:8000`.
2. Kirim `POST {{base_url}}/api/v1/auth/register`.
3. Salin `data.token` dari respons.
4. Buat environment variable bernama `token`.
5. Kirim `GET {{base_url}}/api/v1/auth/me` dengan `Authorization: Bearer {{token}}`.
6. Kirim `POST {{base_url}}/api/v1/auth/logout` dengan bearer token yang sama.
7. Kirim `GET {{base_url}}/api/v1/auth/me` lagi dan pastikan respons berisi `UNAUTHENTICATED`.
