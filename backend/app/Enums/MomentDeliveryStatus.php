<?php

namespace App\Enums;

enum MomentDeliveryStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case READ = 'read';
    case FAILED = 'failed';
    case SUPPRESSED = 'suppressed'; // Blocked by quiet hours, limit, etc.
}
