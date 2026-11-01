import 'activity_model.dart';

class LifeStatusModel {
  final String characterName;
  final String characterStatus;
  final DateTime currentUtcTime;
  final DateTime characterLocalTime;
  final String characterTimezone;
  final ActivityModel? currentActivity;
  final Map<String, dynamic>? nextPlannedActivity;

  LifeStatusModel({
    required this.characterName,
    required this.characterStatus,
    required this.currentUtcTime,
    required this.characterLocalTime,
    required this.characterTimezone,
    this.currentActivity,
    this.nextPlannedActivity,
  });

  factory LifeStatusModel.fromJson(Map<String, dynamic> json) {
    final charData = json['character'] as Map<String, dynamic>? ?? {};
    final timeData = json['virtual_time'] as Map<String, dynamic>? ?? {};

    return LifeStatusModel(
      characterName: charData['name'] as String? ?? 'AURA',
      characterStatus: charData['status'] as String? ?? 'active',
      currentUtcTime: DateTime.parse(timeData['current_utc'] as String),
      characterLocalTime: DateTime.parse(timeData['character_local'] as String),
      characterTimezone: timeData['character_timezone'] as String? ?? 'Asia/Jakarta',
      currentActivity: json['current_activity'] != null
          ? ActivityModel.fromJson(json['current_activity'] as Map<String, dynamic>)
          : null,
      nextPlannedActivity: json['next_planned_activity'] as Map<String, dynamic>?,
    );
  }
}
