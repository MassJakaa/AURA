<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visual_prompts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prompt_code')->unique();
            $table->string('version')->default('1.0.0');
            $table->text('base_prompt');
            $table->text('negative_prompt')->nullable();
            $table->string('style_preset')->default('2d_soft_anime');
            $table->json('parameters')->nullable(); // cfg, steps, aspect_ratio
            $table->timestamps();
        });

        Schema::create('visual_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('character_id')->nullable()->constrained('characters')->nullOnDelete();
            $table->foreignUuid('activity_id')->nullable()->constrained('activities')->nullOnDelete();
            $table->foreignUuid('location_id')->nullable()->constrained('virtual_locations')->nullOnDelete();
            $table->string('asset_type'); // character_illustration, environment, placeholder
            $table->string('asset_category')->default('contextual'); // reusable, contextual, newly_generated
            $table->string('file_path');
            $table->string('cdn_url')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('dimensions')->default('1024x1024');
            $table->string('mime_type')->default('image/webp');
            $table->string('approval_status')->default('draft');
            $table->string('master_reference_version')->nullable();
            $table->foreignUuid('visual_prompt_id')->nullable()->constrained('visual_prompts')->nullOnDelete();
            $table->json('metadata')->nullable(); // pose, expression, outfit, lighting
            $table->timestamps();

            $table->index(['asset_type', 'approval_status']);
            $table->index(['activity_id', 'location_id']);
        });

        Schema::create('visual_generation_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('correlation_id')->index();
            $table->string('provider')->default('mock'); // mock, stability, dall_e, comfy_ui
            $table->text('prompt_text');
            $table->text('negative_prompt')->nullable();
            $table->json('parameters')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled
            $table->integer('attempts_count')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->decimal('cost_units', 8, 4)->default(0);
            $table->string('storage_path')->nullable();
            $table->string('result_url')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('visual_generation_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_id')->constrained('visual_generation_jobs')->cascadeOnDelete();
            $table->integer('attempt_number');
            $table->string('provider');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->integer('latency_ms')->default(0);
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['job_id', 'attempt_number']);
        });

        Schema::create('visual_asset_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('visual_asset_id')->constrained('visual_assets')->cascadeOnDelete();
            $table->string('reference_type'); // master_sheet, moodboard, pose_guide
            $table->string('reference_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visual_asset_references');
        Schema::dropIfExists('visual_generation_attempts');
        Schema::dropIfExists('visual_generation_jobs');
        Schema::dropIfExists('visual_assets');
        Schema::dropIfExists('visual_prompts');
    }
};
