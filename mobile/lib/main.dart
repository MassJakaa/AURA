import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'core/network/api_client.dart';
import 'core/theme/app_theme.dart';
import 'services/aura_api_service.dart';
import 'state/chat_provider.dart';
import 'state/companion_preferences_provider.dart';
import 'state/life_provider.dart';
import 'state/moments_provider.dart';
import 'ui/screens/home_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  final apiClient = ApiClient();
  final apiService = AuraApiService(apiClient);

  runApp(
    MultiProvider(
      providers: [
        Provider<ApiClient>.value(value: apiClient),
        Provider<AuraApiService>.value(value: apiService),
        ChangeNotifierProvider(create: (_) => LifeProvider(apiService)),
        ChangeNotifierProvider(create: (_) => MomentsProvider(apiService)),
        ChangeNotifierProvider(create: (_) => CompanionPreferencesProvider(apiService)),
        ChangeNotifierProvider(create: (_) => ChatProvider(apiService)),
      ],
      child: const AuraApp(),
    ),
  );
}

class AuraApp extends StatelessWidget {
  const AuraApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'AURA - AI Companion',
      theme: AppTheme.lightTheme,
      debugShowCheckedModeBanner: false,
      home: const HomeScreen(),
    );
  }
}
