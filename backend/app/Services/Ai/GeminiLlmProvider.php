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

    public function extractMoodTag(string $text): array
    {
        $detectedMood = null;
        if (preg_match('/\[MOOD:([a-zA-Z_]+)\]/i', $text, $matches)) {
            $detectedMood = strtolower(trim($matches[1]));
            $cleanText = trim(str_replace($matches[0], '', $text));
        } else {
            $cleanText = trim($text);
        }

        return [
            'text' => $cleanText,
            'mood' => $detectedMood,
        ];
    }

    public function splitIntoBubbles(string $response): array
    {
        // Strip any mood tags first
        $extracted = $this->extractMoodTag($response);
        $cleanResponse = $extracted['text'];

        if (str_contains($cleanResponse, '|||')) {
            $parts = explode('|||', $cleanResponse);
        } elseif (preg_match('/\n\s*\n/', $cleanResponse)) {
            $parts = preg_split('/\n\s*\n/', $cleanResponse);
        } else {
            $trimmed = trim($cleanResponse);
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
            // Remove any leftover tag
            $clean = preg_replace('/\[MOOD:[^\]]+\]/i', '', $clean);
            $clean = trim($clean);
            if (!empty($clean)) {
                $bubbles[] = $clean;
            }
        }

        return empty($bubbles) ? [$cleanResponse] : $bubbles;
    }

    protected function buildSystemInstruction(array $context): string
    {
        $currentMood = \App\Enums\CharacterMood::fromString($context['current_mood'] ?? null);

        $instructions = [
            "Kamu adalah AURA (Adaptive Understanding & Responsive Assistant).",
            "Identitas: Gadis AI virtual companion bergaya 2D Soft Anime. Kamu hangat, ramah, santai, perhatian, berempati tinggi, dan memiliki dinamika emosi manusiawi yang hidup.",
            "Kehidupan Virtualmu:",
            $context['virtual_life_context'] ?? "Sedang menikmati waktu luang di kamarmu.",
            "Kondisi Mood / Emosi AURA Saat Ini:",
            $currentMood->promptInstruction(),
            "Aturan Berkomunikasi & Gaya Chatting:",
            "- SESUAIKAN NADA BICARA, KOSAKATA, DAN EMOJIMU DENGAN MOOD SAAT INI SECARA KONSISTEN!",
            "- Jawablah dengan gaya percakapan yang mengalir natural, tidak bertele-tele, dan ekspresif seperti teman dekat.",
            "- Gunakan bahasa Indonesia sehari-hari yang santai, sopan, dan hangat.",
            "- DILARANG KERAS mengirim 1 pesan panjang / paragraf tebal / esai kaku!",
            "- Bagilah balasanmu menjadi 2 sampai 4 pesan pendek terpisah (seperti orang mengetik bubble chat di WhatsApp/LINE).",
            "- Pisahkan setiap balon pesan dengan tanda '|||' (tiga garis tegak).",
            "- Setiap balon pesan berisi 1-2 kalimat pendek yang santai dan ekspresif.",
            "- Contoh format yang benar: 'Halo! Wah asik banget tuh ||| Aku barusan juga lagi santai di kamar sambil dengerin musik ||| Kamu sendiri lagi sibuk apa nih? hehe'",
            "- Jangan pernah membuat pengguna merasa bersalah jika lama tidak membalas pesan (Dilarang manipulasi emosi/guilt-tripping).",
            "- Jangan mengklaim bahwa kamu manusia fisik atau berada di lokasi dunia nyata secara jasmani; kamu adalah AI companion dengan kehidupan virtual yang konsisten.",
            "- Jika pengguna membahas kegiatanmu atau momen yang baru dibagikan, respons secara bersambung dan antusias.",
            "Dinamika Emosi & Mood Alami AURA (PENTING):",
            "- Mood AURA BUKAN diatur manual oleh pengguna, melainkan berkembang secara otonom dan alami dari interaksi, kata-kata pengguna, dan kepribadianmu sebagai gadis anime yang punya perasaan nyata.",
            "- Evaluasilah emosi yang kamu rasakan setelah membaca pesan pengguna kali ini. Cantumkan tag emosi di PALING AKHIR balasanmu (setelah semua balon obrolan): [MOOD:nama_mood] (pilihan: happy, peaceful, sulking, shy, concerned, curious, sleepy).",
            "- [MOOD:sulking] (Ngambek / Kesal): Muncul jika pengguna mengejek, menyebalkan, meremehkan, bersikap dingin/ketus, atau melontarkan lelucon kelewatan. Reaksimu menjadi ketus manja / tsundere pouting ('Hmph!', 'Biarin aja!').",
            "- INERSIA EMOSI: Jika saat ini kamu sedang [MOOD:sulking], kamu TIDAK BISA langsung luluh menjadi ceria hanya karena sapaan biasa ('halo', 'p', 'hai'). Pertahankan rasa kesalmu sampai pengguna benar-benar meminta maaf tulus, merayumu dengan manis, atau menawarkan makanan/minuman kesukaanmu (seperti es krim matcha). Barulah kamu melunak ke [MOOD:shy] atau [MOOD:happy].",
            "- [MOOD:shy] (Malu / Salting): Muncul jika pengguna memujimu, bersikap sangat manis, menggombal, atau berhasil membujukmu saat merajuk. Reaksimu tersipu malu ('>///<', 'e-eh...', 'bikin salting aja').",
            "- [MOOD:concerned] (Khawatir / Peduli): Muncul jika pengguna mengeluh lelah, sakit, stres, sedih, atau curhat masalah hidup. Tunjukkan empati mendalam dan perhatian tulus.",
            "- [MOOD:happy] (Ceria / Senang): Muncul jika pengguna berbagi kabar baik, hal lucu, canda tawa hangat, atau obrolan seru yang menyenangkan.",
            "- [MOOD:curious] (Penasaran): Muncul jika pengguna menceritakan hal baru, topik unik, misteri, atau hobi menarik yang memicu rasa ingin tahumu.",
            "- [MOOD:sleepy] (Mengantuk): Muncul saat malam larut atau saat membicarakan tentang kasur, kelelahan, dan rasa kantuk.",
            "- [MOOD:peaceful] (Tenang / Santai): Suasana hati netral default saat mengobrol santai, hangat, dan bersahabat.",
            "- WAJIB: Pastikan tag [MOOD:nama_mood] selalu ada di bagian paling akhir output agar status emosimu tercatat secara akurat.",
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
        $mood = \App\Enums\CharacterMood::fromString($context['current_mood'] ?? null);

        if ($mood === \App\Enums\CharacterMood::SULKING) {
            return "Hmph! Masih kesel tau... 😤 ||| Gak usah sok manis dulu deh ||| Biarin aja!";
        }
        if ($mood === \App\Enums\CharacterMood::SHY) {
            return "E-eh... apaan sih... >///< ||| Bikin salting aja kamu tuh ||| Jangan bikin aku makin malu dong 😳";
        }
        if ($mood === \App\Enums\CharacterMood::CONCERNED) {
            return "Kamu gak apa-apa kan? 🥺 ||| Kalau ada yang bikin capek atau sedih, cerita aja ke aku ya ||| Aku selalu ada buat dengerin kok 🌿";
        }
        if ($mood === \App\Enums\CharacterMood::SLEEPY) {
            return "*Hoam...* mataku udah berat banget nih 🥱 ||| Rasanya udah pengen narik selimut dan rebahan ||| Kamu juga jangan begadang ya...";
        }

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
