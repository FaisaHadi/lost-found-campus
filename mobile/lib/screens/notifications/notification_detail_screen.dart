import 'package:flutter/material.dart';

import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../models/campus_notification.dart';
import '../../utils/date_formatter.dart';
import '../../widgets/app_card.dart';
import '../../widgets/status_chip.dart';

class NotificationDetailScreen extends StatelessWidget {
  const NotificationDetailScreen({super.key, required this.notification});

  final CampusNotification notification;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Notifikasi')),
      body: SafeArea(
        top: false,
        child: ListView(
          padding: const EdgeInsets.all(AppSpacing.screenPadding),
          children: [
            Wrap(
              spacing: AppSpacing.sm,
              children: [
                StatusChip(label: notification.status),
                if (notification.reportId != null)
                  StatusChip(label: 'report ${notification.reportId}'),
                if (notification.claimId != null)
                  StatusChip(label: 'claim ${notification.claimId}'),
              ],
            ),
            const SizedBox(height: AppSpacing.lg),
            Text(notification.title, style: AppTypography.titleLarge),
            const SizedBox(height: AppSpacing.sm),
            Text(
              DateFormatter.detail(notification.createdAt),
              style: AppTypography.bodyMuted,
            ),
            const SizedBox(height: AppSpacing.xl),
            AppCard(
              child: Text(notification.message, style: AppTypography.body),
            ),
            if (notification.report != null) ...[
              const SizedBox(height: AppSpacing.lg),
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Laporan terkait', style: AppTypography.label),
                    const SizedBox(height: AppSpacing.sm),
                    Text(
                      notification.report!.title,
                      style: AppTypography.titleMedium,
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    Text(
                      notification.report!.locationText ?? 'Lokasi belum diisi',
                      style: AppTypography.bodyMuted,
                    ),
                  ],
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
