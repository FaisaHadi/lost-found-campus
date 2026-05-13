import 'package:flutter/material.dart';

import '../constants/app_colors.dart';
import '../constants/app_spacing.dart';
import '../constants/app_typography.dart';
import '../models/report_model.dart';
import '../utils/api_url.dart';
import '../utils/date_formatter.dart';
import 'app_card.dart';
import 'status_chip.dart';

class ReportCard extends StatelessWidget {
  const ReportCard({super.key, required this.report, this.onTap});

  final ReportModel report;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final imageUrl = ApiUrl.resolveMediaUrl(report.imageUrl);

    return AppCard(
      onTap: onTap,
      padding: EdgeInsets.zero,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (imageUrl != null)
            ClipRRect(
              borderRadius: const BorderRadius.vertical(
                top: Radius.circular(AppSpacing.radius),
              ),
              child: AspectRatio(
                aspectRatio: 16 / 9,
                child: Image.network(
                  imageUrl,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) =>
                      _imageFallback(),
                ),
              ),
            ),
          Padding(
            padding: const EdgeInsets.all(AppSpacing.lg),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Wrap(
                  spacing: AppSpacing.sm,
                  runSpacing: AppSpacing.sm,
                  children: [
                    StatusChip(label: report.reportType),
                    StatusChip(label: report.status),
                  ],
                ),
                const SizedBox(height: AppSpacing.md),
                Text(
                  report.title,
                  style: AppTypography.titleMedium,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: AppSpacing.sm),
                Text(
                  report.description,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: AppTypography.bodyMuted,
                ),
                const SizedBox(height: AppSpacing.md),
                Row(
                  children: [
                    const Icon(
                      Icons.place_outlined,
                      size: 18,
                      color: AppColors.muted,
                    ),
                    const SizedBox(width: AppSpacing.xs),
                    Expanded(
                      child: Text(
                        report.locationText?.isNotEmpty == true
                            ? report.locationText!
                            : 'Lokasi belum diisi',
                        style: AppTypography.bodyMuted,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    Text(
                      DateFormatter.compact(report.createdAt),
                      style: AppTypography.bodyMuted,
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _imageFallback() {
    return Container(
      color: AppColors.border,
      alignment: Alignment.center,
      child: const Icon(Icons.image_not_supported_outlined),
    );
  }
}
