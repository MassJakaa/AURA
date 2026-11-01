<?php

namespace App\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case ACTIVITY_MOMENT = 'activity_moment';
    case PHOTO_MOMENT = 'photo_moment';
    case ENVIRONMENT_MOMENT = 'environment_moment';
    case SYSTEM_EVENT = 'system_event';

    public function isMoment(): bool
    {
        return in_array($this, [self::ACTIVITY_MOMENT, self::PHOTO_MOMENT, self::ENVIRONMENT_MOMENT]);
    }
}
