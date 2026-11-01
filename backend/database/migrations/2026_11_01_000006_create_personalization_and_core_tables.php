<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_companion_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('communication_style')->default('warm_natural'); // warm_natural, calm_gentle, playful
            $table->string('interaction_frequency')->default('normal'); // low, normal, frequent
            $table->json('preferred_topics')->nullable(); // e.g. ["photography", "coffee", "books"]
            $table->json('avoided_topics')->nullable();
            $table->boolean('photo_sharing_enabled')->default(true);
            $table->boolean('proactive_messaging_enabled')->default(true);
            $table->string('quiet_hours_start')->default('22:00');
            $table->string('quiet_hours_end')->default('08:00');
            $table->integer('max_daily_proactive_messages')->default(2);
            $table->integer('max_daily_photo_moments')->default(1);
            $table->string('timezone')->default('Asia/Jakarta');
            $table->boolean('memory_enabled')->default(true);
            $table->boolean('notifications_enabled')->default(true);
            $table->string('relationship_context')->default('friendly_companion');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('user_moment_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('moment_type');
            $table->boolean('is_enabled')->default(true);
            $table->integer('preference_score')->default(5); // 1-10 scale
            $table->timestamps();

            $table->unique(['user_id', 'moment_type']);
        });

        Schema::create('user_activity_contexts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('last_shared_activity_id')->nullable()->constrained('activities')->nullOnDelete();
            $table->timestamp('last_shared_at')->nullable();
            $table->json('conversation_context')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'last_shared_at']);
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active', 'last_message_at']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->string('sender_type'); // user, companion, system
            $table->string('sender_id')->nullable();
            $table->string('message_type')->default('text'); // text, image, activity_moment, photo_moment, environment_moment, system_event
            $table->text('content');
            $table->foreignUuid('moment_id')->nullable()->constrained('moments')->nullOnDelete();
            $table->foreignUuid('activity_instance_id')->nullable()->constrained('activity_instances')->nullOnDelete();
            $table->foreignUuid('visual_asset_id')->nullable()->constrained('visual_assets')->nullOnDelete();
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['moment_id', 'message_type']);
        });

        Schema::create('memories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('category')->default('preference'); // preference, fact, topic, relationship, moment
            $table->text('content');
            $table->decimal('importance_score', 3, 2)->default(0.50);
            $table->foreignUuid('source_message_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active', 'category']);
        });

        Schema::create('memory_embeddings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('memory_id')->constrained('memories')->cascadeOnDelete();
            $table->string('embedding_model')->default('text-embedding-3-small');
            $table->text('embedding_vector'); // Stores JSON array or vector literal
            $table->integer('dimensions')->default(1536);
            $table->timestamps();

            $table->index(['memory_id']);
        });

        Schema::create('initiative_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('moment_id')->nullable()->constrained('moments')->nullOnDelete();
            $table->foreignUuid('message_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->string('trigger_type'); // scheduled_evaluation, spontaneous_event, follow_up
            $table->string('decision'); // sent, suppressed_quiet_hours, suppressed_frequency_limit, suppressed_opt_out, suppressed_recent_chat
            $table->text('reason')->nullable();
            $table->string('channel')->default('in_app'); // in_app, push_notification
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'decision', 'created_at']);
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('platform')->default('android'); // android, ios, web
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });

        Schema::create('memory_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('memory_id')->constrained('memories')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('feedback_type'); // correct, incorrect, outdated, sensitive
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memory_feedback');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('initiative_logs');
        Schema::dropIfExists('memory_embeddings');
        Schema::dropIfExists('memories');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('user_activity_contexts');
        Schema::dropIfExists('user_moment_preferences');
        Schema::dropIfExists('user_companion_preferences');
    }
};
