import '../models/api_page.dart';
import '../models/claim_model.dart';
import 'api_client.dart';

class ClaimService {
  const ClaimService(this._apiClient);

  final ApiClient _apiClient;

  Future<ApiPage<ClaimModel>> fetchClaims({
    int page = 1,
    int perPage = 12,
    String? status,
    int? reportId,
  }) async {
    final json = await _apiClient.get(
      '/claims',
      queryParameters: _cleanQuery({
        'page': page,
        'per_page': perPage,
        'status': status,
        'report_id': reportId,
        'sort_by': 'created_at',
        'sort_dir': 'desc',
      }),
    );
    return ApiPage.fromJson(json, ClaimModel.fromJson);
  }

  Future<ClaimModel> fetchClaim(int id) async {
    final json = await _apiClient.get('/claims/$id');
    return ClaimModel.fromJson(_dataObject(json));
  }

  Future<ClaimModel> submitClaim({
    required int reportId,
    required String proofText,
  }) async {
    final json = await _apiClient.post(
      '/claims',
      data: {'report_id': reportId, 'proof_text': proofText},
    );
    return ClaimModel.fromJson(_dataObject(json));
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
