import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../models/report_model.dart';
import '../../providers/claims_provider.dart';
import '../../utils/snackbars.dart';
import '../../utils/validators.dart';
import '../../widgets/app_button.dart';
import '../../widgets/app_card.dart';
import '../../widgets/app_text_field.dart';
import '../../widgets/status_chip.dart';

class ClaimSubmissionScreen extends StatefulWidget {
  const ClaimSubmissionScreen({super.key, required this.report});

  final ReportModel report;

  @override
  State<ClaimSubmissionScreen> createState() => _ClaimSubmissionScreenState();
}

class _ClaimSubmissionScreenState extends State<ClaimSubmissionScreen> {
  final _formKey = GlobalKey<FormState>();
  final _proofController = TextEditingController();

  @override
  void dispose() {
    _proofController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    FocusScope.of(context).unfocus();
    if (!_formKey.currentState!.validate()) return;

    final claims = context.read<ClaimsProvider>();
    final ok = await claims.submitClaim(
      reportId: widget.report.id,
      proofText: _proofController.text.trim(),
    );

    if (!mounted) return;
    if (ok) {
      AppSnackBars.showSuccess(context, 'Klaim dikirim untuk ditinjau.');
      Navigator.of(context).pop();
    } else {
      AppSnackBars.showError(
        context,
        claims.errorMessage ?? 'Klaim tidak dapat dikirim.',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final claims = context.watch<ClaimsProvider>();

    return Scaffold(
      appBar: AppBar(title: const Text('Ajukan klaim')),
      body: SafeArea(
        top: false,
        child: Form(
          key: _formKey,
          child: ListView(
            keyboardDismissBehavior: ScrollViewKeyboardDismissBehavior.onDrag,
            padding: const EdgeInsets.all(AppSpacing.screenPadding),
            children: [
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Wrap(
                      spacing: AppSpacing.sm,
                      children: [
                        StatusChip(label: widget.report.reportType),
                        StatusChip(label: widget.report.status),
                      ],
                    ),
                    const SizedBox(height: AppSpacing.md),
                    Text(widget.report.title, style: AppTypography.titleMedium),
                    const SizedBox(height: AppSpacing.sm),
                    Text(
                      widget.report.locationText ?? 'Tidak ada catatan lokasi',
                      style: AppTypography.bodyMuted,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: AppSpacing.xl),
              const Text('Bukti kepemilikan', style: AppTypography.titleMedium),
              const SizedBox(height: AppSpacing.sm),
              const Text(
                'Jelaskan detail yang hanya diketahui pemilik. Admin akan meninjau klaim sebelum status berubah.',
                style: AppTypography.bodyMuted,
              ),
              const SizedBox(height: AppSpacing.lg),
              AppTextField(
                label: 'Deskripsi bukti',
                controller: _proofController,
                keyboardType: TextInputType.multiline,
                minLines: 6,
                maxLines: 9,
                prefixIcon: const Icon(Icons.fact_check_outlined),
                validator: (value) =>
                    Validators.minLength(value, 20, label: 'Bukti'),
              ),
              const SizedBox(height: AppSpacing.xl),
              AppButton(
                label: 'Kirim klaim',
                icon: Icons.send_rounded,
                isLoading: claims.isSubmitting,
                onPressed: _submit,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
