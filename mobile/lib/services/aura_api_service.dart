import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/activity_model.dart';
import '../models/character_model.dart';
import '../models/chat_message_model.dart';
import '../models/life_status_model.dart';
import '../models/moment_model.dart';
import '../models/user_companion_preference_model.dart';

class AuraApiService {
  final ApiClient client;

  AuraApiService(this.client);

  // Authenticate
  Future<bool> login(String email, String password) async {
    final res = await client.post('/auth/token', body: {
      'email': email,
      'password': password,
    });
    if (res.success && res.data != null) {
      final token = res.data['token'] as String?;
      if (token != null) {
        client.setAuthToken(token);
        return true;
      }
    }
    return false;
  }

  // Character
  Future<CharacterModel?> getCharacter() async {
    final res = await client.get(ApiConstants.character);
    if (res.success && res.data != null) {
      return CharacterModel.fromJson(res.data as Map<String, dynamic>);
    }
    return null;
  }

  // Life Status
  Future<LifeStatusModel?> getLifeStatus() async {
    final res = await client.get(ApiConstants.lifeStatus);
    if (res.success && res.data != null) {
      return LifeStatusModel.fromJson(res.data as Map<String, dynamic>);
    }
    return null;
  }

  // Today Schedule
  Future<List<ActivityModel>> getTodayActivities() async {
    final res = await client.get(ApiConstants.lifeToday);
    if (res.success && res.data != null) {
      return (res.data as List<dynamic>)
          .map((item) => ActivityModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }
    return [];
  }

  // Moments List
  Future<List<MomentModel>> getMoments({int page = 1, int perPage = 10}) async {
    final res = await client.get(ApiConstants.moments, queryParams: {
      'page': page.toString(),
      'per_page': perPage.toString(),
    });
    if (res.success && res.data != null) {
      return (res.data as List<dynamic>)
          .map((item) => MomentModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }
    return [];
  }

  // Moment Interaction (like, view, reply)
  Future<bool> sendMomentInteraction(String momentId, String type) async {
    final res = await client.post(ApiConstants.momentInteraction(momentId), body: {
      'interaction_type': type,
    });
    return res.success;
  }

  // Companion Preferences
  Future<UserCompanionPreferenceModel?> getPreferences() async {
    final res = await client.get(ApiConstants.companionPreferences);
    if (res.success && res.data != null) {
      return UserCompanionPreferenceModel.fromJson(res.data as Map<String, dynamic>);
    }
    return null;
  }

  Future<bool> updatePreferences(Map<String, dynamic> updates) async {
    final res = await client.patch(ApiConstants.companionPreferences, body: updates);
    return res.success;
  }

  // Chat
  Future<List<Map<String, dynamic>>> getConversations() async {
    final res = await client.get(ApiConstants.conversations);
    if (res.success && res.data != null) {
      return (res.data as List<dynamic>).cast<Map<String, dynamic>>();
    }
    return [];
  }

  Future<List<ChatMessageModel>> getMessages(String conversationId) async {
    final res = await client.get(ApiConstants.conversationMessages(conversationId));
    if (res.success && res.data != null) {
      return (res.data as List<dynamic>)
          .map((item) => ChatMessageModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }
    return [];
  }

  Future<ChatMessageModel?> sendMessage(
    String conversationId,
    String content, {
    String? referencingMomentId,
  }) async {
    final res = await client.post(
      ApiConstants.conversationMessages(conversationId),
      body: {
        'content': content,
        if (referencingMomentId != null) 'referencing_moment_id': referencingMomentId,
      },
    );
    if (res.success && res.data != null && res.data['companion_reply'] != null) {
      return ChatMessageModel.fromJson(res.data['companion_reply'] as Map<String, dynamic>);
    }
    return null;
  }
}
