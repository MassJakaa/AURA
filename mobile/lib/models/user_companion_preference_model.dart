class UserCompanionPreferenceModel {
  String communicationStyle;
  String interactionFrequency;
  List<String> preferredTopics;
  List<String> avoidedTopics;
  bool photoSharingEnabled;
  bool proactiveMessagingEnabled;
  String quietHoursStart;
  String quietHoursEnd;
  bool isCurrentlyInQuietHours;
  int maxDailyProactiveMessages;
  int maxDailyPhotoMoments;
  String timezone;
  bool memoryEnabled;
  bool notificationsEnabled;
  String relationshipContext;

  UserCompanionPreferenceModel({
    required this.communicationStyle,
    required this.interactionFrequency,
    required this.preferredTopics,
    required this.avoidedTopics,
    required this.photoSharingEnabled,
    required this.proactiveMessagingEnabled,
    required this.quietHoursStart,
    required this.quietHoursEnd,
    required this.isCurrentlyInQuietHours,
    required this.maxDailyProactiveMessages,
    required this.maxDailyPhotoMoments,
    required this.timezone,
    required this.memoryEnabled,
    required this.notificationsEnabled,
    required this.relationshipContext,
  });

  factory UserCompanionPreferenceModel.fromJson(Map<String, dynamic> json) {
    final quiet = json['quiet_hours'] as Map<String, dynamic>? ?? {};

    return UserCompanionPreferenceModel(
      communicationStyle: json['communication_style'] as String? ?? 'warm_natural',
      interactionFrequency: json['interaction_frequency'] as String? ?? 'normal',
      preferredTopics: (json['preferred_topics'] as List<dynamic>?)?.map((e) => e.toString()).toList() ?? [],
      avoidedTopics: (json['avoided_topics'] as List<dynamic>?)?.map((e) => e.toString()).toList() ?? [],
      photoSharingEnabled: json['photo_sharing_enabled'] as bool? ?? true,
      proactiveMessagingEnabled: json['proactive_messaging_enabled'] as bool? ?? true,
      quietHoursStart: quiet['start'] as String? ?? '22:00',
      quietHoursEnd: quiet['end'] as String? ?? '08:00',
      isCurrentlyInQuietHours: quiet['is_currently_active'] as bool? ?? false,
      maxDailyProactiveMessages: json['max_daily_proactive_messages'] as int? ?? 2,
      maxDailyPhotoMoments: json['max_daily_photo_moments'] as int? ?? 1,
      timezone: json['timezone'] as String? ?? 'Asia/Jakarta',
      memoryEnabled: json['memory_enabled'] as bool? ?? true,
      notificationsEnabled: json['notifications_enabled'] as bool? ?? true,
      relationshipContext: json['relationship_context'] as String? ?? 'friendly_companion',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'communication_style': communicationStyle,
      'interaction_frequency': interactionFrequency,
      'preferred_topics': preferredTopics,
      'avoided_topics': avoidedTopics,
      'photo_sharing_enabled': photoSharingEnabled,
      'proactive_messaging_enabled': proactiveMessagingEnabled,
      'quiet_hours_start': quietHoursStart,
      'quiet_hours_end': quietHoursEnd,
      'max_daily_proactive_messages': maxDailyProactiveMessages,
      'max_daily_photo_moments': maxDailyPhotoMoments,
      'timezone': timezone,
      'memory_enabled': memoryEnabled,
      'notifications_enabled': notificationsEnabled,
      'relationship_context': relationshipContext,
    };
  }
}
