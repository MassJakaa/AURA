import 'dart:async';
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

  Timer? _readTimer;
  final List<String> _pendingUserMessages = [];
  bool _isProcessing = false;

  List<ChatMessageModel> get messages => _messages;
  bool get isLoading => _isLoading;
  bool get isSending => _isSending;
  bool get isTyping => _isTyping;
  String? get errorMessage => _errorMessage;

  ChatProvider(this.apiService) {
    initConversation();
  }

  @override
  void dispose() {
    _readTimer?.cancel();
    super.dispose();
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

  void sendMessage(String text, {String? referencingMomentId}) {
    if (text.trim().isEmpty || _conversationId == null) return;

    // 1. Optimistic immediate user bubble (user can send multiple chats)
    final tempUserMsg = ChatMessageModel(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      conversationId: _conversationId!,
      senderType: 'user',
      messageType: 'text',
      content: text.trim(),
      isRead: true,
      createdAt: DateTime.now(),
    );
    _messages.add(tempUserMsg);
    _pendingUserMessages.add(text.trim());
    notifyListeners();

    // 2. Reading delay window (3.2 seconds): resets if user sends another message
    if (!_isProcessing) {
      _readTimer?.cancel();
      _readTimer = Timer(const Duration(milliseconds: 3200), () {
        _processPendingMessages(referencingMomentId: referencingMomentId);
      });
    }
  }

  Future<void> _processPendingMessages({String? referencingMomentId}) async {
    if (_isProcessing || _pendingUserMessages.isEmpty || _conversationId == null) return;

    _isProcessing = true;
    _readTimer = null;
    _isTyping = true;
    notifyListeners();

    final batch = List<String>.from(_pendingUserMessages);
    _pendingUserMessages.clear();
    final combinedText = batch.join("\n");

    try {
      final replies = await apiService.sendMessages(
        _conversationId!,
        combinedText,
        referencingMomentId: referencingMomentId,
      );

      for (int i = 0; i < replies.length; i++) {
        if (i > 0) {
          _isTyping = true;
          notifyListeners();
          final delayMs = (replies[i].content.length * 28).clamp(900, 2200);
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
      _isTyping = false;
      _isProcessing = false;
      notifyListeners();

      // If user typed more messages while AURA was replying, schedule next response cycle
      if (_pendingUserMessages.isNotEmpty) {
        _readTimer = Timer(const Duration(milliseconds: 2500), () {
          _processPendingMessages(referencingMomentId: referencingMomentId);
        });
      }
    }
  }
}
