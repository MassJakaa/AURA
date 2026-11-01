<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Character extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'gender_presentation',
        'visual_style',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(CharacterVersion::class);
    }

    public function activeVersion(): HasOne
    {
        return $this->hasOne(CharacterVersion::class)->where('is_active', true);
    }

    public function personalityConfigs(): HasMany
    {
        return $this->hasMany(CharacterPersonalityConfig::class);
    }

    public function currentPersonality(): HasOne
    {
        return $this->hasOne(CharacterPersonalityConfig::class)->latestOfMany();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(CharacterAsset::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(LifeSchedule::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function activityInstances(): HasMany
    {
        return $this->hasMany(ActivityInstance::class);
    }

    public function moments(): HasMany
    {
        return $this->hasMany(Moment::class);
    }
}
