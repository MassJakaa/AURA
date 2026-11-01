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
