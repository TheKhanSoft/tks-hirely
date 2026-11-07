<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for system governance, statutory pipelines, workflow statuses,
     * system settings, and institutional communication templates.
     */
    public function up(): void
    {
        // 1. System Settings Key-Value Store
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->longText('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->string('type')->default('string'); // boolean, integer, string, json
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Statutory Committee Constitutions
        Schema::create('committee_constitutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('organization_sector')->default('ACADEMIC'); // ACADEMIC, CORPORATE, HEALTHCARE, PUBLIC_SECTOR, JUDICIAL, DEFENSE, GENERAL
            $table->string('committee_type', 50)->default('SELECTION_BOARD'); // SCRUTINY, SELECTION_BOARD, EXPERT_REVIEW, APPELLATE, EXECUTIVE, CUSTOM
            $table->string('responsible_stage', 100)->nullable();
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

        // 3. Dynamic Recruitment Process Flows
        Schema::create('recruitment_process_flows', function (Blueprint $table) {
            $table->id();
            $table->string('public_id', 32)->nullable()->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sector')->default('ACADEMIC'); // ACADEMIC, CORPORATE, HEALTHCARE, PUBLIC_SECTOR, GENERAL
            $table->text('description')->nullable();
            
            // Dynamic multi-stage branching pipeline
            $table->json('stages')->nullable();
            
            // Flags
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Workflow Lifecycle Statuses
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

        // 5. Official Email Templates Desk
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique()->index();
            $table->string('name', 200);
            $table->enum('recipient_group', ['CANDIDATE', 'REVIEWER', 'BOARD_EXPERT', 'SECURITY_AUTH', 'GENERAL'])->default('CANDIDATE')->index();
            $table->string('trigger_event', 100)->nullable()->index();
            $table->string('subject', 255);
            $table->string('preheader', 255)->nullable();
            $table->longText('body_html');
            $table->text('body_plain')->nullable();
            $table->json('placeholders')->nullable();
            $table->boolean('is_trigger_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
        });

        // 6. Email Delivery & Audit Trail Logs
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_email', 255)->index();
            $table->string('recipient_name', 255)->nullable();
            $table->string('recipient_type', 50)->nullable();
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('template_slug', 100)->nullable()->index();
            $table->string('subject', 255);
            $table->longText('body_html');
            $table->enum('status', ['SENT', 'FAILED', 'TRIGGER_DISABLED_SKIPPED'])->default('SENT');
            $table->text('error_message')->nullable();
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('dispatch_type', 50)->default('AUTOMATED_TRIGGER');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('workflow_statuses');
        Schema::dropIfExists('recruitment_process_flows');
        Schema::dropIfExists('committee_constitutions');
        Schema::dropIfExists('system_settings');
    }
};
