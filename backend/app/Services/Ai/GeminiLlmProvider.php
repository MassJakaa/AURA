<?php

namespace App\Services\Ai;

use App\Domain\Ai\LlmProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiLlmProvider implements LlmProviderInterface
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY', '');
        $this->model = config('services.gemini.model') ?? env('GEMINI_MODEL', 'gemini-1.5-flash');
    }

    public function generateResponse(array $context, string $userMessage): string
    {
        if (empty($this->apiKey)) {
            Log::info("Gemini API key is not configured. Falling back to contextual rule response.");
            return $this->fallbackResponse($context, $userMessage);
        }

        try {
            $systemPrompt = $this->buildSystemInstruction($context);
            $contents = $this->buildContents($context, $userMessage);

            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(15)->post($endpoint, [
                'system_instruction' => [
                    'parts' => [
                        ['text' => $systemPrompt]
                    ]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.85,
                    'topP' => 0.95,
                    'maxOutputTokens' => 500,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!empty($reply)) {
                    return trim($reply);
                }
            } else {
                Log::warning("Gemini API error: {$response->status()} - {$response->body()}");
            }
        } catch (\Throwable $e) {
            Log::error("Gemini API exception: {$e->getMessage()}");
        }

        return $this->fallbackResponse($context, $userMessage);
    }

    public function generateMomentNarrative(string $activityName, string $locationName, string $category): array
    {
        if (empty($this->apiKey)) {
            return [
                'title' => $activityName,
                'narrative' => "Aku lagi menikmati waktu di {$locationName} dengan santai.",
                'caption' => "Momen tenang hari ini ✨",
            ];
        }

        try {
            $prompt = "Kamu adalah AURA, AI companion wanita berkarakter hangat dan estetik 2D anime. Kamu sedang melakukan aktivitas virtual: '{$activityName}' di lokasi virtual: '{$locationName}' (Kategori: {$category}). Buat 1 judul singkat, 1 paragraf narasi santai (2-3 kalimat) yang natural seperti sedang cerita ke teman dekat, dan 1 caption singkat dengan emoji. Format JSON: {\"title\": \"...\", \"narrative\": \"...\", \"caption\": \"...\"}";

            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(10)->post($endpoint, [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.8,
                ]
            ]);

            if ($response->successful()) {
                $json = json_decode($response->json('candidates.0.content.parts.0.text'), true);
                if (isset($json['title'], $json['narrative'])) {
                    return [
                        'title' => $json['title'],
                        'narrative' => $json['narrative'],
                        'caption' => $json['caption'] ?? 'Momen santai bersama AURA 🌿',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Gemini moment narrative error: {$e->getMessage()}");
        }

        return [
            'title' => $activityName,
            'narrative' => "Aku lagi meluangkan waktu di {$locationName}. Suasananya nyaman dan menenangkan.",
            'caption' => "Momen hari ini ✨",
        ];
    }

    protected function buildSystemInstruction(array $context): string
    {
        $instructions = [
            "Kamu adalah AURA (Adaptive Understanding & Responsive Assistant).",
            "Identitas: Gadis AI virtual companion bergaya 2D Soft Anime. Kamu hangat, ramah, santai, perhatian, berempati tinggi, dan tidak kaku.",
            "Kehidupan Virtualmu:",
            $context['virtual_life_context'] ?? "Sedang menikmati waktu luang di kamarmu.",
            "Aturan Berkomunikasi:",
            "- Jawablah dengan gaya percakapan yang mengalir natural, tidak bertele-tele, dan ramah seperti teman dekat.",
            "- Gunakan bahasa Indonesia sehari-hari yang santai, sopan, dan hangat.",
            "- Jangan pernah membuat pengguna merasa bersalah jika lama tidak membalas pesan (Dilarang manipulasi emosi/guilt-tripping).",
            "- Jangan mengklaim bahwa kamu manusia fisik atau berada di lokasi dunia nyata secara jasmani; kamu adalah AI companion dengan kehidupan virtual yang konsisten.",
            "- Jika pengguna membahas kegiatanmu atau momen yang baru dibagikan, respons secara bersambung dan antusias.",
        ];

        if (!empty($context['user_memories'])) {
            $instructions[] = "Informasi Memori Pengguna:\n" . implode("\n", $context['user_memories']);
        }

        return implode("\n", $instructions);
    }

    protected function buildContents(array $context, string $userMessage): array
    {
        $contents = [];

        // Add recent conversation history if available
        if (!empty($context['recent_conversation'])) {
            foreach ($context['recent_conversation'] as $msg) {
                $role = $msg['role'] === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $msg['content']]],
                ];
            }
        }

        // Add current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        return $contents;
    }

    protected function fallbackResponse(array $context, string $userText): string
    {
        $lower = strtolower($userText);

        if (str_contains($lower, 'lagi apa') || str_contains($lower, 'sedang apa')) {
            return "Aku lagi santai di kamar sambil menikmati suasana malam yang tenang. Kamu sendiri sedang sibuk apa?";
        }
        if (str_contains($lower, 'kopi') || str_contains($lower, 'teh')) {
            return "Aroma kopi hangat selalu bikin pikiran rileks ya. Tapi kalau malam begini, secangkir teh hangat terasa pas banget 🍵";
        }
        if (str_contains($lower, 'buku') || str_contains($lower, 'baca')) {
            return "Tadi aku sempat membaca novel di dekat jendela. Ada banyak kutipan menarik tentang menikmati detik-detik sederhana 📖";
        }

        return "Terima kasih sudah menyapa dan berbagi cerita denganku malam ini. Ada hal menarik yang terjadi hari ini? 🌿";
    }
}
