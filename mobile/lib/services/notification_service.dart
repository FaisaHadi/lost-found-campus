import '../models/api_page.dart';
import '../models/campus_notification.dart';
import 'api_client.dart';

class NotificationService {
  const NotificationService(this._apiClient);

  final ApiClient _apiClient;

  Future<ApiPage<CampusNotification>> fetchNotifications({
    int page = 1,
    int perPage = 15,
    String? status,
  }) async {
    final json = await _apiClient.get(
      '/notifications',
      queryParameters: _cleanQuery({
        'page': page,
        'per_page': perPage,
        'status': status,
        'sort_by': 'created_at',
        'sort_dir': 'desc',
      }),
    );
    return ApiPage.fromJson(json, CampusNotification.fromJson);
  }

  Future<CampusNotification> markAsRead(int id) async {
    final json = await _apiClient.patch('/notifications/$id/read');
    return CampusNotification.fromJson(_dataObject(json));
  }

  Map<String, dynamic> _dataObject(Map<String, dynamic> json) {
    final data = json['data'];
    if (data is Map) return Map<String, dynamic>.from(data);
    return <String, dynamic>{};
  }

  Map<String, dynamic> _cleanQuery(Map<String, dynamic> query) {
    return Map<String, dynamic>.from(query)
      ..removeWhere((key, value) => value == null || value == '');
  }
}
