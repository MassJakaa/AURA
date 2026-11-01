import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../state/chat_provider.dart';
import '../widgets/chat_bubble.dart';

class ChatScreen extends StatefulWidget {
  const ChatScreen({Key? key}) : super(key: key);

  @override
  State<ChatScreen> createState() => _ChatScreenState();
}

class _ChatScreenState extends State<ChatScreen> {
  final TextEditingController _textController = TextEditingController();
  final ScrollController _scrollController = ScrollController();

  @override
  void dispose() {
    _textController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 250),
          curve: Curves.easeOut,
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final chatProv = context.watch<ChatProvider>();

    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 8,
              height: 8,
              decoration: const BoxDecoration(
                color: AppColors.statusOnline,
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: 8),
            const Text('AURA'),
          ],
        ),
      ),
      body: Column(
        children: [
          // Message Thread
          Expanded(
            child: chatProv.isLoading
                ? const Center(
                    child: CircularProgressIndicator(color: AppColors.mutedLavender),
                  )
                : chatProv.messages.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: const [
                            Icon(Icons.chat_bubble_outline, size: 40, color: AppColors.textTertiary),
                            SizedBox(height: 12),
                            Text(
                              'Mulai percakapan santai dengan AURA ✨',
                              style: TextStyle(color: AppColors.textSecondary, fontSize: 14),
                            ),
                          ],
                        ),
                      )
                    : ListView.builder(
                        controller: _scrollController,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        itemCount: chatProv.messages.length,
                        itemBuilder: (context, index) {
                          return ChatBubble(message: chatProv.messages[index]);
                        },
                      ),
          ),

          // Sending Indicator
          if (chatProv.isSending)
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 4),
              child: Row(
                children: const [
                  SizedBox(
                    width: 12,
                    height: 12,
                    child: CircularProgressIndicator(strokeWidth: 1.5, color: AppColors.mutedLavender),
                  ),
                  SizedBox(width: 8),
                  Text(
                    'AURA sedang merangkai balasan...',
                    style: TextStyle(fontSize: 12, color: AppColors.textTertiary),
                  ),
                ],
              ),
            ),

          // Input Bar
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            decoration: const BoxDecoration(
              color: AppColors.surfaceWarm,
              border: Border(top: BorderSide(color: AppColors.borderLight, width: 1)),
            ),
            child: SafeArea(
              child: Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: _textController,
                      textInputAction: TextInputAction.send,
                      onSubmitted: (val) {
                        if (val.trim().isNotEmpty) {
                          chatProv.sendMessage(val.trim());
                          _textController.clear();
                          _scrollToBottom();
                        }
                      },
                      decoration: InputDecoration(
                        hintText: 'Tulis pesan untuk AURA...',
                        hintStyle: const TextStyle(fontSize: 14, color: AppColors.textTertiary),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
                        fillColor: AppColors.creamBackground,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton(
                    icon: const Icon(Icons.send_rounded, color: AppColors.mutedLavender),
                    onPressed: () {
                      final text = _textController.text.trim();
                      if (text.isNotEmpty) {
                        chatProv.sendMessage(text);
                        _textController.clear();
                        _scrollToBottom();
                      }
                    },
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
