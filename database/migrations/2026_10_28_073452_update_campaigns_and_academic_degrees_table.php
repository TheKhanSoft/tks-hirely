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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'require_transcript_proof')) {
                $table->boolean('require_transcript_proof')->default(true)->after('is_active');
            }
        });

        Schema::table('academic_degrees', function (Blueprint $table) {
            $table->string('proof_path', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'require_transcript_proof')) {
                $table->dropColumn('require_transcript_proof');
            }
        });

        Schema::table('academic_degrees', function (Blueprint $table) {
            $table->string('proof_path', 500)->nullable(false)->change();
        });
    }
};
