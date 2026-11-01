<?php

namespace App\Models;

use App\Enums\MomentStatus;
use App\Enums\MomentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Moment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'character_id',
        'activity_instance_id',
        'visual_asset_id',
        'visual_generation_job_id',
        'moment_type',
        'title',
        'narrative',
        'caption',
        'visual_requirement',
        'priority',
        'status',
        'scheduled_at',
        'expires_at',
        'idempotency_key',
        'metadata',
    ];

    protected $casts = [
        'moment_type' => MomentType::class,
        'status' => MomentStatus::class,
        'priority' => 'integer',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function activityInstance(): BelongsTo
    {
        return $this->belongsTo(ActivityInstance::class);
    }

    public function visualAsset(): BelongsTo
    {
        return $this->belongsTo(VisualAsset::class);
    }

    public function visualJob(): BelongsTo
    {
        return $this->belongsTo(VisualGenerationJob::class, 'visual_generation_job_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(MomentDelivery::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
