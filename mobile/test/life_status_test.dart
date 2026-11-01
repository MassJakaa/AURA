import 'package:aura_mobile/core/theme/app_theme.dart';
import 'package:aura_mobile/models/activity_model.dart';
import 'package:aura_mobile/models/life_status_model.dart';
import 'package:aura_mobile/ui/widgets/life_status_card.dart';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('LifeStatusCard displays current activity name and location', (WidgetTester tester) async {
    final status = LifeStatusModel(
      characterName: 'AURA',
      characterStatus: 'active',
      currentUtcTime: DateTime.parse('2026-11-01T01:00:00Z'),
      characterLocalTime: DateTime.parse('2026-11-01T08:00:00Z'),
      characterTimezone: 'Asia/Jakarta',
      currentActivity: ActivityModel(
        id: 'act-1',
        name: 'Membaca Buku Novel',
        category: 'hobby',
        description: 'Membaca bab 4 di dekat jendela.',
        locationName: 'AURA Bedroom',
        startTime: DateTime.parse('2026-11-01T07:30:00Z'),
        expectedEndTime: DateTime.parse('2026-11-01T09:00:00Z'),
        status: 'active',
      ),
    );

    await tester.pumpWidget(
      MaterialApp(
        theme: AppTheme.lightTheme,
        home: Scaffold(
          body: LifeStatusCard(status: status),
        ),
      ),
    );

    expect(find.text('AURA'), findsOneWidget);
    expect(find.text('Membaca Buku Novel'), findsOneWidget);
    expect(find.text('AURA Bedroom'), findsOneWidget);
  });
}
