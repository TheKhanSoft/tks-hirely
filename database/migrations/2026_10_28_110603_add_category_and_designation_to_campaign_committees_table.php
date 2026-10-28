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
        Schema::table('campaign_committees', function (Blueprint $table) {
            $table->string('member_category')->default('INTERNAL')->after('is_appellate_panel');
            $table->string('role_designation')->nullable()->after('member_category');
            $table->string('affiliation')->nullable()->after('role_designation');
            $table->string('country')->nullable()->after('affiliation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_committees', function (Blueprint $table) {
            $table->dropColumn(['member_category', 'role_designation', 'affiliation', 'country']);
        });
    }
};
