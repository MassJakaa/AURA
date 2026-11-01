import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/constants/app_colors.dart';
import '../../models/moment_model.dart';

class MomentCard extends StatelessWidget {
  final MomentModel moment;
  final VoidCallback? onLikeToggle;
  final VoidCallback? onTap;

  const MomentCard({
    Key? key,
    required this.moment,
    this.onLikeToggle,
    this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        decoration: BoxDecoration(
          color: AppColors.surfaceWarm,
          borderRadius: BorderRadius.circular(22),
          border: Border.all(color: AppColors.borderLight, width: 1),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Visual header if available
            if (moment.visualUrl != null)
              AspectRatio(
                aspectRatio: 16 / 9,
                child: CachedNetworkImage(
                  imageUrl: moment.visualUrl!,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => Container(
                    color: AppColors.lavenderLight,
                    child: const Center(
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: AppColors.mutedLavender,
                      ),
                    ),
                  ),
                  errorWidget: (context, url, error) => Container(
                    color: AppColors.lavenderLight,
                    child: const Center(
                      child: Icon(Icons.broken_image_outlined, color: AppColors.textTertiary),
                    ),
                  ),
                ),
              ),

            // Text and details
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          moment.title,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                            color: AppColors.textPrimary,
                          ),
                        ),
                      ),
                      Text(
                        DateFormat('d MMM, HH:mm').format(moment.createdAt.toLocal()),
                        style: const TextStyle(
                          fontSize: 11,
                          color: AppColors.textTertiary,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    moment.narrative,
                    style: const TextStyle(
                      fontSize: 13.5,
                      color: AppColors.textSecondary,
                      height: 1.45,
                    ),
                  ),
                  if (moment.caption != null) ...[
                    const SizedBox(height: 8),
                    Text(
                      moment.caption!,
                      style: const TextStyle(
                        fontSize: 12,
                        fontStyle: FontStyle.italic,
                        color: AppColors.mutedLavender,
                      ),
                    ),
                  ],
                  const SizedBox(height: 12),
                  // Bottom actions (Like & Activity tag)
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      if (moment.activityLocation != null)
                        Row(
                          children: [
                            const Icon(Icons.place_outlined, size: 14, color: AppColors.textTertiary),
                            const SizedBox(width: 4),
                            Text(
                              moment.activityLocation!,
                              style: const TextStyle(fontSize: 11.5, color: AppColors.textTertiary),
                            ),
                          ],
                        )
                      else
                        const SizedBox(),
                      IconButton(
                        icon: Icon(
                          moment.isLiked ? Icons.favorite : Icons.favorite_border,
                          color: moment.isLiked ? AppColors.duskyRose : AppColors.textTertiary,
                          size: 20,
                        ),
                        onPressed: onLikeToggle,
                        padding: EdgeInsets.zero,
                        constraints: const BoxConstraints(),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
