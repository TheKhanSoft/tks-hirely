<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\ReviewerProfile;
use App\Models\CommitteeConstitution;
use App\Models\RecruitmentProcessFlow;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use App\Livewire\Admin\CampaignCrud;
use App\Livewire\Admin\ReviewerManagement;
use App\Livewire\Admin\ApplicationManagement;

class PositionLinkedConstitutionsAndEvaluationCriteriaTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $registrar;
    protected CommitteeConstitution $asstProfBoard;
    protected RecruitmentProcessFlow $academicFlow;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles & Permissions
        $adminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $registrarRole = Role::firstOrCreate(['name' => 'registrar']);
        $reviewerRole = Role::firstOrCreate(['name' => 'academic-reviewer']);
        $applicantRole = Role::firstOrCreate(['name' => 'applicant']);

        $perms = [
            'view-campaign', 'create-campaign', 'edit-campaign', 'delete-campaign',
            'view-committee', 'assign-committee', 'remove-committee', 'tune-constitution',
            'manage-reviewers', 'assign-reviewer', 'view-review-reports',
            'view-application', 'change-application-status',
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }
        $adminRole->syncPermissions(Permission::all());
        $registrarRole->syncPermissions(Permission::all());

        // Users
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@system.gov',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->registrar = User::factory()->create([
            'email' => 'registrar@system.gov',
            'is_active' => true,
        ]);
        $this->registrar->assignRole('registrar');

        // Statutory Board Constitution: Assistant Professor (6 members, 3 peer reviewers)
        $this->asstProfBoard = CommitteeConstitution::create([
            'name' => 'Assistant Professor Statutory Selection Board',
            'slug' => 'asst-prof-board',
            'organization_sector' => 'GENERAL',
            'committee_type' => 'SELECTION_BOARD',
            'responsible_stage' => 'stage_selection_board',
            'required_total' => 6,
            'required_internal' => 2,
            'required_external' => 3,
            'required_international' => 1,
            'required_independent' => 0,
            'required_peer_reviewers' => 3,
            'enforce_strict_quota' => true,
            'is_active' => true,
        ]);

        // Recruitment Process Flow
        $this->academicFlow = RecruitmentProcessFlow::create([
            'name' => 'Higher Education Standard 6-Stage Pipeline',
            'slug' => 'he-standard-pipeline',
            'sector' => 'ACADEMIC',
            'is_default' => true,
            'is_active' => true,
            'stages' => [
                ['step' => 1, 'name' => 'Application Submission', 'is_enabled' => true],
                ['step' => 2, 'name' => 'Dossier Scrutiny Vetting', 'is_enabled' => true],
                ['step' => 3, 'name' => 'External Academic Peer Review', 'is_enabled' => true],
                ['step' => 4, 'name' => 'Statutory Selection Board Interview', 'is_enabled' => true],
                ['step' => 5, 'name' => 'Syndicate Sanction', 'is_enabled' => true],
            ],
        ]);
    }

    public function test_campaign_can_link_statutory_board_and_process_flow_on_create_and_link_modal()
    {
        $this->actingAs($this->superAdmin);

        // 1. Create campaign with linked board and flow
        Livewire::test(CampaignCrud::class)
            ->call('openCreateForm')
            ->set('job_code', 'CS-AP-2026')
            ->set('title', 'Assistant Professor Computer Science')
            ->set('department', 'Computer Science')
            ->set('track_type', 'ACADEMIC')
            ->set('application_fee', 2500)
            ->set('opening_date', now()->subDay()->format('Y-m-d\TH:i'))
            ->set('closing_date', now()->addDays(30)->format('Y-m-d\TH:i'))
            ->set('constitution_id', $this->asstProfBoard->id)
            ->set('process_flow_id', $this->academicFlow->id)
            ->call('saveCampaign')
            ->assertHasNoErrors();

        $campaign = Campaign::where('job_code', 'CS-AP-2026')->first();
        $this->assertNotNull($campaign);
        $this->assertEquals($this->asstProfBoard->id, $campaign->constitution_id);
        $this->assertEquals($this->academicFlow->id, $campaign->process_flow_id);
        $this->assertEquals(3, $campaign->getRequiredPeerReviewersCount());

        // 2. Change linkage using quick Link Modal
        $anotherBoard = CommitteeConstitution::create([
            'name' => 'Executive Syndicate Board',
            'slug' => 'exec-syndicate',
            'organization_sector' => 'GENERAL',
            'committee_type' => 'EXECUTIVE',
            'responsible_stage' => 'stage_syndicate',
            'required_total' => 4,
            'required_peer_reviewers' => 2,
            'is_active' => true,
        ]);

        Livewire::test(CampaignCrud::class)
            ->call('openLinkModal', $campaign->id)
            ->assertSet('linkCampaignId', $campaign->id)
            ->assertSet('link_constitution_id', $this->asstProfBoard->id)
            ->set('link_constitution_id', $anotherBoard->id)
            ->call('saveLinkedBoardAndFlow')
            ->assertHasNoErrors();

        $campaign->refresh();
        $this->assertEquals($anotherBoard->id, $campaign->constitution_id);
        $this->assertEquals(2, $campaign->getRequiredPeerReviewersCount());
    }

    public function test_exact_peer_reviewer_quota_enforced_by_linked_constitution()
    {
        $this->actingAs($this->superAdmin);

        $campaign = Campaign::create([
            'job_code' => 'CS-PROF-101',
            'title' => 'Professor Computer Science',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'application_fee' => 3000,
            'constitution_id' => $this->asstProfBoard->id, // requires 3 peer reviewers with strict quota
            'is_active' => true,
        ]);

        $applicant = User::factory()->create(['email' => 'candidate@hirely.test']);
        $applicant->assignRole('applicant');

        $application = Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-EXACT-001',
            'status' => 'PAID',
        ]);

        // Create 4 empanelled reviewers
        $reviewers = [];
        for ($i = 1; $i <= 4; $i++) {
            $revUser = User::factory()->create(['email' => "expert{$i}@university.edu"]);
            $revUser->assignRole('academic-reviewer');
            ReviewerProfile::create([
                'user_id' => $revUser->id,
                'name' => "Prof. Expert {$i}",
                'subject' => 'Computer Science',
                'academic_rank' => 'Professor',
                'university' => "Tech University {$i}",
                'country' => 'Pakistan',
                'is_active' => true,
            ]);
            $reviewers[] = $revUser;
        }

        // Test 1: Selecting only 2 experts when 3 are strictly required is rejected by quota validation
        Livewire::test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->set('selectedReviewerIds', [$reviewers[0]->id, $reviewers[1]->id])
            ->call('dispatchApplicationToReviewers')
            ->assertHasErrors(['selectedReviewerIds']);

        $application->refresh();
        $this->assertEquals('PAID', $application->status);

        // Test 2: Selecting exactly 3 experts succeeds and transitions to UNDER_REVIEW
        Livewire::test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->set('selectedReviewerIds', [$reviewers[0]->id, $reviewers[1]->id, $reviewers[2]->id])
            ->call('dispatchApplicationToReviewers')
            ->assertHasNoErrors();

        $application->refresh();
        $this->assertEquals('UNDER_REVIEW', $application->status);
        $this->assertCount(3, $application->reviews);
    }

    public function test_peer_review_evaluation_rule_advances_on_majority_pass()
    {
        $this->actingAs($this->superAdmin);

        $campaign = Campaign::create([
            'job_code' => 'CS-ASST-202',
            'title' => 'Assistant Professor Software Engineering',
            'department' => 'Software Engineering',
            'track_type' => 'ACADEMIC',
            'application_fee' => 2000,
            'constitution_id' => $this->asstProfBoard->id, // 3 reviewers required, 2/3 passing threshold
            'is_active' => true,
        ]);

        $applicant = User::factory()->create(['email' => 'asst_cand@hirely.test']);
        $applicant->assignRole('applicant');

        $application = Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-PASS-002',
            'status' => 'UNDER_REVIEW',
        ]);

        // Create 3 reviews: 2 Positive (RECOMMEND/STRONGLY_RECOMMEND) and 1 Reject
        $rev1 = User::factory()->create();
        $rev2 = User::factory()->create();
        $rev3 = User::factory()->create();

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $rev1->id,
            'assigned_by' => $this->superAdmin->id,
            'status' => 'SUBMITTED',
            'recommendation' => 'STRONGLY_RECOMMENDED',
            'overall_score' => 88.5,
        ]);

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $rev2->id,
            'assigned_by' => $this->superAdmin->id,
            'status' => 'SUBMITTED',
            'recommendation' => 'RECOMMENDED',
            'overall_score' => 74.0,
        ]);

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $rev3->id,
            'assigned_by' => $this->superAdmin->id,
            'status' => 'SUBMITTED',
            'recommendation' => 'NOT_RECOMMENDED',
            'overall_score' => 45.0,
        ]);

        // Evaluate Peer Review Results from ReviewerManagement
        Livewire::test(ReviewerManagement::class)
            ->call('evaluatePeerReviewResults', $application->id);

        $application->refresh();
        $this->assertEquals('ELIGIBLE', $application->status);
    }

    public function test_peer_review_evaluation_rule_rejects_with_appeal_right_on_majority_fail()
    {
        $this->actingAs($this->superAdmin);

        $campaign = Campaign::create([
            'job_code' => 'CS-ASST-303',
            'title' => 'Assistant Professor Cybersecurity',
            'department' => 'Cybersecurity',
            'track_type' => 'ACADEMIC',
            'application_fee' => 2000,
            'constitution_id' => $this->asstProfBoard->id,
            'is_active' => true,
        ]);

        $applicant = User::factory()->create(['email' => 'cyber_cand@hirely.test']);
        $applicant->assignRole('applicant');

        $application = Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-FAIL-003',
            'status' => 'UNDER_REVIEW',
        ]);

        // Create 3 reviews: 1 Positive and 2 Negative (Fails 2/3 requirement)
        $rev1 = User::factory()->create();
        $rev2 = User::factory()->create();
        $rev3 = User::factory()->create();

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $rev1->id,
            'assigned_by' => $this->superAdmin->id,
            'status' => 'SUBMITTED',
            'recommendation' => 'RECOMMENDED',
            'overall_score' => 65.0,
        ]);

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $rev2->id,
            'assigned_by' => $this->superAdmin->id,
            'status' => 'SUBMITTED',
            'recommendation' => 'NOT_RECOMMENDED',
            'overall_score' => 40.0,
        ]);

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $rev3->id,
            'assigned_by' => $this->superAdmin->id,
            'status' => 'SUBMITTED',
            'recommendation' => 'NOT_RECOMMENDED',
            'overall_score' => 38.0,
        ]);

        // Evaluate from ApplicationManagement
        Livewire::test(ApplicationManagement::class)
            ->call('evaluatePeerReviewResults', $application->id);

        $application->refresh();
        $this->assertEquals('REJECTED', $application->status);
    }

    public function test_evaluating_incomplete_peer_reviews_is_prevented()
    {
        $this->actingAs($this->superAdmin);

        $campaign = Campaign::create([
            'job_code' => 'CS-ASST-404',
            'title' => 'Assistant Professor AI',
            'department' => 'Artificial Intelligence',
            'track_type' => 'ACADEMIC',
            'application_fee' => 2000,
            'constitution_id' => $this->asstProfBoard->id,
            'is_active' => true,
        ]);

        $applicant = User::factory()->create(['email' => 'ai_cand@hirely.test']);
        $applicant->assignRole('applicant');

        $application = Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-INCOMPLETE-004',
            'status' => 'UNDER_REVIEW',
        ]);

        // Only 1 review submitted out of 3 required
        $rev1 = User::factory()->create();
        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $rev1->id,
            'assigned_by' => $this->superAdmin->id,
            'status' => 'SUBMITTED',
            'recommendation' => 'RECOMMENDED',
            'overall_score' => 80.0,
        ]);

        Livewire::test(ReviewerManagement::class)
            ->call('evaluatePeerReviewResults', $application->id);

        $application->refresh();
        $this->assertEquals('UNDER_REVIEW', $application->status);
    }
}
