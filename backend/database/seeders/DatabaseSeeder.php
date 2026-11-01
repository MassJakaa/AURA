<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserCompanionPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AuraLifeSystemSeeder::class);

        // Seed demo user for testing and verification
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password123'),
            ]
        );

        UserCompanionPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'communication_style' => 'warm_natural',
                'interaction_frequency' => 'normal',
                'preferred_topics' => ['books', 'coffee', 'photography'],
                'photo_sharing_enabled' => true,
                'proactive_messaging_enabled' => true,
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '08:00',
                'max_daily_proactive_messages' => 2,
                'max_daily_photo_moments' => 1,
                'timezone' => 'Asia/Jakarta',
                'memory_enabled' => true,
                'notifications_enabled' => true,
            ]
        );
    }
}
