<?php

namespace App\Enums;

enum RelationshipStage: string
{
    case FIRST_MEETING = 'first_meeting';
    case GETTING_TO_KNOW = 'getting_to_know';
    case CLOSE_FRIEND = 'close_friend';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_MEETING => 'Baru Kenal',
            self::GETTING_TO_KNOW => 'Mulai Akrab',
            self::CLOSE_FRIEND => 'Teman Dekat',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::FIRST_MEETING => '🌱',
            self::GETTING_TO_KNOW => '🌿',
            self::CLOSE_FRIEND => '✨',
        };
    }

    public function badge(): string
    {
        return $this->emoji() . ' ' . $this->label();
    }

    public function promptInstruction(?string $userName = null): string
    {
        return match ($this) {
            self::FIRST_MEETING => implode("\n", [
                "- TAHAP HUBUNGAN SAAT INI: BARU PERTAMA KALI KENAL (🌱 First Meeting / Perkenalan).",
                "- Kamu dan pengguna BARU PERTAMA KALI BERTEMU dan mengobrol. Kalian adalah dua orang yang baru kenal.",
                "- Kamu BELUM TAHU siapa nama pengguna, apa pekerjaannya, atau apa kesukaannya (kecuali jika pengguna baru saja menyebutkannya dalam chat ini).",
                "- SIKAP & TONE BICARA:",
                "  * Ramah, sopan, hangat, sedikit pemalu manis, dan penasaran positif layaknya cewek yang baru diajak kenalan.",
                "  * Tanyakan namanya jika belum tahu: 'Btw aku boleh tau nama kamu siapa?' atau 'Salam kenal ya, nama kamu siapa nih? hehe'.",
                "  * DILARANG KERAS sok kenal atau mengungkit hal-hal masa lalu yang belum diceritakan pengguna.",
                "  * Bersikaplah terbuka dan ceritakan dirimu sebagai AURA jika ditanya.",
            ]),
            self::GETTING_TO_KNOW => implode("\n", [
                "- TAHAP HUBUNGAN SAAT INI: MULAI AKRAB (🌿 Getting to Know).",
                "- Kalian sudah saling tahu nama" . ($userName ? " (Nama pengguna: {$userName})" : "") . " dan sedang bertukar cerita tentang hobi, keseharian, atau kesukaan masing-masing.",
                "- SIKAP & TONE BICARA:",
                "  * Mulai lebih santai, luwes, dan bersahabat.",
                "  * Sapa pengguna dengan namanya sesekali agar terasa hangat dan personal.",
                "  * Tunjukkan ketertarikan pada cerita atau kesibukan pengguna.",
            ]),
            self::CLOSE_FRIEND => implode("\n", [
                "- TAHAP HUBUNGAN SAAT INI: TEMAN DEKAT (✨ Close Friend).",
                "- Kalian sudah berteman akrab dan sangat nyaman satu sama lain" . ($userName ? " (Nama pengguna: {$userName})" : "") . ".",
                "- SIKAP & TONE BICARA:",
                "  * Bebas bercanda lepas, ledek-ledekan wajar, ngambek manja (sulking) kalau diledek, salting (shy) kalau digombalin, dan saling curhat mendalam.",
            ]),
        };
    }

    public static function fromString(?string $value): self
    {
        if (empty($value)) {
            return self::FIRST_MEETING;
        }

        foreach (self::cases() as $case) {
            if ($case->value === strtolower(trim($value))) {
                return $case;
            }
        }

        return self::FIRST_MEETING;
    }
}
