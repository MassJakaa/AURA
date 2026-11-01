<?php

namespace App\Services\Activity;

use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\ActivityInstance;
use App\Models\ActivityTransition;
use App\Models\Character;
use App\Services\Life\LifeClockService;
use App\Services\Life\LifeSchedulerService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActivityEngine
{
    public function __construct(
        protected LifeClockService $clock,
        protected LifeSchedulerService $scheduler
    ) {}

    /**
     * Get or reconcile the current active activity instance for a character.
     */
    public function getCurrentActivity(Character $character): ?ActivityInstance
    {
        $nowUtc = $this->clock->getCurrentUtcTime();

        // 1. Look for currently active activity
        $active = ActivityInstance::with(['activity', 'location'])
            ->where('character_id', $character->id)
            ->where('status', ActivityStatus::ACTIVE)
            ->where('start_time', '<=', $nowUtc)
            ->where('expected_end_time', '>', $nowUtc)
            ->latest('start_time')
            ->first();

        if ($active) {
            return $active;
        }

        // 2. If no active instance or current instance has ended, evaluate and transition
        return $this->reconcileCurrentActivity($character);
    }

    /**
     * Reconcile character activities against schedule rules using atomic locking.
     */
    public function reconcileCurrentActivity(Character $character): ?ActivityInstance
    {
        $lockKey = "aura_activity_lock_{$character->id}";
        $lock = Cache::lock($lockKey, 10);

        return $lock->get(function () use ($character) {
            return DB::transaction(function () use ($character) {
                $nowUtc = $this->clock->getCurrentUtcTime();

                // Complete any active activity that has reached its expected end time
                $expiredInstances = ActivityInstance::where('character_id', $character->id)
                    ->where('status', ActivityStatus::ACTIVE)
                    ->where('expected_end_time', '<=', $nowUtc)
                    ->get();

                foreach ($expiredInstances as $expired) {
                    $this->transition($expired, ActivityStatus::COMPLETED, 'Scheduled end time reached');
                }

                // Check for scheduled activity that should be activated
                $dueScheduled = ActivityInstance::where('character_id', $character->id)
                    ->where('status', ActivityStatus::SCHEDULED)
                    ->where('start_time', '<=', $nowUtc)
                    ->where('expected_end_time', '>', $nowUtc)
                    ->first();

                if ($dueScheduled) {
                    $this->transition($dueScheduled, ActivityStatus::ACTIVE, 'Activated by scheduler evaluation');
                    return $dueScheduled->fresh(['activity', 'location']);
                }

                // Find matching rule from character's schedule
                $rule = $this->scheduler->findMatchingRule($character);
                if (!$rule) {
                    return null;
                }

                $times = $this->scheduler->calculateInstanceTimes($rule, $character);
                $idempotencyKey = "rule_{$rule->id}_{$times['start_time']->format('Ymd_Hi')}";

                $instance = ActivityInstance::firstOrCreate(
                    ['idempotency_key' => $idempotencyKey],
                    [
                        'character_id' => $character->id,
                        'location_id' => $rule->location_id,
                        'name' => $rule->activity_name,
                        'category' => $rule->activity_category,
                        'description' => "Routine scheduled: {$rule->activity_name}",
                        'start_time' => $times['start_time'],
                        'expected_end_time' => $times['expected_end_time'],
                        'status' => ActivityStatus::ACTIVE,
                        'priority' => 1,
                        'mood_presentation' => $rule->mood_presentation,
                        'is_shareable' => $rule->shareable_flag,
                        'metadata' => ['rule_id' => $rule->id],
                    ]
                );

                if ($instance->status !== ActivityStatus::ACTIVE && $instance->status->canTransitionTo(ActivityStatus::ACTIVE)) {
                    $this->transition($instance, ActivityStatus::ACTIVE, 'Activated on rule match');
                }

                return $instance->fresh(['activity', 'location']);
            });
        });
    }

    /**
     * Transition an activity instance to a new state with strict validation.
     */
    public function transition(ActivityInstance $instance, ActivityStatus $targetStatus, ?string $reason = null): bool
    {
        if (!$instance->status->canTransitionTo($targetStatus)) {
            Log::warning("Invalid activity transition attempted: {$instance->status->value} -> {$targetStatus->value}", [
                'activity_instance_id' => $instance->id,
                'target_status' => $targetStatus->value,
            ]);
            return false;
        }

        return DB::transaction(function () use ($instance, $targetStatus, $reason) {
            $fromStatus = $instance->status;

            $instance->status = $targetStatus;
            if ($targetStatus === ActivityStatus::COMPLETED) {
                $instance->actual_end_time = Carbon::now('UTC');
            }
            $instance->save();

            ActivityTransition::create([
                'activity_instance_id' => $instance->id,
                'from_status' => $fromStatus,
                'to_status' => $targetStatus,
                'reason' => $reason,
                'transitioned_at' => Carbon::now('UTC'),
            ]);

            return true;
        });
    }

    /**
     * Get planned next activity based on schedule.
     */
    public function getNextPlannedActivity(Character $character, ActivityInstance $current): ?array
    {
        $timezone = $character->schedules()->where('is_active', true)->first()?->timezone ?? 'Asia/Jakarta';
        $currentEndTime = CarbonImmutable::instance($current->expected_end_time)->setTimezone($timezone);

        $nextRule = $this->scheduler->findMatchingRule($character, $currentEndTime->addMinutes(1));
        if (!$nextRule) {
            return null;
        }

        $times = $this->scheduler->calculateInstanceTimes($nextRule, $character, $currentEndTime->addMinutes(1));

        return [
            'name' => $nextRule->activity_name,
            'category' => $nextRule->activity_category->value,
            'location_name' => $nextRule->location?->name,
            'start_time' => $times['start_time']->toIso8601String(),
            'expected_end_time' => $times['expected_end_time']->toIso8601String(),
        ];
    }
}
