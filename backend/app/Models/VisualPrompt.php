<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisualPrompt extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'prompt_code',
        'version',
        'base_prompt',
        'negative_prompt',
        'style_preset',
        'parameters',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(VisualAsset::class);
    }
}
