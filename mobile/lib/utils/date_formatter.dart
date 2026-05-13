class DateFormatter {
  const DateFormatter._();

  static const _months = [
    '',
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'Mei',
    'Jun',
    'Jul',
    'Agu',
    'Sep',
    'Okt',
    'Nov',
    'Des',
  ];

  static String compact(DateTime? value) {
    if (value == null) return 'Tidak ada tanggal';
    final local = value.toLocal();
    return '${local.day} ${_months[local.month]} ${local.year}';
  }

  static String detail(DateTime? value) {
    if (value == null) return 'Tanggal tidak tersedia';
    final local = value.toLocal();
    final hour = local.hour.toString().padLeft(2, '0');
    final minute = local.minute.toString().padLeft(2, '0');
    return '${compact(local)}, $hour:$minute';
  }
}
