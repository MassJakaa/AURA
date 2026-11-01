<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityTransition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'activity_instance_id',
        'from_status',
        'to_status',
        'reason',
        'transitioned_at',
        'metadata',
    ];

    protected $casts = [
        'from_status' => ActivityStatus::class,
        'to_status' => ActivityStatus::class,
        'transitioned_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(ActivityInstance::class, 'activity_instance_id');
    }
}
