<?php

namespace App\Models;

use App\Enums\VisualGenerationStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisualGenerationJob extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'correlation_id',
        'provider',
        'prompt_text',
        'negative_prompt',
        'parameters',
        'status',
        'attempts_count',
        'max_attempts',
        'cost_units',
        'storage_path',
        'result_url',
        'error_message',
    ];

    protected $casts = [
        'status' => VisualGenerationStatus::class,
        'parameters' => 'array',
        'attempts_count' => 'integer',
        'max_attempts' => 'integer',
        'cost_units' => 'float',
    ];

    public function attempts(): HasMany
    {
        return $this->hasMany(VisualGenerationAttempt::class, 'job_id');
    }

    public function moment(): HasOne
    {
        return $this->hasOne(Moment::class, 'visual_generation_job_id');
    }
}
