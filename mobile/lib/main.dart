import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'constants/app_theme.dart';
import 'providers/auth_provider.dart';
import 'providers/claims_provider.dart';
import 'providers/notifications_provider.dart';
import 'providers/reports_provider.dart';
import 'routes/app_router.dart';
import 'screens/auth/login_screen.dart';
import 'screens/home_screen.dart';
import 'screens/splash_screen.dart';
import 'services/api_client.dart';
import 'services/auth_service.dart';
import 'services/camera_service.dart';
import 'services/claim_service.dart';
import 'services/location_service.dart';
import 'services/notification_service.dart';
import 'services/report_service.dart';
import 'services/token_storage_service.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  final tokenStorage = TokenStorageService();
  final apiClient = ApiClient(tokenStorage);
  final authProvider = AuthProvider(AuthService(apiClient, tokenStorage));

  apiClient.onUnauthorized = authProvider.markSessionExpired;

  runApp(
    MultiProvider(
      providers: [
        Provider<CameraService>(create: (_) => CameraService()),
        Provider<LocationService>(create: (_) => LocationService()),
        ChangeNotifierProvider<AuthProvider>(
          create: (_) => authProvider..bootstrap(),
        ),
        ChangeNotifierProvider<ReportsProvider>(
          create: (_) => ReportsProvider(ReportService(apiClient)),
        ),
        ChangeNotifierProvider<ClaimsProvider>(
          create: (_) => ClaimsProvider(ClaimService(apiClient)),
        ),
        ChangeNotifierProvider<NotificationsProvider>(
          create: (_) => NotificationsProvider(NotificationService(apiClient)),
        ),
      ],
      child: const LostFoundCampusApp(),
    ),
  );
}

class LostFoundCampusApp extends StatelessWidget {
  const LostFoundCampusApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Lost & Found Campus',
      theme: AppTheme.light(),
      debugShowCheckedModeBanner: false,
      onGenerateRoute: AppRouter.onGenerateRoute,
      home: const AuthGate(),
    );
  }
}

class AuthGate extends StatelessWidget {
  const AuthGate({super.key});

  @override
  Widget build(BuildContext context) {
    final status = context.select<AuthProvider, AuthStatus>(
      (provider) => provider.status,
    );

    return switch (status) {
      AuthStatus.unknown => const SplashScreen(),
      AuthStatus.authenticated => const HomeScreen(),
      AuthStatus.unauthenticated => const LoginScreen(),
    };
  }
}
