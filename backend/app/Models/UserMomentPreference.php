<?php

namespace App\Models;

use App\Enums\MomentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMomentPreference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'moment_type',
        'is_enabled',
        'preference_score',
    ];

    protected $casts = [
        'moment_type' => MomentType::class,
        'is_enabled' => 'boolean',
        'preference_score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
