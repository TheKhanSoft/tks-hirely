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
        // 1. Campaigns
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('job_code', 50)->unique();
            $table->string('title', 255);
            $table->enum('track_type', ['ACADEMIC', 'ADMINISTRATIVE', 'SUPPORT']);
            $table->string('department', 150);
            $table->timestamp('opening_date')->useCurrent();
            $table->timestamp('closing_date')->useCurrent();
            $table->decimal('application_fee', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->json('scoring_rules')->nullable(); // For custom weights set by Registrar
            $table->timestamps();
        });

        // 2. Basic Infos
        Schema::create('basic_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('track_type', ['ACADEMIC', 'ADMINISTRATIVE', 'SUPPORT']);
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('cnic_number', 15)->unique();
            $table->date('date_of_birth');
            $table->string('quota', 50)->default('General');
            $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male');
            $table->string('father_name', 150)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('secondary_phone', 20)->nullable();
            $table->string('secondary_email', 100)->nullable();
            $table->boolean('same_address')->default(false);
            $table->string('permanent_address', 500)->nullable();
            $table->string('mailing_address', 500)->nullable();
            $table->string('place_of_birth', 100)->nullable();
            $table->string('nationality', 100)->default('Pakistani');
            $table->string('religion', 100)->default('Islam');
            $table->string('marital_status', 50)->default('Single');
            $table->string('domicile', 100)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->json('dynamic_attributes')->nullable(); // Stores fallback wizard track data
            $table->timestamps();
        });

        // 3. Academic Medals Ledger
        Schema::create('academic_medals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('degree_title', 150);
            $table->enum('medal_type', ['GOLD', 'SILVER', 'BRONZE']);
            $table->integer('year_awarded');
            $table->string('transcription_proof_path', 500);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 4. Academic Publications
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('title', 500);
            $table->string('journal_name', 300);
            $table->string('issn_print', 9);
            $table->enum('hjrs_category', ['W', 'X', 'Y', 'Z']);
            $table->decimal('impact_factor', 5, 3)->default(0.000);
            $table->integer('author_order');
            $table->boolean('is_corresponding_author')->default(false);
            $table->string('authors', 500)->nullable();
            $table->string('doi', 100)->nullable();
            $table->string('volume_issue', 100)->nullable();
            $table->string('url', 255)->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->integer('publication_year')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 5. Seminars, Conferences and Workshops
        Schema::create('professional_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('event_name', 300);
            $table->enum('event_type', ['SEMINAR', 'CONFERENCE', 'WORKSHOP', 'SYMPOSIUM']);
            $table->enum('event_role', ['LEADER_ORGANIZER', 'ASSOCIATE_ORGANIZER', 'PRESENTER_INTERNATIONAL', 'PRESENTER_NATIONAL', 'ATTENDEE']);
            $table->string('paper_title', 500)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location', 255)->nullable();
            $table->string('organizer', 255)->nullable();
            $table->string('certificate_proof_path', 500);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 6. Research Projects and Grants
        Schema::create('research_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('project_title', 500);
            $table->string('funding_agency', 255);
            $table->decimal('grant_amount', 15, 2);
            $table->date('start_date');
            $table->string('role_in_project', 100);
            $table->decimal('turnitin_similarity_percentage', 5, 2)->default(0.00);
            $table->enum('status', ['ONGOING', 'COMPLETED'])->default('COMPLETED');
            $table->string('co_pis', 500)->nullable();
            $table->string('award_letter_proof_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 7. Scrutiny Committees
        Schema::create('campaign_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_appellate_panel')->default(false);
            $table->timestamp('assigned_at')->useCurrent();
            $table->unique(['campaign_id', 'user_id', 'is_appellate_panel'], 'campaign_comm_unique');
        });

        // 8. Applications
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('restrict');
            $table->string('status', 50)->default('DRAFT');
            $table->timestamp('submission_timestamp')->nullable();
            $table->text('disqualification_reason')->nullable(); // Structural code / administrative reference on eligibility failure
            $table->text('appeal_letter')->nullable(); // User's appeal text
            $table->string('appeal_proof_path')->nullable(); // Uploaded proof for appeals
            $table->timestamps();
        });

        // 9. Billing Ledger
        Schema::create('billing_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('consumer_id', 20)->unique(); // 12-digit challan ID
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['PENDING', 'AUTHORIZED', 'SETTLED', 'EXPIRED'])->default('PENDING');
            $table->string('transaction_id', 100)->unique()->nullable();
            $table->timestamp('payment_timestamp')->nullable();
            $table->json('raw_response_log')->nullable();
            $table->timestamps();
        });

        // 10. Live Evaluations
        Schema::create('live_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('board_member_id')->constrained('users')->onDelete('cascade');
            $table->decimal('academic_score', 5, 2)->default(0.00);
            $table->decimal('medals_score', 5, 2)->default(0.00);
            $table->decimal('publications_score', 5, 2)->default(0.00);
            $table->decimal('events_score', 5, 2)->default(0.00);
            $table->decimal('project_score', 5, 2)->default(0.00);
            $table->decimal('interview_score', 5, 2)->default(0.00);
            $table->boolean('is_outlier')->default(false);
            $table->text('outlier_justification')->nullable();
            $table->timestamp('last_modified')->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();
        });

        // 11. Security Audit Logs
        Schema::create('security_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address', 45);
            $table->string('action_type', 100);
            $table->string('table_name', 100);
            $table->unsignedBigInteger('record_id');
            $table->json('state_before')->nullable();
            $table->json('state_after');
            $table->timestamp('logged_at')->useCurrent();
        });

        // 12. Academic Degrees
        Schema::create('academic_degrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('degree_level', 50);
            $table->string('degree_title', 150);
            $table->string('country', 100)->default('Pakistan');
            $table->string('institution', 255);
            $table->integer('year');
            $table->string('gpa', 50);
            $table->string('scale', 50)->default('4.0');
            $table->string('major', 150);
            $table->string('board', 255);
            $table->string('division', 100);
            $table->string('proof_path', 500);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 13. Work Experiences
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('job_title', 150);
            $table->string('organization', 255);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('experience_type', 100);
            $table->string('sector', 50)->default('Private');
            $table->string('salary', 50)->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 14. Admin Milestones
        Schema::create('admin_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('title', 255);
            $table->decimal('budget', 15, 2)->nullable();
            $table->integer('year');
            $table->text('description')->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 15. Support Tools
        Schema::create('support_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('tool_name', 150);
            $table->string('proficiency_level', 50)->default('Intermediate');
            $table->string('experience_years', 50)->nullable();
            $table->string('fitness_proof_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 16. Patents
        Schema::create('patents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('registry_id', 150);
            $table->string('country', 100)->default('Pakistan');
            $table->date('date_registered');
            $table->boolean('is_licensed')->default(false);
            $table->string('proof_path', 500);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 17. Portfolio Items
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('url', 255)->nullable();
            $table->date('date_completed')->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 18. Trainings
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('body', 255);
            $table->date('expiry_date')->nullable();
            $table->string('proof_path', 500);
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 19. Referees
        Schema::create('referees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('email', 150);
            $table->string('phone', 50);
            $table->string('designation', 150);
            $table->string('organization', 255);
            $table->string('relation', 100);
            $table->string('address', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referees');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('patents');
        Schema::dropIfExists('support_tools');
        Schema::dropIfExists('admin_milestones');
        Schema::dropIfExists('work_experiences');
        Schema::dropIfExists('academic_degrees');
        Schema::dropIfExists('security_audit_logs');
        Schema::dropIfExists('live_evaluations');
        Schema::dropIfExists('billing_ledger');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('campaign_committees');
        Schema::dropIfExists('research_projects');
        Schema::dropIfExists('professional_events');
        Schema::dropIfExists('publications');
        Schema::dropIfExists('academic_medals');
        Schema::dropIfExists('basic_infos');
        Schema::dropIfExists('campaigns');
    }
};
