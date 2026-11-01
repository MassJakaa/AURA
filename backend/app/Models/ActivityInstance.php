<?php

namespace App\Models;

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityInstance extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'activity_id',
        'character_id',
        'location_id',
        'name',
        'category',
        'description',
        'start_time',
        'expected_end_time',
        'actual_end_time',
        'status',
        'priority',
        'mood_presentation',
        'is_shareable',
        'recurrence_rule',
        'idempotency_key',
        'metadata',
    ];

    protected $casts = [
        'category' => ActivityCategory::class,
        'status' => ActivityStatus::class,
        'start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'priority' => 'integer',
        'is_shareable' => 'boolean',
        'metadata' => 'array',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(VirtualLocation::class, 'location_id');
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(ActivityTransition::class);
    }

    public function moments(): HasMany
    {
        return $this->hasMany(Moment::class);
    }
}
