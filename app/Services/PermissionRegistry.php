<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionRegistry
{
    /**
     * Complete catalogue of system-generated permissions mapped to modules, actions, and methods.
     */
    public static function catalogue(): array
    {
        return [
            'Advertisements & Notices' => [
                'view-advertisement' => [
                    'label' => 'View Advertisements',
                    'description' => 'Browse and inspect job advertisements, dates, and newspaper references',
                ],
                'create-advertisement' => [
                    'label' => 'Create Advertisement',
                    'description' => 'Publish and draft new statutory job advertisement notices',
                ],
                'edit-advertisement' => [
                    'label' => 'Edit Advertisement',
                    'description' => 'Modify advertisement particulars and update details',
                ],
                'delete-advertisement' => [
                    'label' => 'Delete Advertisement',
                    'description' => 'Remove advertisement notices',
                ],
                'extend-advertisement' => [
                    'label' => 'Extend Advertisement Validity',
                    'description' => 'Issue official corrigendum extensions and track dates',
                ],
            ],

            'Campaigns & Governance' => [
                'view-campaign' => [
                    'label' => 'View Campaigns',
                    'description' => 'View job campaigns list, deadlines, and scoring rules',
                ],
                'create-campaign' => [
                    'label' => 'Create Campaign',
                    'description' => 'Publish and configure new recruitment job campaigns',
                ],
                'edit-campaign' => [
                    'label' => 'Edit Campaign',
                    'description' => 'Modify job campaign details, dates, fees, and weighting formulas',
                ],
                'delete-campaign' => [
                    'label' => 'Delete Campaign',
                    'description' => 'Delete campaign records and cascade dependencies',
                ],
                'export-campaign' => [
                    'label' => 'Export Campaigns Data',
                    'description' => 'Download recruitment campaign rosters and summary statistics',
                ],
                'tune-constitution' => [
                    'label' => 'Tune Committee Constitution',
                    'description' => 'Configure statutory committee quotas, rules, and presets',
                ],
            ],

            'Operational Committees' => [
                'view-committee' => [
                    'label' => 'View Committee Allocations',
                    'description' => 'View active operational committee ledger and quorum tallies',
                ],
                'assign-committee' => [
                    'label' => 'Assign Committee Member',
                    'description' => 'Empanel members to scrutiny and selection committee panels',
                ],
                'remove-committee' => [
                    'label' => 'Remove Committee Assignment',
                    'description' => 'De-assign personnel from campaign committee panels',
                ],
            ],

            'Applications & Scrutiny' => [
                'view-application' => [
                    'label' => 'View Applications',
                    'description' => 'Access and review submitted candidate applications',
                ],
                'create-application' => [
                    'label' => 'Create Application',
                    'description' => 'Register application dossiers administratively or on behalf of applicants',
                ],
                'edit-application' => [
                    'label' => 'Edit Application',
                    'description' => 'Update application particulars, dates, and campaign linkage',
                ],
                'delete-application' => [
                    'label' => 'Delete Application',
                    'description' => 'Permanently remove application records',
                ],
                'change-application-status' => [
                    'label' => 'Change Application Status',
                    'description' => 'Transition statuses (Eligible, Ineligible, Approved, Rejected)',
                ],
                'submit-applications' => [
                    'label' => 'Submit Applications',
                    'description' => 'Apply to open recruitment vacancies as a candidate',
                ],
                'scrutinize-application' => [
                    'label' => 'Scrutinize Applications',
                    'description' => 'Perform scrutiny desk evaluations and verify eligibility criteria',
                ],
                'evaluate-application' => [
                    'label' => 'Evaluate Candidates',
                    'description' => 'Conduct selection board evaluations and assign assessment marks',
                ],
                'export-application' => [
                    'label' => 'Export Applications Data',
                    'description' => 'Download applications forensic CSV datasets',
                ],
            ],

            'Document Verification' => [
                'verify-documents' => [
                    'label' => 'Verify Qualification Documents',
                    'description' => 'Verify academic degree transcripts and certified proofs',
                ],
                'verify-degree' => [
                    'label' => 'Verify Academic Degrees',
                    'description' => 'Audit degree titles, institutes, divisions, and marks',
                ],
                'verify-biodata' => [
                    'label' => 'Verify Candidate Biodata',
                    'description' => 'Confirm candidate CNIC, demographics, and domicile proof',
                ],
                'verify-medal' => [
                    'label' => 'Verify Academic Medals',
                    'description' => 'Inspect and verify gold, silver, and bronze distinction medals',
                ],
                'verify-publication' => [
                    'label' => 'Verify Publications',
                    'description' => 'Validate HEC journal categorization (W/X/Y/Z) and DOIs',
                ],
                'verify-project' => [
                    'label' => 'Verify Research Projects',
                    'description' => 'Audit external research grants and industrial grant values',
                ],
                'verify-event' => [
                    'label' => 'Verify Events & Conferences',
                    'description' => 'Confirm conference organizer/attendee credentials',
                ],
            ],

            'Appeals & Grievances' => [
                'view-appeal' => [
                    'label' => 'View Candidate Appeals',
                    'description' => 'Access the appellate grievance queue and candidate statements',
                ],
                'submit-appeal' => [
                    'label' => 'Submit Grievance Appeal',
                    'description' => 'Lodge formal disqualification grievance appeal with proof',
                ],
                'process-appeal' => [
                    'label' => 'Process Appeals',
                    'description' => 'Approve or reject candidate grievance appeals with formal justification',
                ],
            ],

            'Academic Reviewers' => [
                'manage-reviewers' => [
                    'label' => 'Manage Reviewer Directory',
                    'description' => 'Empanel, search, and manage external academic referees',
                ],
                'view-reviewer' => [
                    'label' => 'View Reviewers Directory',
                    'description' => 'Browse empanelled academic reviewer profiles and dossier ledgers',
                ],
                'create-reviewer' => [
                    'label' => 'Empanel Academic Reviewer',
                    'description' => 'Register external experts from other universities and international bodies',
                ],
                'edit-reviewer' => [
                    'label' => 'Edit Reviewer Profile',
                    'description' => 'Update expert academic rank, institution, country, and field',
                ],
                'delete-reviewer' => [
                    'label' => 'De-empanel Reviewer',
                    'description' => 'Remove reviewer profile from active expert roster',
                ],
                'assign-reviewer' => [
                    'label' => 'Dispatch Review Assignments',
                    'description' => 'Assign candidate dossiers to academic referees for peer review',
                ],
                'view-review-reports' => [
                    'label' => 'View Evaluation Reports',
                    'description' => 'Inspect completed peer review scorecards and qualitative evaluations',
                ],
                'evaluate-academic-dossier' => [
                    'label' => 'Perform Peer Evaluation',
                    'description' => 'Score applicant academic dossiers on external evaluation desk',
                ],
            ],

            'Candidate Profiles' => [
                'view-basic-info' => [
                    'label' => 'View Candidate Profiles',
                    'description' => 'Browse candidate demographic records and qualification profiles',
                ],
                'create-basic-info' => [
                    'label' => 'Create Candidate Profile',
                    'description' => 'Establish new candidate demographic and qualification records',
                ],
                'edit-basic-info' => [
                    'label' => 'Edit Candidate Profile',
                    'description' => 'Modify candidate names, CNIC, and contact information',
                ],
                'delete-basic-info' => [
                    'label' => 'Delete Candidate Profile',
                    'description' => 'Remove applicant qualification dossiers',
                ],
                'lock-candidate-profile' => [
                    'label' => 'Lock/Unlock Candidate Profile',
                    'description' => 'Toggle edit lock status on applicant qualification dossiers',
                ],
                'view-candidate-dossier' => [
                    'label' => 'View Full Candidate Dossier',
                    'description' => 'Open complete qualification modal (degrees, publications, projects)',
                ],
            ],

            'Payments & Vouchers' => [
                'view-payment' => [
                    'label' => 'View Payments Ledger',
                    'description' => 'View Kuickpay transactions, challan slips, and billing ledger',
                ],
                'create-payment' => [
                    'label' => 'Create Payment Voucher',
                    'description' => 'Generate manual challan or record payment invoice',
                ],
                'edit-payment' => [
                    'label' => 'Edit Payment Record',
                    'description' => 'Update transaction amounts, statuses, and payment modes',
                ],
                'delete-payment' => [
                    'label' => 'Delete Payment Transaction',
                    'description' => 'Remove erroneous payment transactions from ledger',
                ],
                'reconcile-payment' => [
                    'label' => 'Reconcile Candidate Payments',
                    'description' => 'Mark payments as verified/paid and transition application status',
                ],
                'simulate-payment' => [
                    'label' => 'Use Payment Simulator',
                    'description' => 'Test 1Bill / Kuickpay consumer number resolution and payment webhook',
                ],
            ],

            'User Accounts & Personnel' => [
                'view-user' => [
                    'label' => 'View User Accounts',
                    'description' => 'Access user directory and personnel accounts',
                ],
                'create-user' => [
                    'label' => 'Register User Account',
                    'description' => 'Create new authentication credentials and assign initial roles',
                ],
                'edit-user' => [
                    'label' => 'Edit User Account',
                    'description' => 'Update user email, password, and primary system role',
                ],
                'delete-user' => [
                    'label' => 'Delete User Account',
                    'description' => 'Remove user accounts from the directory',
                ],
                'toggle-user-status' => [
                    'label' => 'Toggle User Active Status',
                    'description' => 'Block or unblock user login access',
                ],
                'view-user-dossier' => [
                    'label' => 'View Personnel Dossier',
                    'description' => 'View comprehensive employee, reviewer, or committee profile modal',
                ],
                'assign-user-permissions' => [
                    'label' => 'Assign Direct User Permissions',
                    'description' => 'Grant or revoke extra direct permissions for a specific user apart from their role',
                ],
                'impersonate-user' => [
                    'label' => 'Impersonate User Account',
                    'description' => 'Switch session into target user account for testing, support, and administrative verification',
                ],
            ],

            'Roles & Permissions' => [
                'view-role' => [
                    'label' => 'View Roles',
                    'description' => 'View system security roles',
                ],
                'create-role' => [
                    'label' => 'Create Role',
                    'description' => 'Define new security roles for institutional personnel',
                ],
                'edit-role' => [
                    'label' => 'Edit Role',
                    'description' => 'Modify role definitions and titles',
                ],
                'delete-role' => [
                    'label' => 'Delete Role',
                    'description' => 'Remove custom security roles',
                ],
                'view-permission' => [
                    'label' => 'View Permissions',
                    'description' => 'Inspect granular action permissions catalogue',
                ],
                'create-permission' => [
                    'label' => 'Create Permission',
                    'description' => 'Define new granular permission capabilities',
                ],
                'edit-permission' => [
                    'label' => 'Edit Permission',
                    'description' => 'Modify permission parameters',
                ],
                'delete-permission' => [
                    'label' => 'Delete Permission',
                    'description' => 'Remove obsolete system permissions',
                ],
                'assign-role-permissions' => [
                    'label' => 'Assign Role Permissions Matrix',
                    'description' => 'Toggle capability matrix mapping permissions to security roles',
                ],
            ],

            'Security & Auditing' => [
                'view-audit-log' => [
                    'label' => 'View Security Audit Logs',
                    'description' => 'Audit administrative actions, IP addresses, and state changes',
                ],
                'delete-audit-log' => [
                    'label' => 'Delete Audit Log',
                    'description' => 'Remove individual audit record',
                ],
                'purge-audit-logs' => [
                    'label' => 'Purge Audit Logs',
                    'description' => 'Execute bulk historical purge of audit entries',
                ],
            ],

            'Selection Board Live Grading' => [
                'access-board-grading' => [
                    'label' => 'Access Board Live Grading Panel',
                    'description' => 'Participate in live candidate interview grading sessions',
                ],
                'submit-board-score' => [
                    'label' => 'Submit Board Scores',
                    'description' => 'Submit live rubric scores and candidate grading evaluations',
                ],
            ],

            'System & Workflow Settings' => [
                'manage-settings' => [
                    'label' => 'Manage System Settings',
                    'description' => 'Configure recruitment pipelines, document requirements, statutory constitutions, and organization settings',
                ],
                'manage-statuses' => [
                    'label' => 'Manage Dynamic Statuses & Lifecycles',
                    'description' => 'Configure application, applicant, and pipeline statuses, gate requirements, and transition flows',
                ],
                'manage-constitutions' => [
                    'label' => 'Manage Board & Committee Constitutions',
                    'description' => 'Create, edit, and configure statutory boards, routing pipelines, eligibility criteria, and quotas across sectors',
                ],
            ],
        ];
    }

    /**
     * Get flat list of all system permission names.
     */
    public static function allPermissionNames(): array
    {
        $names = [];
        foreach (self::catalogue() as $group => $perms) {
            foreach (array_keys($perms) as $permName) {
                $names[] = $permName;
            }
        }
        return $names;
    }

    /**
     * Automatically ensure all system permissions exist in database.
     */
    public static function syncAll(): int
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $count = 0;
        foreach (self::allPermissionNames() as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $count++;
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return $count;
    }
}

