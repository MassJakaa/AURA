<?php

namespace Database\Seeders;

use App\Enums\AssetApprovalStatus;
use App\Models\Character;
use App\Models\CharacterAsset;
use App\Models\CharacterVersion;
use App\Models\VisualPrompt;
use Illuminate\Database\Seeder;

class ApproveMasterSheetSeeder extends Seeder
{
    public function run(): void
    {
        $char = Character::where('code', 'aura_main')->firstOrFail();

        // 1. Deactivate old placeholder version
        CharacterVersion::where('character_id', $char->id)->update(['is_active' => false]);

        // 2. Create official approved version
        $version = CharacterVersion::updateOrCreate(
            [
                'character_id' => $char->id,
                'version_tag' => 'v1.1.0-official',
            ],
            [
                'is_active' => true,
                'release_notes' => 'Official master character reference sheet approved. Full turnaround, expressions, poses, and color palette locked.',
                'metadata' => [
                    'approval_date' => '2026-11-01',
                    'status' => 'approved',
                    'palette' => [
                        'hair' => '#49352F',
                        'eyes' => '#A87842',
                        'cardigan' => '#F2E8D9',
                        'inner_top' => '#A99ABF',
                        'blush_skin' => '#DCA9A5',
                        'skirt_tights' => '#29272B',
                    ],
                    'expressions' => ['neutral', 'happy', 'curious', 'laughing', 'concerned', 'shy', 'thinking'],
                    'poses' => ['standing', 'sitting', 'waving', 'thinking'],
                    'turnaround' => ['front', 'three_quarters', 'side', 'back'],
                ],
            ]
        );

        // 3. Mark old assets as not master reference
        CharacterAsset::where('character_id', $char->id)->update(['is_master_reference' => false]);

        // 4. Register official master sheet asset
        CharacterAsset::updateOrCreate(
            [
                'character_id' => $char->id,
                'name' => 'AURA Official Master Sheet',
            ],
            [
                'version_id' => $version->id,
                'asset_type' => 'master_reference',
                'file_path' => 'assets/characters/aura_master_reference.jpg',
                'cdn_url' => '/assets/characters/aura_master_reference.jpg',
                'approval_status' => AssetApprovalStatus::APPROVED,
                'is_master_reference' => true,
                'metadata' => [
                    'verified' => true,
                    'source' => 'user_uploaded_sheet',
                    'dimensions' => '1024x576',
                    'turnaround_included' => true,
                    'expressions_included' => true,
                    'color_palette_included' => true,
                ],
            ]
        );

        // 5. Update character metadata
        $char->update([
            'metadata' => array_merge($char->metadata ?? [], [
                'master_sheet_approved' => true,
                'official_palette' => [
                    'hair' => '#49352F',
                    'eyes' => '#A87842',
                    'cardigan' => '#F2E8D9',
                    'inner_top' => '#A99ABF',
                    'skirt' => '#29272B',
                ],
                'eye_color' => 'Warm amber hazel (#A87842)',
                'hair_color' => 'Deep chestnut brown (#49352F)',
                'hair_style' => 'Shoulder-length loose wave with soft bangs',
                'outfit_default' => 'Oversized cream cardigan (#F2E8D9), muted lavender inner shirt (#A99ABF), dark charcoal skirt and tights (#29272B)',
            ]),
        ]);

        // 6. Update visual prompt
        VisualPrompt::updateOrCreate(
            ['prompt_code' => 'aura_soft_anime_standard'],
            [
                'version' => '1.1.0',
                'base_prompt' => 'masterpiece, best quality, 2D anime style, soft clean lineart, character AURA, female, shoulder-length wavy chestnut brown hair (#49352F), soft bangs framing face, warm amber hazel eyes (#A87842), gentle warm friendly expression, wearing oversized cozy cream knit cardigan (#F2E8D9), muted soft lavender top (#A99ABF), dark charcoal skirt with dark tights (#29272B), soft natural lighting, pastel warm cream background',
                'negative_prompt' => 'photorealistic, 3D, CGI, chibi, deformed, bad anatomy, bad hands, extra limbs, low quality, harsh shadows, aggressive, grotesque',
                'style_preset' => '2d_soft_anime',
                'parameters' => [
                    'steps' => 28,
                    'cfg_scale' => 6.5,
                    'aspect_ratio' => '4:5',
                    'reference_version' => 'v1.1.0-official',
                ],
            ]
        );
    }
}
