<?php

namespace App\Services\Visual;

use App\Domain\Visual\ImageGenerationProviderInterface;
use App\Enums\AssetApprovalStatus;
use App\Enums\VisualGenerationStatus;
use App\Models\ActivityInstance;
use App\Models\Character;
use App\Models\VisualAsset;
use App\Models\VisualGenerationAttempt;
use App\Models\VisualGenerationJob;
use App\Models\VisualPrompt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VisualGenerationService
{
    public function __construct(
        protected ImageGenerationProviderInterface $provider,
        protected CostControlService $costControl,
        protected VisualAssetManager $assetManager
    ) {}

    /**
     * Request visual generation for an activity instance.
     */
    public function requestGenerationForActivity(ActivityInstance $instance, Character $character): ?VisualAsset
    {
        // 1. Check if a reusable/contextual asset already exists (Strategy A & B)
        $existing = $this->assetManager->findMatchingAsset($instance->activity, $instance->location);
        if ($existing) {
            return $existing;
        }

        // 2. Check cost & generation limits
        if (!$this->costControl->canGenerate()) {
            Log::info("Generation throttled by daily budget limit for activity: {$instance->name}");
            return null;
        }

        // 3. Check character master reference status
        $activeVersion = $character->activeVersion;
        $masterRef = $character->assets()
            ->where('is_master_reference', true)
            ->where('approval_status', AssetApprovalStatus::APPROVED)
            ->first();

        // 4. Retrieve prompt template
        $promptModel = VisualPrompt::where('style_preset', '2d_soft_anime')->latest()->first();
        $basePrompt = $promptModel ? $promptModel->base_prompt : '2D soft anime illustration, warm lighting';
        $promptText = "{$basePrompt}, character engaging in: {$instance->name}, location: " . ($instance->location?->name ?? 'cozy room');
        $negativePrompt = $promptModel?->negative_prompt ?? 'photorealistic, 3d, cgi, chibi';

        $correlationId = (string) Str::uuid();

        // 5. Create Job record
        $job = VisualGenerationJob::create([
            'correlation_id' => $correlationId,
            'provider' => 'mock',
            'prompt_text' => $promptText,
            'negative_prompt' => $negativePrompt,
            'status' => VisualGenerationStatus::PROCESSING,
            'attempts_count' => 1,
            'max_attempts' => $this->costControl->getMaxAttempts(),
        ]);

        $startTime = microtime(true);
        try {
            $params = [
                'prompt' => $promptText,
                'negative_prompt' => $negativePrompt,
                'correlation_id' => $correlationId,
            ];

            if ($masterRef && file_exists(storage_path("app/{$masterRef->file_path}"))) {
                $result = $this->provider->generateFromReference($masterRef->file_path, $params);
            } else {
                // If master character sheet is not yet approved or file missing, use provider generation
                $result = $this->provider->generate($params);
            }

            $latency = (int) round((microtime(true) - $startTime) * 1000);

            VisualGenerationAttempt::create([
                'job_id' => $job->id,
                'attempt_number' => 1,
                'provider' => 'mock',
                'request_payload' => $params,
                'response_payload' => $result,
                'latency_ms' => $latency,
                'status' => 'completed',
            ]);

            $job->update([
                'status' => VisualGenerationStatus::COMPLETED,
                'result_url' => $result['url'] ?? null,
                'storage_path' => $result['storage_path'] ?? null,
                'cost_units' => $result['cost_units'] ?? 0.02,
            ]);

            // Save as VisualAsset with metadata and approval status
            return VisualAsset::create([
                'character_id' => $character->id,
                'activity_id' => $instance->activity_id,
                'location_id' => $instance->location_id,
                'asset_type' => 'character_illustration',
                'asset_category' => 'newly_generated',
                'file_path' => $result['storage_path'] ?? "visuals/generated/{$correlationId}.webp",
                'cdn_url' => $result['url'] ?? null,
                'dimensions' => '1024x1024',
                'mime_type' => 'image/webp',
                'approval_status' => AssetApprovalStatus::APPROVED, // Mock approval for pipeline testing
                'master_reference_version' => $activeVersion?->version_tag ?? 'v1.0.0-placeholder',
                'visual_prompt_id' => $promptModel?->id,
                'metadata' => [
                    'job_id' => $job->id,
                    'activity_name' => $instance->name,
                ],
            ]);
        } catch (\Throwable $e) {
            $latency = (int) round((microtime(true) - $startTime) * 1000);

            VisualGenerationAttempt::create([
                'job_id' => $job->id,
                'attempt_number' => 1,
                'provider' => 'mock',
                'latency_ms' => $latency,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $job->update([
                'status' => VisualGenerationStatus::FAILED,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Visual generation failed: {$e->getMessage()}", ['job_id' => $job->id]);
            return null;
        }
    }
}
