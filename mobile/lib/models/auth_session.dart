import 'user_model.dart';

class AuthSession {
  const AuthSession({
    required this.user,
    required this.token,
    required this.tokenType,
  });

  final UserModel user;
  final String token;
  final String tokenType;

  factory AuthSession.fromJson(Map<String, dynamic> json) {
    return AuthSession(
      user: UserModel.fromJson(Map<String, dynamic>.from(json['user'] ?? {})),
      token: json['token']?.toString() ?? '',
      tokenType: json['token_type']?.toString() ?? 'Bearer',
    );
  }
}
