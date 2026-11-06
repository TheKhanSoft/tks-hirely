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
        if (!Schema::hasTable('email_templates')) {
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
        }

        if (!Schema::hasTable('email_logs')) {
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

        // Seed permissions if table exists
        if (Schema::hasTable('permissions')) {
            $permissions = [
                'manage-email-templates',
                'view-email-templates',
                'send-custom-email',
                'toggle-email-triggers',
            ];
            foreach ($permissions as $perm) {
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
            $registrarRole = \Spatie\Permission\Models\Role::where('name', 'registrar')->first();
            if ($registrarRole) {
                $registrarRole->givePermissionTo($permissions);
            }
        }

        // Seed default templates during migration for initial setup
        \App\Models\EmailTemplate::seedDefaultTemplates();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
    }
};
