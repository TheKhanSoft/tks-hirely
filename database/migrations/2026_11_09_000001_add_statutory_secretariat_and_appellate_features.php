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
        // 1. Add tokenized magic link columns to application_reviews
        Schema::table('application_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('application_reviews', 'access_token')) {
                $table->string('access_token', 64)->nullable()->unique()->after('status');
            }
            if (!Schema::hasColumn('application_reviews', 'token_expires_at')) {
                $table->dateTime('token_expires_at')->nullable()->after('access_token');
            }
        });

        // 2. Add interview call letter and appellate adjudication columns to applications
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'interview_date')) {
                $table->date('interview_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('applications', 'interview_time')) {
                $table->string('interview_time', 50)->nullable()->after('interview_date');
            }
            if (!Schema::hasColumn('applications', 'interview_venue')) {
                $table->string('interview_venue', 255)->nullable()->after('interview_time');
            }
            if (!Schema::hasColumn('applications', 'interview_committee_room')) {
                $table->string('interview_committee_room', 100)->nullable()->after('interview_venue');
            }
            if (!Schema::hasColumn('applications', 'interview_reporting_time')) {
                $table->string('interview_reporting_time', 50)->nullable()->after('interview_committee_room');
            }
            if (!Schema::hasColumn('applications', 'interview_call_letter_issued_at')) {
                $table->dateTime('interview_call_letter_issued_at')->nullable()->after('interview_reporting_time');
            }
            if (!Schema::hasColumn('applications', 'interview_instructions')) {
                $table->text('interview_instructions')->nullable()->after('interview_call_letter_issued_at');
            }

            // Appellate adjudication audit
            if (!Schema::hasColumn('applications', 'appeal_decision_remarks')) {
                $table->text('appeal_decision_remarks')->nullable()->after('appeal_proof_path');
            }
            if (!Schema::hasColumn('applications', 'appeal_decided_by')) {
                $table->foreignId('appeal_decided_by')->nullable()->after('appeal_decision_remarks')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('applications', 'appeal_decided_at')) {
                $table->dateTime('appeal_decided_at')->nullable()->after('appeal_decided_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_reviews', function (Blueprint $table) {
            $table->dropColumn(['access_token', 'token_expires_at']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['appeal_decided_by']);
            $table->dropColumn([
                'interview_date',
                'interview_time',
                'interview_venue',
                'interview_committee_room',
                'interview_reporting_time',
                'interview_call_letter_issued_at',
                'interview_instructions',
                'appeal_decision_remarks',
                'appeal_decided_by',
                'appeal_decided_at',
            ]);
        });
    }
};
