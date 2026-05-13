# Aturan Engineering

Dokumen ini menetapkan disiplin engineering untuk Platform Lost & Found Campus. Project harus tetap realistis, mudah dipelihara, dan sesuai dengan arsitektur yang sudah dipilih.

## 1. Strategi Branch

- `main` hanya berisi kode stabil yang sudah ditinjau.
- `develop` berisi pekerjaan terintegrasi untuk milestone stabil berikutnya.
- `feature/<short-name>` dipakai untuk pekerjaan fitur.
- `fix/<short-name>` dipakai untuk perbaikan bug.
- `docs/<short-name>` dipakai untuk perubahan dokumentasi.
- Satu branch harus punya scope jelas dan tidak mencampur pekerjaan yang tidak terkait.

## 2. Konvensi Commit

Gunakan format Conventional Commit:

```text
type(scope): deskripsi singkat
```

Tipe yang dipakai:

- `feat`: fitur baru
- `fix`: perbaikan bug
- `docs`: pembaruan dokumentasi
- `refactor`: restrukturisasi kode tanpa perubahan perilaku
- `test`: penambahan atau pembaruan test
- `chore`: tugas pemeliharaan
- `style`: perubahan formatting saja

Contoh:

```text
docs(api): add reports contract foundation
chore(backend): initialize laravel project
```

## 3. Standar Respons API

Semua response API memakai bentuk JSON yang konsisten.

Respons sukses:

```json
{
  "success": true,
  "message": "Permintaan berhasil diproses.",
  "data": {},
  "errors": null,
  "meta": {}
}
```

Respons error:

```json
{
  "success": false,
  "message": "Permintaan gagal.",
  "data": null,
  "errors": {
    "field": [
      "Pesan validasi."
    ]
  },
  "meta": {}
}
```

Controller tidak boleh mengembalikan bentuk response ad hoc yang tidak konsisten.

## 4. Konvensi Penamaan

- Class backend memakai PascalCase.
- Method dan variable backend memakai camelCase.
- Tabel database memakai snake_case jamak.
- Kolom database memakai snake_case.
- Route API memakai kebab-case atau nama resource REST yang jelas.
- File dan folder Flutter memakai snake_case.
- Class Flutter memakai PascalCase.
- File dokumentasi memakai kebab-case.

## 5. Kebijakan Struktur Folder

Repository diatur berdasarkan platform dan kebutuhan engineering:

```text
backend/
mobile/
docs/
api-contract/
ui-design/
```

Tanggung jawab backend:

- Controller menangani input dan output HTTP.
- Service menyimpan alur bisnis.
- Repository menangani batas akses data.
- Model mewakili entity database.
- Policy menangani keputusan otorisasi.
- Notification menangani pesan sistem.

Tanggung jawab Flutter:

- `models/` untuk model data.
- `services/` untuk API dan integrasi layanan platform.
- `providers/` untuk state management.
- `screens/` untuk UI level layar.
- `widgets/` untuk komponen UI reusable.
- `utils/` untuk helper bersama.

## 6. Disiplin Pengembangan

- Jangan membuat fitur bisnis tanpa scope fase yang jelas.
- Jangan memperkenalkan microservice.
- Jangan memperkenalkan websocket realtime.
- Jangan menambahkan fitur AI.
- Jangan melewati kontrak REST API.
- Jangan membuat file monolitik yang terlalu besar.
- Pilih kode yang jelas, sederhana, dan mudah dirawat.
- Pastikan perubahan cukup kecil untuk ditinjau.

## 7. Code Review

Review harus memeriksa:

- Scope sesuai fase.
- Kontrak API dan implementasi konsisten.
- Batas autentikasi dan otorisasi dihormati.
- Validasi ada di semua input penting.
- Penamaan dan penempatan folder sesuai aturan.
- Tidak ada refactor tidak terkait.
- Test atau catatan verifikasi disertakan bila perlu.

## 8. Kontrol Scope

- Phase 0 hanya untuk fondasi.
- Implementasi fitur dimulai setelah kontrak dan roadmap disepakati.
- Satu task tidak boleh melebar ke modul tidak terkait.
- Fitur khusus platform harus tetap di batas platformnya.
- Dependency baru harus punya alasan jelas dan cocok dengan stack.

## 9. Workflow API-First

Kontrak REST API menjadi titik koordinasi backend, web, dan mobile.

Workflow wajib:

1. Definisikan perilaku endpoint di `api-contract/`.
2. Sepakati struktur request dan response.
3. Implementasikan route, validasi, service, repository, dan model di backend.
4. Hubungkan web dan mobile ke API terdokumentasi.
5. Jaga perubahan API tetap sadar kompatibilitas dan terdokumentasi.

## 10. Prinsip Stability-First

Sistem mengutamakan fondasi stabil dibanding kompleksitas dini.

- Gunakan praktik standar Laravel dan Flutter selama tidak ada alasan kuat untuk menyimpang.
- Pusatkan format response bersama.
- Jaga autentikasi konsisten melalui Sanctum.
- Buat akses database mudah diprediksi melalui repository saat berguna.
- Gunakan validasi dan otorisasi eksplisit.
- Hindari abstraksi spekulatif sampai pola berulang benar-benar terbukti.
