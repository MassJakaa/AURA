<?php

namespace Tests\Feature;

use App\Enums\MomentType;
use App\Models\Character;
use App\Models\Moment;
use App\Models\MomentDelivery;
use App\Models\User;
use Database\Seeders\AuraLifeSystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MomentApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Character $character;
    protected Moment $moment;
    protected MomentDelivery $delivery;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuraLifeSystemSeeder::class);

        $this->user = User::factory()->create();
        $this->character = Character::first();

        $this->moment = Moment::create([
            'character_id' => $this->character->id,
            'moment_type' => MomentType::PHOTO_MOMENT,
            'title' => 'Momen Membaca',
            'narrative' => 'Menemukan buku yang menarik sore ini.',
            'caption' => 'Buku dan secangkir teh 🍵',
            'status' => 'ready',
        ]);

        $this->delivery = MomentDelivery::create([
            'moment_id' => $this->moment->id,
            'user_id' => $this->user->id,
            'status' => 'delivered',
            'delivered_at' => now(),
            'idempotency_key' => 'delivery_test_1',
        ]);
    }

    public function test_user_can_list_their_moments(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/moments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'type', 'title', 'narrative', 'caption', 'status'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Momen Membaca', $response->json('data.0.title'));
    }

    public function test_user_can_record_interaction(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->postJson(
            "/api/v1/moments/{$this->moment->id}/interaction",
            ['interaction_type' => 'liked']
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Interaction recorded successfully',
            ]);

        $this->assertDatabaseHas('moment_interactions', [
            'moment_delivery_id' => $this->delivery->id,
            'user_id' => $this->user->id,
            'interaction_type' => 'liked',
        ]);
    }
}
