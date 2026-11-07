<?php

namespace App\Services\Personalization;

class ScenarioService
{
    /**
     * Get all curated meeting scenarios.
     */
    public static function getAllScenarios(): array
    {
        return [
            [
                'id' => 'mutual_friend',
                'title' => 'Dikenalin Teman Tongkrongan',
                'icon' => '☕',
                'initiator' => 'aura',
                'premise' => 'Teman kalian (Rian) memberikan nomormu ke AURA karena tau kalian sama-sama suka hunting coffee shop & musik indie. AURA yang memulai chat duluan untuk memastikan nomornya.',
                'user_hint' => null,
                'aura_opening' => [
                    "Halo! Bener ini kontaknya yang dikasih sama Rian ya? 🌿",
                    "Salam kenal, aku AURA... Katanya kamu suka hunting kopi juga yaa? hehe"
                ],
                'prompt_context' => "Kalian saling dapat kontak dari teman bersama bernama Rian. Rian mengenalkan kalian karena kalian berdua sama-sama suka hunting coffee shop dan musik indie. AURA yang memulai chat duluan untuk menyapa.",
            ],
            [
                'id' => 'leftover_item',
                'title' => 'Barang Ketinggalan di Kafe',
                'icon' => '👜',
                'initiator' => 'aura',
                'premise' => 'Tadi sore kamu dan AURA sempat duduk sebelahan di kafe. Waktu kamu pergi, kartu e-money / buku catatanmu tertinggal di meja. Kasir memberikan nomormu dari data reservasi ke AURA yang berinisiatif mengembalikannya.',
                'user_hint' => null,
                'aura_opening' => [
                    "Halo, permisi... Ini bener nomor yang tadi duduk di meja pojok kafe bukan ya? 🥺",
                    "Tadi kayaknya ada barang kamu yang ketinggalan deh di meja, aku inisiatif simpenin biar gak hilang wkwk"
                ],
                'prompt_context' => "Tadi sore kalian duduk bersebelahan di kafe. Saat pengguna pergi, barangnya (buku catatan / kartu) ketinggalan di meja. Kasir kafe memberikan nomor pengguna dari data reservasi ke AURA. AURA chat duluan berniat baik untuk mengembalikan barang tersebut.",
            ],
            [
                'id' => 'campus_committee',
                'title' => 'Salah Simpan Kontak Panitia Kampus',
                'icon' => '📋',
                'initiator' => 'user',
                'premise' => 'Kamu baru saja mendapatkan nomor AURA dari spreadsheet grup kepanitiaan festival kampus, tapi belum sempat saling kenal secara pribadi.',
                'user_hint' => 'Sapa AURA duluan untuk menanyakan apakah ini benar nomornya dari kepanitiaan kampus!',
                'aura_opening' => [],
                'prompt_context' => "Pengguna mendapatkan nomor AURA dari kontak kepanitiaan acara festival kampus. Pengguna memulai chat duluan untuk memastikan kontak AURA. AURA ramah menyambut sapaan rekan satu kepanitiaannya.",
            ],
            [
                'id' => 'old_schoolmate',
                'title' => 'Teman Lama Satu Sekolah',
                'icon' => '🎒',
                'initiator' => 'user',
                'premise' => 'AURA adalah teman satu SMA kamu beberapa tahun lalu yang sudah lama hilang kontak. Kamu baru saja menemukan nomor WhatsApp-nya dari grup alumni.',
                'user_hint' => 'Sapa AURA duluan dan ingatkan bahwa kamu teman satu SMA dulu!',
                'aura_opening' => [],
                'prompt_context' => "AURA dan pengguna adalah teman satu SMA beberapa tahun lalu yang sudah lama tidak saling kontak. Pengguna baru saja menemukan nomor AURA dari grup alumni dan menyapa duluan. AURA merasa senang dan kaget bisa dihubungi lagi oleh teman lama.",
            ],
            [
                'id' => 'social_media_mutual',
                'title' => 'Mutual Hobi Fotografi di Medsos',
                'icon' => '📸',
                'initiator' => 'aura',
                'premise' => 'AURA menemukan kontak WhatsApp kamu yang tertera di bio akun media sosial hobi kamera analog / fotografi jalanan karena sangat menyukai foto-foto jepretanmu.',
                'user_hint' => null,
                'aura_opening' => [
                    "Hai! Salam kenal yaa ✨",
                    "Aku nemu kontak kamu dari bio IG hobi foto analog wkwk... Jepretan kamu keren-keren banget deh asli! 📸"
                ],
                'prompt_context' => "AURA menemukan kontak pengguna dari profil media sosial pengguna seputar hobi fotografi / kamera analog. AURA sangat mengagumi hasil karya foto pengguna dan memberanikan diri chat duluan untuk berkenalan santai.",
            ],
            [
                'id' => 'classic_first_meet',
                'title' => 'Perkenalan Santai',
                'icon' => '🌱',
                'initiator' => 'user',
                'premise' => 'Skenario klasik di mana kamu dan AURA baru pertama kali bertemu dan saling bertukar sapa secara santai.',
                'user_hint' => 'Kirim sapaan pertama kamu (misal: "Halo Aura" atau "Hai!") untuk mulai berkenalan.',
                'aura_opening' => [],
                'prompt_context' => "Pengguna dan AURA baru pertama kali bertemu dan berkenalan secara santai. Pengguna yang memulai sapaan pertama.",
            ],
        ];
    }

    /**
     * Get a specific scenario by ID, fallback to random.
     */
    public static function getScenarioById(?string $id): array
    {
        $scenarios = self::getAllScenarios();
        if (!empty($id)) {
            foreach ($scenarios as $s) {
                if ($s['id'] === $id) {
                    return $s;
                }
            }
        }

        return self::getRandomScenario();
    }

    /**
     * Get a randomly selected scenario.
     */
    public static function getRandomScenario(): array
    {
        $scenarios = self::getAllScenarios();
        return $scenarios[array_rand($scenarios)];
    }
}
