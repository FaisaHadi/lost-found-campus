# API Laporan dan Kategori

URL dasar: `/api/v1`

Semua endpoint laporan memerlukan autentikasi bearer Sanctum. Daftar kategori bersifat publik. Mutasi kategori memerlukan token admin.

## Aturan Bisnis Laporan

- Laporan baru dibuat dengan `status=pending` dan `moderation_status=pending`.
- Persetujuan admin mengubah `status=approved` dan `moderation_status=approved`.
- Penolakan admin mengubah `status=rejected` dan `moderation_status=rejected`.
- Laporan yang disetujui dapat menerima klaim.
- Klaim yang disetujui mengubah laporan terkait menjadi `status=claimed`.
- Laporan hanya dapat ditandai `completed` setelah berstatus `claimed`.
- Pemilik laporan dan admin dapat memperbarui atau menghapus laporan.
- Pengguna biasa dapat melihat laporan yang disetujui dan laporan miliknya sendiri.
- Gambar laporan disimpan di disk `public` pada folder `reports/` dengan nama unik.
- Penggantian atau penghapusan gambar laporan akan menghapus file lama dari storage.

## Daftar Laporan

`GET /api/v1/reports`

Autentikasi: wajib

Query parameter:

| Nama | Deskripsi |
| --- | --- |
| `keyword` | Mencari judul, deskripsi, dan catatan lokasi |
| `category_id` | Filter berdasarkan ID kategori |
| `category_slug` | Filter berdasarkan slug kategori |
| `report_type` | `lost` atau `found` |
| `status` | `pending`, `approved`, `rejected`, `claimed`, `completed` |
| `moderation_status` | Filter khusus admin: `pending`, `approved`, `rejected` |
| `sort_by` | `created_at`, `updated_at`, `title`, `status`, `report_type` |
| `sort_dir` | `asc` atau `desc` |
| `per_page` | 1 sampai 100 |

Contoh:

```http
GET /api/v1/reports?keyword=phone&category_slug=electronics&report_type=lost&status=approved
Authorization: Bearer <token>
```

Respons:

```json
{
  "success": true,
  "message": "Laporan berhasil diambil.",
  "data": [
    {
      "id": 1,
      "title": "Ponsel hilang dekat perpustakaan",
      "report_type": "lost",
      "image_url": "http://localhost/storage/reports/example.png",
      "status": "approved",
      "moderation_status": "approved"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

## Buat Laporan

`POST /api/v1/reports`

Autentikasi: wajib

Gunakan `multipart/form-data` saat mengirim gambar.

Field:

```json
{
  "category_id": 1,
  "title": "Ponsel hilang dekat perpustakaan",
  "description": "Ponsel hitam dengan casing retak.",
  "report_type": "lost",
  "image": "<jpg|jpeg|png|webp maksimal 4MB>",
  "latitude": -6.2,
  "longitude": 106.816666,
  "location_text": "Pintu masuk perpustakaan utama"
}
```

Respons:

```json
{
  "success": true,
  "message": "Laporan berhasil dibuat dan menunggu moderasi.",
  "data": {
    "id": 1,
    "status": "pending",
    "moderation_status": "pending"
  }
}
```

## Detail Laporan

`GET /api/v1/reports/{id}`

Autentikasi: wajib

Respons:

```json
{
  "success": true,
  "message": "Laporan berhasil diambil.",
  "data": {
    "id": 1,
    "title": "Ponsel hilang dekat perpustakaan",
    "category": {
      "id": 1,
      "name": "Elektronik"
    }
  }
}
```

## Perbarui Laporan

`PUT /api/v1/reports/{id}`

Autentikasi: pemilik laporan atau admin

Field yang diterima: `category_id`, `title`, `description`, `report_type`, `image`, `remove_image`, `latitude`, `longitude`, `location_text`, `status`.

Hanya `status=completed` yang diterima melalui endpoint ini, dan hanya untuk laporan yang sudah diklaim.

```json
{
  "location_text": "Kantor keamanan",
  "status": "completed"
}
```

## Hapus Laporan

`DELETE /api/v1/reports/{id}`

Autentikasi: pemilik laporan atau admin

Menghapus laporan dengan soft delete dan menghapus file gambar yang tersimpan.

## Moderasi Laporan Admin

`PATCH /api/v1/admin/reports/{id}/approve`

Autentikasi: admin

Menyetujui laporan dan membuat notifikasi belum dibaca untuk pemilik laporan.

`PATCH /api/v1/admin/reports/{id}/reject`

Autentikasi: admin

```json
{
  "reason": "Foto tidak jelas."
}
```

Menolak laporan dan membuat notifikasi belum dibaca untuk pemilik laporan.

## Kategori

### Daftar Kategori

`GET /api/v1/categories`

Autentikasi: tidak wajib

Query parameter: `keyword`, `status`, `sort_by`, `sort_dir`, `per_page`, `page`.

### Buat Kategori

`POST /api/v1/categories`

Autentikasi: admin

```json
{
  "name": "Elektronik",
  "description": "Ponsel, laptop, charger, dan aksesori.",
  "status": "active"
}
```

### Perbarui Kategori

`PUT /api/v1/categories/{id}`

Autentikasi: admin

```json
{
  "status": "inactive"
}
```

### Hapus Kategori

`DELETE /api/v1/categories/{id}`

Autentikasi: admin

Kategori dihapus dengan soft delete. Laporan tetap menyimpan referensi kategori yang nullable.
