<?php

namespace App\Services\Moment;

use App\Enums\MomentStatus;
use App\Enums\MomentType;
use App\Models\ActivityInstance;
use App\Models\Character;
use App\Models\Moment;
use App\Services\Narrative\NarrativeService;
use App\Services\Visual\VisualGenerationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomentGenerator
{
    public function __construct(
        protected NarrativeService $narrativeService,
        protected VisualGenerationService $visualService
    ) {}

    /**
     * Generate a shareable moment from an active activity instance.
     */
    public function generateFromActivity(Character $character, ActivityInstance $instance): ?Moment
    {
        if (!$instance->is_shareable) {
            return null;
        }

        $idempotencyKey = "moment_act_{$instance->id}";

        // Return existing moment if already generated for this activity
        $existing = Moment::where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            return $existing;
        }

        // Determine moment type
        $momentType = match($instance->category->value) {
            'exploration', 'hobby' => MomentType::PHOTO_MOMENT,
            'relaxation' => MomentType::ENVIRONMENT_MOMENT,
            default => MomentType::ACTIVITY_MOMENT,
        };

        return DB::transaction(function () use ($character, $instance, $momentType, $idempotencyKey) {
            $narrativeData = $this->narrativeService->generateNarrative($character, $instance);

            // Visual asset handling
            $visualAsset = null;
            if ($momentType->requiresVisual()) {
                $visualAsset = $this->visualService->requestGenerationForActivity($instance, $character);
            }

            return Moment::create([
                'character_id' => $character->id,
                'activity_instance_id' => $instance->id,
                'visual_asset_id' => $visualAsset?->id,
                'moment_type' => $momentType,
                'title' => $narrativeData['title'],
                'narrative' => $narrativeData['narrative'],
                'caption' => $narrativeData['caption'],
                'visual_requirement' => $momentType->requiresVisual() ? 'required' : 'none',
                'priority' => 1,
                'status' => MomentStatus::READY,
                'scheduled_at' => Carbon::now('UTC'),
                'expires_at' => Carbon::now('UTC')->addHours(24),
                'idempotency_key' => $idempotencyKey,
                'metadata' => [
                    'activity_name' => $instance->name,
                    'location_name' => $instance->location?->name,
                ],
            ]);
        });
    }
}
