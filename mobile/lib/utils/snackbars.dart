import 'package:flutter/material.dart';

import '../constants/app_colors.dart';

class AppSnackBars {
  const AppSnackBars._();

  static void showError(BuildContext context, String message) {
    _show(context, message, AppColors.danger);
  }

  static void showSuccess(BuildContext context, String message) {
    _show(context, message, AppColors.success);
  }

  static void _show(BuildContext context, String message, Color color) {
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: Text(message),
          backgroundColor: color,
          behavior: SnackBarBehavior.floating,
        ),
      );
  }
}
