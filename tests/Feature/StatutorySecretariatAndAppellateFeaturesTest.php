<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\Publication;
use Spatie\Permission\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\HecHjrsJournalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StatutorySecretariatAndAppellateFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $applicant;
    protected Campaign $campaign;
    protected Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        SystemSetting::seedDefaultSettings();

        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $applicantRole = Role::firstOrCreate(['name' => 'applicant', 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'email' => 'admin_statutory@hirely.test',
        ]);
        $this->admin->assignRole($adminRole);

        $this->applicant = User::factory()->create([
            'email' => 'applicant_statutory@hirely.test',
        ]);
        $this->applicant->assignRole($applicantRole);

        BasicInfo::create([
            'user_id' => $this->applicant->id,
            'first_name' => 'Dr. Candidate',
            'last_name' => 'Scholar',
            'father_name' => 'Prof. Scholar Senior',
            'cnic_number' => '12345-6789012-3',
            'date_of_birth' => '1990-01-01',
            'mobile_number' => '+923001234567',
            'phone_number' => '+923001234567',
            'city' => 'Mardan',
            'postal_address' => 'University Road, Mardan',
        ]);

        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026-01',
            'title' => 'Statutory Faculty Advertisement 2026',
            'tracking_code' => 'ADV-2026-STAT',
            'year' => 2026,
            'valid_from' => now()->subDay(),
            'valid_to' => now()->addMonth(),
            'is_active' => true,
        ]);

        $this->campaign = Campaign::create([
            'advertisement_id' => $ad->id,
            'department' => 'Department of Computer Science',
            'title' => 'Assistant Professor of Data Science',
            'job_code' => 'DS-ASST-2026',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $this->application = Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->campaign->id,
            'tracking_number' => 'APP-STAT-001',
            'status' => 'ELIGIBLE',
        ]);
    }

    /**
     * Test 1: Feature toggles in SystemSetting model and Admin Settings Livewire.
     */
    public function test_statutory_feature_toggles_default_to_enabled_and_can_be_toggled(): void
    {
        // 1. Defaults should be true
        $this->assertTrue(SystemSetting::isFeatureEnabled('interview_call_letters'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('referee_magic_links'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('hjrs_journal_lookup'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('appellate_workbench'));

        // 2. Disable toggles
        SystemSetting::set('enable_interview_call_letters', false, 'pipeline', 'boolean');
        SystemSetting::set('enable_referee_magic_links', false, 'pipeline', 'boolean');
        SystemSetting::set('enable_hjrs_journal_lookup', false, 'academic_criteria', 'boolean');
        SystemSetting::set('enable_appellate_workbench', false, 'pipeline', 'boolean');

        $this->assertFalse(SystemSetting::isFeatureEnabled('interview_call_letters'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('referee_magic_links'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('hjrs_journal_lookup'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('appellate_workbench'));
    }

    /**
     * Test 2: Interview Call Letter issuance, PDF print view, and candidate access.
     */
    public function test_interview_call_letter_issuance_and_printable_view(): void
    {
        // Schedule and issue call letter
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Admin\ApplicationManagement::class)
            ->call('openCallLetterModal', $this->application->id)
            ->set('callLetterDate', '2026-12-15')
            ->set('callLetterTime', '10:30 AM')
            ->set('callLetterReportingTime', '09:00 AM')
            ->set('callLetterVenue', 'Senate Hall, Central Administration')
            ->set('callLetterCommitteeRoom', 'Selection Board Room 1')
            ->set('callLetterInstructions', 'Bring original terminal PhD degree, HEC equivalence, and NOC.')
            ->set('callLetterSendEmail', false)
            ->set('callLetterSendSms', false)
            ->call('issueCallLetter');

        $this->application->refresh();

        $this->assertEquals('2026-12-15 00:00:00', $this->application->interview_date->format('Y-m-d H:i:s'));
        $this->assertEquals('10:30 AM', $this->application->interview_time);
        $this->assertEquals('09:00 AM', $this->application->interview_reporting_time);
        $this->assertEquals('Senate Hall, Central Administration', $this->application->interview_venue);
        $this->assertNotNull($this->application->interview_call_letter_issued_at);
        $this->assertTrue($this->application->hasCallLetterIssued());

        // Test Printable View as Staff
        $response = $this->get("/application/{$this->application->id}/call-letter");
        $response->assertStatus(200);
        $response->assertSee('STATUTORY SUMMONS');
        $response->assertSee('Senate Hall, Central Administration');
        $response->assertSee('10:30 AM');
        $response->assertSee('Dr. Candidate Scholar');

        // Test Candidate Access to their own call letter
        $this->actingAs($this->applicant);
        $candResponse = $this->get("/application/{$this->application->id}/call-letter");
        $candResponse->assertStatus(200);
        $candResponse->assertSee('Senate Hall, Central Administration');

        // Test Another Candidate cannot access (403)
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);
        $unauthResponse = $this->get("/application/{$this->application->id}/call-letter");
        $unauthResponse->assertStatus(403);

        // Test Disabled Feature Toggle (403)
        SystemSetting::set('enable_interview_call_letters', false, 'pipeline', 'boolean');
        $this->actingAs($this->admin);
        $disabledResponse = $this->get("/application/{$this->application->id}/call-letter");
        $disabledResponse->assertStatus(403);
    }

    /**
     * Test 3: Passwordless Tokenized Review Desk for External Academic Referees.
     */
    public function test_external_referee_tokenized_magic_link_review(): void
    {
        $reviewerUser = User::factory()->create(['email' => 'dean_foreign@oxford.test']);

        $review = ApplicationReview::create([
            'application_id' => $this->application->id,
            'reviewer_id' => $reviewerUser->id,
            'assigned_by' => $this->admin->id,
            'status' => 'PENDING',
        ]);

        $token = $review->ensureAccessToken();
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));

        // Guest (unauthenticated external reviewer) accessing via cryptographic token
        $this->post('/logout'); // ensure completely logged out guest

        Livewire::test(\App\Livewire\Reviewer\DirectTokenizedReviewDesk::class, ['token' => $token])
            ->assertSee('Academic Portfolio Evaluation: Assistant Professor of Data Science')
            ->set('overall_score', 88.5)
            ->set('recommendation', 'STRONGLY_RECOMMENDED')
            ->set('report_text', 'The candidate exhibits an exemplary publication record in high-impact journals with sound statistical methodologies.')
            ->call('submitEvaluation')
            ->assertSee('Evaluation Successfully Submitted!');

        $review->refresh();
        $this->assertEquals('SUBMITTED', $review->status);
        $this->assertEquals('88.50', (string) $review->overall_score);
        $this->assertEquals('STRONGLY_RECOMMENDED', $review->recommendation);
        $this->assertNotNull($review->submitted_at);

        // When feature is disabled, desk shows disabled notice
        SystemSetting::set('enable_referee_magic_links', false, 'pipeline', 'boolean');

        Livewire::test(\App\Livewire\Reviewer\DirectTokenizedReviewDesk::class, ['token' => $token])
            ->assertSee('External Referee Direct Desk Currently Disabled');
    }

    /**
     * Test 4: HEC HJRS Journal Lookup & Predatory Journal Filter (REST API & Service).
     */
    public function test_hec_hjrs_journal_lookup_and_predatory_detector(): void
    {
        // 1. Service direct lookup
        $natureResults = HecHjrsJournalService::lookup('Nature');
        $this->assertNotEmpty($natureResults);
        $this->assertEquals('W', $natureResults[0]['category']);
        $this->assertFalse($natureResults[0]['is_predatory']);

        // 2. Predatory detection
        $predCheck = HecHjrsJournalService::isPredatory('Wulfenia Journal', '1561-882X');
        $this->assertTrue($predCheck['is_predatory']);
        $this->assertEquals('CRITICAL', $predCheck['risk_level']);

        // 3. REST API Endpoint - Lookup Nature
        $res = $this->getJson('/api/hjrs/lookup?q=Nature');
        $res->assertStatus(200);
        $res->assertJsonPath('success', true);
        $res->assertJsonPath('is_predatory', false);
        $res->assertJsonFragment(['category' => 'W']);

        // 4. REST API Endpoint - Predatory Detection
        $predRes = $this->getJson('/api/hjrs/lookup?q=Wulfenia');
        $predRes->assertStatus(200);
        $predRes->assertJsonPath('is_predatory', true);
        $predRes->assertJsonFragment(['is_predatory' => true]);

        // 5. REST API when disabled by administrator
        SystemSetting::set('enable_hjrs_journal_lookup', false, 'academic_criteria', 'boolean');
        $disabledApi = $this->getJson('/api/hjrs/lookup?q=Nature');
        $disabledApi->assertStatus(200);
        $disabledApi->assertJsonPath('enabled', false);
        $disabledApi->assertJsonPath('success', false);
    }

    /**
     * Test 5: Grievance Appellate Adjudication Workbench & Statutory Decree Execution.
     */
    public function test_grievance_appellate_workbench_decree_execution(): void
    {
        $appRole = Role::firstOrCreate(['name' => 'appellate-committee', 'guard_name' => 'web']);
        $this->admin->assignRole($appRole);
        $this->actingAs($this->admin);

        // Put application into APPEALED state
        $this->application->update([
            'status' => 'APPEALED',
            'disqualification_reason' => 'Candidate 2nd Division in terminal MS degree without equivalence.',
            'appeal_letter' => 'Please find attached the HEC Equivalence Certificate confirming my foreign degree as 1st Class.',
        ]);

        // Execute Appellate Decree: Condon Deficiency & Reinstate as ELIGIBLE
        Livewire::test(\App\Livewire\Admin\AppellateReview::class)
            ->call('selectAppeal', $this->application->id)
            ->call('openResolutionModal', 'ELIGIBLE')
            ->set('statutoryOrderNotes', 'HEC Equivalence Certificate validated. Candidate condoned and restored as ELIGIBLE.')
            ->call('executeAppellateDecree');

        $this->application->refresh();

        $this->assertEquals('ELIGIBLE', $this->application->status);
        $this->assertNull($this->application->disqualification_reason);
        $this->assertEquals('HEC Equivalence Certificate validated. Candidate condoned and restored as ELIGIBLE.', $this->application->appeal_decision_remarks);
        $this->assertEquals($this->admin->id, $this->application->appeal_decided_by);
        $this->assertNotNull($this->application->appeal_decided_at);

        // When toggle is disabled, execution is blocked
        SystemSetting::set('enable_appellate_workbench', false, 'pipeline', 'boolean');

        $this->application->update(['status' => 'APPEALED']);

        Livewire::test(\App\Livewire\Admin\AppellateReview::class)
            ->call('selectAppeal', $this->application->id)
            ->call('openResolutionModal', 'REJECTED')
            ->call('executeAppellateDecree');

        $this->application->refresh();
        $this->assertEquals('APPEALED', $this->application->status); // unchanged because feature disabled
    }

    public function test_appellate_hearing_notice_summons_and_automated_readmission(): void
    {
        $this->actingAs($this->admin);
        SystemSetting::set('enable_appellate_workbench', true, 'pipeline', 'boolean');

        $this->application->update([
            'status' => 'APPEALED',
            'disqualification_reason' => 'Scrutiny deficiency: incomplete degree transcript.',
            'appeal_letter' => 'I have uploaded the verified official university transcript.',
        ]);

        $futureDate = now()->addDays(7)->format('Y-m-d');

        // 1. Schedule hearing notice
        Livewire::test(\App\Livewire\Admin\AppellateReview::class)
            ->call('selectAppeal', $this->application->id)
            ->call('openHearingModal', $this->application->id)
            ->assertSet('isHearingModalOpen', true)
            ->set('hearingDate', $futureDate)
            ->set('hearingTime', '02:30 PM PST')
            ->set('hearingVenue', 'Senate Executive Hall, Administration Block')
            ->set('hearingMode', 'IN_PERSON')
            ->set('hearingNotes', 'Bring original matric, fsc, bs transcripts and CNIC.')
            ->call('scheduleHearing')
            ->assertSet('isHearingModalOpen', false);

        $this->application->refresh();
        $this->assertTrue($this->application->isHearingScheduled());
        $this->assertEquals($futureDate, $this->application->appeal_hearing_date->format('Y-m-d'));
        $this->assertEquals('02:30 PM PST', $this->application->appeal_hearing_time);
        $this->assertEquals('Senate Executive Hall, Administration Block', $this->application->appeal_hearing_venue);
        $this->assertNotNull($this->application->appeal_hearing_scheduled_at);

        // 2. Issue ELIGIBLE Decree with automated re-admission
        Livewire::test(\App\Livewire\Admin\AppellateReview::class)
            ->call('selectAppeal', $this->application->id)
            ->call('openResolutionModal', 'ELIGIBLE')
            ->set('statutoryOrderNotes', 'Appellant presented original sealed transcripts. Deficiency condoned; candidate restored & re-admitted.')
            ->call('executeAppellateDecree');

        $this->application->refresh();
        $this->assertEquals('ELIGIBLE', $this->application->status);
        $this->assertNull($this->application->disqualification_reason);
        $this->assertNotNull($this->application->appeal_readmitted_at);
        $this->assertNotNull($this->application->appeal_decided_at);
        $this->assertEquals($this->admin->id, $this->application->appeal_decided_by);
    }
}
