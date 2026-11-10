<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SpatieRoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Sync all system-generated permissions from PermissionRegistry
        \App\Services\PermissionRegistry::syncAll();

        // Roles & Permissions Mapping
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        // Super admin receives all privileges implicitly via AppServiceProvider gate bypass

        $registrar = Role::firstOrCreate(['name' => 'registrar', 'guard_name' => 'web']);
        $registrar->syncPermissions([
            // Advertisements & Notices
            'view-advertisement',
            'create-advertisement',
            'edit-advertisement',
            'delete-advertisement',
            'extend-advertisement',
            'export-advertisement',

            // Campaign CRUD & Governance
            'view-campaign',
            'create-campaign',
            'edit-campaign',
            'delete-campaign',
            'export-campaign',
            'tune-constitution',
            
            // Committee CRUD
            'view-committee',
            'assign-committee',
            'remove-committee',
            
            // Users CRUD & Direct Permissions
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',
            'toggle-user-status',
            'view-user-dossier',
            'assign-user-permissions',
            'reset-user-password',
            'export-user',
            
            // Roles & Permissions CRUD
            'view-role',
            'create-role',
            'edit-role',
            'delete-role',
            'view-permission',
            'create-permission',
            'edit-permission',
            'delete-permission',
            'assign-role-permissions',
            'export-role',
            
            // Applications CRUD & Status
            'view-application',
            'create-application',
            'edit-application',
            'delete-application',
            'change-application-status',
            'export-application',
            'generate-scrutiny-certificate',

            // Candidate Profiles & Dossiers
            'view-basic-info',
            'create-basic-info',
            'edit-basic-info',
            'delete-basic-info',
            'lock-candidate-profile',
            'view-candidate-dossier',
            'export-candidate',

            // Payments & Vouchers
            'view-payment',
            'create-payment',
            'edit-payment',
            'delete-payment',
            'reconcile-payment',
            'simulate-payment',
            'export-payment',

            // Audit Logs
            'view-audit-log',
            'delete-audit-log',
            'purge-audit-logs',
            'export-audit-log',

            // Academic Reviewer Management
            'manage-reviewers',
            'view-reviewer',
            'create-reviewer',
            'edit-reviewer',
            'delete-reviewer',
            'assign-reviewer',
            'view-review-reports',
            'export-reviewer',

            // System Settings, Workflows & Analytics
            'manage-settings',
            'manage-email-templates',
            'view-email-templates',
            'send-custom-email',
            'toggle-email-triggers',
            'manage-security-settings',
            'manage-system-maintenance',
            'view-system-health',
            'manage-sms-settings',
            'manage-whatsapp-settings',
            'send-whatsapp-alerts',
            'verify-otp',
            'manage-merit-formula',
            'manage-statuses',
            'manage-constitutions',
            'export-constitution',
            'export-status',
            'view-dashboard',
            'view-analytics',
            'view-executive-analytics',
            'export-executive-analytics',
            'view-board-roster',
            'view-merit-list',
            'generate-merit-list',
            'export-merit-list',
            'publish-merit-list',
            'view-projector-merit-board',
            'manage-projector-merit-board',
            'impersonate-user',
        ]);

        $scrutinyPanel = Role::firstOrCreate(['name' => 'scrutiny-committee', 'guard_name' => 'web']);
        $scrutinyPanel->syncPermissions([
            'view-application',
            'scrutinize-application',
            'generate-scrutiny-certificate',
            'verify-documents',
            'verify-degree',
            'verify-biodata',
            'verify-experience',
            'verify-medal',
            'verify-publication',
            'verify-project',
            'verify-patent',
            'verify-training',
            'verify-event',
        ]);

        $appellatePanel = Role::firstOrCreate(['name' => 'appellate-committee', 'guard_name' => 'web']);
        $appellatePanel->syncPermissions([
            'view-appeal',
            'process-appeal',
            'verify-documents',
            'verify-degree',
            'verify-biodata',
            'verify-experience',
            'verify-patent',
            'verify-training',
        ]);

        $selectionBoard = Role::firstOrCreate(['name' => 'selection-board-member', 'guard_name' => 'web']);
        $selectionBoard->syncPermissions([
            'view-application',
            'evaluate-application',
            'access-board-grading',
            'submit-board-score',
            'view-board-roster',
            'view-merit-list',
            'view-projector-merit-board',
            'manage-projector-merit-board',
        ]);

        $applicant = Role::firstOrCreate(['name' => 'applicant', 'guard_name' => 'web']);
        $applicant->syncPermissions([
            'submit-applications',
            'submit-appeal',
        ]);

        $reviewer = Role::firstOrCreate(['name' => 'academic-reviewer', 'guard_name' => 'web']);
        $reviewer->syncPermissions([
            'view-application',
            'evaluate-academic-dossier',
        ]);

        // ==========================================
        // ⚡ SHORT ROLE ALIASES (Concise developer & operator shortcuts)
        // ==========================================
        $boardShort = Role::firstOrCreate(['name' => 'board-member', 'guard_name' => 'web']);
        $boardShort->syncPermissions($selectionBoard->permissions);

        $boardUltraShort = Role::firstOrCreate(['name' => 'board', 'guard_name' => 'web']);
        $boardUltraShort->syncPermissions($selectionBoard->permissions);

        $scrutinyShort = Role::firstOrCreate(['name' => 'scrutiny', 'guard_name' => 'web']);
        $scrutinyShort->syncPermissions($scrutinyPanel->permissions);

        $appellateShort = Role::firstOrCreate(['name' => 'appellate', 'guard_name' => 'web']);
        $appellateShort->syncPermissions($appellatePanel->permissions);

        $reviewerShort = Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        $reviewerShort->syncPermissions($reviewer->permissions);

        $adminShort = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminShort->syncPermissions($registrar->permissions);
    }
}
