<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'gender_presentation' => $this->gender_presentation,
            'visual_style' => $this->visual_style,
            'description' => $this->description,
            'personality' => [
                'base_tone' => $this->currentPersonality?->base_tone ?? 'warm_friendly',
                'empathy_level' => $this->currentPersonality?->empathy_level ?? 0.88,
                'warmth' => $this->currentPersonality?->warmth ?? 0.92,
                'verbosity' => $this->currentPersonality?->verbosity ?? 'balanced',
            ],
            'active_version' => $this->activeVersion?->version_tag ?? 'v1.0.0-placeholder',
            'metadata' => $this->metadata,
        ];
    }
}
