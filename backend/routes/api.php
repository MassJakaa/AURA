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
    Route::post('/chat', function (Request $request, \App\Services\Character\CharacterService $charService, \App\Services\Activity\ActivityEngine $actEngine, \App\Domain\Ai\LlmProviderInterface $llm) {
        $message = $request->input('message', '');
        if (empty($message)) {
            return response()->json(['success' => false, 'reply' => 'Pesan tidak boleh kosong.']);
        }

        $character = $charService->getPrimaryCharacter();
        $activity = $actEngine->getCurrentActivity($character);

        $context = [
            'virtual_life_context' => $activity
                ? "AURA saat ini sedang: {$activity->name} di {$activity->location?->name}. Mood: {$activity->mood_presentation}."
                : "AURA sedang santai di kamarnya.",
            'user_memories' => [
                "- Pengguna suka topik santai, buku, dan kopi.",
            ],
            'recent_conversation' => [],
        ];

        $reply = $llm->generateResponse($context, $message);
        $bubbles = method_exists($llm, 'splitIntoBubbles')
            ? $llm->splitIntoBubbles($reply)
            : preg_split('/\|\|\||\n\s*\n/', $reply, -1, PREG_SPLIT_NO_EMPTY);
        $bubbles = array_values(array_map('trim', array_filter($bubbles)));
        if (empty($bubbles)) {
            $bubbles = [trim($reply)];
        }

        return response()->json([
            'success' => true,
            'reply' => implode("\n\n", $bubbles),
            'replies' => $bubbles,
            'current_activity' => $activity?->name,
            'location' => $activity?->location?->name,
        ]);
    });
});
