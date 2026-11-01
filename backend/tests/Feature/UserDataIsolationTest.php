<?php

namespace Tests\Feature;

use App\Enums\MomentType;
use App\Models\Character;
use App\Models\Moment;
use App\Models\MomentDelivery;
use App\Models\User;
use App\Models\UserCompanionPreference;
use Database\Seeders\AuraLifeSystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected Moment $momentA;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuraLifeSystemSeeder::class);

        $this->userA = User::factory()->create(['email' => 'user_a@test.com']);
        $this->userB = User::factory()->create(['email' => 'user_b@test.com']);

        $character = Character::first();

        // Moment delivered strictly to User A
        $this->momentA = Moment::create([
            'character_id' => $character->id,
            'moment_type' => MomentType::ACTIVITY_MOMENT,
            'title' => 'Moment Exclusive A',
            'narrative' => 'Special narrative for user A',
            'status' => 'ready',
        ]);

        MomentDelivery::create([
            'moment_id' => $this->momentA->id,
            'user_id' => $this->userA->id,
            'status' => 'delivered',
            'delivered_at' => now(),
            'idempotency_key' => 'del_iso_a',
        ]);

        UserCompanionPreference::create([
            'user_id' => $this->userA->id,
            'communication_style' => 'warm_natural',
            'preferred_topics' => ['photography'],
        ]);

        UserCompanionPreference::create([
            'user_id' => $this->userB->id,
            'communication_style' => 'calm_gentle',
            'preferred_topics' => ['classical_music'],
        ]);
    }

    public function test_user_b_cannot_view_user_a_moment(): void
    {
        // User B attempts to access Moment A
        $response = $this->actingAs($this->userB, 'sanctum')->getJson("/api/v1/moments/{$this->momentA->id}");

        $response->assertStatus(404);
    }

    public function test_user_preferences_are_isolated(): void
    {
        // User B views own preference
        $responseB = $this->actingAs($this->userB, 'sanctum')->getJson('/api/v1/companion/preferences');
        $responseB->assertStatus(200);
        $this->assertEquals('calm_gentle', $responseB->json('data.communication_style'));
        $this->assertEquals(['classical_music'], $responseB->json('data.preferred_topics'));

        // User A views own preference
        $responseA = $this->actingAs($this->userA, 'sanctum')->getJson('/api/v1/companion/preferences');
        $responseA->assertStatus(200);
        $this->assertEquals('warm_natural', $responseA->json('data.communication_style'));
        $this->assertEquals(['photography'], $responseA->json('data.preferred_topics'));
    }
}
