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
        // 1. Advertisements Table
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

        // 2. Advertisement Date Tracking / Extension History Table
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

        // 3. Link Campaigns (Jobs) to Advertisements
        if (Schema::hasTable('campaigns') && !Schema::hasColumn('campaigns', 'advertisement_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->foreignId('advertisement_id')->nullable()->after('id')->constrained('advertisements')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'advertisement_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropConstrainedForeignId('advertisement_id');
            });
        }

        Schema::dropIfExists('advertisement_date_tracks');
        Schema::dropIfExists('advertisements');
    }
};
