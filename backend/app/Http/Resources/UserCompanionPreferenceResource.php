<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCompanionPreferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'communication_style' => $this->communication_style,
            'interaction_frequency' => $this->interaction_frequency,
            'preferred_topics' => $this->preferred_topics ?? [],
            'avoided_topics' => $this->avoided_topics ?? [],
            'photo_sharing_enabled' => $this->photo_sharing_enabled,
            'proactive_messaging_enabled' => $this->proactive_messaging_enabled,
            'quiet_hours' => [
                'start' => $this->quiet_hours_start,
                'end' => $this->quiet_hours_end,
                'is_currently_active' => $this->isInQuietHours(),
            ],
            'max_daily_proactive_messages' => $this->max_daily_proactive_messages,
            'max_daily_photo_moments' => $this->max_daily_photo_moments,
            'timezone' => $this->timezone,
            'memory_enabled' => $this->memory_enabled,
            'notifications_enabled' => $this->notifications_enabled,
            'relationship_context' => $this->relationship_context,
        ];
    }
}
