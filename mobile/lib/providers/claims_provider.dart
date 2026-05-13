import 'package:flutter/foundation.dart';

import '../models/claim_model.dart';
import '../services/api_exception.dart';
import '../services/claim_service.dart';
import '../utils/view_state.dart';

class ClaimsProvider extends ChangeNotifier {
  ClaimsProvider(this._claimService);

  final ClaimService _claimService;

  final List<ClaimModel> _claims = [];
  ClaimModel? _selectedClaim;
  ViewState _state = ViewState.idle;
  bool _isLoadingMore = false;
  bool _isSubmitting = false;
  String? _errorMessage;
  String? _status;
  int _page = 1;
  bool _hasMore = true;

  List<ClaimModel> get claims => List.unmodifiable(_claims);
  ClaimModel? get selectedClaim => _selectedClaim;
  ViewState get state => _state;
  bool get isLoadingMore => _isLoadingMore;
  bool get isSubmitting => _isSubmitting;
  String? get errorMessage => _errorMessage;
  String? get status => _status;
  bool get hasMore => _hasMore;

  Future<void> loadInitial({String? status}) async {
    _status = _normalize(status);
    _page = 1;
    _hasMore = true;
    _state = ViewState.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      final page = await _claimService.fetchClaims(
        page: _page,
        status: _status,
      );
      _claims
        ..clear()
        ..addAll(page.items);
      _hasMore = page.hasMore;
      _state = _claims.isEmpty ? ViewState.empty : ViewState.success;
    } on ApiException catch (error) {
      _errorMessage = error.message;
      _state = ViewState.error;
    } catch (_) {
      _errorMessage = 'Klaim tidak dapat dimuat.';
      _state = ViewState.error;
    }
    notifyListeners();
  }

  Future<void> refresh() => loadInitial(status: _status);

  Future<void> loadMore() async {
    if (!_hasMore || _isLoadingMore || _state == ViewState.loading) return;
    _isLoadingMore = true;
    notifyListeners();

    try {
      final nextPage = _page + 1;
      final page = await _claimService.fetchClaims(
        page: nextPage,
        status: _status,
      );
      _page = nextPage;
      _claims.addAll(page.items);
      _hasMore = page.hasMore;
    } on ApiException catch (error) {
      _errorMessage = error.message;
    } catch (_) {
      _errorMessage = 'Klaim berikutnya tidak dapat dimuat.';
    }

    _isLoadingMore = false;
    notifyListeners();
  }

  Future<bool> submitClaim({
    required int reportId,
    required String proofText,
  }) async {
    _isSubmitting = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final claim = await _claimService.submitClaim(
        reportId: reportId,
        proofText: proofText,
      );
      _claims.insert(0, claim);
      _isSubmitting = false;
      notifyListeners();
      return true;
    } on ApiException catch (error) {
      _errorMessage = error.message;
      _isSubmitting = false;
      notifyListeners();
      return false;
    } catch (_) {
      _errorMessage = 'Klaim Anda tidak dapat dikirim.';
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
