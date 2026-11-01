import 'package:flutter/material.dart';
import '../models/user_companion_preference_model.dart';
import '../services/aura_api_service.dart';

class CompanionPreferencesProvider extends ChangeNotifier {
  final AuraApiService apiService;

  UserCompanionPreferenceModel? _preferences;
  bool _isLoading = false;
  bool _isSaving = false;
  String? _statusMessage;

  UserCompanionPreferenceModel? get preferences => _preferences;
  bool get isLoading => _isLoading;
  bool get isSaving => _isSaving;
  String? get statusMessage => _statusMessage;

  CompanionPreferencesProvider(this.apiService) {
    loadPreferences();
  }

  Future<void> loadPreferences() async {
    _isLoading = true;
    notifyListeners();

    try {
      final pref = await apiService.getPreferences();
      _preferences = pref;
    } catch (e) {
      _statusMessage = 'Gagal memuat preferensi: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> updateProactiveMessaging(bool enabled) async {
    if (_preferences == null) return false;
    _preferences!.proactiveMessagingEnabled = enabled;
    notifyListeners();

    return await _saveUpdates({'proactive_messaging_enabled': enabled});
  }

  Future<bool> updatePhotoSharing(bool enabled) async {
    if (_preferences == null) return false;
    _preferences!.photoSharingEnabled = enabled;
    notifyListeners();

    return await _saveUpdates({'photo_sharing_enabled': enabled});
  }

  Future<bool> updateQuietHours(String start, String end) async {
    if (_preferences == null) return false;
    _preferences!.quietHoursStart = start;
    _preferences!.quietHoursEnd = end;
    notifyListeners();

    return await _saveUpdates({
      'quiet_hours_start': start,
      'quiet_hours_end': end,
    });
  }

  Future<bool> updateLimits(int maxProactive, int maxPhoto) async {
    if (_preferences == null) return false;
    _preferences!.maxDailyProactiveMessages = maxProactive;
    _preferences!.maxDailyPhotoMoments = maxPhoto;
    notifyListeners();

    return await _saveUpdates({
      'max_daily_proactive_messages': maxProactive,
      'max_daily_photo_moments': maxPhoto,
    });
  }

  Future<bool> updateMemoryEnabled(bool enabled) async {
    if (_preferences == null) return false;
    _preferences!.memoryEnabled = enabled;
    notifyListeners();

    return await _saveUpdates({'memory_enabled': enabled});
  }

  Future<bool> _saveUpdates(Map<String, dynamic> data) async {
    _isSaving = true;
    notifyListeners();

    final success = await apiService.updatePreferences(data);
    _isSaving = false;
    _statusMessage = success ? 'Pengaturan disimpan' : 'Gagal menyimpan pengaturan';
    notifyListeners();
    return success;
  }
}
