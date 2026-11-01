<?php

namespace App\Models;

use App\Enums\MessageType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'message_type',
        'content',
        'moment_id',
        'activity_instance_id',
        'visual_asset_id',
        'is_read',
        'metadata',
    ];

    protected $casts = [
        'message_type' => MessageType::class,
        'is_read' => 'boolean',
        'metadata' => 'array',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function moment(): BelongsTo
    {
        return $this->belongsTo(Moment::class);
    }

    public function activityInstance(): BelongsTo
    {
        return $this->belongsTo(ActivityInstance::class);
    }

    public function visualAsset(): BelongsTo
    {
        return $this->belongsTo(VisualAsset::class);
    }
}
