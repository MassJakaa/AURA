<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualLocation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'environment_asset_path',
        'allowed_activities',
        'visual_theme',
        'is_available',
        'metadata',
    ];

    protected $casts = [
        'allowed_activities' => 'array',
        'is_available' => 'boolean',
        'metadata' => 'array',
    ];

    public function activityInstances(): HasMany
    {
        return $this->hasMany(ActivityInstance::class, 'location_id');
    }

    public function scheduleRules(): HasMany
    {
        return $this->hasMany(LifeScheduleRule::class, 'location_id');
    }

    public function visualAssets(): HasMany
    {
        return $this->hasMany(VisualAsset::class, 'location_id');
    }
}
