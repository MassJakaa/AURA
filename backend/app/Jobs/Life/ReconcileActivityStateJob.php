<?php

namespace App\Jobs\Life;

use App\Models\Character;
use App\Services\Activity\ActivityEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReconcileActivityStateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function handle(ActivityEngine $engine): void
    {
        $characters = Character::all();
        foreach ($characters as $character) {
            $engine->reconcileCurrentActivity($character);
        }
    }
}
