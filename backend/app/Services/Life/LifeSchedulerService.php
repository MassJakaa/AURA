<?php

namespace App\Services\Life;

use App\Models\Character;
use App\Models\LifeSchedule;
use App\Models\LifeScheduleRule;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class LifeSchedulerService
{
    public function __construct(
        protected LifeClockService $clock
    ) {}

    /**
     * Find the active rule for character at given time.
     */
    public function findMatchingRule(Character $character, ?CarbonImmutable $time = null): ?LifeScheduleRule
    {
        $schedule = $character->schedules()
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->first();

        if (!$schedule) {
            return null;
        }

        $charTime = $time ?? $this->clock->getCharacterTime($character);
        $timeStr = $charTime->format('H:i:s');
        $dayOfWeek = $charTime->dayOfWeek; // 0 (Sun) - 6 (Sat)

        // Query rules matching day of week (or null for all days)
        $rules = $schedule->rules()
            ->where(function ($q) use ($dayOfWeek) {
                $q->whereNull('day_of_week')->orWhere('day_of_week', $dayOfWeek);
            })
            ->get();

        foreach ($rules as $rule) {
            $start = $rule->start_time_of_day;
            $end = $rule->end_time_of_day;

            if ($start <= $end) {
                // Same day window: e.g. 08:30 to 11:30
                if ($timeStr >= $start && $timeStr < $end) {
                    return $rule;
                }
            } else {
                // Overnight window: e.g. 23:00 to 07:00
                if ($timeStr >= $start || $timeStr < $end) {
                    return $rule;
                }
            }
        }

        return null;
    }

    /**
     * Calculate start and end UTC timestamps for an activity instance based on a rule.
     *
     * @return array{start_time: Carbon, expected_end_time: Carbon}
     */
    public function calculateInstanceTimes(LifeScheduleRule $rule, Character $character, ?CarbonImmutable $time = null): array
    {
        $timezone = $rule->schedule->timezone ?? 'Asia/Jakarta';
        $charTime = $time ?? $this->clock->getCharacterTime($character);
        $dateStr = $charTime->format('Y-m-d');

        $startStr = $rule->start_time_of_day;
        $endStr = $rule->end_time_of_day;

        $localStart = Carbon::parse("$dateStr $startStr", $timezone);
        $localEnd = Carbon::parse("$dateStr $endStr", $timezone);

        if ($startStr > $endStr) {
            // Overnight rule: e.g. 23:00 to 07:00
            if ($charTime->format('H:i:s') < $endStr) {
                // It is currently morning (e.g. 02:00), started yesterday at 23:00
                $localStart->subDay();
            } else {
                // Started today at 23:00, ends tomorrow at 07:00
                $localEnd->addDay();
            }
        }

        return [
            'start_time' => $localStart->setTimezone('UTC'),
            'expected_end_time' => $localEnd->setTimezone('UTC'),
        ];
    }
}
