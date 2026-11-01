<?php

namespace App\Domain\Visual;

interface ImageGenerationProviderInterface
{
    /**
     * Generate an image from a prompt specification.
     *
     * @param array{prompt: string, negative_prompt?: string, parameters?: array, correlation_id?: string} $params
     * @return array{id: string, status: string, url?: string, storage_path?: string, cost_units: float}
     */
    public function generate(array $params): array;

    /**
     * Generate an image conditioned on a reference image (e.g. Master Character Reference).
     *
     * @param string $referencePath
     * @param array{prompt: string, negative_prompt?: string, parameters?: array, correlation_id?: string} $params
     * @return array{id: string, status: string, url?: string, storage_path?: string, cost_units: float}
     */
    public function generateFromReference(string $referencePath, array $params): array;

    /**
     * Retrieve asynchronous generation status.
     *
     * @return array{id: string, status: string, url?: string, error?: string}
     */
    public function getGenerationStatus(string $jobId): array;

    /**
     * Cancel generation if in progress.
     */
    public function cancelGeneration(string $jobId): bool;

    /**
     * Validate generation output dimensions, format, and moderation safety.
     */
    public function validateOutput(array $result): bool;
}
