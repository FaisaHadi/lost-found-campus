import 'package:dio/dio.dart';

class ApiException implements Exception {
  const ApiException({
    required this.message,
    this.statusCode,
    this.fieldErrors = const {},
  });

  final String message;
  final int? statusCode;
  final Map<String, List<String>> fieldErrors;

  bool get isUnauthorized => statusCode == 401;

  factory ApiException.fromDio(DioException error) {
    final response = error.response;
    final data = response?.data;
    String message = _networkMessage(error);
    Map<String, List<String>> fieldErrors = const {};

    if (data is Map) {
      final body = Map<String, dynamic>.from(data);
      message =
          body['message']?.toString() ??
          _nestedMessage(body['error']) ??
          message;
      fieldErrors = _normalizeErrors(body['errors'] ?? _nestedDetails(body));
    }

    return ApiException(
      message: message,
      statusCode: response?.statusCode,
      fieldErrors: fieldErrors,
    );
  }

  static String _networkMessage(DioException error) {
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.receiveTimeout:
      case DioExceptionType.sendTimeout:
        return 'Koneksi terlalu lama. Silakan coba lagi.';
      case DioExceptionType.badCertificate:
      case DioExceptionType.connectionError:
        return 'Server tidak dapat dihubungi. Periksa koneksi internet Anda.';
      case DioExceptionType.cancel:
        return 'Permintaan dibatalkan.';
      case DioExceptionType.badResponse:
      case DioExceptionType.unknown:
        return 'Terjadi kesalahan. Silakan coba lagi.';
    }
  }

  static String? _nestedMessage(Object? value) {
    if (value is Map) {
      return value['message']?.toString();
    }
    return null;
  }

  static Object? _nestedDetails(Map<String, dynamic> body) {
    final error = body['error'];
    if (error is Map) return error['details'];
    return null;
  }

  static Map<String, List<String>> _normalizeErrors(Object? value) {
    if (value is! Map) return const {};

    return value.map((key, raw) {
      final messages = raw is List
          ? raw.map((message) => message.toString()).toList()
          : <String>[raw.toString()];
      return MapEntry(key.toString(), messages);
    });
  }

  @override
  String toString() => message;
}
