import '../constants/api_constants.dart';

class ApiUrl {
  const ApiUrl._();

  static String? resolveMediaUrl(String? value) {
    if (value == null || value.isEmpty) return null;
    if (value.startsWith('http://') || value.startsWith('https://')) {
      return value;
    }

    final origin = ApiConstants.baseUrl.replaceFirst(RegExp(r'/api/v1/?$'), '');
    final path = value.startsWith('/') ? value : '/$value';
    return '$origin$path';
  }
}
