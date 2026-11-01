<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Memory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'category',
        'content',
        'importance_score',
        'source_message_id',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'importance_score' => 'float',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'source_message_id');
    }

    public function embedding(): HasOne
    {
        return $this->hasOne(MemoryEmbedding::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(MemoryFeedback::class);
    }
}
