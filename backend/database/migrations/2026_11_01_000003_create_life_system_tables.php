<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('life_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('name');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['character_id', 'is_active']);
        });

        Schema::create('life_schedule_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('life_schedule_id')->constrained('life_schedules')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->nullable(); // 0 (Sun) to 6 (Sat), null = all days
            $table->time('start_time_of_day');
            $table->time('end_time_of_day');
            $table->string('activity_name');
            $table->string('activity_category')->default('daily_routine');
            $table->foreignUuid('location_id')->nullable()->constrained('virtual_locations')->nullOnDelete();
            $table->string('mood_presentation')->nullable();
            $table->boolean('shareable_flag')->default(true);
            $table->string('recurrence_rule')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['life_schedule_id', 'day_of_week', 'start_time_of_day']);
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->default('daily_routine');
            $table->text('description')->nullable();
            $table->foreignUuid('default_location_id')->nullable()->constrained('virtual_locations')->nullOnDelete();
            $table->integer('base_duration_minutes')->default(60);
            $table->string('default_mood')->nullable();
            $table->boolean('default_shareable')->default(true);
            $table->string('recurrence_rule')->nullable();
            $table->text('visual_prompt_template')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['character_id', 'category']);
        });

        Schema::create('activity_instances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_id')->nullable()->constrained('activities')->nullOnDelete();
            $table->foreignUuid('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignUuid('location_id')->nullable()->constrained('virtual_locations')->nullOnDelete();
            $table->string('name');
            $table->string('category')->default('daily_routine');
            $table->text('description')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('expected_end_time');
            $table->timestamp('actual_end_time')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, active, paused, completed, skipped, cancelled
            $table->integer('priority')->default(1);
            $table->string('mood_presentation')->nullable();
            $table->boolean('is_shareable')->default(true);
            $table->string('recurrence_rule')->nullable();
            $table->string('idempotency_key')->unique()->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['character_id', 'status', 'start_time']);
            $table->index(['start_time', 'expected_end_time']);
        });

        Schema::create('activity_transitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_instance_id')->constrained('activity_instances')->cascadeOnDelete();
            $table->string('from_status');
            $table->string('to_status');
            $table->string('reason')->nullable();
            $table->timestamp('transitioned_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['activity_instance_id', 'transitioned_at']);
        });

        Schema::create('activity_location_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignUuid('location_id')->constrained('virtual_locations')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['activity_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_location_assignments');
        Schema::dropIfExists('activity_transitions');
        Schema::dropIfExists('activity_instances');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('life_schedule_rules');
        Schema::dropIfExists('life_schedules');
    }
};
