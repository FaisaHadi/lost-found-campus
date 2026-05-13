# Slot Arsip APK

Salin APK final ke folder ini saat menyusun arsip eksternal.

APK staging saat ini:

```text
delivery/apk/lost-found-campus-release.apk
```

Sebelum distribusi nyata, build ulang dengan URL API produksi asli:

```bash
cd mobile
flutter build apk --release --dart-define=API_BASE_URL=https://your-domain.example/api/v1
```
