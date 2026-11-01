class ActivityModel {
  final String id;
  final String name;
  final String category;
  final String? description;
  final String? locationName;
  final String? locationCode;
  final DateTime startTime;
  final DateTime expectedEndTime;
  final DateTime? actualEndTime;
  final String status;
  final int priority;
  final String? moodPresentation;
  final bool isShareable;
  final Map<String, dynamic>? metadata;

  ActivityModel({
    required this.id,
    required this.name,
    required this.category,
    this.description,
    this.locationName,
    this.locationCode,
    required this.startTime,
    required this.expectedEndTime,
    this.actualEndTime,
    required this.status,
    this.priority = 1,
    this.moodPresentation,
    this.isShareable = true,
    this.metadata,
  });

  factory ActivityModel.fromJson(Map<String, dynamic> json) {
    return ActivityModel(
      id: json['id'] as String,
      name: json['name'] as String,
      category: json['category'] as String? ?? 'daily_routine',
      description: json['description'] as String?,
      locationName: json['location']?['name'] as String?,
      locationCode: json['location']?['code'] as String?,
      startTime: DateTime.parse(json['start_time'] as String),
      expectedEndTime: DateTime.parse(json['expected_end_time'] as String),
      actualEndTime: json['actual_end_time'] != null
          ? DateTime.parse(json['actual_end_time'] as String)
          : null,
      status: json['status'] as String? ?? 'scheduled',
      priority: json['priority'] as int? ?? 1,
      moodPresentation: json['mood_presentation'] as String?,
      isShareable: json['shareable_flag'] as bool? ?? true,
      metadata: json['metadata'] as Map<String, dynamic>?,
    );
  }
}
