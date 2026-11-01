<?php

namespace App\Enums;

enum MomentStatus: string
{
    case DRAFT = 'draft';
    case EVALUATING = 'evaluating';
    case APPROVED = 'approved';
    case GENERATING = 'generating';
    case READY = 'ready';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

    public function isFinal(): bool
    {
        return in_array($this, [self::DELIVERED, self::FAILED, self::EXPIRED, self::CANCELLED]);
    }
}
