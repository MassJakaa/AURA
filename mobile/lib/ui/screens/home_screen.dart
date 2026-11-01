import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../state/life_provider.dart';
import '../../state/moments_provider.dart';
import '../widgets/life_status_card.dart';
import '../widgets/moment_card.dart';
import 'chat_screen.dart';
import 'companion_preferences_screen.dart';
import 'moments_history_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;

  final List<Widget> _tabs = [
    const _HomeFeedTab(),
    const MomentsHistoryScreen(),
    const ChatScreen(),
    const CompanionPreferencesScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: _tabs[_selectedIndex],
      bottomNavigationBar: Container(
        decoration: const BoxDecoration(
          color: AppColors.surfaceWarm,
          border: Border(top: BorderSide(color: AppColors.borderLight, width: 1)),
        ),
        child: BottomNavigationBar(
          currentIndex: _selectedIndex,
          onTap: (index) => setState(() => _selectedIndex = index),
          backgroundColor: AppColors.surfaceWarm,
          selectedItemColor: AppColors.mutedLavender,
          unselectedItemColor: AppColors.textTertiary,
          type: BottomNavigationBarType.fixed,
          elevation: 0,
          selectedFontSize: 12,
          unselectedFontSize: 12,
          items: const [
            BottomNavigationBarItem(
              icon: Icon(Icons.wb_sunny_outlined),
              activeIcon: Icon(Icons.wb_sunny),
              label: 'Kehidupan',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.photo_library_outlined),
              activeIcon: Icon(Icons.photo_library),
              label: 'Momen',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.chat_bubble_outline),
              activeIcon: Icon(Icons.chat_bubble),
              label: 'Percakapan',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.tune_outlined),
              activeIcon: Icon(Icons.tune),
              label: 'Preferensi',
            ),
          ],
        ),
      ),
    );
  }
}

class _HomeFeedTab extends StatelessWidget {
  const _HomeFeedTab({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final lifeProv = context.watch<LifeProvider>();
    final momentsProv = context.watch<MomentsProvider>();

    return Scaffold(
      appBar: AppBar(
        title: const Text('AURA'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_outlined, size: 20),
            onPressed: () {
              lifeProv.fetchLifeStatus();
              momentsProv.loadInitialMoments();
            },
          ),
        ],
      ),
      body: RefreshIndicator(
        color: AppColors.mutedLavender,
        onRefresh: () async {
          await lifeProv.fetchLifeStatus();
          await momentsProv.loadInitialMoments();
        },
        child: ListView(
          padding: const EdgeInsets.only(bottom: 24),
          children: [
            // Life Status Card
            if (lifeProv.isLoading && lifeProv.lifeStatus == null)
              Container(
                margin: const EdgeInsets.all(16),
                height: 140,
                decoration: BoxDecoration(
                  color: AppColors.surfaceWarm,
                  borderRadius: BorderRadius.circular(24),
                ),
                child: const Center(
                  child: CircularProgressIndicator(color: AppColors.mutedLavender),
                ),
              )
            else if (lifeProv.lifeStatus != null)
              LifeStatusCard(status: lifeProv.lifeStatus!)
            else
              Container(
                margin: const EdgeInsets.all(16),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: AppColors.surfaceWarm,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Text('Menyambungkan ke AURA Life System...'),
              ),

            const SizedBox(height: 16),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 20),
              child: Text(
                'Momen Terbaru',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: AppColors.textPrimary,
                ),
              ),
            ),
            const SizedBox(height: 8),

            // Moments list highlight
            if (momentsProv.moments.isEmpty && !momentsProv.isLoading)
              Container(
                margin: const EdgeInsets.all(16),
                padding: const EdgeInsets.all(32),
                decoration: BoxDecoration(
                  color: AppColors.surfaceWarm,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: AppColors.borderLight),
                ),
                child: const Column(
                  children: [
                    Icon(Icons.photo_outlined, size: 40, color: AppColors.textTertiary),
                    SizedBox(height: 12),
                    Text(
                      'Belum ada momen yang dibagikan',
                      style: TextStyle(color: AppColors.textSecondary, fontSize: 14),
                    ),
                  ],
                ),
              )
            else
              ...momentsProv.moments.take(3).map(
                    (m) => MomentCard(
                      moment: m,
                      onLikeToggle: () => momentsProv.toggleLike(m),
                    ),
                  ),
          ],
        ),
      ),
    );
  }
}
