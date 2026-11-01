<?php

namespace App\Services\Visual;

use App\Domain\Visual\ImageGenerationProviderInterface;
use Illuminate\Support\Str;

class MockImageGenerationProvider implements ImageGenerationProviderInterface
{
    public function generate(array $params): array
    {
        $id = $params['correlation_id'] ?? (string) Str::uuid();

        return [
            'id' => $id,
            'status' => 'completed',
            'url' => "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80",
            'storage_path' => "visuals/mock/{$id}.webp",
            'cost_units' => 0.02,
        ];
    }

    public function generateFromReference(string $referencePath, array $params): array
    {
        $id = $params['correlation_id'] ?? (string) Str::uuid();

        return [
            'id' => $id,
            'status' => 'completed',
            'url' => "https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=1000&q=80",
            'storage_path' => "visuals/mock_ref/{$id}.webp",
            'cost_units' => 0.04,
        ];
    }

    public function getGenerationStatus(string $jobId): array
    {
        return [
            'id' => $jobId,
            'status' => 'completed',
            'url' => "https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=1000&q=80",
        ];
    }

    public function cancelGeneration(string $jobId): bool
    {
        return true;
    }

    public function validateOutput(array $result): bool
    {
        return !empty($result['url']) || !empty($result['storage_path']);
    }
}
