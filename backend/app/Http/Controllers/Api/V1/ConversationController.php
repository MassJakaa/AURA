<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MessageType;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Moment;
use App\Services\Activity\ActivityEngine;
use App\Services\Character\CharacterService;
use App\Services\Narrative\PromptContextBuilder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    public function __construct(
        protected CharacterService $characterService,
        protected ActivityEngine $activityEngine,
        protected PromptContextBuilder $promptBuilder
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $conversations = Conversation::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Conversations retrieved successfully',
            'data' => $conversations,
            'meta' => [],
        ]);
    }

    public function messages(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $conversation = Conversation::where('user_id', $user->id)->findOrFail($id);

        $messages = $conversation->messages()
            ->with(['moment.visualAsset'])
            ->orderBy('created_at', 'asc')
            ->paginate($request->integer('per_page', 30));

        return response()->json([
            'success' => true,
            'message' => 'Messages retrieved successfully',
            'data' => MessageResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function sendMessage(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $conversation = Conversation::where('user_id', $user->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'max:2000'],
            'referencing_moment_id' => ['nullable', 'uuid', 'exists:moments,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $character = $this->characterService->getPrimaryCharacter();
        $currentActivity = $this->activityEngine->getCurrentActivity($character);

        return DB::transaction(function () use ($request, $user, $conversation, $character, $currentActivity) {
            // 1. Save user message
            $userMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'user',
                'sender_id' => (string) $user->id,
                'message_type' => MessageType::TEXT,
                'content' => $request->string('content'),
                'moment_id' => $request->input('referencing_moment_id'),
                'is_read' => true,
            ]);

            // 2. Build prompt context with character identity, current activity, memory, recent moment
            $promptContext = $this->promptBuilder->build($character, $user, $conversation, $currentActivity);

            // 3. Companion reply generator (incorporates moment context if user replied to moment)
            $replyContent = $this->generateCompanionReply($userMessage->content, $promptContext, $request->input('referencing_moment_id'));

            $companionMessage = Message::create([
                'conversation_id' => $conversation->id,
                'sender_type' => 'companion',
                'sender_id' => $character->id,
                'message_type' => MessageType::TEXT,
                'content' => $replyContent,
                'activity_instance_id' => $currentActivity?->id,
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => Carbon::now('UTC')]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'user_message' => new MessageResource($userMessage),
                    'companion_reply' => new MessageResource($companionMessage),
                ],
                'meta' => [],
            ]);
        });
    }

    protected function generateCompanionReply(string $userText, array $context, ?string $momentId): string
    {
        // Deterministic contextual response based on conversation topic and current activity
        $lower = strtolower($userText);

        if ($momentId) {
            $moment = Moment::find($momentId);
            if ($moment) {
                return "Iya, tadi aku sempat kepikiran buat cerita itu ke kamu. Menurutmu bagian mana yang paling menarik?";
            }
        }

        if (str_contains($lower, 'lagi apa') || str_contains($lower, 'sedang apa')) {
            return "Aku lagi santai sejenak sambil menikmati waktu. Suasananya tenang banget hari ini. Kamu sendiri lagi sibuk apa?";
        }

        if (str_contains($lower, 'kopi') || str_contains($lower, 'teh')) {
            return "Aroma kopi hangat memang selalu bikin rileks ya. Kamu lebih suka kopi hitam atau yang pakai susu?";
        }

        if (str_contains($lower, 'buku') || str_contains($lower, 'baca')) {
            return "Membaca buku di dekat jendela selalu jadi caraku buat melepas penat. Kamu ada buku favorit yang sering dibaca ulang?";
        }

        return "Terima kasih sudah berbagi cerita sama aku. Mendengar kabarmu selalu bikin hariku terasa lebih menyenangkan 🌿";
    }
}
