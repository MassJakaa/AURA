<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\User;
use Database\Seeders\AuraLifeSystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LifeSystemApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuraLifeSystemSeeder::class);
        $this->user = User::factory()->create();
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/v1/life/status');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_retrieve_life_status(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/life/status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'character' => ['id', 'name', 'status'],
                    'virtual_time' => ['current_utc', 'character_local', 'character_timezone'],
                    'current_activity',
                    'next_planned_activity',
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals('AURA', $response->json('data.character.name'));
    }

    public function test_character_appearance_reports_placeholder_status(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/character/appearance');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals('2d_soft_anime', $data['visual_style']);
        $this->assertTrue($data['placeholder_active']);
        $this->assertFalse($data['master_sheet_approved']);
    }

    public function test_virtual_locations_list(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/v1/life/locations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'code', 'name', 'category', 'is_available'],
                ],
            ]);

        $this->assertGreaterThan(0, count($response->json('data')));
    }
}
