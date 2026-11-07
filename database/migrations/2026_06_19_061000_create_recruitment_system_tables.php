<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for core recruitment tables, advertisements, campaigns,
     * candidate profiles, dossiers, committees, evaluations, and reviewer management.
     */
    public function up(): void
    {
        // 1. Advertisements
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('advertisement_no', 100)->index();
            $table->string('title');
            $table->integer('year')->default(date('Y'))->index();
            $table->text('details')->nullable();
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->string('newspaper_reference')->nullable();
            $table->string('attachment_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Advertisement Date Tracking / Extension History
        Schema::create('advertisement_date_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained('advertisements')->cascadeOnDelete();
            $table->dateTime('previous_valid_to');
            $table->dateTime('extended_valid_to');
            $table->string('corrigendum_no', 100)->nullable();
            $table->text('reason')->nullable();
            $table->dateTime('extended_at');
            $table->foreignId('extended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        // 3. Campaigns (Recruitment Positions & Openings)
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->nullable()->constrained('advertisements')->nullOnDelete();
            $table->string('job_code', 50)->unique();
            $table->string('title', 255);
            $table->string('track_type', 50)->default('ACADEMIC'); // ACADEMIC, ADMINISTRATIVE, SUPPORT, EXECUTIVE, GOVERNMENT
            $table->foreignId('constitution_id')->nullable()->constrained('committee_constitutions')->nullOnDelete();
            $table->foreignId('process_flow_id')->nullable()->constrained('recruitment_process_flows')->nullOnDelete();
            $table->string('department', 150);
            $table->timestamp('opening_date')->useCurrent();
            $table->timestamp('closing_date')->useCurrent();
            $table->decimal('application_fee', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->boolean('require_transcript_proof')->default(true);
            $table->json('scoring_rules')->nullable(); // Custom weights and statutory constitution criteria
            $table->timestamps();
        });

        // 4. Candidate Basic Information & Demographics
        Schema::create('basic_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('track_type', 50)->default('ACADEMIC');
            $table->string('profile_picture', 500)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('cnic_number', 15)->unique();
            $table->date('date_of_birth');
            $table->string('quota', 50)->default('General');
            $table->string('gender', 20)->default('Male');
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

        // 5. Academic Medals Ledger
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

        // 6. Academic Publications
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

        // 7. Seminars, Conferences, and Workshops
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

        // 8. Research Projects and Grants
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

        // 9. Campaign Committees (Scrutiny, Selection Board, Appellate Panels)
        Schema::create('campaign_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_appellate_panel')->default(false);
            $table->string('member_category')->default('INTERNAL'); // INTERNAL, EXTERNAL, INTERNATIONAL, INDEPENDENT
            $table->string('role_designation')->nullable();
            $table->string('affiliation')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('committee_constitution_id')->nullable()->constrained('committee_constitutions')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->unique(['campaign_id', 'user_id', 'is_appellate_panel'], 'campaign_comm_unique');
        });

        // 10. Candidate Applications
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('restrict');
            $table->string('status', 100)->default('DRAFT');
            $table->timestamp('submission_timestamp')->nullable();
            $table->text('disqualification_reason')->nullable(); // Structural code / administrative reference on eligibility failure
            $table->text('appeal_letter')->nullable(); // User's appeal text
            $table->string('appeal_proof_path')->nullable(); // Uploaded proof for appeals
            $table->timestamps();
        });

        // 11. 1Link / Bank Challan Billing Ledger
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

        // 12. Live Selection Board Evaluations & Interview Scoring
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

        // 13. Security Audit Logs
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

        // 14. Academic Degrees
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
            $table->string('proof_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 15. Work Experiences
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

        // 16. Admin Milestones
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

        // 17. Support Tools & Trade Certifications
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

        // 18. Patents & Intellectual Property
        Schema::create('patents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('registry_id', 150);
            $table->string('country', 100)->default('Pakistan');
            $table->date('date_registered');
            $table->boolean('is_licensed')->default(false);
            $table->string('proof_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 19. Portfolio Items
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

        // 20. Professional Trainings & Certifications
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_info_id')->constrained('basic_infos')->onDelete('cascade');
            $table->string('title', 255);
            $table->string('body', 255);
            $table->date('expiry_date')->nullable();
            $table->string('proof_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 21. Referees
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

        // 22. Reviewer Profiles (External Experts / Referees)
        Schema::create('reviewer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('subject'); // e.g. Computer Science, Biotechnology, etc.
            $table->string('academic_rank'); // e.g. Professor, Associate Professor, etc.
            $table->string('university'); // e.g. Quaid-i-Azam University, NUST, etc.
            $table->string('country')->default('Pakistan');
            $table->string('phone')->nullable();
            $table->text('specialization')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 23. Application Reviews (External Academic Peer Review Assignments)
        Schema::create('application_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent();
            $table->date('due_date')->nullable();
            $table->text('confidential_notes')->nullable();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'SUBMITTED', 'DECLINED'])->default('PENDING');
            $table->enum('recommendation', ['STRONGLY_RECOMMENDED', 'RECOMMENDED', 'CONDITIONALLY_RECOMMENDED', 'NOT_RECOMMENDED'])->nullable();
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->text('report_text')->nullable();
            $table->string('evaluation_file_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['application_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_reviews');
        Schema::dropIfExists('reviewer_profiles');
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
        Schema::dropIfExists('advertisement_date_tracks');
        Schema::dropIfExists('advertisements');
    }
};
