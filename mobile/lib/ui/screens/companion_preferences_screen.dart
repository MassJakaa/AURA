import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../core/constants/app_colors.dart';
import '../../state/companion_preferences_provider.dart';

class CompanionPreferencesScreen extends StatelessWidget {
  const CompanionPreferencesScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final prov = context.watch<CompanionPreferencesProvider>();
    final pref = prov.preferences;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Pengaturan & Preferensi'),
      ),
      body: prov.isLoading && pref == null
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.mutedLavender),
            )
          : pref == null
              ? const Center(child: Text('Gagal memuat pengaturan.'))
              : ListView(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  children: [
                    // Section 1: Inisiatif & Pesan Proaktif
                    _buildSectionHeader('Inisiatif Percakapan AURA'),
                    Card(
                      child: Column(
                        children: [
                          SwitchListTile(
                            activeColor: AppColors.mutedLavender,
                            title: const Text(
                              'Pesan Proaktif',
                              style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14.5),
                            ),
                            subtitle: const Text(
                              'Izinkan AURA menyapa atau membagikan cerita tanpa harus diminta',
                              style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
                            ),
                            value: pref.proactiveMessagingEnabled,
                            onChanged: (val) => prov.updateProactiveMessaging(val),
                          ),
                          const Divider(height: 1),
                          SwitchListTile(
                            activeColor: AppColors.mutedLavender,
                            title: const Text(
                              'Berbagi Momen Foto',
                              style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14.5),
                            ),
                            subtitle: const Text(
                              'Izinkan AURA membagikan ilustrasi kegiatan virtualnya',
                              style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
                            ),
                            value: pref.photoSharingEnabled,
                            onChanged: (val) => prov.updatePhotoSharing(val),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 16),

                    // Section 2: Waktu Tenang (Quiet Hours)
                    _buildSectionHeader('Waktu Tenang (Quiet Hours)'),
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'AURA tidak akan mengirim pesan atau notifikasi proaktif pada rentang waktu ini.',
                              style: TextStyle(fontSize: 12.5, color: AppColors.textSecondary),
                            ),
                            const SizedBox(height: 16),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                _buildTimeBox('Mulai', pref.quietHoursStart),
                                const Icon(Icons.arrow_forward_rounded, color: AppColors.textTertiary, size: 20),
                                _buildTimeBox('Selesai', pref.quietHoursEnd),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),

                    const SizedBox(height: 16),

                    // Section 3: Batas Harian
                    _buildSectionHeader('Batas Frekuensi Harian'),
                    Card(
                      child: Column(
                        children: [
                          ListTile(
                            title: const Text(
                              'Maksimal Pesan Proaktif Per Hari',
                              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
                            ),
                            trailing: Text(
                              '${pref.maxDailyProactiveMessages} kali',
                              style: const TextStyle(
                                fontWeight: FontWeight.w700,
                                color: AppColors.mutedLavender,
                              ),
                            ),
                          ),
                          const Divider(height: 1),
                          ListTile(
                            title: const Text(
                              'Maksimal Momen Foto Per Hari',
                              style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
                            ),
                            trailing: Text(
                              '${pref.maxDailyPhotoMoments} kali',
                              style: const TextStyle(
                                fontWeight: FontWeight.w700,
                                color: AppColors.mutedLavender,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 16),

                    // Section 4: Privasi & Memori
                    _buildSectionHeader('Privasi & Memori Percakapan'),
                    Card(
                      child: SwitchListTile(
                        activeColor: AppColors.mutedLavender,
                        title: const Text(
                          'Memori Otomatis Per Pengguna',
                          style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14.5),
                        ),
                        subtitle: const Text(
                          'AURA mengingat topik preferensimu secara terpisah dan privat',
                          style: TextStyle(fontSize: 12, color: AppColors.textSecondary),
                        ),
                        value: pref.memoryEnabled,
                        onChanged: (val) => prov.updateMemoryEnabled(val),
                      ),
                    ),
                  ],
                ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 8),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 13,
          fontWeight: FontWeight.w700,
          color: AppColors.textSecondary,
          letterSpacing: 0.3,
        ),
      ),
    );
  }

  Widget _buildTimeBox(String label, String time) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
      decoration: BoxDecoration(
        color: AppColors.creamBackground,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.borderLight),
      ),
      child: Column(
        children: [
          Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textTertiary)),
          const SizedBox(height: 4),
          Text(
            time,
            style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
          ),
        ],
      ),
    );
  }
}
