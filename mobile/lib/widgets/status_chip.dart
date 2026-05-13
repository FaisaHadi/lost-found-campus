import 'package:flutter/material.dart';

import '../constants/app_colors.dart';

class StatusChip extends StatelessWidget {
  const StatusChip({super.key, required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    final color = _colorFor(label);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        _humanize(label),
        style: TextStyle(
          color: color,
          fontSize: 12,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }

  Color _colorFor(String value) {
    return switch (value) {
      'approved' || 'completed' || 'read' => AppColors.success,
      'pending' || 'unread' => AppColors.warning,
      'rejected' => AppColors.danger,
      'claimed' => AppColors.info,
      'lost' => AppColors.danger,
      'found' => AppColors.primary,
      _ => AppColors.muted,
    };
  }

  String _humanize(String value) {
    const labels = {
      'pending': 'Menunggu',
      'approved': 'Disetujui',
      'rejected': 'Ditolak',
      'claimed': 'Diklaim',
      'completed': 'Selesai',
      'lost': 'Hilang',
      'found': 'Ditemukan',
      'read': 'Dibaca',
      'unread': 'Belum dibaca',
      'active': 'Aktif',
      'inactive': 'Tidak aktif',
    };

    if (value.isEmpty) return 'Tidak diketahui';
    if (labels.containsKey(value)) return labels[value]!;
    if (value.startsWith('report ')) {
      return 'Laporan #${value.substring('report '.length)}';
    }
    if (value.startsWith('claim ')) {
      return 'Klaim #${value.substring('claim '.length)}';
    }

    return value
        .split('_')
        .where((part) => part.isNotEmpty)
        .map((part) => '${part[0].toUpperCase()}${part.substring(1)}')
        .join(' ');
  }
}
