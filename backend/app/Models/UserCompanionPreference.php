<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCompanionPreference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'communication_style',
        'interaction_frequency',
        'preferred_topics',
        'avoided_topics',
        'photo_sharing_enabled',
        'proactive_messaging_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'max_daily_proactive_messages',
        'max_daily_photo_moments',
        'timezone',
        'memory_enabled',
        'notifications_enabled',
        'relationship_context',
        'metadata',
    ];

    protected $casts = [
        'preferred_topics' => 'array',
        'avoided_topics' => 'array',
        'photo_sharing_enabled' => 'boolean',
        'proactive_messaging_enabled' => 'boolean',
        'max_daily_proactive_messages' => 'integer',
        'max_daily_photo_moments' => 'integer',
        'memory_enabled' => 'boolean',
        'notifications_enabled' => 'boolean',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if given time is in quiet hours for user's timezone.
     */
    public function isInQuietHours(?\DateTimeInterface $dateTime = null): bool
    {
        $timezone = new \DateTimeZone($this->timezone ?? 'Asia/Jakarta');
        $now = $dateTime ? \Carbon\Carbon::instance($dateTime)->setTimezone($timezone) : \Carbon\Carbon::now($timezone);

        $startStr = $this->quiet_hours_start ?? '22:00';
        $endStr = $this->quiet_hours_end ?? '08:00';

        $start = \Carbon\Carbon::parse($startStr, $timezone);
        $end = \Carbon\Carbon::parse($endStr, $timezone);

        if ($start->greaterThan($end)) {
            // e.g. 22:00 to 08:00 overnight
            return $now->greaterThanOrEqualTo($start) || $now->lessThan($end);
        }

        // e.g. 13:00 to 15:00 same day
        return $now->greaterThanOrEqualTo($start) && $now->lessThan($end);
    }
}
