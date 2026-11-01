<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->default('AURA');
            $table->string('code')->unique()->default('aura_main');
            $table->string('gender_presentation')->default('female');
            $table->string('visual_style')->default('2d_soft_anime');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('character_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('character_id')->constrained('characters')->cascadeOnDelete();
            $table->string('version_tag'); // e.g. v1.0.0-placeholder, v1.1.0-master
            $table->boolean('is_active')->default(false);
            $table->text('release_notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['character_id', 'version_tag']);
        });

        Schema::create('character_personality_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignUuid('version_id')->nullable()->constrained('character_versions')->nullOnDelete();
            $table->string('base_tone')->default('warm_friendly');
            $table->decimal('empathy_level', 3, 2)->default(0.85);
            $table->decimal('warmth', 3, 2)->default(0.90);
            $table->string('verbosity')->default('balanced');
            $table->json('allowed_topics')->nullable();
            $table->json('forbidden_topics')->nullable();
            $table->text('prompt_system_instruction')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('character_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignUuid('version_id')->nullable()->constrained('character_versions')->nullOnDelete();
            $table->string('asset_type'); // master_reference, placeholder, environment, outfit, expression
            $table->string('name');
            $table->string('file_path');
            $table->string('cdn_url')->nullable();
            $table->string('approval_status')->default('draft'); // draft, pending_approval, approved, rejected
            $table->boolean('is_master_reference')->default(false);
            $table->json('metadata')->nullable(); // poses, expressions, colors, hair, eyes
            $table->timestamps();

            $table->index(['character_id', 'asset_type', 'approval_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_assets');
        Schema::dropIfExists('character_personality_configs');
        Schema::dropIfExists('character_versions');
        Schema::dropIfExists('characters');
    }
};
