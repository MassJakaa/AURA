<?php

use App\Jobs\Life\EvaluateLifeScheduleJob;
use App\Jobs\Life\GenerateDailyLifeSummaryJob;
use App\Jobs\Life\ReconcileActivityStateJob;
use App\Jobs\Moment\CleanupExpiredMomentsJob;
use App\Jobs\Moment\EvaluateMomentOpportunityJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// AURA Life System periodic schedulers with distributed locks
Schedule::job(new EvaluateLifeScheduleJob())
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->name('aura_evaluate_life_schedule');

Schedule::job(new EvaluateMomentOpportunityJob())
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->name('aura_evaluate_moment_opportunity');

Schedule::job(new ReconcileActivityStateJob())
    ->hourly()
    ->withoutOverlapping()
    ->name('aura_reconcile_activity_state');

Schedule::job(new CleanupExpiredMomentsJob())
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->name('aura_cleanup_expired_moments');

Schedule::job(new GenerateDailyLifeSummaryJob())
    ->dailyAt('23:59')
    ->withoutOverlapping()
    ->name('aura_daily_life_summary');
