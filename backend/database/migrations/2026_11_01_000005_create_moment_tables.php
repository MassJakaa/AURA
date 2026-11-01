<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moment_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('category')->default('activity');
            $table->string('moment_type')->default('activity_moment');
            $table->string('title_template');
            $table->text('narrative_template');
            $table->text('caption_template')->nullable();
            $table->string('required_visual_type')->nullable(); // none, character, environment
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('moments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignUuid('activity_instance_id')->nullable()->constrained('activity_instances')->nullOnDelete();
            $table->foreignUuid('visual_asset_id')->nullable()->constrained('visual_assets')->nullOnDelete();
            $table->foreignUuid('visual_generation_job_id')->nullable()->constrained('visual_generation_jobs')->nullOnDelete();
            $table->string('moment_type')->default('activity_moment'); // activity_moment, photo_moment, environment_moment, discovery_moment, reflection_moment, special_moment
            $table->string('title');
            $table->text('narrative');
            $table->text('caption')->nullable();
            $table->string('visual_requirement')->default('optional'); // none, optional, required
            $table->integer('priority')->default(1);
            $table->string('status')->default('draft'); // draft, evaluating, approved, generating, ready, delivered, failed, expired, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('idempotency_key')->unique()->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['character_id', 'status', 'created_at']);
            $table->index(['moment_type', 'status']);
        });

        Schema::create('moment_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('moment_id')->constrained('moments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('conversation_id')->nullable(); // linked conversation
            $table->uuid('message_id')->nullable(); // linked chat message
            $table->string('status')->default('pending'); // pending, sent, delivered, read, failed, suppressed
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('idempotency_key')->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['moment_id', 'user_id']);
        });

        Schema::create('moment_interactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('moment_delivery_id')->constrained('moment_deliveries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('interaction_type'); // viewed, liked, replied, dismissed, saved
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'interaction_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moment_interactions');
        Schema::dropIfExists('moment_deliveries');
        Schema::dropIfExists('moments');
        Schema::dropIfExists('moment_templates');
    }
};
