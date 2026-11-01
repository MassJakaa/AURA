class ChatMessageModel {
  final String id;
  final String conversationId;
  final String senderType; // 'user' or 'companion'
  final String messageType; // 'text', 'activity_moment', 'photo_moment', 'environment_moment'
  final String content;
  final Map<String, dynamic>? moment;
  final bool isRead;
  final DateTime createdAt;

  ChatMessageModel({
    required this.id,
    required this.conversationId,
    required this.senderType,
    required this.messageType,
    required this.content,
    this.moment,
    required this.isRead,
    required this.createdAt,
  });

  bool get isFromUser => senderType == 'user';
  bool get isMomentMessage =>
      messageType == 'activity_moment' ||
      messageType == 'photo_moment' ||
      messageType == 'environment_moment';

  factory ChatMessageModel.fromJson(Map<String, dynamic> json) {
    return ChatMessageModel(
      id: json['id'] as String,
      conversationId: json['conversation_id'] as String,
      senderType: json['sender_type'] as String,
      messageType: json['message_type'] as String? ?? 'text',
      content: json['content'] as String? ?? '',
      moment: json['moment'] as Map<String, dynamic>?,
      isRead: json['is_read'] as bool? ?? false,
      createdAt: DateTime.parse(json['created_at'] as String),
    );
  }
}
