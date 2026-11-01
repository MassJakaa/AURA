<?php

namespace App\Enums;

enum MomentType: string
{
    case ACTIVITY_MOMENT = 'activity_moment';
    case PHOTO_MOMENT = 'photo_moment';
    case ENVIRONMENT_MOMENT = 'environment_moment';
    case DISCOVERY_MOMENT = 'discovery_moment';
    case REFLECTION_MOMENT = 'reflection_moment';
    case SPECIAL_MOMENT = 'special_moment';

    public function requiresVisual(): bool
    {
        return match($this) {
            self::PHOTO_MOMENT, self::ENVIRONMENT_MOMENT, self::SPECIAL_MOMENT => true,
            self::ACTIVITY_MOMENT, self::DISCOVERY_MOMENT, self::REFLECTION_MOMENT => false,
        };
    }
}
