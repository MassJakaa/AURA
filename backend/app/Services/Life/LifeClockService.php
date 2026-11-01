<?php

namespace App\Services\Life;

use App\Models\Character;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class LifeClockService
{
    /**
     * Get current virtual time in UTC.
     */
    public function getCurrentUtcTime(): CarbonImmutable
    {
        return CarbonImmutable::now('UTC');
    }

    /**
     * Get current time formatted in character's primary timezone.
     */
    public function getCharacterTime(Character $character): CarbonImmutable
    {
        $timezone = $character->schedules()->where('is_active', true)->first()?->timezone ?? 'Asia/Jakarta';
        return CarbonImmutable::now($timezone);
    }

    /**
     * Convert UTC timestamp to target timezone.
     */
    public function convertToTimezone(\DateTimeInterface $dateTime, string $timezone): Carbon
    {
        return Carbon::instance($dateTime)->setTimezone($timezone);
    }

    /**
     * Format time of day for rule matching (HH:MM:SS).
     */
    public function getTimeOfDayString(CarbonImmutable $dateTime): string
    {
        return $dateTime->format('H:i:s');
    }
}
