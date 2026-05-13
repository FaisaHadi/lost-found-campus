import 'package:dio/dio.dart';

import '../constants/api_constants.dart';
import 'api_exception.dart';
import 'token_storage_service.dart';

class ApiClient {
  ApiClient(this._tokenStorage)
    : dio = Dio(
        BaseOptions(
          baseUrl: ApiConstants.baseUrl,
          connectTimeout: ApiConstants.connectTimeout,
          receiveTimeout: ApiConstants.receiveTimeout,
          headers: const {'Accept': 'application/json'},
        ),
      ) {
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await _tokenStorage.readToken();
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          handler.next(options);
        },
        onError: (error, handler) async {
          if (error.response?.statusCode == 401) {
            await _tokenStorage.clearToken();
            await onUnauthorized?.call();
          }
          handler.next(error);
        },
      ),
    );
  }

  final TokenStorageService _tokenStorage;
  final Dio dio;
  Future<void> Function()? onUnauthorized;

  Future<Map<String, dynamic>> get(
    String path, {
    Map<String, dynamic>? queryParameters,
  }) {
    return _request(() => dio.get(path, queryParameters: queryParameters));
  }

  Future<Map<String, dynamic>> post(
    String path, {
    Object? data,
    Map<String, dynamic>? queryParameters,
  }) {
    return _request(
      () => dio.post(path, data: data, queryParameters: queryParameters),
    );
  }

  Future<Map<String, dynamic>> patch(String path, {Object? data}) {
    return _request(() => dio.patch(path, data: data));
  }

  Future<Map<String, dynamic>> put(String path, {Object? data}) {
    return _request(() => dio.put(path, data: data));
  }

  Future<Map<String, dynamic>> delete(String path) {
    return _request(() => dio.delete(path));
  }

  Future<Map<String, dynamic>> _request(
    Future<Response<dynamic>> Function() request,
  ) async {
    try {
      final response = await request();
      final data = response.data;
      if (data is Map) {
        return Map<String, dynamic>.from(data);
      }
      return <String, dynamic>{'data': data};
    } on DioException catch (error) {
      throw ApiException.fromDio(error);
    }
  }
}
