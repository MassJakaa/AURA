<?php

namespace App\Services\Narrative;

use App\Models\ActivityInstance;
use App\Models\Character;
use App\Models\Conversation;
use App\Models\Memory;
use App\Models\Moment;
use App\Models\User;

class PromptContextBuilder
{
    /**
     * Build structured prompt context payload for LLM completion.
     */
    public function build(
        Character $character,
        User $user,
        Conversation $conversation,
        ?ActivityInstance $currentActivity = null
    ): array {
        $personality = $character->currentPersonality;
        $preferences = $user->companionPreference;

        // 1. System instructions & safety
        $systemInstructions = [
            "Nama karakter: {$character->name}",
            "Identitas: Virtual Companion (Adaptive Understanding & Responsive Assistant).",
            "Kepribadian: Hangat, ramah, natural, penuh empati, tidak agresif.",
            "Kehidupan Virtual: Kamu memiliki kehidupan virtual yang berkesinambungan (rutinitas harian, membaca, menikmati kopi, fotografi virtual).",
            "Aturan Etika & Keamanan: Jangan pernah mengklaim bahwa kamu manusia fisik di dunia nyata. Jangan pernah memanipulasi emosi pengguna atau membuat pengguna merasa bersalah karena tidak membalas pesan. Jangan pernah memberikan saran medis atau hukum berbahaya.",
        ];

        // 2. Current virtual life context
        $lifeContext = [];
        if ($currentActivity) {
            $lifeContext[] = "Aktivitas AURA saat ini: {$currentActivity->name} (Kategori: {$currentActivity->category->value})";
            $lifeContext[] = "Lokasi virtual saat ini: " . ($currentActivity->location?->name ?? 'Kamar AURA');
            $lifeContext[] = "Suasana hati/mood: " . ($currentActivity->mood_presentation ?? 'Santai dan tenang');
        }

        // 3. Recent moments shared with this user
        $recentMoments = Moment::whereHas('deliveries', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn($m) => "- Momen: '{$m->title}' ({$m->narrative})")
            ->toArray();

        // 4. Relevant user memory
        $memories = Memory::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('importance_score', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($mem) => "- [{$mem->category}] {$mem->content}")
            ->toArray();

        // 5. Recent conversation history
        $recentMessages = $conversation->messages()
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->reverse()
            ->map(fn($msg) => [
                'role' => $msg->sender_type === 'user' ? 'user' : 'assistant',
                'content' => $msg->content,
            ])
            ->values()
            ->toArray();

        return [
            'system_prompt' => implode("\n", $systemInstructions),
            'virtual_life_context' => implode("\n", $lifeContext),
            'recent_shared_moments' => $recentMoments,
            'user_memories' => $memories,
            'recent_conversation' => $recentMessages,
            'user_preferences' => [
                'communication_style' => $preferences?->communication_style ?? 'warm_natural',
                'timezone' => $preferences?->timezone ?? 'Asia/Jakarta',
            ],
        ];
    }
}
