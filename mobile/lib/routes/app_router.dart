import 'package:flutter/material.dart';

import '../models/campus_notification.dart';
import '../models/report_model.dart';
import '../screens/auth/login_screen.dart';
import '../screens/auth/register_screen.dart';
import '../screens/claims/claim_submission_screen.dart';
import '../screens/home_screen.dart';
import '../screens/notifications/notification_detail_screen.dart';
import '../screens/reports/create_report_screen.dart';
import '../screens/reports/report_detail_screen.dart';
import 'route_names.dart';

class AppRouter {
  const AppRouter._();

  static Route<dynamic> onGenerateRoute(RouteSettings settings) {
    return MaterialPageRoute(
      settings: settings,
      builder: (context) => switch (settings.name) {
        RouteNames.login => const LoginScreen(),
        RouteNames.register => const RegisterScreen(),
        RouteNames.home => const HomeScreen(),
        RouteNames.createReport => const CreateReportScreen(),
        RouteNames.reportDetail => _reportDetail(settings.arguments),
        RouteNames.submitClaim => _claimSubmission(settings.arguments),
        RouteNames.notificationDetail => _notificationDetail(
          settings.arguments,
        ),
        _ => const HomeScreen(),
      },
    );
  }

  static Widget _reportDetail(Object? arguments) {
    final reportId = switch (arguments) {
      final int id => id,
      final ReportModel report => report.id,
      _ => null,
    };

    if (reportId == null) {
      return const _RouteError(message: 'ID laporan tidak ditemukan.');
    }
    return ReportDetailScreen(reportId: reportId);
  }

  static Widget _claimSubmission(Object? arguments) {
    if (arguments is! ReportModel) {
      return const _RouteError(message: 'Data laporan tidak ditemukan.');
    }
    return ClaimSubmissionScreen(report: arguments);
  }

  static Widget _notificationDetail(Object? arguments) {
    if (arguments is! CampusNotification) {
      return const _RouteError(message: 'Data notifikasi tidak ditemukan.');
    }
    return NotificationDetailScreen(notification: arguments);
  }
}

class _RouteError extends StatelessWidget {
  const _RouteError({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
      body: Center(child: Text(message)),
    );
  }
}
