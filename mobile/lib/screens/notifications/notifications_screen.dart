import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../../constants/app_colors.dart';
import '../../constants/app_spacing.dart';
import '../../constants/app_typography.dart';
import '../../models/campus_notification.dart';
import '../../providers/notifications_provider.dart';
import '../../routes/route_names.dart';
import '../../utils/date_formatter.dart';
import '../../utils/view_state.dart';
import '../../widgets/app_card.dart';
import '../../widgets/empty_state.dart';
import '../../widgets/error_state.dart';
import '../../widgets/loading_view.dart';
import '../../widgets/status_chip.dart';

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});

  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  final _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_handleScroll);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<NotificationsProvider>().loadInitial();
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
      context.read<NotificationsProvider>().loadMore();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<NotificationsProvider>(
      builder: (context, notifications, child) {
        return SafeArea(
          top: false,
          child: RefreshIndicator(
            onRefresh: notifications.refresh,
            child: ListView(
              controller: _scrollController,
              padding: const EdgeInsets.fromLTRB(
                AppSpacing.screenPadding,
                AppSpacing.md,
                AppSpacing.screenPadding,
                96,
              ),
              children: [
                const _NotificationFilterChips(),
                const SizedBox(height: AppSpacing.lg),
                if (notifications.state == ViewState.loading)
                  const SizedBox(height: 360, child: LoadingView())
                else if (notifications.state == ViewState.error)
                  SizedBox(
                    height: 360,
                    child: ErrorState(
                      message:
                          notifications.errorMessage ??
                          'Notifikasi tidak dapat dimuat.',
                      onRetry: notifications.refresh,
                    ),
                  )
                else if (notifications.state == ViewState.empty)
                  const SizedBox(
                    height: 360,
                    child: EmptyState(
                      title: 'Belum ada notifikasi',
                      message:
                          'Persetujuan laporan, tinjauan klaim, dan pembaruan akan muncul di sini.',
                      icon: Icons.notifications_none_rounded,
                    ),
                  )
                else
                  ..._notificationItems(context, notifications),
              ],
            ),
          ),
        );
      },
    );
  }

  List<Widget> _notificationItems(
    BuildContext context,
    NotificationsProvider provider,
  ) {
    final items = <Widget>[];
    for (final notification in provider.notifications) {
      items.add(
        _NotificationCard(
          notification: notification,
          onTap: () async {
            await provider.markAsRead(notification);
            if (!context.mounted) return;
            Navigator.of(context).pushNamed(
              RouteNames.notificationDetail,
              arguments: notification.copyWith(
                status: 'read',
                readAt: DateTime.now(),
              ),
            );
          },
        ),
      );
      items.add(const SizedBox(height: AppSpacing.md));
    }
    if (provider.isLoadingMore) {
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

class _NotificationFilterChips extends StatelessWidget {
  const _NotificationFilterChips();

  @override
  Widget build(BuildContext context) {
    final current = context.select<NotificationsProvider, String?>(
      (provider) => provider.status,
    );
    final values = <String?, String>{
      null: 'Semua',
      'unread': 'Belum dibaca',
      'read': 'Dibaca',
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
                context.read<NotificationsProvider>().loadInitial(
                  status: entry.key,
                );
              },
            ),
          );
        }).toList(),
      ),
    );
  }
}

class _NotificationCard extends StatelessWidget {
  const _NotificationCard({required this.notification, required this.onTap});

  final CampusNotification notification;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return AppCard(
      onTap: onTap,
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 10,
            height: 10,
            margin: const EdgeInsets.only(top: 7),
            decoration: BoxDecoration(
              color: notification.isUnread
                  ? AppColors.secondary
                  : AppColors.border,
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        notification.title,
                        style: AppTypography.titleMedium,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: AppSpacing.sm),
                    StatusChip(label: notification.status),
                  ],
                ),
                const SizedBox(height: AppSpacing.sm),
                Text(
                  notification.message,
                  maxLines: 3,
                  overflow: TextOverflow.ellipsis,
                  style: AppTypography.bodyMuted,
                ),
                const SizedBox(height: AppSpacing.sm),
                Text(
                  DateFormatter.detail(notification.createdAt),
                  style: AppTypography.bodyMuted,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
