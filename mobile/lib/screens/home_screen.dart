import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../constants/app_spacing.dart';
import '../providers/auth_provider.dart';
import '../providers/notifications_provider.dart';
import 'claims/claim_status_screen.dart';
import 'notifications/notifications_screen.dart';
import 'reports/report_list_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _currentIndex = 0;

  static const _titles = ['Laporan Kampus', 'Klaim', 'Notifikasi'];

  @override
  Widget build(BuildContext context) {
    final unreadCount = context.select<NotificationsProvider, int>(
      (provider) => provider.unreadCount,
    );

    return Scaffold(
      appBar: AppBar(
        title: Text(_titles[_currentIndex]),
        actions: [
          IconButton(
            tooltip: 'Keluar',
            onPressed: () => context.read<AuthProvider>().logout(),
            icon: const Icon(Icons.logout_rounded),
          ),
          const SizedBox(width: AppSpacing.sm),
        ],
      ),
      body: IndexedStack(
        index: _currentIndex,
        children: [
          const ReportListScreen(),
          const ClaimStatusScreen(),
          const NotificationsScreen(),
        ],
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: (index) {
          setState(() => _currentIndex = index);
        },
        items: [
          const BottomNavigationBarItem(
            icon: Icon(Icons.list_alt_rounded),
            label: 'Laporan',
          ),
          const BottomNavigationBarItem(
            icon: Icon(Icons.assignment_turned_in_outlined),
            label: 'Klaim',
          ),
          BottomNavigationBarItem(
            icon: Badge.count(
              count: unreadCount,
              isLabelVisible: unreadCount > 0,
              child: const Icon(Icons.notifications_none_rounded),
            ),
            label: 'Pemberitahuan',
          ),
        ],
      ),
    );
  }
}
