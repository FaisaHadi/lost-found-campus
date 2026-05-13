import 'package:flutter/foundation.dart';

import '../models/campus_notification.dart';
import '../services/api_exception.dart';
import '../services/notification_service.dart';
import '../utils/view_state.dart';

class NotificationsProvider extends ChangeNotifier {
  NotificationsProvider(this._notificationService);

  final NotificationService _notificationService;

  final List<CampusNotification> _notifications = [];
  ViewState _state = ViewState.idle;
  bool _isLoadingMore = false;
  String? _errorMessage;
  String? _status;
  int _page = 1;
  bool _hasMore = true;

  List<CampusNotification> get notifications =>
      List.unmodifiable(_notifications);
  ViewState get state => _state;
  bool get isLoadingMore => _isLoadingMore;
  String? get errorMessage => _errorMessage;
  String? get status => _status;
  bool get hasMore => _hasMore;
  int get unreadCount =>
      _notifications.where((notification) => notification.isUnread).length;

  Future<void> loadInitial({String? status}) async {
    _status = _normalize(status);
    _page = 1;
    _hasMore = true;
    _state = ViewState.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      final page = await _notificationService.fetchNotifications(
        page: _page,
        status: _status,
      );
      _notifications
        ..clear()
        ..addAll(page.items);
      _hasMore = page.hasMore;
      _state = _notifications.isEmpty ? ViewState.empty : ViewState.success;
    } on ApiException catch (error) {
      _errorMessage = error.message;
      _state = ViewState.error;
    } catch (_) {
      _errorMessage = 'Notifikasi tidak dapat dimuat.';
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
      final page = await _notificationService.fetchNotifications(
        page: nextPage,
        status: _status,
      );
      _page = nextPage;
      _notifications.addAll(page.items);
      _hasMore = page.hasMore;
    } on ApiException catch (error) {
      _errorMessage = error.message;
    } catch (_) {
      _errorMessage = 'Notifikasi berikutnya tidak dapat dimuat.';
    }

    _isLoadingMore = false;
    notifyListeners();
  }

  Future<void> markAsRead(CampusNotification notification) async {
    if (!notification.isUnread) return;

    try {
      final updated = await _notificationService.markAsRead(notification.id);
      final index = _notifications.indexWhere((item) => item.id == updated.id);
      if (index != -1) {
        _notifications[index] = updated;
        notifyListeners();
      }
    } on ApiException catch (error) {
      _errorMessage = error.message;
      notifyListeners();
    }
  }

  String? _normalize(String? value) {
    final trimmed = value?.trim();
    if (trimmed == null || trimmed.isEmpty || trimmed == 'all') return null;
    return trimmed;
  }
}
