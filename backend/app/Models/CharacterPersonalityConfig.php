<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterPersonalityConfig extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'character_id',
        'version_id',
        'base_tone',
        'empathy_level',
        'warmth',
        'verbosity',
        'allowed_topics',
        'forbidden_topics',
        'prompt_system_instruction',
        'metadata',
    ];

    protected $casts = [
        'empathy_level' => 'float',
        'warmth' => 'float',
        'allowed_topics' => 'array',
        'forbidden_topics' => 'array',
        'metadata' => 'array',
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(CharacterVersion::class, 'version_id');
    }
}
