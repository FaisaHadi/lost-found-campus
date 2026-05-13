# Placeholder SQL

File export database opsional dapat ditempatkan di folder ini untuk pengumpulan akademik.

Pendekatan yang disarankan:

- Utamakan migrasi dan seeder Laravel sebagai artifact database utama.
- Sertakan export SQL hanya jika diminta dosen.
- Jangan export secret pengguna nyata atau data produksi privat.

Perintah rebuild database bersih:

```bash
cd backend
php artisan migrate:fresh --seed
```
