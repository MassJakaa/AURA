<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisualGenerationAttempt extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'job_id',
        'attempt_number',
        'provider',
        'request_payload',
        'response_payload',
        'latency_ms',
        'status',
        'error_message',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'latency_ms' => 'integer',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(VisualGenerationJob::class, 'job_id');
    }
}
