<?php

namespace App\Jobs\Life;

use App\Models\ActivityInstance;
use App\Models\Character;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDailyLifeSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $todayStart = Carbon::now('UTC')->startOfDay();
        $characters = Character::all();

        foreach ($characters as $character) {
            $count = ActivityInstance::where('character_id', $character->id)
                ->where('start_time', '>=', $todayStart)
                ->count();

            Log::info("Daily summary for {$character->name}: {$count} activity instances logged today.");
        }
    }
}
