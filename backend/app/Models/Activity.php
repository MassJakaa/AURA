<?php

namespace App\Models;

use App\Enums\ActivityCategory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'character_id',
        'name',
        'category',
        'description',
        'default_location_id',
        'base_duration_minutes',
        'default_mood',
        'default_shareable',
        'recurrence_rule',
        'visual_prompt_template',
        'metadata',
    ];

    protected $casts = [
        'category' => ActivityCategory::class,
        'base_duration_minutes' => 'integer',
        'default_shareable' => 'boolean',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function defaultLocation(): BelongsTo
    {
        return $this->belongsTo(VirtualLocation::class, 'default_location_id');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(VirtualLocation::class, 'activity_location_assignments')
            ->withPivot('is_primary', 'notes')
            ->withTimestamps();
    }

    public function instances(): HasMany
    {
        return $this->hasMany(ActivityInstance::class);
    }

    public function visualAssets(): HasMany
    {
        return $this->hasMany(VisualAsset::class);
    }
}
