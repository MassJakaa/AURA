class MomentModel {
  final String id;
  final String type;
  final String title;
  final String narrative;
  final String? caption;
  final String status;
  final String? visualUrl;
  final String? visualStatus;
  final String? activityName;
  final String? activityLocation;
  final DateTime createdAt;
  bool isLiked;

  MomentModel({
    required this.id,
    required this.type,
    required this.title,
    required this.narrative,
    this.caption,
    required this.status,
    this.visualUrl,
    this.visualStatus,
    this.activityName,
    this.activityLocation,
    required this.createdAt,
    this.isLiked = false,
  });

  factory MomentModel.fromJson(Map<String, dynamic> json) {
    final visual = json['visual'] as Map<String, dynamic>?;
    final activity = json['activity'] as Map<String, dynamic>?;

    return MomentModel(
      id: json['id'] as String,
      type: json['type'] as String? ?? 'activity_moment',
      title: json['title'] as String? ?? '',
      narrative: json['narrative'] as String? ?? '',
      caption: json['caption'] as String?,
      status: json['status'] as String? ?? 'ready',
      visualUrl: visual?['url'] as String?,
      visualStatus: visual?['status'] as String?,
      activityName: activity?['name'] as String?,
      activityLocation: activity?['location'] as String?,
      createdAt: DateTime.parse(json['created_at'] as String),
    );
  }
}
