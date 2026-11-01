import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../state/moments_provider.dart';
import '../widgets/moment_card.dart';

class MomentsHistoryScreen extends StatefulWidget {
  const MomentsHistoryScreen({Key? key}) : super(key: key);

  @override
  State<MomentsHistoryScreen> createState() => _MomentsHistoryScreenState();
}

class _MomentsHistoryScreenState extends State<MomentsHistoryScreen> {
  final ScrollController _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.removeListener(_onScroll);
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      context.read<MomentsProvider>().loadMore();
    }
  }

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<MomentsProvider>();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Riwayat Momen AURA'),
      ),
      body: RefreshIndicator(
        color: AppColors.mutedLavender,
        onRefresh: () => provider.loadInitialMoments(),
        child: provider.isLoading && provider.moments.isEmpty
            ? const Center(
                child: CircularProgressIndicator(color: AppColors.mutedLavender),
              )
            : provider.moments.isEmpty
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: const [
                        Icon(Icons.photo_library_outlined, size: 48, color: AppColors.textTertiary),
                        SizedBox(height: 12),
                        Text(
                          'AURA belum membagikan momen cerita.',
                          style: TextStyle(color: AppColors.textSecondary, fontSize: 14),
                        ),
                      ],
                    ),
                  )
                : ListView.builder(
                    controller: _scrollController,
                    padding: const EdgeInsets.only(top: 8, bottom: 24),
                    itemCount: provider.moments.length + (provider.hasMore ? 1 : 0),
                    itemBuilder: (context, index) {
                      if (index == provider.moments.length) {
                        return const Padding(
                          padding: EdgeInsets.all(16),
                          child: Center(
                            child: SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: AppColors.mutedLavender,
                              ),
                            ),
                          ),
                        );
                      }

                      final moment = provider.moments[index];
                      return MomentCard(
                        moment: moment,
                        onLikeToggle: () => provider.toggleLike(moment),
                      );
                    },
                  ),
      ),
    );
  }
}
