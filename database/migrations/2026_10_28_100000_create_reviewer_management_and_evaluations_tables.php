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
        // 1. Reviewer Profiles (External Experts / Referees)
        Schema::create('reviewer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('subject'); // e.g. Computer Science, Biotechnology, etc.
            $table->string('academic_rank'); // e.g. Professor, Associate Professor, Assistant Professor, etc.
            $table->string('university'); // e.g. Quaid-i-Azam University, NUST, etc.
            $table->string('country')->default('Pakistan');
            $table->string('phone')->nullable();
            $table->text('specialization')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Application Reviews (External Academic Peer Review Assignments)
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
    }
};
