import 'claim_model.dart';
import 'report_model.dart';

class CampusNotification {
  const CampusNotification({
    required this.id,
    required this.title,
    required this.message,
    required this.status,
    this.readAt,
    this.reportId,
    this.claimId,
    this.report,
    this.claim,
    this.createdAt,
    this.updatedAt,
  });

  final int id;
  final String title;
  final String message;
  final String status;
  final DateTime? readAt;
  final int? reportId;
  final int? claimId;
  final ReportModel? report;
  final ClaimModel? claim;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  bool get isUnread => status == 'unread' || readAt == null;

  factory CampusNotification.fromJson(Map<String, dynamic> json) {
    return CampusNotification(
      id: _asInt(json['id']) ?? 0,
      title: json['title']?.toString() ?? '',
      message: json['message']?.toString() ?? '',
      status: json['status']?.toString() ?? '',
      readAt: _asDate(json['read_at']),
      reportId: _asInt(json['report_id']),
      claimId: _asInt(json['claim_id']),
      report: _nested(json['report'], ReportModel.fromJson),
      claim: _nested(json['claim'], ClaimModel.fromJson),
      createdAt: _asDate(json['created_at']),
      updatedAt: _asDate(json['updated_at']),
    );
  }

  CampusNotification copyWith({String? status, DateTime? readAt}) {
    return CampusNotification(
      id: id,
      title: title,
      message: message,
      status: status ?? this.status,
      readAt: readAt ?? this.readAt,
      reportId: reportId,
      claimId: claimId,
      report: report,
      claim: claim,
      createdAt: createdAt,
      updatedAt: updatedAt,
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
