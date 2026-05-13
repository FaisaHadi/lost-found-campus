import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../models/claim_model.dart';
import '../../providers/claims_provider.dart';
import '../../utils/date_formatter.dart';
import '../../utils/view_state.dart';
import '../../widgets/app_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/error_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/status_chip.dart';

class ClaimStatusScreen extends StatefulWidget {
  const ClaimStatusScreen({super.key});

  @override
  State<ClaimStatusScreen> createState() => _ClaimStatusScreenState();
}

class _ClaimStatusScreenState extends State<ClaimStatusScreen> {
  final _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_handleScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<ClaimsProvider>().loadInitial();
    });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _handleScroll() {
    if (!_scrollController.hasClients) return;
    final position = _scrollController.position;
    if (position.pixels >= position.maxScrollExtent - 220) {
      context.read<ClaimsProvider>().loadMore();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<ClaimsProvider>(
      builder: (context, claims, child) {
        return SafeArea(
          top: false,
          child: RefreshIndicator(
            onRefresh: claims.refresh,
            child: ListView(
              controller: _scrollController,
              padding: const EdgeInsets.fromLTRB(
                AppSpacing.screenPadding,
                AppSpacing.md,
                AppSpacing.screenPadding,
                96,
              ),
              children: [
                const _ClaimFilterChips(),
                const SizedBox(height: AppSpacing.lg),
                if (claims.state == ViewState.loading)
                  const SizedBox(height: 360, child: LoadingView())
                else if (claims.state == ViewState.error)
                  SizedBox(
                    height: 360,
                    child: ErrorState(
                      message:
                          claims.errorMessage ?? 'Klaim tidak dapat dimuat.',
                      onRetry: claims.refresh,
                    ),
                  )
                else if (claims.state == ViewState.empty)
                  const SizedBox(
                    height: 360,
                    child: EmptyState(
                      title: 'Belum ada klaim',
                      message:
                          'Klaim kepemilikan yang dikirim akan muncul di sini beserta status tinjaunya.',
                      icon: Icons.assignment_outlined,
                    ),
                  )
                else
                  ..._claimItems(claims),
              ],
            ),
          ),
        );
      },
    );
  }

  List<Widget> _claimItems(ClaimsProvider claims) {
    final items = <Widget>[];
    for (final claim in claims.claims) {
      items.add(_ClaimCard(claim: claim));
      items.add(const SizedBox(height: AppSpacing.md));
    }
    if (claims.isLoadingMore) {
      items.add(
        const Padding(
          padding: EdgeInsets.all(AppSpacing.lg),
          child: Center(child: CircularProgressIndicator()),
        ),
      );
    }
    return items;
  }
}

class _ClaimFilterChips extends StatelessWidget {
  const _ClaimFilterChips();

  @override
  Widget build(BuildContext context) {
    final current = context.select<ClaimsProvider, String?>(
      (provider) => provider.status,
    );
    final values = <String?, String>{
      null: 'Semua',
      'pending': 'Menunggu',
      'approved': 'Disetujui',
      'rejected': 'Ditolak',
    };

    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: values.entries.map((entry) {
          return Padding(
            padding: const EdgeInsets.only(right: AppSpacing.sm),
            child: ChoiceChip(
              label: Text(entry.value),
              selected: current == entry.key,
              onSelected: (_) {
                context.read<ClaimsProvider>().loadInitial(status: entry.key);
              },
            ),
          );
        }).toList(),
      ),
    );
  }
}

class _ClaimCard extends StatelessWidget {
  const _ClaimCard({required this.claim});

  final ClaimModel claim;

  @override
  Widget build(BuildContext context) {
    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              StatusChip(label: claim.status),
              const Spacer(),
              Text(
                DateFormatter.compact(claim.createdAt),
                style: AppTypography.bodyMuted,
              ),
            ],
          ),
          const SizedBox(height: AppSpacing.md),
          Text(
            claim.report?.title ?? 'Laporan #${claim.reportId}',
            style: AppTypography.titleMedium,
          ),
          const SizedBox(height: AppSpacing.sm),
          Text(
            claim.proofText,
            maxLines: 3,
            overflow: TextOverflow.ellipsis,
            style: AppTypography.bodyMuted,
          ),
          if (claim.reviewedAt != null) ...[
            const SizedBox(height: AppSpacing.sm),
            Text(
              'Ditinjau ${DateFormatter.detail(claim.reviewedAt)}',
              style: AppTypography.bodyMuted,
            ),
          ],
        ],
      ),
    );
  }
}
