<?php

use App\Http\Controllers\Api\V1\CharacterController;
use App\Http\Controllers\Api\V1\CompanionPreferenceController;
use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\LifeController;
use App\Http\Controllers\Api\V1\MomentController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// Authentication endpoint for Sanctum tokens
Route::prefix('v1/auth')->group(function () {
    Route::post('/token', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('aura_mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token generated successfully',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ],
        ]);
    });
});

// Protected API v1 Routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Character
    Route::get('/character', [CharacterController::class, 'show']);
    Route::get('/character/assets', [CharacterController::class, 'assets']);
    Route::get('/character/appearance', [CharacterController::class, 'appearance']);

    // Life System
    Route::get('/life/status', [LifeController::class, 'status']);
    Route::get('/life/today', [LifeController::class, 'today']);
    Route::get('/life/activities', [LifeController::class, 'activities']);
    Route::get('/life/activities/{id}', [LifeController::class, 'showActivity']);
    Route::get('/life/locations', [LifeController::class, 'locations']);

    // Moments
    Route::get('/moments', [MomentController::class, 'index']);
    Route::get('/moments/{id}', [MomentController::class, 'show']);
    Route::post('/moments/{id}/interaction', [MomentController::class, 'interact']);
    Route::get('/moments/{id}/visual', [MomentController::class, 'visual']);

    // Companion Preferences
    Route::get('/companion/preferences', [CompanionPreferenceController::class, 'show']);
    Route::patch('/companion/preferences', [CompanionPreferenceController::class, 'update']);

    // Conversations / Chat
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{id}/messages', [ConversationController::class, 'sendMessage']);
});

// Dedicated Web Simulator endpoints (for direct browser preview without login)
Route::prefix('v1/simulator')->group(function () {
    Route::get('/status', [LifeController::class, 'status']);

    Route::get('/history', function (
        \App\Services\Character\CharacterService $charService,
        \App\Services\Personalization\RelationshipService $relService
    ) {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Demo User', 'password' => \Illuminate\Support\Facades\Hash::make('password123')]
        );
        $character = $charService->getPrimaryCharacter();
        $conversation = $relService->getOrCreateActiveConversation($user, $character);
        $stage = $relService->evaluateRelationshipStage($conversation);

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->sender_type === 'user' ? 'user' : 'aura',
                    'content' => $msg->content,
                    'time' => $msg->created_at->format('H:i'),
                    'is_read' => (bool) $msg->is_read,
                    'metadata' => $msg->metadata,
                ];
            });

        $cachedMood = \Illuminate\Support\Facades\Cache::get('aura_simulator_mood', 'peaceful');
        $mood = \App\Enums\CharacterMood::fromString($cachedMood);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'relationship_stage' => [
                'value' => $stage->value,
                'label' => $stage->label(),
                'emoji' => $stage->emoji(),
                'badge' => $stage->badge(),
            ],
            'user_name' => $conversation->metadata['user_name'] ?? null,
            'current_mood' => [
                'value' => $mood->value,
                'label' => $mood->label(),
                'emoji' => $mood->emoji(),
            ],
            'scenario' => \App\Services\Personalization\ScenarioService::hydrateScenario($conversation->metadata['scenario'] ?? null),
            'messages' => $messages,
        ]);
    });

    Route::get('/scenarios', function () {
        return response()->json([
            'success' => true,
            'scenarios' => \App\Services\Personalization\ScenarioService::getAllScenarios(),
        ]);
    });

    Route::get('/debug-llm', function () {
        $key = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
        $model = config('services.gemini.model') ?? env('GEMINI_MODEL', 'gemini-2.5-flash');

        $maskedKey = empty($key) ? 'NOT_SET' : (substr($key, 0, 8) . '...' . substr($key, -4) . ' (length: ' . strlen($key) . ')');
        $isPlaceholder = ($key === 'your_gemini_api_key_here');

        $testEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";

        $googleResponse = null;
        $error = null;
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(10)->post($testEndpoint, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => 'Halo']]]
                ]
            ]);
            $googleResponse = [
                'status' => $res->status(),
                'body' => $res->json() ?? $res->body(),
            ];
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return response()->json([
            'api_key' => $maskedKey,
            'model' => $model,
            'is_placeholder' => $isPlaceholder,
            'google_response' => $googleResponse,
            'exception' => $error,
        ]);
    });

    Route::post('/reset', function (
        Request $request,
        \App\Services\Character\CharacterService $charService,
        \App\Services\Personalization\RelationshipService $relService
    ) {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Demo User', 'password' => \Illuminate\Support\Facades\Hash::make('password123')]
        );
        $character = $charService->getPrimaryCharacter();
        $scenarioId = $request->input('scenario_id');
        $conversation = $relService->resetConversation($user, $character, $scenarioId);
        $stage = \App\Enums\RelationshipStage::FIRST_MEETING;

        \Illuminate\Support\Facades\Cache::put('aura_simulator_mood', 'peaceful', now()->addDays(1));
        $mood = \App\Enums\CharacterMood::PEACEFUL;

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->sender_type === 'user' ? 'user' : 'aura',
                    'content' => $msg->content,
                    'time' => $msg->created_at->format('H:i'),
                    'is_read' => (bool) $msg->is_read,
                    'metadata' => $msg->metadata,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Percakapan baru telah dimulai dari tahap perkenalan.',
            'conversation_id' => $conversation->id,
            'relationship_stage' => [
                'value' => $stage->value,
                'label' => $stage->label(),
                'emoji' => $stage->emoji(),
                'badge' => $stage->badge(),
            ],
            'user_name' => null,
            'current_mood' => [
                'value' => $mood->value,
                'label' => $mood->label(),
                'emoji' => $mood->emoji(),
            ],
            'scenario' => \App\Services\Personalization\ScenarioService::hydrateScenario($conversation->metadata['scenario'] ?? null),
            'messages' => $messages,
        ]);
    });

    Route::post('/chat', function (
        Request $request,
        \App\Services\Character\CharacterService $charService,
        \App\Services\Activity\ActivityEngine $actEngine,
        \App\Services\Personalization\RelationshipService $relService,
        \App\Domain\Ai\LlmProviderInterface $llm
    ) {
        $messagesInput = $request->input('messages');
        $rawList = [];
        if (is_array($messagesInput) && !empty($messagesInput)) {
            $rawList = array_values(array_filter(array_map('trim', $messagesInput)));
            $message = implode("\n", $rawList);
        } else {
            $message = trim($request->input('message', ''));
            if (!empty($message)) {
                $rawList = [$message];
            }
        }

        if (empty($message)) {
            return response()->json(['success' => false, 'reply' => 'Pesan tidak boleh kosong.']);
        }

        $user = \App\Models\User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Demo User', 'password' => \Illuminate\Support\Facades\Hash::make('password123')]
        );
        $character = $charService->getPrimaryCharacter();
        $activity = $actEngine->getCurrentActivity($character);

        $conversation = $relService->getOrCreateActiveConversation($user, $character);

        // Detect and save user name if mentioned in this message
        $detectedName = $relService->detectAndStoreUserName($conversation, $message);

        $userName = $conversation->metadata['user_name'] ?? null;
        if (!empty($userName)) {
            \App\Models\Memory::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'category' => 'identity',
                ],
                [
                    'content' => "Nama pengguna adalah {$userName}.",
                    'importance_score' => 1.0,
                    'is_active' => true,
                ]
            );
        }

        // Build recent conversation history directly from database BEFORE saving new message (to prevent duplication)
        $recentMessages = $conversation->messages()
            ->latest('created_at')
            ->take(35)
            ->get()
            ->reverse();

        $recentConversation = [];
        foreach ($recentMessages as $m) {
            $recentConversation[] = [
                'role' => $m->sender_type === 'user' ? 'user' : 'model',
                'content' => $m->content,
            ];
        }

        // Save incoming user message(s) to database
        $replyTo = $request->input('reply_to');
        foreach ($rawList as $userText) {
            \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'user',
                'sender_id' => (string) $user->id,
                'message_type' => \App\Enums\MessageType::TEXT,
                'content' => $userText,
                'is_read' => true,
                'metadata' => (is_array($replyTo) && !empty($replyTo['content'])) ? ['reply_to' => $replyTo] : null,
            ]);
        }

        // Re-evaluate relationship stage based on message count & name
        $stage = $relService->evaluateRelationshipStage($conversation);

        $cachedMood = \Illuminate\Support\Facades\Cache::get('aura_simulator_mood', 'peaceful');
        $currentMood = \App\Enums\CharacterMood::fromString($cachedMood);

        $currentTimeWib = now()->timezone('Asia/Jakarta')->format('H:i');
        $hour = (int) now()->timezone('Asia/Jakarta')->format('G');
        $isLateNight = ($hour >= 23 || $hour < 6);

        // Dynamic memories from database
        $userMemories = \App\Models\Memory::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('content')
            ->toArray();

        $charCount = mb_strlen(trim($message));
        $wordCount = str_word_count(strip_tags($message));
        $bubbleCount = count($rawList);

        $density = 'moderate';
        if ($charCount >= 85 || $wordCount >= 18 || ($bubbleCount >= 3 && $charCount >= 50)) {
            $density = 'long_detailed';
        } elseif ($charCount <= 25 && $wordCount <= 4 && $bubbleCount <= 1) {
            $density = 'short_brief';
        }

        $context = [
            'virtual_life_context' => $activity
                ? "AURA saat ini sedang: {$activity->name} di {$activity->location?->name}."
                : "AURA sedang santai di kamarnya.",
            'current_time_of_day' => "Pukul {$currentTimeWib} WIB" . ($isLateNight ? ' (Larut Malam / Dini Hari)' : ''),
            'current_mood' => $currentMood->value,
            'relationship_stage' => $stage->value,
            'user_name' => $conversation->metadata['user_name'] ?? null,
            'user_memories' => $userMemories,
            'recent_conversation' => $recentConversation,
            'chat_density' => $density,
        ];

        $scenario = \App\Services\Personalization\ScenarioService::hydrateScenario($conversation->metadata['scenario'] ?? null);
        $context['scenario'] = $scenario;
        if (!empty($scenario['prompt_context'])) {
            $context['scenario_context'] = $scenario['prompt_context'];
        }

        if (is_array($replyTo) && !empty($replyTo['content'])) {
            $sender = $replyTo['sender'] ?? 'AURA';
            $quoted = $replyTo['content'];
            $context['reply_context'] = "Pengguna sedang membalas secara spesifik pesan {$sender} berikut: \"{$quoted}\". Tanggapi balasan pengguna ini dengan mengaitkannya langsung ke topik kutipan tersebut secara alami.";
        }

        $reply = $llm->generateResponse($context, $message);

        // Check if mood dynamically shifted
        if (method_exists($llm, 'extractMoodTag')) {
            $extracted = $llm->extractMoodTag($reply);
            if (!empty($extracted['mood'])) {
                $newMood = \App\Enums\CharacterMood::fromString($extracted['mood']);
                \Illuminate\Support\Facades\Cache::put('aura_simulator_mood', $newMood->value, now()->addDays(1));
                $currentMood = $newMood;
            }
        }

        $bubbles = method_exists($llm, 'splitIntoBubbles')
            ? $llm->splitIntoBubbles($reply)
            : preg_split('/\|\|\||\n\s*\n/', $reply, -1, PREG_SPLIT_NO_EMPTY);
        $bubbles = array_values(array_map('trim', array_filter($bubbles)));
        if (empty($bubbles)) {
            $bubbles = [trim($reply)];
        }

        // Save each bubble reply from companion into database
        foreach ($bubbles as $idx => $bubbleText) {
            \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'companion',
                'sender_id' => (string) $character->id,
                'message_type' => \App\Enums\MessageType::TEXT,
                'content' => $bubbleText,
                'is_read' => true,
                'metadata' => [
                    'mood' => $currentMood->value,
                    'bubble_index' => $idx,
                    'stage' => $stage->value,
                ],
            ]);
        }

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'reply' => implode("\n\n", $bubbles),
            'replies' => $bubbles,
            'current_mood' => [
                'value' => $currentMood->value,
                'label' => $currentMood->label(),
                'emoji' => $currentMood->emoji(),
            ],
            'relationship_stage' => [
                'value' => $stage->value,
                'label' => $stage->label(),
                'emoji' => $stage->emoji(),
                'badge' => $stage->badge(),
            ],
            'user_name' => $conversation->metadata['user_name'] ?? null,
            'scenario' => \App\Services\Personalization\ScenarioService::hydrateScenario($conversation->metadata['scenario'] ?? null),
            'current_activity' => $activity?->name,
            'location' => $activity?->location?->name,
        ]);
    });

    Route::post('/proactive', function (
        Request $request,
        \App\Services\Character\CharacterService $charService,
        \App\Services\Activity\ActivityEngine $actEngine,
        \App\Services\Personalization\RelationshipService $relService,
        \App\Domain\Ai\LlmProviderInterface $llm
    ) {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Demo User', 'password' => \Illuminate\Support\Facades\Hash::make('password123')]
        );
        $character = $charService->getPrimaryCharacter();
        $activity = $actEngine->getCurrentActivity($character);

        $conversation = $relService->getOrCreateActiveConversation($user, $character);

        // Do not trigger proactive chat if user has never sent any message yet
        if ($conversation->messages()->where('sender_type', 'user')->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Menunggu pesan/sapaan pertama dari pengguna.',
            ]);
        }

        $stage = $relService->evaluateRelationshipStage($conversation);

        $cachedMood = \Illuminate\Support\Facades\Cache::get('aura_simulator_mood', 'happy');
        $currentMood = \App\Enums\CharacterMood::fromString($cachedMood);

        $recentMessages = $conversation->messages()
            ->latest('created_at')
            ->take(30)
            ->get()
            ->reverse();

        $recentConversation = [];
        foreach ($recentMessages as $m) {
            $recentConversation[] = [
                'role' => $m->sender_type === 'user' ? 'user' : 'model',
                'content' => $m->content,
            ];
        }

        $currentTimeWib = now()->timezone('Asia/Jakarta')->format('H:i');
        $hour = (int) now()->timezone('Asia/Jakarta')->format('G');
        $isLateNight = ($hour >= 23 || $hour < 6);

        $userMemories = \App\Models\Memory::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('content')
            ->toArray();

        $context = [
            'virtual_life_context' => $activity
                ? "AURA saat ini sedang: {$activity->name} di {$activity->location?->name}."
                : "AURA sedang santai di kamarnya.",
            'current_time_of_day' => "Pukul {$currentTimeWib} WIB" . ($isLateNight ? ' (Larut Malam / Dini Hari)' : ''),
            'current_mood' => $currentMood->value,
            'relationship_stage' => $stage->value,
            'user_name' => $conversation->metadata['user_name'] ?? null,
            'user_memories' => $userMemories,
            'recent_conversation' => $recentConversation,
            'is_proactive' => true,
        ];

        $scenario = \App\Services\Personalization\ScenarioService::hydrateScenario($conversation->metadata['scenario'] ?? null);
        $context['scenario'] = $scenario;
        if (!empty($scenario['prompt_context'])) {
            $context['scenario_context'] = $scenario['prompt_context'];
        }

        $reply = $llm->generateResponse($context, '');

        if (method_exists($llm, 'extractMoodTag')) {
            $extracted = $llm->extractMoodTag($reply);
            if (!empty($extracted['mood'])) {
                $newMood = \App\Enums\CharacterMood::fromString($extracted['mood']);
                \Illuminate\Support\Facades\Cache::put('aura_simulator_mood', $newMood->value, now()->addDays(1));
                $currentMood = $newMood;
            }
        }

        $bubbles = method_exists($llm, 'splitIntoBubbles')
            ? $llm->splitIntoBubbles($reply)
            : preg_split('/\|\|\||\n\s*\n/', $reply, -1, PREG_SPLIT_NO_EMPTY);
        $bubbles = array_values(array_map('trim', array_filter($bubbles)));
        if (empty($bubbles)) {
            $bubbles = [trim($reply)];
        }

        // Save proactive companion bubbles to database
        foreach ($bubbles as $idx => $bubbleText) {
            \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'companion',
                'sender_id' => (string) $character->id,
                'message_type' => \App\Enums\MessageType::TEXT,
                'content' => $bubbleText,
                'is_read' => true,
                'metadata' => [
                    'mood' => $currentMood->value,
                    'bubble_index' => $idx,
                    'stage' => $stage->value,
                    'is_proactive' => true,
                ],
            ]);
        }

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'reply' => implode("\n\n", $bubbles),
            'replies' => $bubbles,
            'current_mood' => [
                'value' => $currentMood->value,
                'label' => $currentMood->label(),
                'emoji' => $currentMood->emoji(),
            ],
            'relationship_stage' => [
                'value' => $stage->value,
                'label' => $stage->label(),
                'emoji' => $stage->emoji(),
                'badge' => $stage->badge(),
            ],
            'user_name' => $conversation->metadata['user_name'] ?? null,
            'scenario' => \App\Services\Personalization\ScenarioService::hydrateScenario($conversation->metadata['scenario'] ?? null),
            'current_activity' => $activity?->name,
            'location' => $activity?->location?->name,
        ]);
    });
});
