<?php

namespace Tests\Feature;

use App\Livewire\Admin\ExecutiveAnalytics;
use App\Livewire\Admin\SystemSettings;
use App\Livewire\Board\ProjectorMeritBoard;
use App\Models\Advertisement;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Models\Campaign;
use App\Models\CampaignCommittee;
use App\Models\LiveEvaluation;
use App\Models\SecurityAuditLog;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\PermissionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectorBoardAndExecutiveAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $registrar;
    protected User $boardMember;
    protected User $applicant;
    protected Campaign $campaign;
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        SystemSetting::seedDefaultSettings();
        PermissionRegistry::syncAll();

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $registrarRole = Role::firstOrCreate(['name' => 'registrar', 'guard_name' => 'web']);
        $boardRole = Role::firstOrCreate(['name' => 'selection-board-member', 'guard_name' => 'web']);
        $applicantRole = Role::firstOrCreate(['name' => 'applicant', 'guard_name' => 'web']);

        $this->superAdmin = User::factory()->create(['email' => 'vc_superadmin@university.edu']);
        $this->superAdmin->assignRole($superAdminRole);

        $this->registrar = User::factory()->create(['email' => 'registrar@university.edu']);
        $this->registrar->assignRole($registrarRole);

        $this->boardMember = User::factory()->create(['email' => 'board_expert@university.edu']);
        $this->boardMember->assignRole($boardRole);

        $this->applicant = User::factory()->create(['email' => 'applicant_merit@gmail.com']);
        $this->applicant->assignRole($applicantRole);

        BasicInfo::create([
            'user_id' => $this->applicant->id,
            'first_name' => 'Dr. Ayesha',
            'last_name' => 'Khan',
            'father_name' => 'Muhammad Tariq Khan',
            'cnic_number' => '17301-1234567-1',
            'date_of_birth' => '1988-06-15',
            'gender' => 'FEMALE',
            'quota' => 'OPEN_MERIT',
            'domicile' => 'Khyber Pakhtunkhwa',
            'religion' => 'ISLAM',
            'phone_number' => '+923001234567',
        ]);

        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026-BOARD',
            'title' => 'Statutory Faculty Advertisement 2026',
            'tracking_code' => 'ADV-2026-BOARD',
            'year' => 2026,
            'valid_from' => now()->subDays(20),
            'valid_to' => now()->addDays(10),
            'is_active' => true,
        ]);

        $this->campaign = Campaign::create([
            'advertisement_id' => $ad->id,
            'department' => 'Department of Data Science',
            'title' => 'Professor of Computer Science',
            'job_code' => 'CS-PROF-2026',
            'track_type' => 'TEACHING',
            'opening_date' => now()->subDays(20),
            'closing_date' => now()->addDays(10),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $this->application = Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->campaign->id,
            'tracking_number' => 'APP-BOARD-001',
            'status' => 'ELIGIBLE',
            'interview_date' => now()->addDays(2),
        ]);

        // Empanel a committee member
        CampaignCommittee::create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->boardMember->id,
            'member_category' => 'EXTERNAL',
            'role_designation' => 'Subject Specialist',
            'affiliation' => 'Quaid-i-Azam University',
            'country' => 'Pakistan',
            'assigned_at' => now(),
        ]);
    }

    /**
     * Test 1: Feature toggles can be inspected and updated in SystemSetting & SystemSettings Livewire.
     */
    public function test_projector_and_executive_analytics_feature_toggles(): void
    {
        // 1. Defaults are enabled
        $this->assertTrue(SystemSetting::isFeatureEnabled('projector_merit_board'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('executive_analytics'));

        // 2. Can be toggled via SystemSetting::set
        SystemSetting::set('enable_projector_merit_board', false, 'pipeline', 'boolean');
        SystemSetting::set('enable_executive_analytics', false, 'governance', 'boolean');

        $this->assertFalse(SystemSetting::isFeatureEnabled('projector_merit_board'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('executive_analytics'));

        // 3. SystemSettings Livewire loads and persists toggles
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->assertSet('enable_projector_merit_board', false)
            ->assertSet('enable_executive_analytics', false)
            ->set('enable_projector_merit_board', true)
            ->set('enable_executive_analytics', true)
            ->call('savePipelineSettings');

        $this->assertTrue(SystemSetting::isFeatureEnabled('projector_merit_board'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('executive_analytics'));
    }

    /**
     * Test 2: Security & Permissions check (403 for unauthorized candidates).
     */
    public function test_unauthorized_user_cannot_access_projector_board_or_executive_analytics(): void
    {
        // Applicant cannot access Projector Board
        Livewire::actingAs($this->applicant)
            ->test(ProjectorMeritBoard::class)
            ->assertStatus(403);

        // Applicant cannot access Executive Analytics
        Livewire::actingAs($this->applicant)
            ->test(ExecutiveAnalytics::class)
            ->assertStatus(403);

        // Selection board member can access Projector Board but NOT Executive Analytics
        Livewire::actingAs($this->boardMember)
            ->test(ProjectorMeritBoard::class)
            ->assertStatus(200);

        Livewire::actingAs($this->boardMember)
            ->test(ExecutiveAnalytics::class)
            ->assertStatus(403);

        // Registrar has access to both
        Livewire::actingAs($this->registrar)
            ->test(ProjectorMeritBoard::class)
            ->assertStatus(200);

        Livewire::actingAs($this->registrar)
            ->test(ExecutiveAnalytics::class)
            ->assertStatus(200);
    }

    /**
     * Test 3: Projector Merit Board renders live candidates, anonymization, and scoring rollup.
     */
    public function test_projector_merit_board_rendering_and_controls(): void
    {
        // Record live board member evaluation
        LiveEvaluation::create([
            'application_id' => $this->application->id,
            'board_member_id' => $this->boardMember->id,
            'academic_score' => 38.5,
            'medals_score' => 5.0,
            'publications_score' => 12.0,
            'events_score' => 2.0,
            'project_score' => 3.0,
            'interview_score' => 42.0,
            'is_outlier' => false,
        ]);

        $test = Livewire::actingAs($this->superAdmin)
            ->test(ProjectorMeritBoard::class, ['campaignId' => $this->campaign->id])
            ->assertStatus(200)
            ->assertSee('APP-00001')
            ->assertSee('Dr. Ayesha Khan')
            ->assertSee('Professor of Computer Science')
            ->assertSet('isAnonymized', false);

        // Toggle Anonymization
        $test->call('toggleAnonymization')
            ->assertSet('isAnonymized', true)
            ->assertSee('Candidate #APP-00001 (Masked)')
            ->assertDontSee('Dr. Ayesha Khan');

        // Toggle Theme
        $test->call('toggleTheme')
            ->assertSet('themeMode', 'light');

        // Toggle Auto-Refresh
        $test->call('toggleAutoRefresh')
            ->assertSet('autoRefresh', false);
    }

    /**
     * Test 4: Projector Merit Board respects disabled feature toggle.
     */
    public function test_projector_merit_board_displays_disabled_notice_when_toggled_off(): void
    {
        SystemSetting::set('enable_projector_merit_board', false, 'pipeline', 'boolean');

        Livewire::actingAs($this->superAdmin)
            ->test(ProjectorMeritBoard::class, ['campaignId' => $this->campaign->id])
            ->assertStatus(200)
            ->assertSee('Selection Board Big-Screen Projector Merit Board is Disabled')
            ->assertDontSee('ACTIVE INTERVIEW SPOTLIGHT');
    }

    /**
     * Test 5: Executive Analytics calculates statutory KPIs, quota compliance, and disqualification Pareto.
     */
    public function test_executive_analytics_dashboard_metrics_and_pareto(): void
    {
        // Create an additional disqualified applicant to test Pareto
        $disqUser = User::factory()->create(['email' => 'disq@university.edu']);
        BasicInfo::create([
            'user_id' => $disqUser->id,
            'first_name' => 'Mr. Disqualified',
            'last_name' => 'Applicant',
            'father_name' => 'Senior',
            'cnic_number' => '35201-9999999-1',
            'date_of_birth' => '1992-01-01',
            'gender' => 'MALE',
            'quota' => 'PUNJAB',
            'domicile' => 'Punjab',
            'phone_number' => '+923009999999',
        ]);

        Application::create([
            'user_id' => $disqUser->id,
            'campaign_id' => $this->campaign->id,
            'tracking_number' => 'APP-BOARD-DISQ',
            'status' => 'INELIGIBLE',
            'disqualification_reason' => 'Candidate terminal degree equivalence missing from HEC.',
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(ExecutiveAnalytics::class)
            ->assertStatus(200)
            ->assertSee('Executive Recruitment Intelligence')
            ->assertSee('Total Intake Applications')
            ->assertSee('Scrutiny Clearance Yield')
            ->assertSee('Scrutiny Disqualification Pareto Breakdown')
            ->assertSee('Statutory Quota & Regional Compliance', false)
            ->assertSee('Women Quota')
            ->assertSee('Missing HEC Equivalence / Accreditation');
    }

    /**
     * Test 6: Executive Analytics CSV export works and writes SecurityAuditLog.
     */
    public function test_executive_analytics_csv_export(): void
    {
        $component = Livewire::actingAs($this->registrar)
            ->test(ExecutiveAnalytics::class);

        $response = $component->call('exportExecutiveReport');

        $this->assertNotNull($response);

        // Verify SecurityAuditLog entry was created
        $auditLog = SecurityAuditLog::where('action_type', 'EXPORT_EXECUTIVE_ANALYTICS')->latest('id')->first();
        $this->assertNotNull($auditLog);
        $this->assertEquals($this->registrar->id, $auditLog->user_id);
    }

    /**
     * Test 7: Executive Analytics respects disabled feature toggle.
     */
    public function test_executive_analytics_displays_disabled_notice_when_toggled_off(): void
    {
        SystemSetting::set('enable_executive_analytics', false, 'governance', 'boolean');

        Livewire::actingAs($this->registrar)
            ->test(ExecutiveAnalytics::class)
            ->assertStatus(200)
            ->assertSee('Executive Recruitment Analytics Dashboard is Disabled')
            ->assertDontSee('Total Intake Applications');
    }

    /**
     * Test 8: Statutory Quorum Tracker and Consensus Ballot digital sign-off.
     */
    public function test_projector_quorum_tracker_and_consensus_ballot_sign_off(): void
    {
        $component = Livewire::actingAs($this->superAdmin)
            ->test(ProjectorMeritBoard::class, ['campaignId' => $this->campaign->id])
            ->assertStatus(200)
            ->assertSee('Board Quorum')
            ->assertSee('Consensus Ballot')
            ->call('toggleQuorumModal')
            ->assertSet('showQuorumModal', true)
            ->assertSee('Statutory Selection Board Quorum Tracker')
            ->call('toggleQuorumModal')
            ->assertSet('showQuorumModal', false)
            ->call('openConsensusModal')
            ->assertSet('showConsensusModal', true)
            ->assertSee('Selection Board Consensus Ballot', false)
            ->set('consensusResolution', 'Special Syndicate Recommendation Approved by Selection Board.')
            ->call('signAndLockConsensusBallot')
            ->assertHasNoErrors()
            ->assertSee('Digital Sign-off Recorded');

        // Verify ballot stored in campaign scoring rules
        $freshCamp = $this->campaign->fresh();
        $this->assertNotNull($freshCamp->scoring_rules['consensus_ballot']);
        $this->assertEquals('Special Syndicate Recommendation Approved by Selection Board.', $freshCamp->scoring_rules['consensus_ballot']['resolution']);
        $this->assertEquals($this->superAdmin->id, $freshCamp->scoring_rules['consensus_ballot']['signed_by_user_id']);
    }
}
