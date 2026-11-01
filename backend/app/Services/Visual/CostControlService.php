<?php

namespace App\Services\Visual;

use App\Models\VisualGenerationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CostControlService
{
    protected float $dailyBudgetLimit = 50.0; // Cost units / dollar budget
    protected int $maxDailyGenerations = 100;
    protected int $maxAttemptsPerJob = 3;

    /**
     * Check if visual generation can proceed within budget constraints.
     */
    public function canGenerate(): bool
    {
        $todayStart = Carbon::now('UTC')->startOfDay();
        
        $totalGenerationsToday = VisualGenerationJob::where('created_at', '>=', $todayStart)->count();
        if ($totalGenerationsToday >= $this->maxDailyGenerations) {
            return false;
        }

        $totalCostToday = VisualGenerationJob::where('created_at', '>=', $todayStart)->sum('cost_units');
        if ($totalCostToday >= $this->dailyBudgetLimit) {
            return false;
        }

        return true;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttemptsPerJob;
    }
}
