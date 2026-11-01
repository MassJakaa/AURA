import 'package:flutter/material.dart';
import '../models/chat_message_model.dart';
import '../services/aura_api_service.dart';

class ChatProvider extends ChangeNotifier {
  final AuraApiService apiService;

  List<ChatMessageModel> _messages = [];
  String? _conversationId;
  bool _isLoading = false;
  bool _isSending = false;
  bool _isTyping = false;
  String? _errorMessage;

  List<ChatMessageModel> get messages => _messages;
  bool get isLoading => _isLoading;
  bool get isSending => _isSending;
  bool get isTyping => _isTyping;
  String? get errorMessage => _errorMessage;

  ChatProvider(this.apiService) {
    initConversation();
  }

  Future<void> initConversation() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final conversations = await apiService.getConversations();
      if (conversations.isNotEmpty) {
        _conversationId = conversations.first['id'] as String;
        final msgs = await apiService.getMessages(_conversationId!);
        _messages = msgs;
      }
    } catch (e) {
      _errorMessage = 'Gagal memuat percakapan: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> sendMessage(String text, {String? referencingMomentId}) async {
    if (text.trim().isEmpty || _conversationId == null) return;

    _isSending = true;
    // Optimistic user message addition
    final tempUserMsg = ChatMessageModel(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      conversationId: _conversationId!,
      senderType: 'user',
      messageType: 'text',
      content: text,
      isRead: true,
      createdAt: DateTime.now(),
    );
    _messages.add(tempUserMsg);
    notifyListeners();

    // Natural reading delay before AURA starts typing
    await Future.delayed(const Duration(milliseconds: 700));
    _isTyping = true;
    notifyListeners();

    try {
      final replies = await apiService.sendMessages(
        _conversationId!,
        text,
        referencingMomentId: referencingMomentId,
      );

      for (int i = 0; i < replies.length; i++) {
        if (i > 0) {
          _isTyping = true;
          notifyListeners();
          final delayMs = (replies[i].content.length * 28).clamp(900, 2000);
          await Future.delayed(Duration(milliseconds: delayMs));
        }

        _isTyping = false;
        _messages.add(replies[i]);
        notifyListeners();

        if (i < replies.length - 1) {
          await Future.delayed(const Duration(milliseconds: 400));
        }
      }
    } catch (e) {
      _errorMessage = 'Gagal mengirim pesan: $e';
    } finally {
      _isSending = false;
      _isTyping = false;
      notifyListeners();
    }
  }
}
