import 'package:flutter/foundation.dart';

import '../models/user_model.dart';
import '../services/api_exception.dart';
import '../services/auth_service.dart';

enum AuthStatus { unknown, authenticated, unauthenticated }

class AuthProvider extends ChangeNotifier {
  AuthProvider(this._authService);

  final AuthService _authService;

  AuthStatus _status = AuthStatus.unknown;
  UserModel? _user;
  bool _isBusy = false;
  String? _errorMessage;
  Map<String, List<String>> _fieldErrors = const {};

  AuthStatus get status => _status;
  UserModel? get user => _user;
  bool get isAuthenticated => _status == AuthStatus.authenticated;
  bool get isBusy => _isBusy;
  String? get errorMessage => _errorMessage;
  Map<String, List<String>> get fieldErrors => _fieldErrors;

  Future<void> bootstrap() async {
    if (!await _authService.hasToken()) {
      _status = AuthStatus.unauthenticated;
      notifyListeners();
      return;
    }

    try {
      _user = await _authService.me();
      _status = AuthStatus.authenticated;
    } on ApiException catch (error) {
      _errorMessage = error.message;
      await _authService.clearSession();
      _status = AuthStatus.unauthenticated;
    } catch (_) {
      _errorMessage = 'Sesi Anda tidak dapat dipulihkan.';
      await _authService.clearSession();
      _status = AuthStatus.unauthenticated;
    }
    notifyListeners();
  }

  Future<bool> login({required String email, required String password}) async {
    _startWork();
    try {
      final session = await _authService.login(
        email: email,
        password: password,
      );
      _user = session.user;
      _status = AuthStatus.authenticated;
      return true;
    } on ApiException catch (error) {
      _setError(error);
      return false;
    } catch (_) {
      _errorMessage = 'Saat ini Anda tidak dapat masuk.';
      return false;
    } finally {
      _finishWork();
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
  }) async {
    _startWork();
    try {
      final session = await _authService.register(
        name: name,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
      );
      _user = session.user;
      _status = AuthStatus.authenticated;
      return true;
    } on ApiException catch (error) {
      _setError(error);
      return false;
    } catch (_) {
      _errorMessage = 'Saat ini akun tidak dapat dibuat.';
      return false;
    } finally {
      _finishWork();
    }
  }

  Future<void> logout() async {
    _startWork();
    try {
      await _authService.logout();
    } catch (_) {
      // Local token cleanup still happens below; logout should not trap users.
    } finally {
      _user = null;
      _status = AuthStatus.unauthenticated;
      _finishWork();
    }
  }

  Future<void> markSessionExpired() async {
    _user = null;
    _status = AuthStatus.unauthenticated;
    _errorMessage = 'Sesi Anda berakhir. Silakan masuk kembali.';
    notifyListeners();
  }

  void clearError() {
    _errorMessage = null;
    _fieldErrors = const {};
    notifyListeners();
  }

  void _startWork() {
    _isBusy = true;
    _errorMessage = null;
    _fieldErrors = const {};
    notifyListeners();
  }

  void _finishWork() {
    _isBusy = false;
    notifyListeners();
  }

  void _setError(ApiException error) {
    _errorMessage = error.message;
    _fieldErrors = error.fieldErrors;
    if (error.isUnauthorized) {
      _status = AuthStatus.unauthenticated;
      _user = null;
    }
  }
}
