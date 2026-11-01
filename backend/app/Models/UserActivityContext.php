<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityContext extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'last_shared_activity_id',
        'last_shared_at',
        'conversation_context',
    ];

    protected $casts = [
        'last_shared_at' => 'datetime',
        'conversation_context' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastSharedActivity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'last_shared_activity_id');
    }
}
