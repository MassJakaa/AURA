<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MomentResource;
use App\Models\Moment;
use App\Models\MomentDelivery;
use App\Models\MomentInteraction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MomentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $deliveries = MomentDelivery::with(['moment.visualAsset', 'moment.activityInstance.location'])
            ->where('user_id', $user->id)
            ->latest('delivered_at')
            ->paginate($request->integer('per_page', 10));

        $moments = $deliveries->getCollection()->map(fn($d) => $d->moment)->filter();

        return response()->json([
            'success' => true,
            'message' => 'Moments retrieved successfully',
            'data' => MomentResource::collection($moments),
            'meta' => [
                'current_page' => $deliveries->currentPage(),
                'last_page' => $deliveries->lastPage(),
                'total' => $deliveries->total(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        // Check ownership/delivery
        $delivery = MomentDelivery::where('user_id', $user->id)
            ->where('moment_id', $id)
            ->first();

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Moment not found or unauthorized access',
            ], 404);
        }

        $moment = Moment::with(['visualAsset', 'activityInstance.location'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Moment detail retrieved successfully',
            'data' => new MomentResource($moment),
            'meta' => [
                'delivered_at' => $delivery->delivered_at?->toIso8601String(),
                'status' => $delivery->status->value,
            ],
        ]);
    }

    public function interact(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $delivery = MomentDelivery::where('user_id', $user->id)
            ->where('moment_id', $id)
            ->first();

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Moment delivery not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'interaction_type' => ['required', 'string', 'in:viewed,liked,replied,dismissed,saved'],
            'payload' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $interaction = MomentInteraction::create([
            'moment_delivery_id' => $delivery->id,
            'user_id' => $user->id,
            'interaction_type' => $request->string('interaction_type'),
            'payload' => $request->input('payload'),
        ]);

        if ($request->string('interaction_type') === 'viewed' && !$delivery->read_at) {
            $delivery->update(['read_at' => now('UTC')]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Interaction recorded successfully',
            'data' => $interaction,
            'meta' => [],
        ]);
    }

    public function visual(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $delivery = MomentDelivery::where('user_id', $user->id)
            ->where('moment_id', $id)
            ->first();

        if (!$delivery) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or moment not found',
            ], 404);
        }

        $moment = Moment::with('visualAsset')->findOrFail($id);
        $visual = $moment->visualAsset;

        return response()->json([
            'success' => true,
            'message' => 'Moment visual retrieved successfully',
            'data' => [
                'moment_id' => $moment->id,
                'visual_status' => $visual ? $visual->approval_status->value : 'none',
                'url' => $visual?->cdn_url ?? $visual?->file_path,
                'dimensions' => $visual?->dimensions ?? '1024x1024',
                'is_master_reference' => $visual?->master_reference_version !== null,
            ],
            'meta' => [],
        ]);
    }
}
