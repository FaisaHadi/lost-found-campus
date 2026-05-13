import 'report_model.dart';
import 'user_model.dart';

class ClaimModel {
  const ClaimModel({
    required this.id,
    required this.reportId,
    required this.claimantId,
    required this.proofText,
    required this.status,
    this.reviewedBy,
    this.reviewedAt,
    this.report,
    this.claimant,
    this.reviewer,
    this.createdAt,
    this.updatedAt,
  });

  final int id;
  final int reportId;
  final int claimantId;
  final String proofText;
  final String status;
  final int? reviewedBy;
  final DateTime? reviewedAt;
  final ReportModel? report;
  final UserModel? claimant;
  final UserModel? reviewer;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  factory ClaimModel.fromJson(Map<String, dynamic> json) {
    return ClaimModel(
      id: _asInt(json['id']) ?? 0,
      reportId: _asInt(json['report_id']) ?? 0,
      claimantId: _asInt(json['claimant_id']) ?? 0,
      proofText: json['proof_text']?.toString() ?? '',
      status: json['status']?.toString() ?? '',
      reviewedBy: _asInt(json['reviewed_by']),
      reviewedAt: _asDate(json['reviewed_at']),
      report: _nested(json['report'], ReportModel.fromJson),
      claimant: _nested(json['claimant'], UserModel.fromJson),
      reviewer: _nested(json['reviewer'], UserModel.fromJson),
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

  static DateTime? _asDate(Object? value) {
    if (value == null) return null;
    return DateTime.tryParse(value.toString());
  }
}
