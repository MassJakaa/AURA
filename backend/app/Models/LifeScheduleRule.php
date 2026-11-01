<?php

namespace App\Models;

use App\Enums\ActivityCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifeScheduleRule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'life_schedule_id',
        'day_of_week',
        'start_time_of_day',
        'end_time_of_day',
        'activity_name',
        'activity_category',
        'location_id',
        'mood_presentation',
        'shareable_flag',
        'recurrence_rule',
        'metadata',
    ];

    protected $casts = [
        'activity_category' => ActivityCategory::class,
        'day_of_week' => 'integer',
        'shareable_flag' => 'boolean',
        'metadata' => 'array',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(LifeSchedule::class, 'life_schedule_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(VirtualLocation::class, 'location_id');
    }
}
