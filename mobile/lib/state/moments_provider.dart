import 'package:flutter/material.dart';
import '../models/moment_model.dart';
import '../services/aura_api_service.dart';

class MomentsProvider extends ChangeNotifier {
  final AuraApiService apiService;

  List<MomentModel> _moments = [];
  bool _isLoading = false;
  bool _isLoadingMore = false;
  int _currentPage = 1;
  bool _hasMore = true;
  String? _errorMessage;

  List<MomentModel> get moments => _moments;
  bool get isLoading => _isLoading;
  bool get isLoadingMore => _isLoadingMore;
  bool get hasMore => _hasMore;
  String? get errorMessage => _errorMessage;

  MomentsProvider(this.apiService) {
    loadInitialMoments();
  }

  Future<void> loadInitialMoments() async {
    _isLoading = true;
    _errorMessage = null;
    _currentPage = 1;
    notifyListeners();

    try {
      final items = await apiService.getMoments(page: 1, perPage: 10);
      _moments = items;
      _hasMore = items.length >= 10;
    } catch (e) {
      _errorMessage = 'Gagal memuat momen: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> loadMore() async {
    if (_isLoadingMore || !_hasMore) return;

    _isLoadingMore = true;
    notifyListeners();

    try {
      final nextPage = _currentPage + 1;
      final items = await apiService.getMoments(page: nextPage, perPage: 10);
      if (items.isNotEmpty) {
        _moments.addAll(items);
        _currentPage = nextPage;
        _hasMore = items.length >= 10;
      } else {
        _hasMore = false;
      }
    } catch (e) {
      // Keep existing list on pagination failure
    } finally {
      _isLoadingMore = false;
      notifyListeners();
    }
  }

  Future<void> toggleLike(MomentModel moment) async {
    moment.isLiked = !moment.isLiked;
    notifyListeners();

    await apiService.sendMomentInteraction(
      moment.id,
      moment.isLiked ? 'liked' : 'dismissed',
    );
  }

  Future<void> markViewed(MomentModel moment) async {
    await apiService.sendMomentInteraction(moment.id, 'viewed');
  }
}
