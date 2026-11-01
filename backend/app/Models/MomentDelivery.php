<?php

namespace App\Models;

use App\Enums\MomentDeliveryStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MomentDelivery extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'moment_id',
        'user_id',
        'conversation_id',
        'message_id',
        'status',
        'delivered_at',
        'read_at',
        'error_message',
        'idempotency_key',
        'metadata',
    ];

    protected $casts = [
        'status' => MomentDeliveryStatus::class,
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function moment(): BelongsTo
    {
        return $this->belongsTo(Moment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(MomentInteraction::class);
    }
}
