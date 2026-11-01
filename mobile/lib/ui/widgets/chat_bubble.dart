import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/constants/app_colors.dart';
import '../../models/chat_message_model.dart';

class ChatBubble extends StatelessWidget {
  final ChatMessageModel message;
  final ValueChanged<ChatMessageModel>? onReply;

  const ChatBubble({Key? key, required this.message, this.onReply}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isUser = message.isFromUser;
    final isMoment = message.isMomentMessage;
    final replyTo = message.metadata?['reply_to'] as Map<String, dynamic>?;

    return GestureDetector(
      onDoubleTap: () => onReply?.call(message),
      onLongPress: () => onReply?.call(message),
      child: Align(
        alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
          constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.78),
          decoration: BoxDecoration(
            color: isUser
                ? AppColors.mutedLavender
                : (isMoment ? AppColors.surfaceElevated : AppColors.surfaceWarm),
            borderRadius: BorderRadius.only(
              topLeft: const Radius.circular(18),
              topRight: const Radius.circular(18),
              bottomLeft: Radius.circular(isUser ? 18 : 4),
              bottomRight: Radius.circular(isUser ? 4 : 18),
            ),
            border: isUser ? null : Border.all(color: AppColors.borderLight, width: 1),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.03),
                blurRadius: 8,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          clipBehavior: Clip.antiAlias,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // If moment visual is present
              if (isMoment && message.moment?['visual_url'] != null)
                CachedNetworkImage(
                  imageUrl: message.moment!['visual_url'] as String,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => Container(
                    height: 140,
                    color: AppColors.lavenderLight,
                    child: const Center(
                      child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.mutedLavender),
                    ),
                  ),
                  errorWidget: (context, url, error) => const SizedBox(),
                ),

              // Content
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Quoted message box if present
                    if (replyTo != null && replyTo['content'] != null) ...[
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        margin: const EdgeInsets.only(bottom: 8),
                        decoration: BoxDecoration(
                          color: isUser ? Colors.white.withOpacity(0.2) : Colors.black.withOpacity(0.05),
                          borderRadius: BorderRadius.circular(8),
                          border: Border(
                            left: BorderSide(
                              color: isUser ? Colors.white : AppColors.mutedLavender,
                              width: 3.5,
                            ),
                          ),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              replyTo['sender']?.toString() ?? 'AURA',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: isUser ? Colors.white : AppColors.mutedLavender,
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              replyTo['content']?.toString() ?? '',
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(
                                fontSize: 11.5,
                                color: isUser ? Colors.white.withOpacity(0.9) : AppColors.textSecondary,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                    if (isMoment && message.moment?['title'] != null) ...[
                    Row(
                      children: [
                        const Icon(Icons.auto_awesome, size: 13, color: AppColors.mutedLavender),
                        const SizedBox(width: 4),
                        Text(
                          message.moment!['title'] as String,
                          style: const TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: AppColors.lavenderDeep,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                  ],
                  Text(
                    message.content,
                    style: TextStyle(
                      fontSize: 14,
                      color: isUser ? Colors.white : AppColors.textPrimary,
                      height: 1.4,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Align(
                    alignment: Alignment.bottomRight,
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          DateFormat('HH:mm').format(message.createdAt.toLocal()),
                          style: TextStyle(
                            fontSize: 10,
                            color: isUser ? Colors.white70 : AppColors.textTertiary,
                          ),
                        ),
                        if (isUser) ...[
                          const SizedBox(width: 4),
                          Icon(
                            Icons.done_all_rounded,
                            size: 14,
                            color: message.isRead ? const Color(0xFF53BDEB) : Colors.white70,
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
