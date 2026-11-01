<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCompanionPreferenceResource;
use App\Models\UserCompanionPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanionPreferenceController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $pref = UserCompanionPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'communication_style' => 'warm_natural',
                'interaction_frequency' => 'normal',
                'photo_sharing_enabled' => true,
                'proactive_messaging_enabled' => true,
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '08:00',
                'max_daily_proactive_messages' => 2,
                'max_daily_photo_moments' => 1,
                'timezone' => 'Asia/Jakarta',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Companion preferences retrieved successfully',
            'data' => new UserCompanionPreferenceResource($pref),
            'meta' => [],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'communication_style' => ['nullable', 'string', 'in:warm_natural,calm_gentle,playful'],
            'interaction_frequency' => ['nullable', 'string', 'in:low,normal,frequent'],
            'preferred_topics' => ['nullable', 'array'],
            'avoided_topics' => ['nullable', 'array'],
            'photo_sharing_enabled' => ['nullable', 'boolean'],
            'proactive_messaging_enabled' => ['nullable', 'boolean'],
            'quiet_hours_start' => ['nullable', 'date_format:H:i'],
            'quiet_hours_end' => ['nullable', 'date_format:H:i'],
            'max_daily_proactive_messages' => ['nullable', 'integer', 'min:0', 'max:10'],
            'max_daily_photo_moments' => ['nullable', 'integer', 'min:0', 'max:5'],
            'timezone' => ['nullable', 'timezone'],
            'memory_enabled' => ['nullable', 'boolean'],
            'notifications_enabled' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pref = UserCompanionPreference::firstOrCreate(['user_id' => $user->id]);
        $pref->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Companion preferences updated successfully',
            'data' => new UserCompanionPreferenceResource($pref),
            'meta' => [],
        ]);
    }
}
