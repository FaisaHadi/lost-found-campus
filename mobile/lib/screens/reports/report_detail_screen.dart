import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_colors.dart';
import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../models/report_model.dart';
import '../../providers/auth_provider.dart';
import '../../providers/reports_provider.dart';
import '../../routes/route_names.dart';
import '../../utils/api_url.dart';
import '../../utils/date_formatter.dart';
import '../../utils/view_state.dart';
import '../../widgets/app_button.dart';
import '../../widgets/app_card.dart';
import '../../widgets/error_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/status_chip.dart';

class ReportDetailScreen extends StatefulWidget {
  const ReportDetailScreen({super.key, required this.reportId});

  final int reportId;

  @override
  State<ReportDetailScreen> createState() => _ReportDetailScreenState();
}

class _ReportDetailScreenState extends State<ReportDetailScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ReportsProvider>().loadDetail(widget.reportId);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Detail laporan')),
      body: Consumer<ReportsProvider>(
        builder: (context, provider, child) {
          if (provider.detailState == ViewState.loading) {
            return const LoadingView();
          }
          if (provider.detailState == ViewState.error) {
            return ErrorState(
              message: provider.detailError ?? 'Laporan tidak dapat dimuat.',
              onRetry: () => provider.loadDetail(widget.reportId),
            );
          }

          final report = provider.selectedReport;
          if (report == null) {
            return const ErrorState(message: 'Laporan tidak ditemukan.');
          }

          return _ReportDetailContent(report: report);
        },
      ),
    );
  }
}

class _ReportDetailContent extends StatelessWidget {
  const _ReportDetailContent({required this.report});

  final ReportModel report;

  @override
  Widget build(BuildContext context) {
    final imageUrl = ApiUrl.resolveMediaUrl(report.imageUrl);
    final userId = context.select<AuthProvider, int?>((auth) => auth.user?.id);
    final canClaim = report.isClaimable && !report.isOwnedBy(userId);

    return SafeArea(
      top: false,
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(
          AppSpacing.screenPadding,
          AppSpacing.md,
          AppSpacing.screenPadding,
          AppSpacing.xl,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (imageUrl != null)
              ClipRRect(
                borderRadius: BorderRadius.circular(AppSpacing.radius),
                child: AspectRatio(
                  aspectRatio: 4 / 3,
                  child: Image.network(
                    imageUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (context, error, stackTrace) => Container(
                      color: AppColors.border,
                      alignment: Alignment.center,
                      child: const Icon(Icons.image_not_supported_outlined),
                    ),
                  ),
                ),
              ),
            const SizedBox(height: AppSpacing.lg),
            Wrap(
              spacing: AppSpacing.sm,
              runSpacing: AppSpacing.sm,
              children: [
                StatusChip(label: report.reportType),
                StatusChip(label: report.status),
                StatusChip(label: report.moderationStatus),
              ],
            ),
            const SizedBox(height: AppSpacing.lg),
            Text(report.title, style: AppTypography.titleLarge),
            const SizedBox(height: AppSpacing.sm),
            Text(
              report.category?.name ?? 'Tanpa kategori',
              style: AppTypography.bodyMuted,
            ),
            const SizedBox(height: AppSpacing.xl),
            AppCard(child: Text(report.description, style: AppTypography.body)),
            const SizedBox(height: AppSpacing.lg),
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Lokasi', style: AppTypography.label),
                  const SizedBox(height: AppSpacing.sm),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Icon(
                        Icons.place_outlined,
                        color: AppColors.primary,
                        size: 20,
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(
                        child: Text(
                          report.locationText?.isNotEmpty == true
                              ? report.locationText!
                              : 'Catatan lokasi belum diisi',
                          style: AppTypography.body,
                        ),
                      ),
                    ],
                  ),
                  if (report.latitude != null && report.longitude != null) ...[
                    const SizedBox(height: AppSpacing.sm),
                    Text(
                      '${report.latitude!.toStringAsFixed(6)}, ${report.longitude!.toStringAsFixed(6)}',
                      style: AppTypography.bodyMuted,
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: AppSpacing.lg),
            AppCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Riwayat', style: AppTypography.label),
                  const SizedBox(height: AppSpacing.sm),
                  Text(
                    'Dilaporkan ${DateFormatter.detail(report.createdAt)}',
                    style: AppTypography.bodyMuted,
                  ),
                  if (report.claimsCount != null)
                    Text(
                      '${report.claimsCount} klaim diajukan',
                      style: AppTypography.bodyMuted,
                    ),
                ],
              ),
            ),
            const SizedBox(height: AppSpacing.xl),
            if (canClaim)
              AppButton(
                label: 'Ajukan klaim',
                icon: Icons.assignment_turned_in_outlined,
                onPressed: () {
                  Navigator.of(
                    context,
                  ).pushNamed(RouteNames.submitClaim, arguments: report);
                },
              )
            else
              const Text(
                'Klaim tersedia setelah laporan disetujui dan tidak dapat diajukan untuk laporan milik sendiri.',
                style: AppTypography.bodyMuted,
              ),
          ],
        ),
      ),
    );
  }
}
