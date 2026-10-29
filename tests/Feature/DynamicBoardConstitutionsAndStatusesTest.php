<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Models\CommitteeConstitution;
use App\Models\WorkflowStatus;
use App\Models\ReviewerProfile;
use App\Models\ApplicationReview;
use Livewire\Livewire;
use App\Livewire\Admin\ConstitutionCriteriaManagement;
use App\Livewire\Admin\WorkflowStatusManagement;
use App\Livewire\Admin\ApplicationManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\PermissionRegistry;

class DynamicBoardConstitutionsAndStatusesTest extends TestCase
{
    use RefreshDatabase;

    protected $registrar;
    protected $superAdmin;
    protected $applicantUser;
    protected $reviewerUser;
    protected $campaign;
    protected $application;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(\Database\Seeders\StatutoryConstitutionsAndStatusesSeeder::class);
        PermissionRegistry::syncAll();

        $this->registrar = User::where('email', 'registrar@university.edu')->first();
        $this->superAdmin = User::where('email', 'superadmin@university.edu')->first();

        // Create applicant & application
        $this->applicantUser = User::factory()->create(['email' => 'candidate_dynamic@test.edu']);
        $this->applicantUser->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $this->applicantUser->id,
            'first_name' => 'Dr. Ahmed',
            'last_name' => 'Dynamic',
            'father_name' => 'Dynamic Senior',
            'track_type' => 'ACADEMIC',
            'gender' => 'Male',
            'marital_status' => 'Single',
            'religion' => 'Islam',
            'nationality' => 'Pakistani',
            'domicile_district' => 'Lahore',
            'domicile_province' => 'Punjab',
            'mobile_number' => '03001234567',
            'postal_address' => 'Lahore Office',
            'permanent_address' => 'Lahore City',
            'date_of_birth' => '1990-01-01',
            'cnic_number' => '35201-9988776-1',
        ]);

        $this->campaign = Campaign::where('job_code', 'CS-FAC-2026')->first();

        $this->application = Application::create([
            'user_id' => $this->applicantUser->id,
            'campaign_id' => $this->campaign->id,
            'status' => 'SUBMITTED',
        ]);

        // Create reviewer
        $this->reviewerUser = User::factory()->create(['email' => 'expert_eval@test.edu']);
        $this->reviewerUser->assignRole('academic-reviewer');
        ReviewerProfile::create([
            'user_id' => $this->reviewerUser->id,
            'name' => 'Prof. Dr. Sarah Specialist',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'Oxford University',
            'country' => 'United Kingdom',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_and_registrar_can_access_statutory_boards_and_status_engines(): void
    {
        $this->actingAs($this->superAdmin);
        $this->get('/admin/criteria')->assertOk();
        $this->get('/admin/statuses')->assertOk();

        $this->actingAs($this->registrar);
        $this->get('/registrar/criteria')->assertOk();
        $this->get('/registrar/statuses')->assertOk();
    }

    public function test_dynamic_board_constitution_crud_across_sectors(): void
    {
        $this->actingAs($this->registrar);

        // 1. Create a dynamic Corporate / Enterprise Statutory Board
        $roles = [
            ['id' => 'r1', 'name' => 'Managing Director (Convener)', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'EXECUTIVE', 'role_nature' => 'CONVENER', 'is_mandatory' => true],
            ['id' => 'r2', 'name' => 'Chief Technology Officer', 'count' => 1, 'category' => 'INTERNAL', 'cadre' => 'OFFICER', 'role_nature' => 'MEMBER', 'is_mandatory' => true],
            ['id' => 'r3', 'name' => 'External Industry Expert', 'count' => 2, 'category' => 'EXTERNAL', 'cadre' => 'EXPERT', 'role_nature' => 'MEMBER', 'is_mandatory' => false],
        ];

        $flow = [
            ['step' => 1, 'stage' => 'HR_SCREEN', 'name' => 'HR Screening', 'cadre' => 'OFFICER', 'sla_days' => 3, 'mandatory' => true],
            ['step' => 2, 'stage' => 'TECH_EVAL', 'name' => 'Technical Panel', 'cadre' => 'EXPERT', 'sla_days' => 5, 'mandatory' => true],
            ['step' => 3, 'stage' => 'MD_SANCTION', 'name' => 'Executive Sanction', 'cadre' => 'EXECUTIVE', 'sla_days' => 4, 'mandatory' => true],
        ];

        Livewire::test(ConstitutionCriteriaManagement::class)
            ->set('activeTab', 'board_registry')
            ->call('openCreateBoardModal')
            ->set('board_name', 'Global FinTech Executive Selection Board')
            ->set('board_sector', 'CORPORATE')
            ->set('board_description', 'FinTech executive hiring governance board.')
            ->set('board_required_peer_reviewers', 0)
            ->set('board_enforce_strict_quota', true)
            ->set('board_roles', $roles)
            ->set('board_routing_flow', $flow)
            ->call('saveBoardModel')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('committee_constitutions', [
            'name' => 'Global FinTech Executive Selection Board',
            'organization_sector' => 'CORPORATE',
            'required_total' => 4,
            'required_internal' => 2,
            'required_external' => 2,
        ]);

        $createdBoard = CommitteeConstitution::where('name', 'Global FinTech Executive Selection Board')->first();

        // 2. Clone the board
        Livewire::test(ConstitutionCriteriaManagement::class)
            ->call('cloneBoardModel', $createdBoard->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('committee_constitutions', [
            'name' => 'Global FinTech Executive Selection Board (Clone)',
        ]);

        // 3. Apply master board constitution to active campaign
        Livewire::test(ConstitutionCriteriaManagement::class, ['campaign_id' => $this->campaign->id])
            ->call('applyBoardConstitutionToCampaign', $createdBoard->id)
            ->call('saveConstitution')
            ->assertHasNoErrors();

        $this->assertEquals($createdBoard->id, $this->campaign->fresh()->constitution_id);
    }

    public function test_dynamic_workflow_status_management_and_gate_policy(): void
    {
        $this->actingAs($this->registrar);

        // 1. Create a custom status node in APPLICATION scope
        Livewire::test(WorkflowStatusManagement::class)
            ->set('activeScope', 'APPLICATION')
            ->call('openCreateModal')
            ->set('code', 'SECURITY_VETTED')
            ->set('label', 'Security & Background Vetted')
            ->set('scope', 'APPLICATION')
            ->set('badge_color', 'purple')
            ->set('order', 13)
            ->set('description', 'Clearance verified by institutional security authority.')
            ->set('req_justification', true)
            ->set('allowed_transitions', ['ELIGIBLE', 'DISQUALIFIED'])
            ->call('saveStatus')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('workflow_statuses', [
            'code' => 'SECURITY_VETTED',
            'scope' => 'APPLICATION',
        ]);

        // 2. Test gate validation: transitioning to FEE_PAID when fee is not settled
        $statusValidation = WorkflowStatus::validateApplicationTransition($this->application, 'FEE_PAID');
        $this->assertFalse($statusValidation['allowed']);
        $this->assertStringContainsString('settled billing voucher', $statusValidation['errors'][0]);

        // Now settle billing ledger
        BillingLedger::create([
            'application_id' => $this->application->id,
            'user_id' => $this->applicantUser->id,
            'amount' => 1500,
            'status' => 'SETTLED',
            'consumer_id' => '10001234567',
        ]);

        $statusValidationSettled = WorkflowStatus::validateApplicationTransition($this->application->fresh(), 'FEE_PAID');
        $this->assertTrue($statusValidationSettled['allowed']);
    }

    public function test_direct_operational_actions_from_applications_route(): void
    {
        $this->actingAs($this->registrar);

        // 1. Dispatch to Peer Reviewers directly from /applications
        Livewire::test(ApplicationManagement::class)
            ->call('openDispatchModal', $this->application->id)
            ->assertSet('showDispatchModal', true)
            ->assertSet('dispatchAppId', $this->application->id)
            ->call('toggleReviewerSelection', $this->reviewerUser->id)
            ->call('dispatchApplicationToReviewers')
            ->assertHasNoErrors()
            ->assertSet('showDispatchModal', false);

        $this->assertDatabaseHas('application_reviews', [
            'application_id' => $this->application->id,
            'reviewer_id' => $this->reviewerUser->id,
            'status' => 'PENDING',
        ]);
        $this->assertEquals('UNDER_REVIEW', $this->application->fresh()->status);

        // 2. Mark Incomplete Dossier directly from /applications
        Livewire::test(ApplicationManagement::class)
            ->call('openIncompleteModal', $this->application->id)
            ->assertSet('showIncompleteModal', true)
            ->set('incompleteDeficiencyNotes', 'Official MSc degree transcript missing certified stamp.')
            ->call('markAsIncompleteDossier')
            ->assertHasNoErrors()
            ->assertSet('showIncompleteModal', false);

        $this->assertEquals('INCOMPLETE_DOSSIER', $this->application->fresh()->status);
        $this->assertStringContainsString('Official MSc degree transcript missing certified stamp', $this->application->fresh()->disqualification_reason);

        // 3. Mark Eligible directly from /applications
        Livewire::test(ApplicationManagement::class)
            ->call('markAsEligible', $this->application->id);

        $this->assertEquals('ELIGIBLE', $this->application->fresh()->status);

        // 4. Reject / Disqualify directly from /applications
        Livewire::test(ApplicationManagement::class)
            ->call('openRejectModal', $this->application->id)
            ->assertSet('showRejectModal', true)
            ->set('rejectReason', 'Ineligible: Less than minimum required 3 publications in HEC recognized journals.')
            ->call('rejectApplication')
            ->assertHasNoErrors()
            ->assertSet('showRejectModal', false);

        $this->assertEquals('DISQUALIFIED', $this->application->fresh()->status);
        $this->assertStringContainsString('Less than minimum required 3 publications', $this->application->fresh()->disqualification_reason);

        // 5. Dynamic Status Transition modal directly from /applications
        Livewire::test(ApplicationManagement::class)
            ->call('openTransitionModal', $this->application->id)
            ->assertSet('showTransitionModal', true)
            ->set('transitionTargetStatus', 'APPEALED')
            ->set('transitionNotes', 'Candidate lodged formal grievance letter with proof.')
            ->call('executeStatusTransition')
            ->assertHasNoErrors()
            ->assertSet('showTransitionModal', false);

        $this->assertEquals('APPEALED', $this->application->fresh()->status);
    }
}
