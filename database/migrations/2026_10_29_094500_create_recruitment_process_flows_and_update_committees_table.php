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
        // 1. Add committee_type & responsible_stage to committee_constitutions if not present
        if (Schema::hasTable('committee_constitutions')) {
            Schema::table('committee_constitutions', function (Blueprint $table) {
                if (!Schema::hasColumn('committee_constitutions', 'committee_type')) {
                    $table->string('committee_type', 50)->default('SELECTION_BOARD')->after('organization_sector'); 
                    // SCRUTINY, SELECTION_BOARD, EXPERT_REVIEW, APPELLATE, EXECUTIVE, CUSTOM
                }
                if (!Schema::hasColumn('committee_constitutions', 'responsible_stage')) {
                    $table->string('responsible_stage', 100)->nullable()->after('committee_type');
                }
            });
        }

        // 2. Create recruitment_process_flows table
        Schema::create('recruitment_process_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sector')->default('ACADEMIC'); // ACADEMIC, CORPORATE, HEALTHCARE, PUBLIC_SECTOR, GENERAL
            $table->text('description')->nullable();
            
            // Full dynamic multi-stage branching pipeline
            $table->json('stages')->nullable();
            
            // Flags
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Optional linkage from campaigns to a specific process flow
        if (Schema::hasTable('campaigns') && !Schema::hasColumn('campaigns', 'process_flow_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->foreignId('process_flow_id')->nullable()->after('constitution_id')->constrained('recruitment_process_flows')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'process_flow_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropConstrainedForeignId('process_flow_id');
            });
        }
        Schema::dropIfExists('recruitment_process_flows');

        if (Schema::hasTable('committee_constitutions')) {
            Schema::table('committee_constitutions', function (Blueprint $table) {
                if (Schema::hasColumn('committee_constitutions', 'responsible_stage')) {
                    $table->dropColumn('responsible_stage');
                }
                if (Schema::hasColumn('committee_constitutions', 'committee_type')) {
                    $table->dropColumn('committee_type');
                }
            });
        }
    }
};
