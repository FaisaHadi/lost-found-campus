import 'package:flutter/material.dart';

import '../constants/app_colors.dart';
import '../constants/app_spacing.dart';

enum AppButtonVariant { primary, secondary, ghost, danger }

class AppButton extends StatelessWidget {
  const AppButton({
    super.key,
    required this.label,
    this.onPressed,
    this.icon,
    this.isLoading = false,
    this.fullWidth = true,
    this.variant = AppButtonVariant.primary,
  });

  final String label;
  final VoidCallback? onPressed;
  final IconData? icon;
  final bool isLoading;
  final bool fullWidth;
  final AppButtonVariant variant;

  @override
  Widget build(BuildContext context) {
    final child = isLoading
        ? const SizedBox.square(
            dimension: 20,
            child: CircularProgressIndicator(strokeWidth: 2),
          )
        : Row(
            mainAxisAlignment: MainAxisAlignment.center,
            mainAxisSize: MainAxisSize.min,
            children: [
              if (icon != null) ...[
                Icon(icon, size: 20),
                const SizedBox(width: AppSpacing.sm),
              ],
              Flexible(child: Text(label, overflow: TextOverflow.ellipsis)),
            ],
          );

    final button = switch (variant) {
      AppButtonVariant.primary => FilledButton(
        onPressed: isLoading ? null : onPressed,
        style: _style(AppColors.primary, Colors.white),
        child: child,
      ),
      AppButtonVariant.secondary => FilledButton(
        onPressed: isLoading ? null : onPressed,
        style: _style(AppColors.secondary, AppColors.ink),
        child: child,
      ),
      AppButtonVariant.danger => FilledButton(
        onPressed: isLoading ? null : onPressed,
        style: _style(AppColors.danger, Colors.white),
        child: child,
      ),
      AppButtonVariant.ghost => OutlinedButton(
        onPressed: isLoading ? null : onPressed,
        style: OutlinedButton.styleFrom(
          minimumSize: const Size(0, AppSpacing.buttonHeight),
          foregroundColor: AppColors.primary,
          side: const BorderSide(color: AppColors.border),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppSpacing.radius),
          ),
        ),
        child: child,
      ),
    };

    if (!fullWidth) return button;
    return SizedBox(width: double.infinity, child: button);
  }

  ButtonStyle _style(Color background, Color foreground) {
    return FilledButton.styleFrom(
      minimumSize: const Size(0, AppSpacing.buttonHeight),
      backgroundColor: background,
      foregroundColor: foreground,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppSpacing.radius),
      ),
    );
  }
}
