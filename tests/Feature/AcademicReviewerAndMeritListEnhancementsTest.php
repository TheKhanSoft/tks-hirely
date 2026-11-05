<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\ReviewerProfile;
use App\Models\BasicInfo;
use App\Livewire\Admin\MeritListManagement;
use App\Livewire\Admin\ReviewerManagement;
use App\Mail\ApplicationAssignedReviewerMail;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AcademicReviewerAndMeritListEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $adv;
    protected $academicCampaign;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles & permissions
        $role = Role::firstOrCreate(['name' => 'super-admin']);
        Permission::firstOrCreate(['name' => 'manage-reviewers']);
        Permission::firstOrCreate(['name' => 'view-review-reports']);
        Permission::firstOrCreate(['name' => 'assign-reviewer']);
        Permission::firstOrCreate(['name' => 'export-reviewer']);
        Permission::firstOrCreate(['name' => 'view-application']);
        Permission::firstOrCreate(['name' => 'view-board-roster']);

        $this->adminUser = User::factory()->create([
            'email' => 'superadmin@hirely.test',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('super-admin');

        // Create advertisement
        $this->adv = Advertisement::create([
            'advertisement_no' => '58/2026',
            'title' => 'Accelerated Faculty Induction 2026',
            'year' => 2026,
            'valid_from' => now()->subMonth(),
            'valid_to' => now()->addMonth(),
            'is_active' => true,
        ]);

        // Create academic campaign
        $this->academicCampaign = Campaign::create([
            'advertisement_id' => $this->adv->id,
            'title' => 'Professor of Artificial Intelligence & Autonomous Systems',
            'job_code' => 'PROF-AI-01',
            'department' => 'Department of Computer Science & AI',
            'track_type' => 'ACADEMIC',
            'application_fee' => 3000,
            'positions_count' => 1,
            'is_active' => true,
        ]);
    }

    public function test_merit_list_management_filters_and_csv_export()
    {
        $this->actingAs($this->adminUser);

        // Create candidate with application
        $candidateUser = User::factory()->create(['email' => 'candidate.ai@hirely.test']);
        BasicInfo::create([
            'user_id' => $candidateUser->id,
            'track_type' => 'ACADEMIC',
            'date_of_birth' => '1985-05-15',
            'first_name' => 'Kamran',
            'last_name' => 'Bhatti',
            'father_name' => 'Muhammad Bhatti',
            'cnic_number' => '42101-1234567-1',
            'gender' => 'Male',
            'domicile' => 'Islamabad',
            'quota' => 'Open Merit',
        ]);

        $app = Application::create([
            'user_id' => $candidateUser->id,
            'campaign_id' => $this->academicCampaign->id,
            'status' => 'ELIGIBLE',
        ]);

        // Test Livewire MeritList component
        Livewire::test(MeritListManagement::class, ['campaignId' => $this->academicCampaign->id])
            ->call('setStage', 'intake')
            ->set('advertisementFilter', $this->adv->id)
            ->set('feeFilter', 'PAID')
            ->set('genderFilter', 'Male')
            ->set('quotaFilter', 'Open Merit')
            ->assertSee($app->tracking_number)
            ->assertSee('Kamran Bhatti')
            ->call('sort', 'aggregate_score')
            ->assertSet('sortBy', 'aggregate_score')
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_reviewer_management_candidacy_filters_and_export()
    {
        $this->actingAs($this->adminUser);

        $candidateUser = User::factory()->create(['email' => 'candidate.cs@hirely.test']);
        BasicInfo::create([
            'user_id' => $candidateUser->id,
            'track_type' => 'ACADEMIC',
            'date_of_birth' => '1985-05-15',
            'first_name' => 'Dr. Kamran',
            'last_name' => 'Bhatti',
            'cnic_number' => '42101-9988776-1',
        ]);

        $app = Application::create([
            'user_id' => $candidateUser->id,
            'campaign_id' => $this->academicCampaign->id,
            'status' => 'UNDER_REVIEW',
        ]);

        // Empanel a reviewer
        $revUser = User::factory()->create(['email' => 'kenji.sato@u-tokyo.ac.jp']);
        $profile = ReviewerProfile::create([
            'user_id' => $revUser->id,
            'name' => 'Prof. Dr. Kenji Sato',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'University of Tokyo',
            'country' => 'Japan',
            'is_active' => true,
        ]);

        // Create a review
        $review = ApplicationReview::create([
            'application_id' => $app->id,
            'reviewer_id' => $revUser->id,
            'assigned_by' => $this->adminUser->id,
            'assigned_at' => now()->subDays(2),
            'due_date' => now()->addDays(12),
            'status' => 'PENDING',
        ]);

        // Test component actions
        Mail::fake();

        Livewire::test(ReviewerManagement::class)
            ->call('setTab', 'dispatch')
            ->set('advertisementFilter', $this->adv->id)
            ->assertSee($app->tracking_number)
            ->assertSee('Dr. Kamran Bhatti')
            ->call('sortCandidacies', 'candidate_name')
            ->assertSet('candidacySortBy', 'candidate_name')
            ->call('exportDispatchCsv')
            ->assertFileDownloaded()
            // Open Reports Modal
            ->call('openReportsModal', $app->id)
            ->assertSet('showReportsModal', true)
            ->assertSee('External Peer Review Reports')
            ->assertSee('Prof. Dr. Kenji Sato')
            ->assertSee('Send Reminder Email')
            // Send reminder
            ->call('sendReviewerReminder', $review->id)
            ->assertHasNoErrors()
            // Extend deadline
            ->call('extendReviewDeadline', $review->id, 7)
            ->assertHasNoErrors()
            // Export peer reports CSV
            ->call('exportApplicationPeerReportsCsv', $app->id)
            ->assertFileDownloaded()
            // Remove reviewer assignment
            ->call('removeReviewerAssignment', $review->id)
            ->assertHasNoErrors();

        Mail::assertSent(ApplicationAssignedReviewerMail::class);

        $this->assertDatabaseMissing('application_reviews', ['id' => $review->id]);
    }

    public function test_peer_review_print_controller_routes()
    {
        $this->actingAs($this->adminUser);

        $candidateUser = User::factory()->create();
        BasicInfo::create([
            'user_id' => $candidateUser->id,
            'track_type' => 'ACADEMIC',
            'date_of_birth' => '1985-05-15',
            'first_name' => 'Dr. Kamran',
            'last_name' => 'Bhatti',
            'father_name' => 'Muhammad Bhatti',
            'cnic_number' => '42101-1122334-1',
        ]);

        $app = Application::create([
            'user_id' => $candidateUser->id,
            'campaign_id' => $this->academicCampaign->id,
            'status' => 'UNDER_REVIEW',
        ]);

        $revUser = User::factory()->create();
        ReviewerProfile::create([
            'user_id' => $revUser->id,
            'name' => 'Prof. Dr. Kenji Sato',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'University of Tokyo',
            'country' => 'Japan',
        ]);

        ApplicationReview::create([
            'application_id' => $app->id,
            'reviewer_id' => $revUser->id,
            'assigned_by' => $this->adminUser->id,
            'overall_score' => 91.5,
            'recommendation' => 'STRONGLY_RECOMMENDED',
            'report_text' => 'High quality scholarly dossier. Recommended for statutory appointment.',
            'status' => 'SUBMITTED',
            'submitted_at' => now(),
        ]);

        // Print consensus report route
        $response = $this->get(route('peer-reports.print', $app->id));
        $response->assertStatus(200);
        $response->assertSee('CONFIDENTIAL EXTERNAL PEER REVIEW EVALUATION CERTIFICATE');
        $response->assertSee('Prof. Dr. Kenji Sato');
        $response->assertSee('91.5');
        $response->assertSee('STRONGLY RECOMMENDED');

        // Print dispatch register route
        $dispatchResponse = $this->get(route('peer-dispatch-register.print'));
        $dispatchResponse->assertStatus(200);
        $dispatchResponse->assertSee('CONFIDENTIAL EXTERNAL ACADEMIC PEER REVIEW DISPATCH REGISTER');
        $dispatchResponse->assertSee($app->tracking_number);
    }
}
