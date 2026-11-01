<?php

namespace App\Jobs\Life;

use App\Models\Character;
use App\Services\Activity\ActivityEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateLifeScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function handle(ActivityEngine $engine): void
    {
        $characters = Character::all();

        foreach ($characters as $character) {
            try {
                $activity = $engine->getCurrentActivity($character);
                if ($activity) {
                    Log::info("Life schedule evaluated for {$character->name}: Current activity is '{$activity->name}' ({$activity->status->value})");
                }
            } catch (\Throwable $e) {
                Log::error("Failed to evaluate life schedule for character {$character->id}: {$e->getMessage()}");
            }
        }
    }
}
