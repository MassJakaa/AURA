<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterVersion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'character_id',
        'version_tag',
        'is_active',
        'release_notes',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(CharacterAsset::class, 'version_id');
    }
}
