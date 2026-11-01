<?php

namespace App\Services\Character;

use App\Models\Character;
use App\Models\CharacterAsset;
use App\Models\CharacterVersion;
use Illuminate\Database\Eloquent\Collection;

class CharacterService
{
    /**
     * Get the default primary character (AURA).
     */
    public function getPrimaryCharacter(): Character
    {
        return Character::with(['activeVersion', 'currentPersonality'])
            ->firstOrCreate(
                ['code' => 'aura_main'],
                [
                    'name' => 'AURA',
                    'gender_presentation' => 'female',
                    'visual_style' => '2d_soft_anime',
                    'description' => 'Adaptive Understanding & Responsive Assistant',
                ]
            );
    }

    /**
     * Get character assets filtered by approval status.
     */
    public function getAssets(Character $character): Collection
    {
        return $character->assets()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get appearance configuration and master sheet approval status.
     */
    public function getAppearance(Character $character): array
    {
        $activeVersion = $character->activeVersion;
        $masterRef = $character->assets()->where('is_master_reference', true)->first();

        return [
            'visual_style' => $character->visual_style,
            'hair_style' => $character->metadata['hair_style'] ?? 'Shoulder-length loose wave',
            'hair_color' => $character->metadata['hair_color'] ?? 'Warm dark chestnut brown',
            'eye_color' => $character->metadata['eye_color'] ?? 'Soft amethyst hazel',
            'master_sheet_approved' => $masterRef?->approval_status?->value === 'approved',
            'master_sheet_status' => $masterRef?->approval_status?->value ?? 'pending_approval',
            'placeholder_active' => $masterRef?->approval_status?->value !== 'approved',
            'active_version' => $activeVersion?->version_tag ?? 'v1.0.0-placeholder',
        ];
    }
}
