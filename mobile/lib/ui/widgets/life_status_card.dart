import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/constants/app_colors.dart';
import '../../models/life_status_model.dart';

class LifeStatusCard extends StatelessWidget {
  final LifeStatusModel status;
  final VoidCallback? onRefresh;

  const LifeStatusCard({
    Key? key,
    required this.status,
    this.onRefresh,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final activity = status.currentActivity;
    final isSleeping = activity?.category == 'daily_routine' &&
        (activity?.name.toLowerCase().contains('sleep') ?? false);

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.surfaceWarm,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: AppColors.borderLight, width: 1.2),
        boxShadow: [
          BoxShadow(
            color: AppColors.mutedLavender.withOpacity(0.08),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header: Character name, status pill & local time
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    width: 10,
                    height: 10,
                    decoration: BoxDecoration(
                      color: isSleeping ? AppColors.statusSleeping : AppColors.statusOnline,
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    status.characterName,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppColors.textPrimary,
                      letterSpacing: 0.5,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: AppColors.lavenderLight,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      isSleeping ? 'Sedang Istirahat' : 'Sedang Beraktivitas',
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: AppColors.lavenderDeep,
                      ),
                    ),
                  ),
                ],
              ),
              Text(
                DateFormat('HH:mm').format(status.characterLocalTime),
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Main Activity info
          if (activity != null) ...[
            Text(
              activity.name,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w700,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 6),
            Row(
              children: [
                const Icon(Icons.location_on_outlined, size: 16, color: AppColors.mutedLavender),
                const SizedBox(width: 4),
                Text(
                  activity.locationName ?? 'Kamar AURA',
                  style: const TextStyle(
                    fontSize: 13,
                    color: AppColors.textSecondary,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                const SizedBox(width: 12),
                const Icon(Icons.schedule_outlined, size: 16, color: AppColors.textTertiary),
                const SizedBox(width: 4),
                Text(
                  '${DateFormat('HH:mm').format(activity.startTime.toLocal())} - ${DateFormat('HH:mm').format(activity.expectedEndTime.toLocal())}',
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textTertiary,
                  ),
                ),
              ],
            ),
            if (activity.description != null) ...[
              const SizedBox(height: 10),
              Text(
                activity.description!,
                style: const TextStyle(
                  fontSize: 13,
                  color: AppColors.textSecondary,
                  height: 1.4,
                ),
              ),
            ],
          ] else ...[
            const Text(
              'AURA sedang menikmati waktu luang santai.',
              style: TextStyle(
                fontSize: 15,
                color: AppColors.textSecondary,
              ),
            ),
          ],

          // Planned Next Activity footer
          if (status.nextPlannedActivity != null) ...[
            const Divider(color: AppColors.borderSubtle, height: 24),
            Row(
              children: [
                const Icon(Icons.next_plan_outlined, size: 15, color: AppColors.textTertiary),
                const SizedBox(width: 6),
                Expanded(
                  child: Text(
                    'Selanjutnya: ${status.nextPlannedActivity!['name'] ?? ''}',
                    style: const TextStyle(
                      fontSize: 12,
                      color: AppColors.textSecondary,
                      fontWeight: FontWeight.w500,
                    ),
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }
}
