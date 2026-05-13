import 'category_model.dart';
import 'user_model.dart';

class ReportModel {
  const ReportModel({
    required this.id,
    required this.title,
    required this.description,
    required this.reportType,
    required this.status,
    required this.moderationStatus,
    this.imagePath,
    this.imageUrl,
    this.latitude,
    this.longitude,
    this.locationText,
    this.category,
    this.user,
    this.claimsCount,
    this.createdAt,
    this.updatedAt,
  });

  final int id;
  final String title;
  final String description;
  final String reportType;
  final String status;
  final String moderationStatus;
  final String? imagePath;
  final String? imageUrl;
  final double? latitude;
  final double? longitude;
  final String? locationText;
  final CategoryModel? category;
  final UserModel? user;
  final int? claimsCount;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  bool get isClaimable =>
      status == 'approved' && moderationStatus == 'approved';

  bool isOwnedBy(int? userId) => userId != null && user?.id == userId;

  factory ReportModel.fromJson(Map<String, dynamic> json) {
    return ReportModel(
      id: _asInt(json['id']) ?? 0,
      title: json['title']?.toString() ?? '',
      description: json['description']?.toString() ?? '',
      reportType: json['report_type']?.toString() ?? '',
      status: json['status']?.toString() ?? '',
      moderationStatus: json['moderation_status']?.toString() ?? '',
      imagePath: json['image_path']?.toString(),
      imageUrl: json['image_url']?.toString(),
      latitude: _asDouble(json['latitude']),
      longitude: _asDouble(json['longitude']),
      locationText: json['location_text']?.toString(),
      category: _nested(json['category'], CategoryModel.fromJson),
      user: _nested(json['user'], UserModel.fromJson),
      claimsCount: _asInt(json['claims_count']),
      createdAt: _asDate(json['created_at']),
      updatedAt: _asDate(json['updated_at']),
    );
  }

  static T? _nested<T>(
    Object? value,
    T Function(Map<String, dynamic> json) mapper,
  ) {
    if (value is Map) return mapper(Map<String, dynamic>.from(value));
    return null;
  }

  static int? _asInt(Object? value) {
    if (value is int) return value;
    if (value is num) return value.toInt();
    if (value is String) return int.tryParse(value);
    return null;
  }

  static double? _asDouble(Object? value) {
    if (value is double) return value;
    if (value is num) return value.toDouble();
    if (value is String) return double.tryParse(value);
    return null;
  }

  static DateTime? _asDate(Object? value) {
    if (value == null) return null;
    return DateTime.tryParse(value.toString());
  }
}
