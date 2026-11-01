<?php

namespace App\Services\Personalization;

use App\Enums\MomentType;
use App\Models\InitiativeLog;
use App\Models\Message;
use App\Models\Moment;
use App\Models\MomentDelivery;
use App\Models\User;
use App\Models\UserCompanionPreference;
use Carbon\Carbon;

class ProactiveMessagingPolicy
{
    /**
     * Evaluate if a moment can be proactively delivered to the user.
     *
     * @return array{can_deliver: bool, reason: string}
     */
    public function evaluateDelivery(User $user, Moment $moment): array
    {
        $pref = $user->companionPreference;
        if (!$pref) {
            return ['can_deliver' => false, 'reason' => 'User companion preference not configured'];
        }

        // 1. User opted out of proactive messaging
        if (!$pref->proactive_messaging_enabled) {
            return ['can_deliver' => false, 'reason' => 'Proactive messaging disabled by user'];
        }

        // 2. Photo moment disabled if moment requires photo
        if ($moment->moment_type === MomentType::PHOTO_MOMENT && !$pref->photo_sharing_enabled) {
            return ['can_deliver' => false, 'reason' => 'Photo sharing disabled by user'];
        }

        // 3. Quiet hours check
        if ($pref->isInQuietHours()) {
            return ['can_deliver' => false, 'reason' => 'Quiet hours active for user timezone'];
        }

        $now = Carbon::now('UTC');
        $todayStart = $now->copy()->startOfDay();

        // 4. Duplicate moment check
        $alreadyDelivered = MomentDelivery::where('user_id', $user->id)
            ->where('moment_id', $moment->id)
            ->exists();

        if ($alreadyDelivered) {
            return ['can_deliver' => false, 'reason' => 'Moment has already been delivered to this user'];
        }

        // 5. Daily proactive limit
        $dailyDeliveredCount = MomentDelivery::where('user_id', $user->id)
            ->where('created_at', '>=', $todayStart)
            ->where('status', '!=', 'failed')
            ->count();

        $maxDaily = $pref->max_daily_proactive_messages ?? 2;
        if ($dailyDeliveredCount >= $maxDaily) {
            return ['can_deliver' => false, 'reason' => "Daily proactive limit reached ({$dailyDeliveredCount}/{$maxDaily})"];
        }

        // 6. Daily photo moments limit
        if ($moment->moment_type === MomentType::PHOTO_MOMENT) {
            $dailyPhotoCount = MomentDelivery::where('user_id', $user->id)
                ->where('created_at', '>=', $todayStart)
                ->whereHas('moment', fn($q) => $q->where('moment_type', MomentType::PHOTO_MOMENT))
                ->count();

            $maxPhoto = $pref->max_daily_photo_moments ?? 1;
            if ($dailyPhotoCount >= $maxPhoto) {
                return ['can_deliver' => false, 'reason' => "Daily photo limit reached ({$dailyPhotoCount}/{$maxPhoto})"];
            }
        }

        // 7. Minimum interval check (e.g. at least 4 hours since last proactive delivery)
        $lastDelivery = MomentDelivery::where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        if ($lastDelivery && $lastDelivery->created_at->diffInMinutes($now) < 240) {
            return ['can_deliver' => false, 'reason' => 'Minimum interval between proactive messages not yet elapsed'];
        }

        // 8. Recent chat check (don't interrupt if user was chatting actively within last 15 minutes)
        $recentChat = Message::whereHas('conversation', fn($q) => $q->where('user_id', $user->id))
            ->where('sender_type', 'user')
            ->where('created_at', '>=', $now->copy()->subMinutes(15))
            ->exists();

        if ($recentChat) {
            return ['can_deliver' => false, 'reason' => 'User recently active in conversation'];
        }

        return ['can_deliver' => true, 'reason' => 'Eligible for proactive delivery'];
    }
}
