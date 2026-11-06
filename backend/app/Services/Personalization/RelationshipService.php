<?php

namespace App\Services\Personalization;

use App\Enums\MessageType;
use App\Enums\RelationshipStage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Character;

class RelationshipService
{
    public function getOrCreateActiveConversation(User $user, Character $character): Conversation
    {
        $conversation = Conversation::where('user_id', $user->id)
            ->where('is_active', true)
            ->latest('last_message_at')
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id' => $user->id,
                'title' => 'Obrolan dengan AURA',
                'is_active' => true,
                'last_message_at' => now(),
                'metadata' => [
                    'relationship_stage' => RelationshipStage::FIRST_MEETING->value,
                    'user_name' => null,
                ],
            ]);

            // Create initial first-meeting greeting from AURA
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'companion',
                'sender_id' => $character->id,
                'message_type' => MessageType::TEXT,
                'content' => "Halo! Salam kenal ya... Aku AURA 🌿 Senang akhirnya bisa kenalan sama kamu. Btw boleh tau nama kamu siapa? hehe",
                'is_read' => true,
                'metadata' => [
                    'mood' => 'peaceful',
                    'stage' => RelationshipStage::FIRST_MEETING->value,
                ],
            ]);
        }

        return $conversation;
    }

    public function detectAndStoreUserName(Conversation $conversation, string $userText): ?string
    {
        $metadata = $conversation->metadata ?? [];
        if (!empty($metadata['user_name'])) {
            return $metadata['user_name'];
        }

        // Common Indonesian name declaration patterns
        $patterns = [
            '/(?:nama\s+aku|namaku|nama\s+saya)\s+(?:adalah\s+)?([A-Za-zÀ-ÿ]+(?:\s+[A-Za-zÀ-ÿ]+)?)/i',
            '/(?:panggil\s+aja|panggil\s+aku|bisa\s+panggil)\s+([A-Za-zÀ-ÿ]+)/i',
            '/^(?:aku|saya)\s+([A-Za-zÀ-ÿ]+)$/i',
            '/^(?:halo\s+aura[,\s]+)?(?:kenalin\s+)?(?:aku|saya)\s+([A-Za-zÀ-ÿ]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, trim($userText), $matches)) {
                $rawName = trim($matches[1]);
                $stopwords = ['lagi', 'mau', 'udah', 'sedang', 'bukan', 'cuma', 'cuman', 'juga', 'aja', 'saja', 'tuh', 'nih', 'banget', 'sih', 'kan', 'siapa'];
                if (!in_array(strtolower($rawName), $stopwords) && mb_strlen($rawName) >= 2 && mb_strlen($rawName) <= 25) {
                    $capitalized = ucwords(strtolower($rawName));
                    $metadata['user_name'] = $capitalized;
                    $conversation->metadata = $metadata;
                    $conversation->save();
                    return $capitalized;
                }
            }
        }

        return null;
    }

    public function evaluateRelationshipStage(Conversation $conversation): RelationshipStage
    {
        $metadata = $conversation->metadata ?? [];
        $currentStage = RelationshipStage::fromString($metadata['relationship_stage'] ?? 'first_meeting');
        $hasName = !empty($metadata['user_name']);

        $messageCount = $conversation->messages()->count();

        $newStage = $currentStage;

        if ($currentStage === RelationshipStage::FIRST_MEETING) {
            if ($hasName || $messageCount >= 4) {
                $newStage = RelationshipStage::GETTING_TO_KNOW;
            }
        } elseif ($currentStage === RelationshipStage::GETTING_TO_KNOW) {
            if ($messageCount >= 16) {
                $newStage = RelationshipStage::CLOSE_FRIEND;
            }
        }

        if ($newStage !== $currentStage) {
            $metadata['relationship_stage'] = $newStage->value;
            $conversation->metadata = $metadata;
            $conversation->save();
        }

        return $newStage;
    }

    public function resetConversation(User $user, Character $character): Conversation
    {
        Conversation::where('user_id', $user->id)->update(['is_active' => false]);

        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => 'Obrolan Baru dengan AURA',
            'is_active' => true,
            'last_message_at' => now(),
            'metadata' => [
                'relationship_stage' => RelationshipStage::FIRST_MEETING->value,
                'user_name' => null,
            ],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'companion',
            'sender_id' => $character->id,
            'message_type' => MessageType::TEXT,
            'content' => "Halo! Salam kenal ya... Aku AURA 🌿 Senang akhirnya bisa kenalan sama kamu. Btw boleh tau nama kamu siapa? hehe",
            'is_read' => true,
            'metadata' => [
                'mood' => 'peaceful',
                'stage' => RelationshipStage::FIRST_MEETING->value,
            ],
        ]);

        return $conversation;
    }
}
