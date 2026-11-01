<?php

namespace App\Domain\Ai;

interface LlmProviderInterface
{
    /**
     * Generate companion response using prompt context and user message.
     *
     * @param array{
     *   system_prompt: string,
     *   virtual_life_context: string,
     *   recent_shared_moments?: array,
     *   user_memories?: array,
     *   recent_conversation?: array,
     *   user_preferences?: array
     * } $context
     * @param string $userMessage
     * @return string
     */
    public function generateResponse(array $context, string $userMessage): string;

    /**
     * Generate narrative for a moment based on virtual activity and location.
     */
    public function generateMomentNarrative(string $activityName, string $locationName, string $category): array;
}
