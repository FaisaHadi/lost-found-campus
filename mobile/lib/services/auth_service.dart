import '../models/auth_session.dart';
import '../models/user_model.dart';
import 'api_client.dart';
import 'token_storage_service.dart';

class AuthService {
  const AuthService(this._apiClient, this._tokenStorage);

  final ApiClient _apiClient;
  final TokenStorageService _tokenStorage;

  Future<AuthSession> login({
    required String email,
    required String password,
  }) async {
    final json = await _apiClient.post(
      '/auth/login',
      data: {'email': email, 'password': password},
    );
    final session = AuthSession.fromJson(_dataObject(json));
    await _tokenStorage.saveToken(session.token);
    return session;
  }

  Future<AuthSession> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    final json = await _apiClient.post(
      '/auth/register',
      data: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
      },
    );
    final session = AuthSession.fromJson(_dataObject(json));
    await _tokenStorage.saveToken(session.token);
    return session;
  }

  Future<UserModel> me() async {
    final json = await _apiClient.get('/auth/me');
    final data = _dataObject(json);
    return UserModel.fromJson(
      Map<String, dynamic>.from(data['user'] ?? <String, dynamic>{}),
    );
  }

  Future<void> logout() async {
    try {
      await _apiClient.post('/auth/logout');
    } finally {
      await _tokenStorage.clearToken();
    }
  }

  Future<bool> hasToken() async {
    final token = await _tokenStorage.readToken();
    return token != null && token.isNotEmpty;
  }

  Future<void> clearSession() {
    return _tokenStorage.clearToken();
  }

  Map<String, dynamic> _dataObject(Map<String, dynamic> json) {
    final data = json['data'];
    if (data is Map) return Map<String, dynamic>.from(data);
    return <String, dynamic>{};
  }
}
