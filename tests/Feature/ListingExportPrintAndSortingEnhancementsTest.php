<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Livewire\Scrutiny\Dashboard as ScrutinyDashboard;
use App\Livewire\Board\Dashboard as BoardDashboard;
use App\Livewire\Admin\AppellateReview;
use App\Livewire\Admin\ApplicationManagement;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\PaymentManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\AdvertisementManagement;
use App\Livewire\Admin\ReviewerManagement;
use App\Livewire\Admin\ConstitutionCriteriaManagement;
use App\Livewire\Admin\AuditLogManagement;
use App\Models\ReviewerProfile;
use App\Models\ApplicationReview;
use App\Models\CommitteeConstitution;
use App\Models\SecurityAuditLog;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ListingExportPrintAndSortingEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $adv;
    protected $campaign;
    protected $applicantUser;
    protected $application;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles & Permissions
        $adminRole = Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'applicant']);
        Role::firstOrCreate(['name' => 'scrutiny-committee']);
        Role::firstOrCreate(['name' => 'selection-board-member']);
        Role::firstOrCreate(['name' => 'appellate-committee']);

        Permission::firstOrCreate(['name' => 'view-application']);
        Permission::firstOrCreate(['name' => 'export-application']);
        Permission::firstOrCreate(['name' => 'scrutinize-application']);
        Permission::firstOrCreate(['name' => 'generate-scrutiny-certificate']);
        Permission::firstOrCreate(['name' => 'view-board-roster']);
        Permission::firstOrCreate(['name' => 'access-board-grading']);
        Permission::firstOrCreate(['name' => 'evaluate-application']);
        Permission::firstOrCreate(['name' => 'view-appeal']);
        Permission::firstOrCreate(['name' => 'process-appeal']);
        Permission::firstOrCreate(['name' => 'view-basic-info']);
        Permission::firstOrCreate(['name' => 'export-candidate']);
        Permission::firstOrCreate(['name' => 'view-payment']);
        Permission::firstOrCreate(['name' => 'export-payment']);
        Permission::firstOrCreate(['name' => 'view-user']);
        Permission::firstOrCreate(['name' => 'export-user']);
        Permission::firstOrCreate(['name' => 'view-advertisement']);
        Permission::firstOrCreate(['name' => 'export-advertisement']);
        Permission::firstOrCreate(['name' => 'view-campaign']);
        Permission::firstOrCreate(['name' => 'export-campaign']);
        Permission::firstOrCreate(['name' => 'view-reviewer']);
        Permission::firstOrCreate(['name' => 'manage-reviewers']);
        Permission::firstOrCreate(['name' => 'assign-reviewer']);
        Permission::firstOrCreate(['name' => 'view-review-reports']);
        Permission::firstOrCreate(['name' => 'manage-constitutions']);
        Permission::firstOrCreate(['name' => 'view-audit-log']);

        $this->adminUser = User::factory()->create([
            'email' => 'admin.listings@hirely.test',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('super-admin');

        $this->adv = Advertisement::create([
            'advertisement_no' => '58/2026',
            'title' => 'Faculty Induction Drive 2026',
            'year' => 2026,
            'valid_from' => now()->subMonth(),
            'valid_to' => now()->addMonth(),
            'is_active' => true,
        ]);

        $this->campaign = Campaign::create([
            'advertisement_id' => $this->adv->id,
            'title' => 'Associate Professor of Computer Science',
            'job_code' => 'CS-PROF-2026',
            'department' => 'Department of Computer Science',
            'track_type' => 'ACADEMIC',
            'application_fee' => 2500,
            'positions_count' => 2,
            'is_active' => true,
        ]);

        $this->applicantUser = User::factory()->create(['email' => 'tariq.aziz@hirely.test']);
        $this->applicantUser->assignRole('applicant');

        BasicInfo::create([
            'user_id' => $this->applicantUser->id,
            'track_type' => 'ACADEMIC',
            'date_of_birth' => '1988-04-12',
            'first_name' => 'Tariq',
            'last_name' => 'Aziz',
            'father_name' => 'Aziz Khan',
            'cnic_number' => '37405-1234567-9',
            'gender' => 'Male',
            'domicile' => 'Rawalpindi',
            'quota' => 'Open Merit',
        ]);

        $this->application = Application::create([
            'user_id' => $this->applicantUser->id,
            'campaign_id' => $this->campaign->id,
            'status' => 'PAID',
        ]);

        BillingLedger::create([
            'application_id' => $this->application->id,
            'consumer_id' => '100099887711',
            'amount' => 2500.00,
            'status' => 'SETTLED',
            'transaction_id' => 'TXN-TEST-12345',
            'payment_timestamp' => now(),
        ]);
    }

    public function test_scrutiny_dashboard_export_filters_and_sorting()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(ScrutinyDashboard::class)
            ->set('advertisementFilter', $this->adv->id)
            ->set('campaignFilter', $this->campaign->id)
            ->set('statusFilter', 'PAID')
            ->assertSee('Tariq Aziz')
            ->assertSee('CS-PROF-2026')
            ->call('sort', 'candidate_name')
            ->assertSet('sortBy', 'candidate_name')
            ->call('sort', 'candidate_name')
            ->assertSet('sortDir', 'desc')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_board_dashboard_export_filters_and_sorting()
    {
        $this->actingAs($this->adminUser);

        $this->application->update(['status' => 'ELIGIBLE']);

        Livewire::test(BoardDashboard::class)
            ->set('advertisementFilter', $this->adv->id)
            ->set('campaignFilter', $this->campaign->id)
            ->assertSee('Tariq Aziz')
            ->call('sort', 'candidate_name')
            ->assertSet('sortBy', 'candidate_name')
            ->call('sort', 'candidate_name')
            ->assertSet('sortDir', 'desc')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_appellate_review_export_filters_and_sorting()
    {
        $this->actingAs($this->adminUser);

        $this->application->update([
            'status' => 'APPEALED',
            'disqualification_reason' => 'Degree transcript missing terminal verification',
            'appeal_letter' => 'I have attached HEC verified terminal transcript for condonation.',
        ]);

        Livewire::test(AppellateReview::class)
            ->set('advertisementFilter', $this->adv->id)
            ->set('statusFilter', 'APPEALED')
            ->assertSee('Tariq Aziz')
            ->call('sort', 'candidate_name')
            ->assertSet('sortBy', 'candidate_name')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_payment_management_export_and_sorting()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(PaymentManagement::class)
            ->set('advertisementFilter', $this->adv->id)
            ->assertSee('100099887711')
            ->call('sort', 'consumer_id')
            ->assertSet('sortBy', 'consumer_id')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_candidate_profile_management_export_and_sorting()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(CandidateProfileManagement::class)
            ->set('advertisementFilter', $this->adv->id)
            ->assertSee('Tariq Aziz')
            ->call('sort', 'first_name')
            ->assertSet('sortBy', 'first_name')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_user_management_export_and_sorting()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(UserManagement::class)
            ->assertSee($this->applicantUser->email)
            ->call('sort', 'email')
            ->assertSet('sortBy', 'email')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_advertisement_management_export_and_sorting()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(AdvertisementManagement::class)
            ->assertSee('58/2026')
            ->call('sort', 'advertisement_no')
            ->assertSet('sortBy', 'advertisement_no')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_reviewer_management_sorting_and_reminders()
    {
        \Illuminate\Support\Facades\Mail::fake();
        $this->actingAs($this->adminUser);

        $reviewerUser = User::factory()->create(['email' => 'expert.sato@univ-tokyo.ac.jp']);
        $reviewerUser->assignRole('applicant');

        $profile = ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Prof. Dr. Kenji Sato',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'University of Tokyo',
            'country' => 'Japan',
            'is_active' => true,
        ]);

        $review = ApplicationReview::create([
            'application_id' => $this->application->id,
            'reviewer_id' => $reviewerUser->id,
            'status' => 'PENDING',
            'assigned_by' => $this->adminUser->id,
            'assigned_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        Livewire::test(ReviewerManagement::class)
            ->assertSee('Prof. Dr. Kenji Sato')
            ->call('sortBy', 'name')
            ->assertSet('sortBy', 'name')
            ->call('sortBy', 'name')
            ->assertSet('sortDirection', 'desc')
            ->call('remindAllPendingReviewers', $this->application->id)
            ->assertSee('Dispatched official reminders')
            ->call('sendReviewerReminder', $review->id)
            ->assertSee('Official reminder email sent');

        // Test re-open evaluation
        $review->update([
            'status' => 'SUBMITTED',
            'overall_score' => 91.5,
            'recommendation' => 'STRONGLY_RECOMMENDED',
            'report_text' => 'High quality scholarly dossier.',
            'submitted_at' => now(),
        ]);

        Livewire::test(ReviewerManagement::class)
            ->call('reopenReviewerEvaluation', $review->id)
            ->assertSee('Evaluation report re-opened');

        $this->assertEquals('PENDING', $review->fresh()->status);
    }

    public function test_constitution_criteria_management_sorting()
    {
        $this->actingAs($this->adminUser);

        CommitteeConstitution::create([
            'name' => 'Academic Subject Expert Referees Panel',
            'slug' => 'expert-referees-panel',
            'organization_sector' => 'ACADEMIC',
            'committee_type' => 'SELECTION_BOARD',
            'responsible_stage' => 'stage_peer_review',
            'required_total' => 3,
            'required_internal' => 0,
            'required_external' => 3,
            'required_peer_reviewers' => 3,
            'enforce_strict_quota' => true,
        ]);

        Livewire::test(ConstitutionCriteriaManagement::class)
            ->assertSee('Academic Subject Expert Referees Panel')
            ->call('sortBy', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortDirection', 'desc')
            ->call('sortBy', 'name')
            ->assertSet('sortDirection', 'asc')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_audit_log_management_sorting()
    {
        $this->actingAs($this->adminUser);

        SecurityAuditLog::create([
            'user_id' => $this->adminUser->id,
            'action_type' => 'CREATED',
            'table_name' => 'applications',
            'record_id' => '1001',
            'state_after' => ['status' => 'PAID'],
            'ip_address' => '192.168.1.100',
        ]);

        Livewire::test(AuditLogManagement::class)
            ->assertSee('applications')
            ->call('sortBy', 'action_type')
            ->assertSet('sortBy', 'action_type')
            ->call('sortBy', 'action_type')
            ->assertSet('sortDirection', 'desc')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_help_hint_component_renders_correctly()
    {
        $view = $this->blade('<x-help-hint type="inline" title="Test Title" text="Test Help Text" />');
        $view->assertSee('help-hint-popover');
        $view->assertSee('Test Title');
        $view->assertSee('Test Help Text');
        // Ensure conflicting :style was removed
        $view->assertDontSee(':style="placeBottom');
    }
}
