class ApiConstants {
  static const String baseUrl = 'http://10.0.2.2:8000/api/v1'; // Default Android Emulator host
  static const String localBaseUrl = 'http://localhost:8000/api/v1';

  // Character
  static const String character = '/character';
  static const String characterAssets = '/character/assets';
  static const String characterAppearance = '/character/appearance';

  // Life System
  static const String lifeStatus = '/life/status';
  static const String lifeToday = '/life/today';
  static const String lifeActivities = '/life/activities';
  static const String lifeLocations = '/life/locations';

  // Moments
  static const String moments = '/moments';
  static String momentDetail(String id) => '/moments/$id';
  static String momentInteraction(String id) => '/moments/$id/interaction';
  static String momentVisual(String id) => '/moments/$id/visual';

  // Companion Preferences
  static const String companionPreferences = '/companion/preferences';

  // Chat / Messages
  static const String conversations = '/conversations';
  static String conversationMessages(String id) => '/conversations/$id/messages';
}
