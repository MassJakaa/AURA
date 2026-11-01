<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\VirtualLocationResource;
use App\Models\ActivityInstance;
use App\Services\Activity\ActivityEngine;
use App\Services\Character\CharacterService;
use App\Services\Life\LifeClockService;
use App\Services\Location\LocationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LifeController extends Controller
{
    public function __construct(
        protected CharacterService $characterService,
        protected ActivityEngine $activityEngine,
        protected LocationService $locationService,
        protected LifeClockService $clock
    ) {}

    public function status(Request $request): JsonResponse
    {
        $character = $this->characterService->getPrimaryCharacter();
        $currentActivity = $this->activityEngine->getCurrentActivity($character);
        $nextPlanned = $currentActivity ? $this->activityEngine->getNextPlannedActivity($character, $currentActivity) : null;
        $charTime = $this->clock->getCharacterTime($character);

        $cachedMood = \Illuminate\Support\Facades\Cache::get('aura_simulator_mood', 'peaceful');
        $mood = \App\Enums\CharacterMood::fromString($cachedMood);

        return response()->json([
            'success' => true,
            'message' => 'Virtual life status retrieved successfully',
            'data' => [
                'character' => [
                    'id' => $character->id,
                    'name' => $character->name,
                    'status' => $currentActivity ? $currentActivity->status->value : 'idle',
                ],
                'virtual_time' => [
                    'current_utc' => $this->clock->getCurrentUtcTime()->toIso8601String(),
                    'character_local' => $charTime->toIso8601String(),
                    'character_timezone' => $charTime->getTimezone()->getName(),
                ],
                'current_activity' => $currentActivity ? new ActivityResource($currentActivity) : null,
                'next_planned_activity' => $nextPlanned,
                'current_mood' => [
                    'value' => $mood->value,
                    'label' => $mood->label(),
                    'emoji' => $mood->emoji(),
                ],
                'available_moods' => array_map(fn($m) => [
                    'value' => $m->value,
                    'label' => $m->label(),
                    'emoji' => $m->emoji(),
                ], \App\Enums\CharacterMood::cases()),
            ],
            'meta' => [],
        ]);
    }

    public function today(Request $request): JsonResponse
    {
        $character = $this->characterService->getPrimaryCharacter();
        $charTime = $this->clock->getCharacterTime($character);
        $todayStart = $charTime->copy()->startOfDay()->setTimezone('UTC');
        $todayEnd = $charTime->copy()->endOfDay()->setTimezone('UTC');

        $activities = ActivityInstance::with(['activity', 'location'])
            ->where('character_id', $character->id)
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Today life schedule retrieved successfully',
            'data' => ActivityResource::collection($activities),
            'meta' => [
                'total_today' => $activities->count(),
            ],
        ]);
    }

    public function activities(Request $request): JsonResponse
    {
        $character = $this->characterService->getPrimaryCharacter();
        $activities = ActivityInstance::with(['activity', 'location'])
            ->where('character_id', $character->id)
            ->orderBy('start_time', 'desc')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Activity history retrieved successfully',
            'data' => ActivityResource::collection($activities),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    public function showActivity(string $id): JsonResponse
    {
        $activity = ActivityInstance::with(['activity', 'location', 'transitions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Activity detail retrieved successfully',
            'data' => new ActivityResource($activity),
            'meta' => [
                'transitions' => $activity->transitions,
            ],
        ]);
    }

    public function locations(): JsonResponse
    {
        $locations = $this->locationService->getAvailableLocations();

        return response()->json([
            'success' => true,
            'message' => 'Virtual locations retrieved successfully',
            'data' => VirtualLocationResource::collection($locations),
            'meta' => [
                'total' => $locations->count(),
            ],
        ]);
    }
}
