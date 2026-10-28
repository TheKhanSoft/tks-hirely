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
            // Campaign CRUD & Governance
            'view-campaign',
            'create-campaign',
            'edit-campaign',
            'delete-campaign',
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
            
            // Applications CRUD & Status
            'view-application',
            'create-application',
            'edit-application',
            'delete-application',
            'change-application-status',

            // Candidate Profiles & Dossiers
            'view-basic-info',
            'create-basic-info',
            'edit-basic-info',
            'delete-basic-info',
            'lock-candidate-profile',
            'view-candidate-dossier',

            // Payments & Vouchers
            'view-payment',
            'create-payment',
            'edit-payment',
            'delete-payment',
            'reconcile-payment',
            'simulate-payment',

            // Audit Logs
            'view-audit-log',
            'delete-audit-log',
            'purge-audit-logs',

            // Academic Reviewer Management
            'manage-reviewers',
            'create-reviewer',
            'edit-reviewer',
            'delete-reviewer',
            'assign-reviewer',
            'view-review-reports',

            // System Settings & Workflows
            'manage-settings',
        ]);

        $scrutinyPanel = Role::firstOrCreate(['name' => 'scrutiny-committee', 'guard_name' => 'web']);
        $scrutinyPanel->syncPermissions([
            'view-application',
            'scrutinize-application',
            'verify-documents',
            'verify-medal',
            'verify-publication',
            'verify-project',
            'verify-event',
        ]);

        $appellatePanel = Role::firstOrCreate(['name' => 'appellate-committee', 'guard_name' => 'web']);
        $appellatePanel->syncPermissions([
            'view-appeal',
            'process-appeal',
            'verify-documents',
        ]);

        $selectionBoard = Role::firstOrCreate(['name' => 'selection-board-member', 'guard_name' => 'web']);
        $selectionBoard->syncPermissions([
            'view-application',
            'evaluate-application',
            'access-board-grading',
            'submit-board-score',
        ]);

        $applicant = Role::firstOrCreate(['name' => 'applicant', 'guard_name' => 'web']);
        $applicant->syncPermissions([
            'submit-applications',
            'view-application',
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
    }
}
