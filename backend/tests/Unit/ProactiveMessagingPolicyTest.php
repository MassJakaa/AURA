<?php

namespace Tests\Unit;

use App\Enums\MomentType;
use App\Models\Character;
use App\Models\Moment;
use App\Models\MomentDelivery;
use App\Models\User;
use App\Models\UserCompanionPreference;
use App\Services\Personalization\ProactiveMessagingPolicy;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProactiveMessagingPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Character $character;
    protected Moment $moment;
    protected ProactiveMessagingPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->character = Character::create(['name' => 'AURA', 'code' => 'aura_char']);
        $this->moment = Moment::create([
            'character_id' => $this->character->id,
            'moment_type' => MomentType::PHOTO_MOMENT,
            'title' => 'Moment Test',
            'narrative' => 'Testing narrative',
            'status' => 'ready',
        ]);

        $this->policy = new ProactiveMessagingPolicy();
    }

    public function test_suppresses_when_proactive_messaging_disabled(): void
    {
        UserCompanionPreference::create([
            'user_id' => $this->user->id,
            'proactive_messaging_enabled' => false,
            'quiet_hours_start' => '22:00',
            'quiet_hours_end' => '08:00',
        ]);

        $result = $this->policy->evaluateDelivery($this->user, $this->moment);
        $this->assertFalse($result['can_deliver']);
        $this->assertStringContainsString('disabled', $result['reason']);
    }

    public function test_suppresses_duplicate_moment_delivery(): void
    {
        UserCompanionPreference::create([
            'user_id' => $this->user->id,
            'proactive_messaging_enabled' => true,
            'quiet_hours_start' => '02:00',
            'quiet_hours_end' => '04:00',
        ]);

        MomentDelivery::create([
            'user_id' => $this->user->id,
            'moment_id' => $this->moment->id,
            'status' => 'delivered',
            'delivered_at' => Carbon::now()->subHours(5),
            'idempotency_key' => 'test_idem_dup',
        ]);

        $result = $this->policy->evaluateDelivery($this->user, $this->moment);
        $this->assertFalse($result['can_deliver']);
        $this->assertStringContainsString('already been delivered', $result['reason']);
    }

    public function test_allows_delivery_when_all_conditions_satisfied(): void
    {
        UserCompanionPreference::create([
            'user_id' => $this->user->id,
            'proactive_messaging_enabled' => true,
            'photo_sharing_enabled' => true,
            'quiet_hours_start' => '01:00',
            'quiet_hours_end' => '02:00',
            'timezone' => 'UTC',
        ]);

        Carbon::setTestNow(Carbon::parse('2026-11-01 12:00:00', 'UTC'));

        $result = $this->policy->evaluateDelivery($this->user, $this->moment);
        $this->assertTrue($result['can_deliver']);
        $this->assertEquals('Eligible for proactive delivery', $result['reason']);
    }
}
