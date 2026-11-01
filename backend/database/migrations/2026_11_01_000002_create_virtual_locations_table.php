<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique(); // aura_bedroom, aura_living_room, virtual_cafe, etc.
            $table->string('name');
            $table->string('category')->default('indoor'); // indoor, outdoor, commercial, public
            $table->text('description')->nullable();
            $table->string('environment_asset_path')->nullable();
            $table->json('allowed_activities')->nullable(); // JSON list of allowed activity categories/names
            $table->string('visual_theme')->nullable(); // warm_morning, twilight, cosy_interior
            $table->boolean('is_available')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['code', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_locations');
    }
};
