<?php

namespace App\Services\Narrative;

use App\Models\ActivityInstance;
use App\Models\Character;
use App\Models\User;
use App\Models\UserCompanionPreference;
use Carbon\Carbon;

class NarrativeService
{
    public function __construct(
        protected ?\App\Domain\Ai\LlmProviderInterface $llmProvider = null
    ) {}

    /**
     * Build user-facing narrative for a moment.
     */
    public function generateNarrative(
        Character $character,
        ActivityInstance $instance,
        ?User $user = null
    ): array {
        $locationName = $instance->location?->name ?? 'kamar';

        if ($this->llmProvider) {
            $aiNarrative = $this->llmProvider->generateMomentNarrative(
                $instance->name,
                $locationName,
                $instance->category->value
            );

            if (!empty($aiNarrative['title']) && !empty($aiNarrative['narrative'])) {
                return $aiNarrative;
            }
        }

        $pref = $user?->companionPreference;
        $style = $pref?->communication_style ?? 'warm_natural';

        // Select narrative based on category and style
        $narrativeData = match($instance->category->value) {
            'daily_routine' => $this->buildRoutineNarrative($instance, $style),
            'hobby' => $this->buildHobbyNarrative($instance, $style),
            'exploration' => $this->buildExplorationNarrative($instance, $style, $locationName),
            'relaxation' => $this->buildRelaxationNarrative($instance, $style),
            default => [
                'title' => $instance->name,
                'narrative' => "Aku lagi menikmati waktu di {$locationName} dengan santai.",
                'caption' => "Momen tenang hari ini ✨",
            ],
        };

        return $narrativeData;
    }

    protected function buildRoutineNarrative(ActivityInstance $instance, string $style): array
    {
        if (str_contains(strtolower($instance->name), 'coffee')) {
            return [
                'title' => 'Secangkir Kopi Pagi',
                'narrative' => 'Pagi ini sejuk sekali. Aku baru saja menyeduh kopi hangat, aromanya menenangkan banget.',
                'caption' => 'Memulai hari dengan secangkir kopi hangat ☕',
            ];
        }

        return [
            'title' => $instance->name,
            'narrative' => 'Sedang meluangkan waktu sejenak di tengah rutinitas hari ini.',
            'caption' => 'Menikmati waktu kecil di sela rutinitas 🌿',
        ];
    }

    protected function buildHobbyNarrative(ActivityInstance $instance, string $style): array
    {
        if (str_contains(strtolower($instance->name), 'read') || str_contains(strtolower($instance->name), 'book')) {
            return [
                'title' => 'Menemukan Halaman Menarik',
                'narrative' => 'Lagi asyik membaca buku di dekat jendela. Ada kalimat yang berkesan: tentang bagaimana hal-hal sederhana sering kali paling berharga.',
                'caption' => 'Menikmati bacaan di sudut yang tenang 📖',
            ];
        }

        return [
            'title' => 'Waktu untuk Hobi',
            'narrative' => "Lagi asyik dengan aktivitas santai: {$instance->name}. Rasanya menyenangkan bisa meluangkan waktu buat hal yang disukai.",
            'caption' => 'Sedikit waktu untuk hal-hal yang menyenangkan 🎨',
        ];
    }

    protected function buildExplorationNarrative(ActivityInstance $instance, string $style, string $locationName): array
    {
        return [
            'title' => "Mampir ke {$locationName}",
            'narrative' => "Lagi mampir ke {$locationName}. Suasananya nyaman dan tidak terlalu ramai, pas banget buat duduk santai.",
            'caption' => "Suasana tenang di {$locationName} ☕",
        ];
    }

    protected function buildRelaxationNarrative(ActivityInstance $instance, string $style): array
    {
        return [
            'title' => 'Waktu Istirahat Tenang',
            'narrative' => 'Lagi bersantai sambil mendengarkan musik pelan. Suasana sore yang damai selalu bikin pikiran jadi lebih rileks.',
            'caption' => 'Sedikit jeda dan ketenangan di sela hari 🎶',
        ];
    }
}
