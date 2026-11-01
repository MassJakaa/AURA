<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case SCHEDULED = 'scheduled';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';
    case SKIPPED = 'skipped';
    case CANCELLED = 'cancelled';

    /**
     * Check if transition from current state to target state is valid.
     */
    public function canTransitionTo(self $target): bool
    {
        return match($this) {
            self::SCHEDULED => in_array($target, [self::ACTIVE, self::SKIPPED, self::CANCELLED]),
            self::ACTIVE => in_array($target, [self::PAUSED, self::COMPLETED, self::CANCELLED]),
            self::PAUSED => in_array($target, [self::ACTIVE, self::COMPLETED, self::CANCELLED]),
            self::COMPLETED => false,
            self::SKIPPED => false,
            self::CANCELLED => false,
        };
    }
}
