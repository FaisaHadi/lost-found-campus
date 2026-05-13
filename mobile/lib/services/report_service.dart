import 'package:dio/dio.dart';

import '../models/api_page.dart';
import '../models/category_model.dart';
import '../models/create_report_payload.dart';
import '../models/report_model.dart';
import 'api_client.dart';

class ReportService {
  const ReportService(this._apiClient);

  final ApiClient _apiClient;

  Future<ApiPage<ReportModel>> fetchReports({
    int page = 1,
    int perPage = 12,
    String? keyword,
    String? reportType,
    int? categoryId,
    String? status,
  }) async {
    final json = await _apiClient.get(
      '/reports',
      queryParameters: _cleanQuery({
        'page': page,
        'per_page': perPage,
        'keyword': keyword,
        'report_type': reportType,
        'category_id': categoryId,
        'status': status,
        'sort_by': 'created_at',
        'sort_dir': 'desc',
      }),
    );
    return ApiPage.fromJson(json, ReportModel.fromJson);
  }

  Future<ReportModel> fetchReport(int id) async {
    final json = await _apiClient.get('/reports/$id');
    return ReportModel.fromJson(_dataObject(json));
  }

  Future<List<CategoryModel>> fetchCategories() async {
    final json = await _apiClient.get(
      '/categories',
      queryParameters: const {
        'status': 'active',
        'per_page': 100,
        'sort_by': 'name',
        'sort_dir': 'asc',
      },
    );
    final data = json['data'];
    if (data is! List) return <CategoryModel>[];
    return data
        .whereType<Map>()
        .map((item) => CategoryModel.fromJson(Map<String, dynamic>.from(item)))
        .toList();
  }

  Future<ReportModel> createReport(CreateReportPayload payload) async {
    final formData = FormData.fromMap(await _createReportMap(payload));
    final json = await _apiClient.post('/reports', data: formData);
    return ReportModel.fromJson(_dataObject(json));
  }

  Future<Map<String, dynamic>> _createReportMap(
    CreateReportPayload payload,
  ) async {
    final data = <String, dynamic>{
      'title': payload.title,
      'description': payload.description,
      'report_type': payload.reportType,
      'category_id': payload.categoryId,
      'latitude': payload.latitude,
      'longitude': payload.longitude,
      'location_text': payload.locationText,
    }..removeWhere((key, value) => value == null || value == '');

    final imagePath = payload.imagePath;
    if (imagePath != null && imagePath.isNotEmpty) {
      data['image'] = await MultipartFile.fromFile(
        imagePath,
        filename: imagePath.split(RegExp(r'[\\/]')).last,
      );
    }

    return data;
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
