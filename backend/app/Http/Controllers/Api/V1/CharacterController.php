<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CharacterResource;
use App\Services\Character\CharacterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function __construct(
        protected CharacterService $characterService
    ) {}

    public function show(): JsonResponse
    {
        $character = $this->characterService->getPrimaryCharacter();

        return response()->json([
            'success' => true,
            'message' => 'Character profile retrieved successfully',
            'data' => new CharacterResource($character),
            'meta' => [],
        ]);
    }

    public function assets(): JsonResponse
    {
        $character = $this->characterService->getPrimaryCharacter();
        $assets = $this->characterService->getAssets($character);

        return response()->json([
            'success' => true,
            'message' => 'Character assets retrieved successfully',
            'data' => $assets,
            'meta' => [
                'total_assets' => $assets->count(),
            ],
        ]);
    }

    public function appearance(): JsonResponse
    {
        $character = $this->characterService->getPrimaryCharacter();
        $appearance = $this->characterService->getAppearance($character);

        return response()->json([
            'success' => true,
            'message' => 'Character appearance configuration retrieved successfully',
            'data' => $appearance,
            'meta' => [],
        ]);
    }
}
