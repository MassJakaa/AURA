import 'package:flutter/material.dart';
import '../models/chat_message_model.dart';
import '../services/aura_api_service.dart';

class ChatProvider extends ChangeNotifier {
  final AuraApiService apiService;

  List<ChatMessageModel> _messages = [];
  String? _conversationId;
  bool _isLoading = false;
  bool _isSending = false;
  String? _errorMessage;

  List<ChatMessageModel> get messages => _messages;
  bool get isLoading => _isLoading;
  bool get isSending => _isSending;
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

    try {
      final reply = await apiService.sendMessage(
        _conversationId!,
        text,
        referencingMomentId: referencingMomentId,
      );

      if (reply != null) {
        _messages.add(reply);
      }
    } catch (e) {
      _errorMessage = 'Gagal mengirim pesan: $e';
    } finally {
      _isSending = false;
      notifyListeners();
    }
  }
}
