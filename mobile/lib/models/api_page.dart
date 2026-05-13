class ApiPage<T> {
  const ApiPage({
    required this.items,
    required this.currentPage,
    required this.lastPage,
    required this.perPage,
    required this.total,
  });

  final List<T> items;
  final int currentPage;
  final int lastPage;
  final int perPage;
  final int total;

  bool get hasMore => currentPage < lastPage;

  factory ApiPage.fromJson(
    Map<String, dynamic> json,
    T Function(Map<String, dynamic> item) mapper,
  ) {
    final data = json['data'];
    final meta = json['meta'];
    final items = data is List
        ? data
              .whereType<Map>()
              .map((item) => mapper(Map<String, dynamic>.from(item)))
              .toList()
        : <T>[];

    return ApiPage<T>(
      items: items,
      currentPage: _asInt(meta?['current_page']) ?? 1,
      lastPage: _asInt(meta?['last_page']) ?? 1,
      perPage: _asInt(meta?['per_page']) ?? items.length,
      total: _asInt(meta?['total']) ?? items.length,
    );
  }

  static int? _asInt(Object? value) {
    if (value is int) return value;
    if (value is num) return value.toInt();
    if (value is String) return int.tryParse(value);
    return null;
  }
}
