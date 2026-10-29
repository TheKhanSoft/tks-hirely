<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->string('scope')->default('APPLICATION'); // APPLICATION, APPLICANT, SYSTEM
            $table->string('badge_color')->default('primary'); // primary, success, warning, danger, purple, info, neutral, amber
            $table->string('badge_bg')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            
            // Workflow governance rules
            $table->boolean('is_locked_state')->default(false);
            $table->boolean('is_terminal')->default(false);
            $table->boolean('is_system_default')->default(false);
            
            // Permitted progression branches & gate requirements
            $table->json('allowed_transitions')->nullable();
            $table->json('requirements')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_statuses');
    }
};
