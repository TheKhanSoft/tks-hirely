<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\PermissionRegistry;
use App\Models\SystemSetting;
use App\Models\CommitteeConstitution;
use App\Models\RecruitmentProcessFlow;
use App\Models\WorkflowStatus;
use App\Models\EmailTemplate;

return new class extends Migration
{
    /**
     * Run the migrations to seed mandatory production statutory baseline data:
     * 1. Spatie RBAC Permissions & Roles
     * 2. Default System Settings & Institutional Parameters
     * 3. Statutory Committee Constitutions & Operational Flows
     * 4. Lifecycle Workflow Statuses & Stage Transitions
     * 5. Official Statutory Email Notification Templates
     */
    public function up(): void
    {
        // ---------------------------------------------------------------------
        // 1. SPATIE RBAC ROLES & PERMISSIONS
        // ---------------------------------------------------------------------
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        PermissionRegistry::syncAll();

        // Ensure Core Statutory Roles exist
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $registrar = Role::firstOrCreate(['name' => 'registrar', 'guard_name' => 'web']);
        $scrutiny = Role::firstOrCreate(['name' => 'scrutiny-committee', 'guard_name' => 'web']);
        $board = Role::firstOrCreate(['name' => 'selection-board-member', 'guard_name' => 'web']);
        $appellate = Role::firstOrCreate(['name' => 'appellate-committee', 'guard_name' => 'web']);
        $reviewer = Role::firstOrCreate(['name' => 'academic-reviewer', 'guard_name' => 'web']);
        $applicant = Role::firstOrCreate(['name' => 'applicant', 'guard_name' => 'web']);

        // Assign Permissions to Registrar
        $registrarPermissions = [
            'view-advertisement', 'create-advertisement', 'edit-advertisement', 'delete-advertisement', 'extend-advertisement', 'export-advertisement',
            'view-campaign', 'create-campaign', 'edit-campaign', 'delete-campaign', 'export-campaign', 'tune-constitution',
            'view-committee', 'assign-committee', 'remove-committee',
            'view-user', 'create-user', 'edit-user', 'delete-user', 'toggle-user-status', 'view-user-dossier', 'assign-user-permissions', 'reset-user-password', 'export-user',
            'view-role', 'create-role', 'edit-role', 'delete-role', 'assign-role-permissions',
            'view-security-logs', 'export-security-logs', 'clear-security-logs',
            'view-merit-list', 'generate-merit-list', 'export-merit-list', 'publish-merit-list',
            'view-reviewer-profile', 'create-reviewer-profile', 'edit-reviewer-profile', 'delete-reviewer-profile',
            'assign-application-reviewer', 'view-application-reviews', 'submit-reviewer-evaluation', 'export-reviewer-dossier',
            'view-settings', 'manage-settings', 'manage-constitutions', 'manage-process-flows',
            'manage-email-templates', 'view-email-templates', 'send-custom-email', 'toggle-email-triggers',
        ];
        foreach ($registrarPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $registrar->syncPermissions($registrarPermissions);

        // Assign Scrutiny Committee Permissions
        $scrutinyPermissions = [
            'view-campaign', 'view-user-dossier', 'view-committee',
            'verify-application', 'reject-application', 'open-proof-certificate', 'export-scrutiny-sheet',
        ];
        foreach ($scrutinyPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $scrutiny->syncPermissions($scrutinyPermissions);

        // Assign Selection Board Permissions
        $boardPermissions = [
            'view-campaign', 'view-committee', 'view-user-dossier',
            'evaluate-candidate', 'grade-interview', 'flag-outlier-grade', 'view-board-roster', 'view-merit-list',
        ];
        foreach ($boardPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $board->syncPermissions($boardPermissions);

        // Assign Appellate Committee Permissions
        $appellatePermissions = [
            'view-campaign', 'view-user-dossier',
            'process-appeal', 'uphold-disqualification', 'revert-to-scrutiny', 'view-appeal-dossier',
        ];
        foreach ($appellatePermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $appellate->syncPermissions($appellatePermissions);

        // Assign Academic Reviewer Permissions
        $reviewerPermissions = [
            'view-application-reviews', 'submit-reviewer-evaluation',
        ];
        foreach ($reviewerPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $reviewer->syncPermissions($reviewerPermissions);

        // Assign Applicant Permissions
        $applicantPermissions = [
            'view-campaign', 'apply-job', 'view-my-applications', 'print-my-application', 'submit-appeal',
        ];
        foreach ($applicantPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $applicant->syncPermissions($applicantPermissions);

        // ---------------------------------------------------------------------
        // 2. SYSTEM SETTINGS & INSTITUTIONAL PARAMETERS
        // ---------------------------------------------------------------------
        SystemSetting::seedDefaultSettings();

        // ---------------------------------------------------------------------
        // 3. STATUTORY COMMITTEE CONSTITUTIONS
        // ---------------------------------------------------------------------
        CommitteeConstitution::updateOrCreate(
            ['slug' => 'standing-scrutiny-committee'],
            [
                'name' => 'Standing Scrutiny & Equivalence Committee',
                'organization_sector' => 'ACADEMIC',
                'committee_type' => 'SCRUTINY',
                'responsible_stage' => 'stage_scrutiny',
                'description' => 'Statutory university scrutiny committee responsible for vetting applicant credentials, HEC degree equivalence verification, document adequacy, and fee clearance.',
                'required_peer_reviewers' => 0,
                'enforce_strict_quota' => true,
                'is_default' => false,
                'is_active' => true,
                'required_total' => 3,
                'required_internal' => 3,
                'required_external' => 0,
                'required_international' => 0,
                'required_independent' => 0,
                'roles_breakdown' => [
                    ['id' => 'rule_scrutiny_1', 'name' => 'Scrutiny Convener / Senior Officer', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Chair / Convener of Scrutiny Desk'],
                    ['id' => 'rule_scrutiny_2', 'name' => 'Credential & Equivalence Verifier', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => true, 'notes' => 'Verifies degrees, certifications, and HEC equivalence'],
                    ['id' => 'rule_scrutiny_3', 'name' => 'Department Subject Scrutinizer', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'Internal faculty member verifying subject relevance'],
                ],
                'routing_flow' => [
                    ['step' => 1, 'stage' => 'FEE_AUDIT', 'name' => 'Bank Voucher & Fee Verification', 'cadre' => 'OFFICER', 'sla_days' => 2, 'mandatory' => true],
                    ['step' => 2, 'stage' => 'DOCUMENT_AUDIT', 'name' => 'Credential & Equivalence Verification', 'cadre' => 'OFFICER', 'sla_days' => 5, 'mandatory' => true],
                ],
                'eligibility_criteria' => [
                    'min_degree_level' => 'Relevant Accredited Degree',
                    'min_experience_years' => 1,
                    'min_publications' => 0,
                ],
            ]
        );

        CommitteeConstitution::updateOrCreate(
            ['slug' => 'selection-board-statutory'],
            [
                'name' => 'Statutory University Selection Board',
                'organization_sector' => 'ACADEMIC',
                'committee_type' => 'SELECTION_BOARD',
                'responsible_stage' => 'stage_selection_board',
                'description' => 'Statutory apex recruitment board established under University Model Act & Statutes. Comprises Vice Chancellor (Chair), Dean of Faculty, Department Chairman, Subject Experts, and Syndicate Nominee.',
                'required_peer_reviewers' => 3,
                'enforce_strict_quota' => true,
                'is_default' => true,
                'is_active' => true,
                'required_total' => 6,
                'required_internal' => 4,
                'required_external' => 2,
                'required_international' => 1,
                'required_independent' => 0,
                'roles_breakdown' => [
                    ['id' => 'rule_sb_1', 'name' => 'Vice-Chancellor (Chairman)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Chairperson of the Statutory Selection Board'],
                    ['id' => 'rule_sb_2', 'name' => 'Dean of Concerned Faculty', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => true, 'notes' => 'Ex-officio Member'],
                    ['id' => 'rule_sb_3', 'name' => 'Chairman / Head of Department', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => true, 'notes' => 'Ex-officio Member'],
                    ['id' => 'rule_sb_4', 'name' => 'Internal Subject Professor (Senior Faculty)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => false, 'notes' => 'Internal senior faculty member'],
                    ['id' => 'rule_sb_5', 'name' => 'External Subject Expert (Accredited University)', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'MEMBER', 'is_mandatory' => true, 'notes' => 'External subject professor appointed from outside institution'],
                    ['id' => 'rule_sb_6', 'name' => 'International / Overseas Subject Specialist', 'count' => 1, 'category' => 'INTERNATIONAL', 'cadre' => 'EXPERT', 'role_nature' => 'EXPERT', 'is_mandatory' => false, 'notes' => 'Overseas professor or international referee'],
                ],
                'routing_flow' => [
                    ['step' => 1, 'stage' => 'SYNCHRONOUS_INTERVIEW', 'name' => 'Live Candidate Interview & Defense', 'cadre' => 'FACULTY', 'sla_days' => 1, 'mandatory' => true],
                    ['step' => 2, 'stage' => 'RESEARCH_PRESENTATION', 'name' => 'Scholarly Seminar & Teaching Demo', 'cadre' => 'FACULTY', 'sla_days' => 1, 'mandatory' => true],
                    ['step' => 3, 'stage' => 'STATUTORY_BALLOT', 'name' => 'Consensus Scoring & Recommendation', 'cadre' => 'FACULTY', 'sla_days' => 1, 'mandatory' => true],
                ],
                'eligibility_criteria' => [
                    'min_degree_level' => 'MS / M.Phil / Ph.D. as per HEC criteria',
                    'min_experience_years' => 0,
                    'min_publications' => 0,
                ],
            ]
        );

        CommitteeConstitution::updateOrCreate(
            ['slug' => 'appellate-tribunal'],
            [
                'name' => 'Appellate Tribunal & Grievance Redressal Committee',
                'organization_sector' => 'ACADEMIC',
                'committee_type' => 'APPELLATE',
                'responsible_stage' => 'stage_grievance_redressal',
                'description' => 'Statutory committee designated to arbitrate appeals from candidates aggrieved by scrutiny disqualifications or marks discrepancies before Selection Board interview calls.',
                'required_peer_reviewers' => 0,
                'enforce_strict_quota' => true,
                'is_default' => false,
                'is_active' => true,
                'required_total' => 3,
                'required_internal' => 2,
                'required_external' => 1,
                'required_international' => 0,
                'required_independent' => 0,
                'roles_breakdown' => [
                    ['id' => 'rule_app_1', 'name' => 'Senior Dean / Appellate Convener', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'FACULTY', 'role_nature' => 'CONVENER', 'is_mandatory' => true, 'notes' => 'Convener appointed by the Vice-Chancellor'],
                    ['id' => 'rule_app_2', 'name' => 'Legal Advisor / Legal Directorate', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'LEGAL', 'role_nature' => 'ARBITER', 'is_mandatory' => true, 'notes' => 'Ensures statutory and procedural compliance'],
                    ['id' => 'rule_app_3', 'name' => 'External Academic / Judicial Arbiter', 'count' => 1, 'category' => 'EXTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'ARBITER', 'is_mandatory' => false, 'notes' => 'Independent outside observer or legal expert'],
                ],
                'routing_flow' => [
                    ['step' => 1, 'stage' => 'APPEAL_VERIFICATION', 'name' => 'Grounds & Evidence Assessment', 'cadre' => 'LEGAL', 'sla_days' => 3, 'mandatory' => true],
                    ['step' => 2, 'stage' => 'HEARING_OR_ORDER', 'name' => 'Formal Determination & Binding Verdict', 'cadre' => 'FACULTY', 'sla_days' => 5, 'mandatory' => true],
                ],
                'eligibility_criteria' => [
                    'min_degree_level' => 'Disqualified / Aggrieved Candidate Appeal',
                    'min_experience_years' => 0,
                    'min_publications' => 0,
                ],
            ]
        );

        // ---------------------------------------------------------------------
        // 4. RECRUITMENT PROCESS FLOWS
        // ---------------------------------------------------------------------
        RecruitmentProcessFlow::updateOrCreate(
            ['slug' => 'academic-statutory-pipeline'],
            [
                'name' => 'Higher Education Standard Statutory Recruitment Pipeline',
                'slug' => 'academic-statutory-pipeline',
                'sector' => 'ACADEMIC',
                'description' => 'End-to-end statutory hiring pipeline: 1. Validity & Fee Settlement -> 2. Dossier Scrutiny (one-by-one verification with missing document grace period) -> 3. Academic Subject Experts (Peer Review >= 60%) -> 4. Selection Board Interview & Rubric Scoring -> 5. Syndicate Appointment Sanction (+ Appellate Grievance Branch).',
                'stages' => RecruitmentProcessFlow::defaultAcademicPipeline(),
                'is_default' => true,
                'is_active' => true,
            ]
        );

        // ---------------------------------------------------------------------
        // 5. WORKFLOW LIFECYCLE STATUSES
        // ---------------------------------------------------------------------
        $statuses = [
            ['code' => 'DRAFT', 'label' => 'Application Incomplete / Draft', 'badge_color' => 'neutral', 'order' => 10, 'is_terminal' => false, 'is_locked_state' => false, 'is_system_default' => true, 'description' => 'Candidate is filling profile credentials.'],
            ['code' => 'SUBMITTED', 'label' => 'Submitted & Fee Cleared', 'badge_color' => 'primary', 'order' => 20, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Application successfully submitted and bank payment verified.'],
            ['code' => 'UNDER_SCRUTINY', 'label' => 'Under Scrutiny Verification', 'badge_color' => 'warning', 'order' => 30, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Credential auditing and HEC equivalence verification underway.'],
            ['code' => 'SCRUTINY_APPROVED', 'label' => 'Scrutiny Cleared / Eligible', 'badge_color' => 'success', 'order' => 40, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Candidate confirmed eligible under statutory criteria.'],
            ['code' => 'SCRUTINY_REJECTED', 'label' => 'Scrutiny Rejected / Ineligible', 'badge_color' => 'danger', 'order' => 50, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Candidate deficient under statutory qualifications.'],
            ['code' => 'UNDER_APPEAL', 'label' => 'Grievance Appeal Pending', 'badge_color' => 'purple', 'order' => 60, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Candidate filed appeal against ineligibility determination.'],
            ['code' => 'PEER_REVIEW_DISPATCHED', 'label' => 'Peer Review Dossiers Dispatched', 'badge_color' => 'amber', 'order' => 70, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Dossiers sent to external confidential subject referees.'],
            ['code' => 'PEER_REVIEW_RECEIVED', 'label' => 'Peer Evaluations Received', 'badge_color' => 'cyan', 'order' => 80, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Referees completed and returned blind evaluations.'],
            ['code' => 'SELECTION_BOARD_INVITED', 'label' => 'Selection Board Interview Called', 'badge_color' => 'primary', 'order' => 90, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Candidate shortlisted and summoned for Selection Board interview.'],
            ['code' => 'OFFER_EXTENDED', 'label' => 'Selection Board Recommended / Offer Issued', 'badge_color' => 'success', 'order' => 100, 'is_terminal' => false, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Selection Board recommended candidate for appointment.'],
            ['code' => 'APPOINTED', 'label' => 'Appointed & Service Confirmed', 'badge_color' => 'success', 'order' => 110, 'is_terminal' => true, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Syndicate sanctioned and appointment letter issued.'],
            ['code' => 'WITHDRAWN', 'label' => 'Candidacy Withdrawn by Applicant', 'badge_color' => 'neutral', 'order' => 120, 'is_terminal' => true, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Candidate voluntarily withdrew their application.'],
            ['code' => 'DISQUALIFIED', 'label' => 'Statutorily Disqualified', 'badge_color' => 'danger', 'order' => 130, 'is_terminal' => true, 'is_locked_state' => true, 'is_system_default' => true, 'description' => 'Permanently disqualified due to forged credentials or statute failure.'],
        ];

        foreach ($statuses as $st) {
            WorkflowStatus::updateOrCreate(['code' => $st['code']], $st);
        }

        // ---------------------------------------------------------------------
        // 6. OFFICIAL STATUTORY EMAIL TEMPLATES
        // ---------------------------------------------------------------------
        EmailTemplate::seedDefaultTemplates();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Baseline data rollback - safe cleanup
    }
};
