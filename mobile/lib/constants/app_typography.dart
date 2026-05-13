import 'package:flutter/material.dart';

import 'app_colors.dart';

class AppTypography {
  const AppTypography._();

  static const titleLarge = TextStyle(
    color: AppColors.ink,
    fontSize: 26,
    fontWeight: FontWeight.w700,
    height: 1.18,
  );

  static const titleMedium = TextStyle(
    color: AppColors.ink,
    fontSize: 20,
    fontWeight: FontWeight.w700,
    height: 1.25,
  );

  static const body = TextStyle(
    color: AppColors.ink,
    fontSize: 15,
    height: 1.45,
  );

  static const bodyMuted = TextStyle(
    color: AppColors.muted,
    fontSize: 14,
    height: 1.35,
  );

  static const label = TextStyle(
    color: AppColors.ink,
    fontSize: 13,
    fontWeight: FontWeight.w700,
    height: 1.2,
  );
}
