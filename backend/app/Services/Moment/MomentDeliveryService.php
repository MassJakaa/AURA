<?php

namespace App\Services\Moment;

use App\Enums\MessageType;
use App\Enums\MomentDeliveryStatus;
use App\Models\Conversation;
use App\Models\InitiativeLog;
use App\Models\Message;
use App\Models\Moment;
use App\Models\MomentDelivery;
use App\Models\User;
use App\Services\Notification\NotificationService;
use App\Services\Personalization\ProactiveMessagingPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomentDeliveryService
{
    public function __construct(
        protected ProactiveMessagingPolicy $policy,
        protected NotificationService $notificationService
    ) {}

    /**
     * Deliver a moment to a specific user.
     */
    public function deliverToUser(User $user, Moment $moment): ?MomentDelivery
    {
        $evaluation = $this->policy->evaluateDelivery($user, $moment);

        if (!$evaluation['can_deliver']) {
            InitiativeLog::create([
                'user_id' => $user->id,
                'moment_id' => $moment->id,
                'trigger_type' => 'proactive_evaluation',
                'decision' => 'suppressed',
                'reason' => $evaluation['reason'],
                'channel' => 'in_app',
            ]);

            return null;
        }

        $idempotencyKey = "delivery_{$user->id}_{$moment->id}";

        return DB::transaction(function () use ($user, $moment, $idempotencyKey) {
            // Find or create primary active conversation
            $conversation = Conversation::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'is_active' => true,
                ],
                [
                    'title' => 'Percakapan Bersama AURA',
                    'last_message_at' => Carbon::now('UTC'),
                ]
            );

            // Map moment type to message type
            $messageType = match($moment->moment_type->value) {
                'photo_moment' => MessageType::PHOTO_MOMENT,
                'environment_moment' => MessageType::ENVIRONMENT_MOMENT,
                default => MessageType::ACTIVITY_MOMENT,
            };

            // Create message in chat thread
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'companion',
                'sender_id' => $moment->character_id,
                'message_type' => $messageType,
                'content' => $moment->narrative,
                'moment_id' => $moment->id,
                'activity_instance_id' => $moment->activity_instance_id,
                'visual_asset_id' => $moment->visual_asset_id,
                'is_read' => false,
                'metadata' => [
                    'title' => $moment->title,
                    'caption' => $moment->caption,
                ],
            ]);

            $conversation->update(['last_message_at' => Carbon::now('UTC')]);

            // Create delivery log
            $delivery = MomentDelivery::create([
                'moment_id' => $moment->id,
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
                'status' => MomentDeliveryStatus::DELIVERED,
                'delivered_at' => Carbon::now('UTC'),
                'idempotency_key' => $idempotencyKey,
            ]);

            InitiativeLog::create([
                'user_id' => $user->id,
                'moment_id' => $moment->id,
                'message_id' => $message->id,
                'trigger_type' => 'proactive_evaluation',
                'decision' => 'sent',
                'reason' => 'Delivered successfully into conversation',
                'channel' => 'in_app',
            ]);

            // Dispatch notification
            $this->notificationService->sendMomentNotification($user, $moment);

            return $delivery;
        });
    }
}
