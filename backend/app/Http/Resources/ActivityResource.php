<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category->value,
            'description' => $this->description,
            'location' => $this->location ? [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'code' => $this->location->code,
                'visual_theme' => $this->location->visual_theme,
            ] : null,
            'start_time' => $this->start_time->toIso8601String(),
            'expected_end_time' => $this->expected_end_time->toIso8601String(),
            'actual_end_time' => $this->actual_end_time?->toIso8601String(),
            'status' => $this->status->value,
            'priority' => $this->priority,
            'mood_presentation' => $this->mood_presentation,
            'shareable_flag' => $this->is_shareable,
            'metadata' => $this->metadata,
        ];
    }
}
