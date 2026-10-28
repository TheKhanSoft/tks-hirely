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

        // System Granular Permissions
        $permissions = [
            // User Permissions
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',
            
            // Role Permissions
            'view-role',
            'create-role',
            'edit-role',
            'delete-role',
            
            // Permission Permissions
            'view-permission',
            'create-permission',
            'edit-permission',
            'delete-permission',
            
            // Campaign Permissions
            'view-campaign',
            'create-campaign',
            'edit-campaign',
            'delete-campaign',
            
            // Committee Assignment
            'view-committee',
            'assign-committee',
            'remove-committee',
            
            // Application processing
            'view-application',
            'create-application',
            'edit-application',
            'delete-application',
            'scrutinize-application',
            'evaluate-application',
            'submit-applications',
            
            // basicInfo processing
            'view-basic-info',
            'create-basic-info',
            'edit-basic-info',
            'delete-basic-info',

            // Payment processing
            'view-payment',
            'create-payment',
            'edit-payment',
            'delete-payment',

            // Audit log reading
            'view-audit-log',
            'delete-audit-log',
            
            // Appeal processing
            'view-appeal',
            'process-appeal',
            
            // Document Verification
            'verify-documents',
            'verify-medal',
            'verify-publication',
            'verify-project',
            'verify-event',

            // External Academic Reviewers
            'manage-reviewers',
            'assign-reviewer',
            'view-review-reports',
            'evaluate-academic-dossier',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles & Permissions Mapping
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        // Super admin receives all privileges implicitly via AppServiceProvider gate bypass

        $registrar = Role::firstOrCreate(['name' => 'registrar', 'guard_name' => 'web']);
        $registrar->syncPermissions([
            // Campaign CRUD
            'view-campaign',
            'create-campaign',
            'edit-campaign',
            'delete-campaign',
            
            // Committee CRUD
            'view-committee',
            'assign-committee',
            'remove-committee',
            
            // Users CRUD
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',
            
            // Roles CRUD
            'view-role',
            'create-role',
            'edit-role',
            'delete-role',
            
            // Permissions CRUD
            'view-permission',
            'create-permission',
            'edit-permission',
            'delete-permission',
            
            // Applications CRUD
            'view-application',
            'create-application',
            'edit-application',
            'delete-application',

            // basic_infos CRUD
            'view-basic-info',
            'create-basic-info',
            'edit-basic-info',
            'delete-basic-info',

            // Payments CRUD
            'view-payment',
            'create-payment',
            'edit-payment',
            'delete-payment',

            // Audit Logs
            'view-audit-log',
            'delete-audit-log',

            // Academic Reviewer Management
            'manage-reviewers',
            'assign-reviewer',
            'view-review-reports',
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
    }
}
