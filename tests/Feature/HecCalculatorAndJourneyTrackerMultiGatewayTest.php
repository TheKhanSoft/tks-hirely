<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BillingLedger;
use App\Models\AcademicDegree;
use App\Models\WorkExperience;
use App\Models\Publication;
use App\Models\SystemSetting;
use App\Services\HecStatutoryCalculatorService;
use App\Services\Sms\SmsGatewayService;
use Livewire\Livewire;
use App\Livewire\Applicant\Dashboard as ApplicantDashboard;
use App\Livewire\Admin\ApplicationVerification;
use App\Livewire\Board\LiveGradingPanel;
use App\Livewire\Admin\SystemSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HecCalculatorAndJourneyTrackerMultiGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $permissions = [
            'submit-applications',
            'view-application',
            'scrutinize-application',
            'change-application-status',
            'access-board-grading',
            'evaluate-application',
            'submit-board-score',
            'manage-settings',
            'manage-sms-settings',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'applicant', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'scrutiny-committee', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'selection-board-member', 'guard_name' => 'web']);
    }

    protected function createCampaign(array $overrides = []): Campaign
    {
        static $codeIndex = 1;
        return Campaign::create(array_merge([
            'job_code' => 'CAMP-' . str_pad($codeIndex++, 4, '0', STR_PAD_LEFT),
            'title' => 'Assistant Professor of Computer Science',
            'track_type' => 'ACADEMIC',
            'department' => 'Computer Science',
            'application_fee' => 3000.00,
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'is_active' => true,
        ], $overrides));
    }

    protected function createBasicInfo(User $user, array $overrides = []): BasicInfo
    {
        static $cnicIndex = 1000;
        return BasicInfo::create(array_merge([
            'user_id' => $user->id,
            'first_name' => 'Dr. Ahmed',
            'last_name' => 'Khan',
            'cnic_number' => '17301-123' . ($cnicIndex++) . '-1',
            'date_of_birth' => '1985-05-15',
            'phone_number' => '03001234567',
            'quota' => 'General Merit',
        ], $overrides));
    }

    public function test_candidate_journey_milestones_and_progress_attribute_returns_accurate_six_milestones()
    {
        $user = User::factory()->create();
        $this->createBasicInfo($user);
        $campaign = $this->createCampaign(['title' => 'Assistant Professor of Computer Science']);

        // 1. SUBMITTED application
        $app = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);
        BillingLedger::create([
            'application_id' => $app->id,
            'consumer_id' => '9999900000001',
            'amount' => 3000,
            'status' => 'PENDING',
        ]);

        $milestones = $app->journey_milestones;
        $this->assertCount(6, $milestones);
        $this->assertEquals('Dossier Submitted', $milestones[0]['title']);
        $this->assertEquals('completed', $milestones[0]['state']);
        $this->assertEquals('1Link Fee Settled', $milestones[1]['title']);
        $this->assertEquals('active', $milestones[1]['state']);
        $this->assertEquals(25, $app->journey_progress['percent']);

        // 2. PAID application
        $app->update(['status' => 'PAID']);
        $milestonesPaid = $app->fresh()->journey_milestones;
        $this->assertEquals('completed', $milestonesPaid[1]['state']);
        $this->assertEquals('active', $milestonesPaid[2]['state']);
        $this->assertEquals(50, $app->journey_progress['percent']);

        // 3. ELIGIBLE application
        $app->update(['status' => 'ELIGIBLE']);
        $milestonesEligible = $app->fresh()->journey_milestones;
        $this->assertEquals('completed', $milestonesEligible[3]['state']);
        $this->assertEquals('active', $milestonesEligible[4]['state']);
        $this->assertEquals(80, $app->journey_progress['percent']);

        // 4. APPROVED application (Syndicate)
        $app->update(['status' => 'APPROVED']);
        $milestonesApproved = $app->fresh()->journey_milestones;
        $this->assertEquals('completed', $milestonesApproved[5]['state']);
        $this->assertEquals(100, $app->journey_progress['percent']);
    }

    public function test_applicant_dashboard_displays_journey_tracker_and_switches_applications()
    {
        $user = User::factory()->create();
        $user->assignRole('applicant');
        $user->givePermissionTo('submit-applications');

        $this->createBasicInfo($user);

        $campaign1 = $this->createCampaign(['title' => 'Professor of Mathematics']);
        $campaign2 = $this->createCampaign(['title' => 'Associate Professor of Statistics']);

        $app1 = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign1->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);
        $app2 = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign2->id,
            'status' => 'PAID',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test(ApplicantDashboard::class)
            ->assertSee('Dossier Submitted')
            ->assertSee('Professor of Mathematics')
            ->call('selectJourneyApp', $app2->id)
            ->assertSet('activeJourneyAppId', $app2->id)
            ->assertSee('Associate Professor of Statistics');
    }

    public function test_hec_statutory_calculator_service_tallies_publications_and_experience()
    {
        $user = User::factory()->create();
        $basicInfo = $this->createBasicInfo($user, [
            'first_name' => 'Dr. Sara',
            'last_name' => 'Qureshi',
        ]);

        // Terminal degree: PhD in 2018
        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Ph.D. / Terminal Degree',
            'degree_title' => 'Ph.D. Computer Engineering',
            'institution' => 'University of Engineering and Technology',
            'year' => 2018,
            'gpa' => '3.85',
            'scale' => '4.0',
            'major' => 'Computer Science',
            'board' => 'HEC',
            'division' => 'First',
            'is_verified' => true,
        ]);

        // Publications: 5 Category W, 4 Category X, 3 Category Y
        for ($i = 0; $i < 5; $i++) {
            Publication::create([
                'basic_info_id' => $basicInfo->id,
                'title' => "W Paper {$i}",
                'journal_name' => 'IEEE Transactions',
                'issn_print' => '1000-000' . $i,
                'hjrs_category' => 'W',
                'publication_year' => 2023,
                'author_order' => 1,
                'impact_factor' => 4.5,
                'is_verified' => true,
            ]);
        }
        for ($i = 0; $i < 4; $i++) {
            Publication::create([
                'basic_info_id' => $basicInfo->id,
                'title' => "X Paper {$i}",
                'journal_name' => 'ACM Computing Surveys',
                'issn_print' => '2000-000' . $i,
                'hjrs_category' => 'X',
                'publication_year' => 2022,
                'author_order' => 2,
                'is_corresponding_author' => true,
                'impact_factor' => 2.5,
                'is_verified' => true,
            ]);
        }
        for ($i = 0; $i < 3; $i++) {
            Publication::create([
                'basic_info_id' => $basicInfo->id,
                'title' => "Y Paper {$i}",
                'journal_name' => 'Journal of Systems',
                'issn_print' => '3000-000' . $i,
                'hjrs_category' => 'Y',
                'publication_year' => 2019,
                'author_order' => 3,
                'impact_factor' => 1.0,
                'is_verified' => true,
            ]);
        }

        // Experiences: 6 years post-PhD teaching
        WorkExperience::create([
            'basic_info_id' => $basicInfo->id,
            'job_title' => 'Assistant Professor',
            'organization' => 'National University',
            'start_date' => '2019-01-01',
            'end_date' => '2025-01-01',
            'experience_type' => 'Teaching',
            'is_verified' => true,
        ]);

        $pubSummary = HecStatutoryCalculatorService::calculatePublicationsSummary($basicInfo);
        $this->assertEquals(12, $pubSummary['total']);
        $this->assertEquals(5, $pubSummary['w_category']);
        $this->assertEquals(4, $pubSummary['x_category']);
        $this->assertEquals(3, $pubSummary['y_category']);
        $this->assertEquals(12, $pubSummary['hec_recognized_total']);
        $this->assertEquals(9, $pubSummary['last_5_years']);
        $this->assertEquals(9, $pubSummary['first_or_corr_authors']);

        $expSummary = HecStatutoryCalculatorService::calculateExperienceSummary($basicInfo, 2018);
        $this->assertGreaterThanOrEqual(5.9, $expSummary['total_years']);
        $this->assertGreaterThanOrEqual(5.9, $expSummary['post_terminal_years']);

        // Check Associate Professor evaluation
        $campaignAssoc = $this->createCampaign(['title' => 'Associate Professor of Computer Engineering']);
        $appAssoc = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaignAssoc->id,
            'status' => 'PAID',
        ]);

        $evalAssoc = HecStatutoryCalculatorService::evaluateCandidateCompliance($appAssoc);
        $this->assertTrue($evalAssoc['is_compliant']);
        $this->assertEquals('ASSOCIATE_PROFESSOR', $evalAssoc['target_rank']);
        $this->assertEmpty($evalAssoc['deficiencies']);
        $this->assertStringContainsString('STATUTORY SCRUTINY AUDIT', $evalAssoc['summary_text']);
    }

    public function test_application_verification_can_copy_hec_findings_to_remarks()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $candidate = User::factory()->create();
        $basicInfo = $this->createBasicInfo($candidate, [
            'first_name' => 'Tariq',
            'last_name' => 'Mahmood',
        ]);

        $campaign = $this->createCampaign(['title' => 'Lecturer in Physics']);
        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'PAID',
        ]);

        $this->actingAs($admin);

        Livewire::test(ApplicationVerification::class, ['id' => $application->id])
            ->assertSee('HEC Statutory Criteria & Journal Calculator')
            ->call('copyHecFindingsToRemarks')
            ->assertSet('showDisqualificationModal', true)
            ->assertSee('HEC Scrutiny findings successfully populated into remarks');
    }

    public function test_multi_device_live_grading_panel_touch_steppers_and_quick_scores()
    {
        $member = User::factory()->create();
        $member->assignRole('super-admin');

        $candidate = User::factory()->create();
        $basicInfo = $this->createBasicInfo($candidate, [
            'first_name' => 'Dr. Ali',
            'last_name' => 'Rehman',
        ]);

        $campaign = $this->createCampaign(['title' => 'Professor of Chemistry']);
        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'ELIGIBLE',
        ]);

        $this->actingAs($member);

        Livewire::test(LiveGradingPanel::class, ['id' => $application->id])
            ->assertSet('interview_score', 30.00)
            ->call('adjustScore', 5)
            ->assertSet('interview_score', 35.00)
            ->call('adjustScore', -1)
            ->assertSet('interview_score', 34.00)
            ->call('setQuickScore', 45)
            ->assertSet('interview_score', 45.00);
    }

    public function test_sms_gateway_service_normalizes_numbers_and_dispatches_via_providers()
    {
        // 1. Pakistani local format
        $this->assertEquals('+923001234567', SmsGatewayService::normalizePhoneNumber('03001234567'));
        $this->assertEquals('+923001234567', SmsGatewayService::normalizePhoneNumber('923001234567'));
        $this->assertEquals('+923001234567', SmsGatewayService::normalizePhoneNumber('+923001234567'));

        // 2. Dispatch via log driver
        SystemSetting::set('sms_enabled', true);
        SystemSetting::set('sms_provider', 'log');
        SystemSetting::set('sms_sender_id', 'HIRELY-TEST');

        $result = SmsGatewayService::send('03001234567', 'Test SMS Body');
        $this->assertTrue($result['success']);
        $this->assertEquals('log', $result['provider']);
        $this->assertStringStartsWith('LOG-', $result['message_id']);

        // 3. Test Livewire SystemSettings Test SMS Dispatcher
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(SystemSettings::class, ['tab' => 'email'])
            ->set('test_sms_recipient', '+923001234567')
            ->call('sendTestSms')
            ->assertSee('Sandbox SMS logged successfully');
    }

    public function test_statutory_sweeps_command_runs_successfully()
    {
        $this->artisan('hirely:run-statutory-sweeps --dry-run')
            ->expectsOutputToContain('Initiating Hirely Automated Statutory Sweeps [DRY-RUN MODE]')
            ->expectsOutputToContain('STATUTORY SWEEPS SUMMARY')
            ->assertExitCode(0);
    }
}
