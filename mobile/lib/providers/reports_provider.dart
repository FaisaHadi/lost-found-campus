import 'package:flutter/foundation.dart';

import '../models/category_model.dart';
import '../models/create_report_payload.dart';
import '../models/report_model.dart';
import '../services/api_exception.dart';
import '../services/report_service.dart';
import '../utils/view_state.dart';

class ReportsProvider extends ChangeNotifier {
  ReportsProvider(this._reportService);

  final ReportService _reportService;

  final List<ReportModel> _reports = [];
  final List<CategoryModel> _categories = [];
  ReportModel? _selectedReport;
  ViewState _state = ViewState.idle;
  ViewState _detailState = ViewState.idle;
  bool _isLoadingMore = false;
  bool _isSubmitting = false;
  String? _errorMessage;
  String? _detailError;
  String? _keyword;
  String? _reportType;
  int? _categoryId;
  String? _status;
  int _page = 1;
  bool _hasMore = true;

  List<ReportModel> get reports => List.unmodifiable(_reports);
  List<CategoryModel> get categories => List.unmodifiable(_categories);
  ReportModel? get selectedReport => _selectedReport;
  ViewState get state => _state;
  ViewState get detailState => _detailState;
  bool get isLoadingMore => _isLoadingMore;
  bool get isSubmitting => _isSubmitting;
  String? get errorMessage => _errorMessage;
  String? get detailError => _detailError;
  String? get keyword => _keyword;
  String? get reportType => _reportType;
  int? get categoryId => _categoryId;
  String? get status => _status;
  bool get hasMore => _hasMore;

  Future<void> loadCategories() async {
    try {
      final categories = await _reportService.fetchCategories();
      _categories
        ..clear()
        ..addAll(categories);
      notifyListeners();
    } on ApiException catch (error) {
      _errorMessage = error.message;
      notifyListeners();
    } catch (_) {
      _errorMessage = 'Kategori tidak dapat dimuat.';
      notifyListeners();
    }
  }

  Future<void> loadInitial({
    String? keyword,
    String? reportType,
    int? categoryId,
    String? status,
  }) async {
    _keyword = _normalize(keyword);
    _reportType = _normalize(reportType);
    _categoryId = categoryId;
    _status = _normalize(status);
    _page = 1;
    _hasMore = true;
    _state = ViewState.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      final page = await _reportService.fetchReports(
        page: _page,
        keyword: _keyword,
        reportType: _reportType,
        categoryId: _categoryId,
        status: _status,
      );
      _reports
        ..clear()
        ..addAll(page.items);
      _hasMore = page.hasMore;
      _state = _reports.isEmpty ? ViewState.empty : ViewState.success;
    } on ApiException catch (error) {
      _errorMessage = error.message;
      _state = ViewState.error;
    } catch (_) {
      _errorMessage = 'Laporan tidak dapat dimuat.';
      _state = ViewState.error;
    }
    notifyListeners();
  }

  Future<void> refresh() {
    return loadInitial(
      keyword: _keyword,
      reportType: _reportType,
      categoryId: _categoryId,
      status: _status,
    );
  }

  Future<void> loadMore() async {
    if (!_hasMore || _isLoadingMore || _state == ViewState.loading) return;

    _isLoadingMore = true;
    notifyListeners();

    try {
      final nextPage = _page + 1;
      final page = await _reportService.fetchReports(
        page: nextPage,
        keyword: _keyword,
        reportType: _reportType,
        categoryId: _categoryId,
        status: _status,
      );
      _page = nextPage;
      _reports.addAll(page.items);
      _hasMore = page.hasMore;
    } on ApiException catch (error) {
      _errorMessage = error.message;
    } catch (_) {
      _errorMessage = 'Laporan berikutnya tidak dapat dimuat.';
    }

    _isLoadingMore = false;
    notifyListeners();
  }

  Future<void> loadDetail(int reportId) async {
    _detailState = ViewState.loading;
    _detailError = null;
    notifyListeners();

    try {
      _selectedReport = await _reportService.fetchReport(reportId);
      _detailState = ViewState.success;
    } on ApiException catch (error) {
      _detailError = error.message;
      _detailState = ViewState.error;
    } catch (_) {
      _detailError = 'Laporan ini tidak dapat dimuat.';
      _detailState = ViewState.error;
    }
    notifyListeners();
  }

  Future<bool> createReport(CreateReportPayload payload) async {
    _isSubmitting = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final report = await _reportService.createReport(payload);
      _reports.insert(0, report);
      _isSubmitting = false;
      notifyListeners();
      return true;
    } on ApiException catch (error) {
      _errorMessage = error.message;
      _isSubmitting = false;
      notifyListeners();
      return false;
    } catch (_) {
      _errorMessage = 'Laporan tidak dapat dikirim.';
      _isSubmitting = false;
      notifyListeners();
      return false;
    }
  }

  String? _normalize(String? value) {
    final trimmed = value?.trim();
    if (trimmed == null || trimmed.isEmpty || trimmed == 'all') return null;
    return trimmed;
  }
}
