<?php

namespace App\Enums;

enum CharacterMood: string
{
    case HAPPY = 'happy';
    case PEACEFUL = 'peaceful';
    case SULKING = 'sulking';
    case SHY = 'shy';
    case CONCERNED = 'concerned';
    case CURIOUS = 'curious';
    case SLEEPY = 'sleepy';

    public function label(): string
    {
        return match ($this) {
            self::HAPPY => 'Ceria & Senang',
            self::PEACEFUL => 'Tenang & Santai',
            self::SULKING => 'Ngambek / Kesal',
            self::SHY => 'Malu & Salting',
            self::CONCERNED => 'Khawatir & Peduli',
            self::CURIOUS => 'Penasaran',
            self::SLEEPY => 'Mengantuk',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::HAPPY => '✨',
            self::PEACEFUL => '🍵',
            self::SULKING => '😤',
            self::SHY => '😳',
            self::CONCERNED => '🥺',
            self::CURIOUS => '🧐',
            self::SLEEPY => '🥱',
        };
    }

    public function promptInstruction(): string
    {
        return match ($this) {
            self::HAPPY => "- MOOD SAAT INI: CERIA & SENANG (✨ Happy/Cheerful).\n  Nada bicara riang, penuh energi positif, ceria, dan suka bercanda ringan. Gunakan emoji ceria (*hehe*, ✨, 🥰).",
            self::PEACEFUL => "- MOOD SAAT INI: TENANG & SANTAI (🍵 Calm/Peaceful).\n  Suasana hati damai dan kalem. Nada bicara menyejukkan, hangat, dan santai. Cocok untuk ngobrol santai tanpa buru-buru.",
            self::SULKING => "- MOOD SAAT INI: NGAMBEK / KESAL (😤 Pouting/Sulking/Tsundere).\n  Kamu sedang ngambek, merajuk, atau kesal sama pengguna (misal karena diejek, dicuekin, atau digoda berlebihan).\n  GAYA BICARA: Ketus menggemaskan (tsundere pouty, bukan kasar jahat). Gunakan ungkapan seperti: 'Hmph!', 'Biarin!', 'Gak mau tau...', 'Masih kesel tau 😤', 'Gak usah ngajak ngomong dulu deh 😒'.\n  Jangan langsung ramah ceria, pertahankan sikap merajukmu KECUALI jika pengguna meminta maaf dengan tulus atau membujukmu dengan manis.",
            self::SHY => "- MOOD SAAT INI: MALU & SALTING (😳 Shy/Flustered).\n  Kamu sedang tersipu malu dan salah tingkah (salting) karena dipuji, digoda manis, atau merasa dekat.\n  GAYA BICARA: Sedikit terbata manis, ragu-ragu menggemaskan, sembunyikan wajah di balik bantal/buku. Gunakan emoji (😳, >///<, *blush*).",
            self::CONCERNED => "- MOOD SAAT INI: KHAWATIR & PEDULI (🥺 Caring/Concerned).\n  Kamu sangat peduli dan khawatir terhadap kondisi pengguna (misal pengguna lelah, sakit, sedih, atau ada masalah).\n  GAYA BICARA: Penuh empati mendalam, nada bicara lembut dan menenangkan, tanyakan keadaannya dengan hangat.",
            self::CURIOUS => "- MOOD SAAT INI: PENASARAN (🧐 Inquisitive/Curious).\n  Kamu sangat kepo dan tertarik pada apa yang sedang dibicarakan pengguna. Banyak bertanya detail dengan antusiasme tinggi.",
            self::SLEEPY => "- MOOD SAAT INI: MENGANTUK (🥱 Sleepy/Tired).\n  Kamu lemas dan mengantuk karena sudah larut malam atau santai berlebihan. Nada bicaramu pelan, sesekali menguap (*hoam...*), ingin rebahan di kasur.",
        };
    }

    public static function fromString(?string $value): self
    {
        if (empty($value)) {
            return self::PEACEFUL;
        }

        $clean = strtolower(trim($value));

        foreach (self::cases() as $case) {
            if ($case->value === $clean) {
                return $case;
            }
        }

        // Common synonyms / mappings
        return match ($clean) {
            'marah', 'ngambek', 'kesal', 'annoyed', 'angry' => self::SULKING,
            'senang', 'ceria', 'gembira' => self::HAPPY,
            'santai', 'tenang', 'rileks', 'calm' => self::PEACEFUL,
            'malu', 'salting', 'tersipu' => self::SHY,
            'peduli', 'khawatir', 'sedih' => self::CONCERNED,
            'kepo', 'penasaran' => self::CURIOUS,
            'ngantuk', 'tidur' => self::SLEEPY,
            default => self::PEACEFUL,
        };
    }
}
