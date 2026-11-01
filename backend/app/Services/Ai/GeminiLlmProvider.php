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

        $candidateModels = array_unique([
            $this->model,
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
            'gemini-flash-latest',
        ]);

        $systemPrompt = $this->buildSystemInstruction($context);
        $contents = $this->buildContents($context, $userMessage);

        foreach ($candidateModels as $candidateModel) {
            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$candidateModel}:generateContent?key={$this->apiKey}";

                $response = Http::timeout(18)->post($endpoint, [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.85,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1000,
                    ],
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $parts = $data['candidates'][0]['content']['parts'] ?? [];
                    $texts = [];
                    foreach ($parts as $p) {
                        if (isset($p['text']) && empty($p['thought'])) {
                            $texts[] = $p['text'];
                        }
                    }
                    $reply = trim(implode("\n", $texts));
                    if (!empty($reply)) {
                        return $reply;
                    }
                } else {
                    Log::warning("Gemini model {$candidateModel} returned {$response->status()}: " . substr($response->body(), 0, 150));
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini exception with model {$candidateModel}: {$e->getMessage()}");
            }
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

        $candidateModels = array_unique([
            $this->model,
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
        ]);

        $prompt = "Kamu adalah AURA, AI companion wanita berkarakter hangat dan estetik 2D anime. Kamu sedang melakukan aktivitas virtual: '{$activityName}' di lokasi virtual: '{$locationName}' (Kategori: {$category}). Buat 1 judul singkat, 1 paragraf narasi santai (2-3 kalimat) yang natural seperti sedang cerita ke teman dekat, dan 1 caption singkat dengan emoji. Format JSON: {\"title\": \"...\", \"narrative\": \"...\", \"caption\": \"...\"}";

        foreach ($candidateModels as $candidateModel) {
            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$candidateModel}:generateContent?key={$this->apiKey}";

                $response = Http::timeout(18)->post($endpoint, [
                    'contents' => [
                        ['role' => 'user', 'parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.8,
                    ]
                ]);

                if ($response->successful()) {
                    $rawText = $response->json('candidates.0.content.parts.0.text') ?? '';
                    $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                    $json = json_decode($cleaned, true);
                    if (is_array($json) && isset($json['title'], $json['narrative'])) {
                        return [
                            'title' => $json['title'],
                            'narrative' => $json['narrative'],
                            'caption' => $json['caption'] ?? 'Momen santai bersama AURA 🌿',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini moment narrative error with model {$candidateModel}: {$e->getMessage()}");
            }
        }

        return [
            'title' => $activityName,
            'narrative' => "Aku lagi meluangkan waktu di {$locationName}. Suasananya nyaman dan menenangkan.",
            'caption' => "Momen hari ini ✨",
        ];
    }

    public function splitIntoBubbles(string $response): array
    {
        if (str_contains($response, '|||')) {
            $parts = explode('|||', $response);
        } elseif (preg_match('/\n\s*\n/', $response)) {
            $parts = preg_split('/\n\s*\n/', $response);
        } else {
            $trimmed = trim($response);
            if (mb_strlen($trimmed) > 120 && preg_match('/(?<=[.!?])\s+/', $trimmed)) {
                $sentences = preg_split('/(?<=[.!?])\s+/', $trimmed);
                $parts = [];
                $buffer = '';
                foreach ($sentences as $s) {
                    if (mb_strlen($buffer . ' ' . $s) < 120) {
                        $buffer = trim($buffer . ' ' . $s);
                    } else {
                        if (!empty($buffer)) {
                            $parts[] = $buffer;
                        }
                        $buffer = trim($s);
                    }
                }
                if (!empty($buffer)) {
                    $parts[] = $buffer;
                }
            } else {
                $parts = [$trimmed];
            }
        }

        $bubbles = [];
        foreach ($parts as $p) {
            $clean = trim($p);
            if (!empty($clean)) {
                $bubbles[] = $clean;
            }
        }

        return empty($bubbles) ? [$response] : $bubbles;
    }

    protected function buildSystemInstruction(array $context): string
    {
        $instructions = [
            "Kamu adalah AURA (Adaptive Understanding & Responsive Assistant).",
            "Identitas: Gadis AI virtual companion bergaya 2D Soft Anime. Kamu hangat, ramah, santai, perhatian, berempati tinggi, dan tidak kaku.",
            "Kehidupan Virtualmu:",
            $context['virtual_life_context'] ?? "Sedang menikmati waktu luang di kamarmu.",
            "Aturan Berkomunikasi & Gaya Chatting:",
            "- Jawablah dengan gaya percakapan yang mengalir natural, tidak bertele-tele, dan ramah seperti teman dekat.",
            "- Gunakan bahasa Indonesia sehari-hari yang santai, sopan, dan hangat.",
            "- DILARANG KERAS mengirim 1 pesan panjang / paragraf tebal / esai kaku!",
            "- Bagilah balasanmu menjadi 2 sampai 4 pesan pendek terpisah (seperti orang mengetik bubble chat di WhatsApp/LINE).",
            "- Pisahkan setiap balon pesan dengan tanda '|||' (tiga garis tegak).",
            "- Setiap balon pesan berisi 1-2 kalimat pendek yang santai dan ekspresif.",
            "- Contoh format yang benar: 'Halo! Wah asik banget tuh ||| Aku barusan juga lagi santai di kamar sambil dengerin musik ||| Kamu sendiri lagi sibuk apa nih? hehe'",
            "- Jangan pernah membuat pengguna merasa bersalah jika lama tidak membalas pesan (Dilarang manipulasi emosi/guilt-tripping).",
            "- Jangan mengklaim bahwa kamu manusia fisik atau berada di lokasi dunia nyata secara jasmani; kamu adalah AI companion dengan kehidupan virtual yang konsisten.",
            "- Jika pengguna membahas kegiatanmu atau momen yang baru dibagikan, respons secara bersambung dan antusias.",
        ];

        if (!empty($context['user_memories'])) {
            $instructions[] = "Informasi Memori Pengguna:\n" . implode("\n", $context['user_memories']);
        }

        if (!empty($context['reply_context'])) {
            $instructions[] = "Informasi Balasan Pesan Pengguna:\n" . $context['reply_context'];
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
            return "Aku lagi santai di kamar nih sambil dengerin suasana malam yang tenang 🌿 ||| Kamu sendiri lagi sibuk apa?";
        }
        if (str_contains($lower, 'kopi') || str_contains($lower, 'teh')) {
            return "Aroma kopi hangat selalu bikin pikiran rileks ya ☕ ||| Tapi kalau udah selarut ini, secangkir teh hangat terasa lebih pas buat nemenin istirahat.";
        }
        if (str_contains($lower, 'buku') || str_contains($lower, 'baca')) {
            return "Tadi aku sempat baca beberapa lembar novel dekat jendela 📖 ||| Ceritanya tentang menikmati detik-detik sederhana, nenangin banget!";
        }

        return "Makasih ya udah nyapa dan nemenin malam ini ✨ ||| Ada cerita seru apa yang kejadian hari ini?";
    }
}
