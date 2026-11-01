<?php

namespace Tests\Unit;

use App\Enums\ActivityCategory;
use App\Enums\ActivityStatus;
use App\Models\ActivityInstance;
use App\Models\Character;
use App\Services\Activity\ActivityEngine;
use App\Services\Life\LifeClockService;
use App\Services\Life\LifeSchedulerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityStateTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_state_transitions(): void
    {
        $character = Character::create([
            'name' => 'AURA',
            'code' => 'aura_test',
        ]);

        $instance = ActivityInstance::create([
            'character_id' => $character->id,
            'name' => 'Membaca Buku',
            'category' => ActivityCategory::HOBBY,
            'start_time' => Carbon::now()->subMinutes(30),
            'expected_end_time' => Carbon::now()->addMinutes(30),
            'status' => ActivityStatus::SCHEDULED,
        ]);

        $clock = new LifeClockService();
        $scheduler = new LifeSchedulerService($clock);
        $engine = new ActivityEngine($clock, $scheduler);

        // 1. scheduled -> active
        $this->assertTrue($engine->transition($instance, ActivityStatus::ACTIVE, 'Starting activity'));
        $this->assertEquals(ActivityStatus::ACTIVE, $instance->fresh()->status);

        // 2. active -> paused
        $this->assertTrue($engine->transition($instance, ActivityStatus::PAUSED, 'User requested pause'));
        $this->assertEquals(ActivityStatus::PAUSED, $instance->fresh()->status);

        // 3. paused -> active
        $this->assertTrue($engine->transition($instance, ActivityStatus::ACTIVE, 'Resuming'));
        $this->assertEquals(ActivityStatus::ACTIVE, $instance->fresh()->status);

        // 4. active -> completed
        $this->assertTrue($engine->transition($instance, ActivityStatus::COMPLETED, 'Finished'));
        $this->assertEquals(ActivityStatus::COMPLETED, $instance->fresh()->status);
        $this->assertNotNull($instance->fresh()->actual_end_time);
    }

    public function test_invalid_state_transition_is_rejected(): void
    {
        $character = Character::create([
            'name' => 'AURA',
            'code' => 'aura_test',
        ]);

        $instance = ActivityInstance::create([
            'character_id' => $character->id,
            'name' => 'Selesai Aktivitas',
            'category' => ActivityCategory::DAILY_ROUTINE,
            'start_time' => Carbon::now()->subHours(2),
            'expected_end_time' => Carbon::now()->subHour(),
            'actual_end_time' => Carbon::now()->subHour(),
            'status' => ActivityStatus::COMPLETED,
        ]);

        $clock = new LifeClockService();
        $scheduler = new LifeSchedulerService($clock);
        $engine = new ActivityEngine($clock, $scheduler);

        // completed cannot transition to active
        $this->assertFalse($engine->transition($instance, ActivityStatus::ACTIVE));
        $this->assertEquals(ActivityStatus::COMPLETED, $instance->fresh()->status);
    }
}
