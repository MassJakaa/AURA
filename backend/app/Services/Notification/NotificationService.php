<?php

namespace App\Services\Notification;

use App\Models\DeviceToken;
use App\Models\Moment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send push notification for a delivered moment.
     */
    public function sendMomentNotification(User $user, Moment $moment): bool
    {
        $tokens = DeviceToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('token');

        if ($tokens->isEmpty()) {
            return false;
        }

        $title = 'AURA';
        $body = match($moment->moment_type->value) {
            'photo_moment' => 'AURA baru saja membagikan sebuah momen foto 📷',
            default => 'AURA punya cerita kecil buat kamu ☕',
        };

        $payload = [
            'registration_ids' => $tokens->toArray(),
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'moment_delivery',
                'moment_id' => $moment->id,
                'moment_type' => $moment->moment_type->value,
            ],
        ];

        try {
            $fcmServerKey = config('services.fcm.server_key');
            if (!$fcmServerKey) {
                // Mock FCM dispatch in development
                Log::info("Mock FCM notification dispatched to user {$user->id}: {$body}");
                return true;
            }

            $response = Http::withHeaders([
                'Authorization' => "key={$fcmServerKey}",
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("FCM notification failed: {$e->getMessage()}", ['user_id' => $user->id]);
            return false;
        }
    }
}
