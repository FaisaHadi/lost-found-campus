# Paket Delivery Akhir

Gunakan folder ini sebagai area staging artifact pengumpulan akademik.

Paket akhir yang disarankan:

```text
delivery/
  README.md
  apk/
    lost-found-campus-release.apk
  sql/
    optional-database-export.sql
  lost-found-campus-final/
    README.md
    backend/
    mobile/
    docs/
    presentation/
    database/
    screenshots/
    apk/
```

Folder source tetap berada di root repository:

- `backend/`
- `mobile/`
- `docs/`
- `api-contract/`

Path APK hasil build:

```text
mobile/build/app/outputs/flutter-apk/app-release.apk
```

Salin dan ubah nama APK tersebut menjadi:

```text
delivery/apk/lost-found-campus-release.apk
```

Jangan masukkan file `.env` asli, keystore, atau password signing ke paket akhir.

Skeleton arsip akhir tersedia di:

```text
delivery/lost-found-campus-final/
```

Gunakan skeleton tersebut sebagai manifest untuk menyusun arsip eksternal tanpa menggandakan source besar di dalam repository ini.
