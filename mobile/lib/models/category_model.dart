class CategoryModel {
  const CategoryModel({
    required this.id,
    required this.name,
    required this.slug,
    this.description,
    this.status,
  });

  final int id;
  final String name;
  final String slug;
  final String? description;
  final String? status;

  factory CategoryModel.fromJson(Map<String, dynamic> json) {
    return CategoryModel(
      id: _asInt(json['id']) ?? 0,
      name: json['name']?.toString() ?? '',
      slug: json['slug']?.toString() ?? '',
      description: json['description']?.toString(),
      status: json['status']?.toString(),
    );
  }

  static int? _asInt(Object? value) {
    if (value is int) return value;
    if (value is num) return value.toInt();
    if (value is String) return int.tryParse(value);
    return null;
  }
}
