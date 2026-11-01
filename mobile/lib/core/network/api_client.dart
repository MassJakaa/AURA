import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../constants/api_constants.dart';

class ApiResponse<T> {
  final bool success;
  final String message;
  final T? data;
  final Map<String, dynamic>? meta;

  ApiResponse({
    required this.success,
    required this.message,
    this.data,
    this.meta,
  });
}

class ApiClient {
  late String _baseUrl;
  String? _authToken;

  ApiClient({String? baseUrl}) {
    if (baseUrl != null) {
      _baseUrl = baseUrl;
    } else if (kIsWeb) {
      _baseUrl = ApiConstants.localBaseUrl;
    } else if (defaultTargetPlatform == TargetPlatform.android) {
      // Connect to host PC on LAN so physical phone and emulator both work
      _baseUrl = 'http://192.168.1.39:8000/api/v1';
    } else {
      _baseUrl = ApiConstants.localBaseUrl;
    }
  }

  void setBaseUrl(String url) => _baseUrl = url;
  void setAuthToken(String token) => _authToken = token;

  Map<String, String> _buildHeaders() {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (_authToken != null) {
      headers['Authorization'] = 'Bearer $_authToken';
    }
    return headers;
  }

  Future<ApiResponse<dynamic>> get(String endpoint, {Map<String, String>? queryParams}) async {
    try {
      final uri = Uri.parse('$_baseUrl$endpoint').replace(queryParameters: queryParams);
      final response = await http.get(uri, headers: _buildHeaders());
      return _parseResponse(response);
    } catch (e) {
      return ApiResponse(success: false, message: 'Network request error: $e');
    }
  }

  Future<ApiResponse<dynamic>> post(String endpoint, {Map<String, dynamic>? body}) async {
    try {
      final uri = Uri.parse('$_baseUrl$endpoint');
      final response = await http.post(
        uri,
        headers: _buildHeaders(),
        body: body != null ? jsonEncode(body) : null,
      );
      return _parseResponse(response);
    } catch (e) {
      return ApiResponse(success: false, message: 'Network request error: $e');
    }
  }

  Future<ApiResponse<dynamic>> patch(String endpoint, {Map<String, dynamic>? body}) async {
    try {
      final uri = Uri.parse('$_baseUrl$endpoint');
      final response = await http.patch(
        uri,
        headers: _buildHeaders(),
        body: body != null ? jsonEncode(body) : null,
      );
      return _parseResponse(response);
    } catch (e) {
      return ApiResponse(success: false, message: 'Network request error: $e');
    }
  }

  ApiResponse<dynamic> _parseResponse(http.Response response) {
    try {
      final decoded = jsonDecode(response.body) as Map<String, dynamic>;
      return ApiResponse(
        success: decoded['success'] as bool? ?? (response.statusCode >= 200 && response.statusCode < 300),
        message: decoded['message'] as String? ?? 'Response received',
        data: decoded['data'],
        meta: decoded['meta'] as Map<String, dynamic>?,
      );
    } catch (e) {
      return ApiResponse(
        success: response.statusCode >= 200 && response.statusCode < 300,
        message: 'Failed to parse server response (${response.statusCode})',
      );
    }
  }
}
