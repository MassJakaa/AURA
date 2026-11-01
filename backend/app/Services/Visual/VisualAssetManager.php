<?php

namespace App\Services\Visual;

use App\Enums\AssetApprovalStatus;
use App\Models\Activity;
use App\Models\Character;
use App\Models\VirtualLocation;
use App\Models\VisualAsset;

class VisualAssetManager
{
    /**
     * Find a reusable or contextual visual asset for an activity and location.
     */
    public function findMatchingAsset(?Activity $activity, ?VirtualLocation $location): ?VisualAsset
    {
        // 1. Look for approved asset matching both activity and location
        if ($activity && $location) {
            $asset = VisualAsset::where('activity_id', $activity->id)
                ->where('location_id', $location->id)
                ->where('approval_status', AssetApprovalStatus::APPROVED)
                ->latest()
                ->first();

            if ($asset) {
                return $asset;
            }
        }

        // 2. Look for approved asset matching activity
        if ($activity) {
            $asset = VisualAsset::where('activity_id', $activity->id)
                ->where('approval_status', AssetApprovalStatus::APPROVED)
                ->latest()
                ->first();

            if ($asset) {
                return $asset;
            }
        }

        // 3. Look for approved asset matching location (environment)
        if ($location) {
            $asset = VisualAsset::where('location_id', $location->id)
                ->where('approval_status', AssetApprovalStatus::APPROVED)
                ->latest()
                ->first();

            if ($asset) {
                return $asset;
            }
        }

        return null;
    }

    /**
     * Get placeholder asset for character pending master sheet approval.
     */
    public function getPlaceholderAsset(Character $character): ?VisualAsset
    {
        return VisualAsset::where('character_id', $character->id)
            ->where('asset_type', 'placeholder')
            ->first();
    }
}
