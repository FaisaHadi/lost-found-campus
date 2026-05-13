# Mobile Lost & Found Campus

Folder ini berisi aplikasi mobile Flutter untuk pengguna kampus. Aplikasi dipakai untuk membuat laporan barang hilang/ditemukan, menelusuri laporan, mengajukan klaim, menerima notifikasi, mengambil foto, dan melampirkan lokasi GPS.

## Setup

```bash
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

Gunakan URL backend yang bisa dijangkau emulator atau perangkat fisik.

## Mengatasi Timeout Saat Masuk/Registrasi

Aplikasi mobile harus bisa menjangkau Laravel API dari perangkat Android.

Untuk Android Emulator:

```bash
cd ../backend
php artisan serve --host=127.0.0.1 --port=8000

cd ../mobile
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

Untuk HP Android fisik di Wi-Fi yang sama:

```bash
cd ../backend
php artisan serve --host=0.0.0.0 --port=8000

cd ../mobile
flutter run --dart-define=API_BASE_URL=http://YOUR_LAPTOP_IP:8000/api/v1
```

Contoh:

```bash
flutter run --dart-define=API_BASE_URL=http://192.168.1.10:8000/api/v1
```

Jangan memakai `10.0.2.2` di HP fisik. Alamat itu hanya berlaku dari Android Emulator. Jika masih timeout, pastikan Windows Firewall mengizinkan akses masuk ke port `8000`.

## Validasi

```bash
flutter analyze --no-pub
flutter test --no-pub
```

## Persiapan Rilis Android

1. Buat release keystore di luar version control.
2. Salin `android/key.properties.example` menjadi `android/key.properties`.
3. Isi nilai keystore yang sebenarnya.
4. Build dengan URL API produksi:

```bash
flutter build apk --release --dart-define=API_BASE_URL=https://your-domain.example/api/v1
```

File `android/key.properties` dan file keystore sengaja diabaikan oleh Git.
