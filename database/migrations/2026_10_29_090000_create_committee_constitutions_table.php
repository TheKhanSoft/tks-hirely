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
        Schema::create('committee_constitutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('organization_sector')->default('ACADEMIC'); // ACADEMIC, CORPORATE, HEALTHCARE, PUBLIC_SECTOR, JUDICIAL, DEFENSE, GENERAL
            $table->text('description')->nullable();
            
            // Operational pipeline & routing flow definition
            $table->json('routing_flow')->nullable();
            
            // Dynamic qualification & eligibility criteria
            $table->json('eligibility_criteria')->nullable();
            
            // Statutory quorum & member allocation quotas
            $table->unsignedInteger('required_total')->default(5);
            $table->unsignedInteger('required_internal')->default(2);
            $table->unsignedInteger('required_external')->default(2);
            $table->unsignedInteger('required_international')->default(1);
            $table->unsignedInteger('required_independent')->default(0);
            $table->unsignedInteger('required_peer_reviewers')->default(3);
            $table->boolean('enforce_strict_quota')->default(true);
            
            // Dynamic roles breakdown (repeater array)
            $table->json('roles_breakdown')->nullable();
            
            // Meta & flags
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });

        // Add optional constitution_id foreign key to campaigns if not present
        if (Schema::hasTable('campaigns') && !Schema::hasColumn('campaigns', 'constitution_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->foreignId('constitution_id')->nullable()->after('track_type')->constrained('committee_constitutions')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'constitution_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropConstrainedForeignId('constitution_id');
            });
        }
        Schema::dropIfExists('committee_constitutions');
    }
};
