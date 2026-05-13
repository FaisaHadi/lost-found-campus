import 'package:geolocator/geolocator.dart';

class CapturedLocation {
  const CapturedLocation({
    required this.latitude,
    required this.longitude,
    required this.accuracy,
  });

  final double latitude;
  final double longitude;
  final double accuracy;
}

class LocationFailure implements Exception {
  const LocationFailure(this.message);

  final String message;

  @override
  String toString() => message;
}

class LocationService {
  Future<CapturedLocation> getCurrentLocation() async {
    final serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw const LocationFailure(
        'Layanan lokasi sedang nonaktif. Aktifkan lalu coba lagi.',
      );
    }

    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }

    if (permission == LocationPermission.denied) {
      throw const LocationFailure(
        'Izin lokasi ditolak. Anda tetap dapat mengisi lokasi secara manual.',
      );
    }

    if (permission == LocationPermission.deniedForever) {
      throw const LocationFailure(
        'Izin lokasi diblokir. Aktifkan melalui pengaturan sistem untuk mengisi koordinat otomatis.',
      );
    }

    final position = await Geolocator.getCurrentPosition(
      locationSettings: const LocationSettings(accuracy: LocationAccuracy.high),
    );

    return CapturedLocation(
      latitude: position.latitude,
      longitude: position.longitude,
      accuracy: position.accuracy,
    );
  }
}
