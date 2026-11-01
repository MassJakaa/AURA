<?php

namespace App\Enums;

enum AssetApprovalStatus: string
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DEPRECATED = 'deprecated';
}
