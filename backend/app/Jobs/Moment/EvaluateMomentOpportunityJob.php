<?php

namespace App\Jobs\Moment;

use App\Models\Character;
use App\Models\User;
use App\Services\Activity\ActivityEngine;
use App\Services\Moment\MomentDeliveryService;
use App\Services\Moment\MomentGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateMomentOpportunityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 90;

    public function handle(
        ActivityEngine $activityEngine,
        MomentGenerator $momentGenerator,
        MomentDeliveryService $deliveryService
    ): void {
        $characters = Character::all();

        foreach ($characters as $character) {
            $activity = $activityEngine->getCurrentActivity($character);
            if (!$activity || !$activity->is_shareable) {
                continue;
            }

            // Generate or fetch existing moment for current activity
            $moment = $momentGenerator->generateFromActivity($character, $activity);
            if (!$moment) {
                continue;
            }

            // Deliver moment to users who have proactive messaging enabled
            $users = User::whereHas('companionPreference', function ($q) {
                $q->where('proactive_messaging_enabled', true);
            })->get();

            foreach ($users as $user) {
                try {
                    $deliveryService->deliverToUser($user, $moment);
                } catch (\Throwable $e) {
                    Log::error("Failed to evaluate/deliver moment to user {$user->id}: {$e->getMessage()}");
                }
            }
        }
    }
}
