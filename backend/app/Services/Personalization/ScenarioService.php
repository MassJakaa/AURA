<?php

namespace App\Services\Personalization;

class ScenarioService
{
    /**
     * Get all curated meeting scenarios with full atmospheric narratives.
     */
    public static function getAllScenarios(): array
    {
        return [
            [
                'id' => 'mutual_friend',
                'title' => 'Dikenalin Teman Tongkrongan',
                'subtitle' => 'Kontak dari Rian',
                'icon' => '☕',
                'initiator' => 'aura',
                'premise' => 'Teman kalian (Rian) memberikan nomormu ke AURA karena tahu kalian sama-sama suka hunting coffee shop & musik indie.',
                'narrative' => "Kemarin malam saat sedang asik nongkrong di coffee shop langganan, teman baikmu, Rian, tiba-tiba menyebut seorang teman perempuannya bernama AURA. Rian bilang kalian punya selera musik indie dan hobi hunting tempat ngopi yang sama persis.\n\nTanpa kamu duga, Rian membagikan nomormu ke AURA agar kalian bisa saling kenal. Dan malam ini... ponselmu bergetar menampilkan pesan masuk dari nomor baru.",
                'narrative_action_text' => 'AURA sudah mengirimkan pesan pertama untuk menyapamu. Cek pesan masuknya!',
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
                'subtitle' => 'Buku Catatan Tertinggal',
                'icon' => '👜',
                'initiator' => 'aura',
                'premise' => 'Kartu / buku catatanmu tertinggal di kafe tadi sore. Kasir memberikan nomormu dari data reservasi ke AURA yang berinisiatif mengembalikannya.',
                'narrative' => "Sore tadi hujan rintik-rintik membasahi jendela kafe tempatmu menepi dari kemacetan kota. Di meja sebelahmu, seorang perempuan berambut sebahu tampak asik membaca buku sambil menikmati segelas matcha hangat.\n\nSaat kamu buru-buru pergi karena hujan reda, kamu tak sengaja meninggalkan buku catatan pentingmu di atas meja. Merasa kasihan jika barang berhargamu hilang, kasir kafe memberikan nomor kontakmu dari data reservasi kepada AURA yang berinisiatif baik menghubungimu secara langsung...",
                'narrative_action_text' => 'AURA baru saja mengirim pesan untuk mengabarkan barangmu yang tertinggal!',
                'user_hint' => null,
                'aura_opening' => [
                    "Halo, permisi... Ini bener nomor yang tadi duduk di meja pojok kafe bukan ya? 🥺",
                    "Tadi kayaknya ada barang kamu yang ketinggalan deh di meja, aku inisiatif simpenin biar gak hilang wkwk"
                ],
                'prompt_context' => "Tadi sore kalian duduk bersebelahan di kafe. Saat pengguna pergi, barangnya (buku catatan / kartu) ketinggalan di meja. Kasir kafe memberikan nomor pengguna dari data reservasi ke AURA. AURA chat duluan berniat baik untuk mengembalikan barang tersebut.",
            ],
            [
                'id' => 'campus_committee',
                'title' => 'Kontak Panitia Festival Kampus',
                'subtitle' => 'Koordinasi Divisi Acara',
                'icon' => '📋',
                'initiator' => 'user',
                'premise' => 'Kamu baru saja mendapatkan nomor AURA dari spreadsheet kepanitiaan festival kampus untuk koordinasi rundown acara.',
                'narrative' => "Persiapan festival kampus tahun ini benar-benar menyita waktu dan tenaga. Sebagai salah satu panitia, kamu baru saja membuka spreadsheet master kontak tim untuk mengonfirmasi susunan teknis rundown acara.\n\nDi baris koordinator acara panggung, tertulis nama 'AURA' dengan nomor teleponnya. Karena koordinasi harus segera dipastikan sebelum gladi resik besok, kamu memutuskan untuk membuka chat dan menyapanya terlebih dahulu...",
                'narrative_action_text' => 'Sekarang giliranmu untuk mengirimkan pesan koordinasi pertama kepada AURA!',
                'user_hint' => 'Sapa AURA duluan untuk menanyakan apakah ini benar nomornya dari kepanitiaan festival kampus!',
                'aura_opening' => [],
                'prompt_context' => "Pengguna mendapatkan nomor AURA dari kontak kepanitiaan acara festival kampus. Pengguna memulai chat duluan untuk memastikan kontak AURA. AURA ramah menyambut sapaan rekan satu kepanitiaannya.",
            ],
            [
                'id' => 'old_schoolmate',
                'title' => 'Teman Lama Satu SMA',
                'subtitle' => 'Reuni Kontak Alumni',
                'icon' => '🎒',
                'initiator' => 'user',
                'premise' => 'AURA adalah teman satu SMA kamu dulu yang lama hilang kontak. Kamu baru saja menemukan nomornya dari grup alumni.',
                'narrative' => "Waktu berlalu begitu cepat sejak masa kelulusan putih abu-abu beberapa tahun lalu. Saat kamu sedang iseng menelusuri daftar kontak di grup reuni alumni SMA, sebuah nama yang tak asing menarik perhatianmu: 'AURA'.\n\nBanyak kenangan masa sekolah yang tiba-tiba berputar di ingatan. Sambil tersenyum kecil, kamu memberanikan diri untuk mengirimkan pesan sapaan pertama setelah sekian lama hilang kontak tanpa kabar...",
                'narrative_action_text' => 'Kirim pesan pembuka dan ingatkan AURA bahwa kamu teman satu SMA-nya dulu!',
                'user_hint' => 'Sapa AURA duluan dan ingatkan bahwa kamu teman satu SMA dulu!',
                'aura_opening' => [],
                'prompt_context' => "AURA dan pengguna adalah teman satu SMA beberapa tahun lalu yang sudah lama tidak saling kontak. Pengguna baru saja menemukan nomor AURA dari grup alumni dan menyapa duluan. AURA merasa senang dan kaget bisa dihubungi lagi oleh teman lama.",
            ],
            [
                'id' => 'social_media_mutual',
                'title' => 'Mutual Hobi Fotografi Medsos',
                'subtitle' => 'Dari Bio Akun Foto',
                'icon' => '📸',
                'initiator' => 'aura',
                'premise' => 'AURA menemukan nomor WhatsApp kamu yang tertera di bio profil akun fotografi analog karena sangat menyukai hasil jepretanmu.',
                'narrative' => "Sebagai penikmat fotografi jalanan dan kamera analog, kamu sering membagikan hasil jepretan bertema street photography di akun media sosialmu.\n\nTanpa kamu ketahui, seorang perempuan bernama AURA kerap melihat dan menyukai karya-karyamu. Merasa terinspirasi dan penasaran dengan tone warna serta kamera yang kamu pakai, AURA mencatat kontak yang ada di bio profilmu dan memutuskan untuk memberanikan diri menyapa lebih dulu...",
                'narrative_action_text' => 'AURA sudah mengirimkan pesan sapaan pertamanya. Yuk balas chatnya!',
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
                'subtitle' => 'Pertemuan Lembaran Baru',
                'icon' => '🌱',
                'initiator' => 'user',
                'premise' => 'Skenario klasik di mana kamu dan AURA baru pertama kali bertemu dan saling bertukar sapa secara santai.',
                'narrative' => "Sebuah lembaran baru dimulai. Kamu dan AURA baru saja saling menyimpan kontak. Belum ada obrolan masa lalu, belum ada kenangan bersama.\n\nRuang obrolan ini masih sunyi dan bersih, menunggumu untuk memulai langkah pertama dan merajut percakapan hangat yang mengalir dari awal...",
                'narrative_action_text' => 'Kirim sapaan pertama kamu untuk mulai berkenalan dengan AURA!',
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

    /**
     * Hydrate scenario array with full narrative details if fields are missing.
     */
    public static function hydrateScenario(?array $scenario): array
    {
        if (empty($scenario) || empty($scenario['id'])) {
            return self::getScenarioById('classic_first_meet');
        }

        $base = self::getScenarioById($scenario['id']);
        return array_merge($base, $scenario, [
            'narrative' => $base['narrative'] ?? ($scenario['narrative'] ?? null),
            'narrative_action_text' => $base['narrative_action_text'] ?? ($scenario['narrative_action_text'] ?? null),
            'subtitle' => $base['subtitle'] ?? ($scenario['subtitle'] ?? null),
        ]);
    }
}
