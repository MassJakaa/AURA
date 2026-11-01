import 'dart:async';
import 'package:flutter/material.dart';
import '../models/activity_model.dart';
import '../models/life_status_model.dart';
import '../services/aura_api_service.dart';

class LifeProvider extends ChangeNotifier {
  final AuraApiService apiService;

  LifeStatusModel? _lifeStatus;
  List<ActivityModel> _todayActivities = [];
  bool _isLoading = false;
  String? _errorMessage;
  Timer? _periodicTimer;

  LifeStatusModel? get lifeStatus => _lifeStatus;
  List<ActivityModel> get todayActivities => _todayActivities;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  LifeProvider(this.apiService) {
    fetchLifeStatus();
    // Poll virtual life status softly every 3 minutes
    _periodicTimer = Timer.periodic(const Duration(minutes: 3), (_) {
      fetchLifeStatus(silent: true);
    });
  }

  @override
  void dispose() {
    _periodicTimer?.cancel();
    super.dispose();
  }

  Future<void> fetchLifeStatus({bool silent = false}) async {
    if (!silent) {
      _isLoading = true;
      _errorMessage = null;
      notifyListeners();
    }

    try {
      final status = await apiService.getLifeStatus();
      if (status != null) {
        _lifeStatus = status;
      }
      final today = await apiService.getTodayActivities();
      _todayActivities = today;
    } catch (e) {
      if (!silent) {
        _errorMessage = 'Gagal memuat status kehidupan virtual: $e';
      }
    } finally {
      if (!silent) {
        _isLoading = false;
        notifyListeners();
      } else {
        notifyListeners();
      }
    }
  }
}
