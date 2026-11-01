import 'package:aura_mobile/core/theme/app_theme.dart';
import 'package:aura_mobile/models/moment_model.dart';
import 'package:aura_mobile/ui/widgets/moment_card.dart';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('MomentCard renders title and narrative correctly', (WidgetTester tester) async {
    final moment = MomentModel(
      id: 'test-1',
      type: 'photo_moment',
      title: 'Kopi Hangat Pagi Ini',
      narrative: 'Aromanya harum sekali dan membuat suasana kamar tenang.',
      caption: 'Menikmati secangkir kopi ☕',
      status: 'ready',
      createdAt: DateTime.parse('2026-11-01T08:00:00Z'),
      activityLocation: 'AURA Kitchen',
    );

    await tester.pumpWidget(
      MaterialApp(
        theme: AppTheme.lightTheme,
        home: Scaffold(
          body: MomentCard(moment: moment),
        ),
      ),
    );

    expect(find.text('Kopi Hangat Pagi Ini'), findsOneWidget);
    expect(find.text('Aromanya harum sekali dan membuat suasana kamar tenang.'), findsOneWidget);
    expect(find.text('Menikmati secangkir kopi ☕'), findsOneWidget);
    expect(find.text('AURA Kitchen'), findsOneWidget);
  });
}
