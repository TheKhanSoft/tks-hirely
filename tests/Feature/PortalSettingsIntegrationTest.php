<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Models\Campaign;
use App\Models\Degree;
use App\Models\Publication;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PortalSettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $applicant;
    protected Campaign $academicCampaign;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        SystemSetting::seedDefaultSettings();

        $this->admin = User::factory()->create(['email' => 'admin_test@hirely.test']);
        $this->admin->assignRole('super-admin');

        $this->applicant = User::factory()->create(['email' => 'applicant_test@hirely.test']);
        $this->applicant->assignRole('applicant');

        $ad = Advertisement::create([
            'advertisement_no' => '99/2026',
            'title' => 'Statutory Faculty Drive 2026',
            'year' => 2026,
            'valid_from' => now(),
            'valid_to' => now()->addMonths(3),
            'is_active' => true,
        ]);

        $this->academicCampaign = Campaign::create([
            'advertisement_id' => $ad->id,
            'title' => 'Professor of Computer Science',
            'job_code' => 'CS-PROF-01',
            'department' => 'Department of Computing',
            'track_type' => 'ACADEMIC',
            'application_fee' => 3500.00,
            'is_active' => true,
            'closing_date' => now()->addDays(30),
        ]);
    }

    public function test_system_settings_seed_defaults_correctly()
    {
        $this->assertEquals('Hirely', SystemSetting::getAppName());
        $this->assertTrue(SystemSetting::isPipelineStepEnabled('scrutiny'));
        $this->assertTrue(SystemSetting::isPipelineStepEnabled('peer_review'));
        $this->assertTrue(SystemSetting::isPipelineStepEnabled('appellate'));
        $this->assertTrue(SystemSetting::isPipelineStepEnabled('board_interview'));
        $this->assertEquals('PKR', SystemSetting::get('currency_symbol', 'PKR'));
        $this->assertEquals(14, (int) SystemSetting::get('challan_grace_days', 14));
    }

    public function test_login_page_renders_dynamic_branding_contacts_and_announcement()
    {
        SystemSetting::set('app_name', 'Metropolitan University Portal');
        SystemSetting::set('app_tagline', 'Excellence in Statutory Merit');
        SystemSetting::set('portal_announcement_enabled', true);
        SystemSetting::set('portal_announcement_type', 'warning');
        SystemSetting::set('portal_announcement_message', 'Admissions & Recruitment System Maintenance Scheduled');
        SystemSetting::set('support_email', 'helpdesk@metropolitan.edu.pk');
        SystemSetting::set('support_phone', '+92-51-111-222-333');

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('METROPOLITAN UNIVERSITY PORTAL');
        $response->assertSee('Excellence in Statutory Merit');
        $response->assertSee('Admissions & Recruitment System Maintenance Scheduled');
        $response->assertSee('helpdesk@metropolitan.edu.pk');
        $response->assertSee('+92-51-111-222-333');
    }

    public function test_candidate_dashboard_reflects_custom_currency_and_helpdesk()
    {
        SystemSetting::set('currency_symbol', 'USD');
        SystemSetting::set('support_email', 'admissions@university.org');
        SystemSetting::set('support_whatsapp', '+92-333-9998888');

        $basicInfo = BasicInfo::create([
            'user_id' => $this->applicant->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Alice',
            'last_name' => 'Applicant',
            'cnic_number' => '12345-1234567-1',
            'date_of_birth' => '1990-01-01',
            'gender' => 'Female',
            'father_name' => 'John Doe',
            'quota' => 'OPEN_MERIT',
            'phone_number' => '+92-300-1234567',
            'nationality' => 'Pakistan',
            'marital_status' => 'Single',
            'domicile' => 'Islamabad',
            'religion' => 'Islam',
            'permanent_address' => 'Street 1, Islamabad',
            'mailing_address' => 'Street 1, Islamabad',
            'place_of_birth' => 'Islamabad',
        ]);

        $app = Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->academicCampaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        BillingLedger::create([
            'application_id' => $app->id,
            'consumer_id' => '100099887766',
            'amount' => 3500.00,
            'status' => 'PENDING',
        ]);

        $this->actingAs($this->applicant);

        Livewire::test(\App\Livewire\Applicant\Dashboard::class)
            ->assertSee('USD 3,500.00')
            ->assertSee('admissions@university.org')
            ->assertSee('+92-333-9998888')
            ->assertDontSee('PKR 3,500.00');
    }

    public function test_pipeline_step_toggling_controls_navigation_visibility()
    {
        SystemSetting::set('enable_peer_review_step', false);
        SystemSetting::set('enable_appellate_step', false);

        $this->actingAs($this->admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        // Scrutiny is still enabled
        $response->assertSee('Scrutiny Desk');
        // Peer review and Appellate desks should NOT be in the sidebar when disabled
        $response->assertDontSee('/appellate/dashboard');
        $response->assertDontSee('/reviewer/dashboard');
    }

    public function test_multiple_applications_policy_enforcement()
    {
        SystemSetting::set('allow_multiple_applications', false);

        $basicInfo = BasicInfo::create([
            'user_id' => $this->applicant->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Bob',
            'last_name' => 'Candidate',
            'cnic_number' => '54321-7654321-1',
            'date_of_birth' => '1992-05-15',
            'gender' => 'Male',
            'father_name' => 'Richard Roe',
            'quota' => 'OPEN_MERIT',
            'phone_number' => '+92-321-9876543',
            'nationality' => 'Pakistan',
            'marital_status' => 'Single',
            'domicile' => 'Lahore',
            'religion' => 'Islam',
            'permanent_address' => 'Mall Road, Lahore',
            'mailing_address' => 'Mall Road, Lahore',
            'place_of_birth' => 'Lahore',
        ]);

        // First application exists
        Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->academicCampaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $campaign2 = Campaign::create([
            'advertisement_id' => $this->academicCampaign->advertisement_id,
            'title' => 'Associate Professor of AI',
            'job_code' => 'AI-ASSOC-02',
            'department' => 'Department of Computing',
            'track_type' => 'ACADEMIC',
            'application_fee' => 4000.00,
            'is_active' => true,
        ]);

        $this->actingAs($this->applicant);

        // Attempting to apply for another position via Dashboard
        Livewire::test(\App\Livewire\Applicant\Dashboard::class)
            ->set('selectedCampaignIdForModal', $campaign2->id)
            ->call('submitNewApplication')
            ->assertHasErrors(['selectedCampaignIdForModal']);

        $this->assertEquals(1, Application::where('user_id', $this->applicant->id)->count());
    }

    public function test_prevent_duplicate_campaign_applications_enforcement()
    {
        SystemSetting::set('allow_multiple_applications', true);
        SystemSetting::set('prevent_duplicate_campaign_applications', true);

        $basicInfo = BasicInfo::create([
            'user_id' => $this->applicant->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Carol',
            'last_name' => 'Applicant',
            'cnic_number' => '11111-2222222-3',
            'date_of_birth' => '1988-02-10',
            'gender' => 'Female',
            'father_name' => 'George Applicant',
            'quota' => 'OPEN_MERIT',
            'phone_number' => '+92-311-5554443',
            'nationality' => 'Pakistan',
            'marital_status' => 'Single',
            'domicile' => 'Peshawar',
            'religion' => 'Islam',
            'permanent_address' => 'University Town, Peshawar',
            'mailing_address' => 'University Town, Peshawar',
            'place_of_birth' => 'Peshawar',
        ]);

        // Already applied for academicCampaign
        Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->academicCampaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($this->applicant);

        Livewire::test(\App\Livewire\Applicant\Dashboard::class)
            ->set('selectedCampaignIdForModal', $this->academicCampaign->id)
            ->call('submitNewApplication')
            ->assertHasErrors(['selectedCampaignIdForModal']);
    }

    public function test_appellate_pipeline_flag_blocks_appeals_when_disabled()
    {
        SystemSetting::set('enable_appellate_step', false);

        BasicInfo::create([
            'user_id' => $this->applicant->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Emma',
            'last_name' => 'Appealer',
            'cnic_number' => '99999-8888888-7',
            'date_of_birth' => '1989-11-20',
            'gender' => 'Female',
            'father_name' => 'Edward Appealer',
            'quota' => 'OPEN_MERIT',
            'phone_number' => '+92-300-8887766',
            'nationality' => 'Pakistan',
            'marital_status' => 'Single',
            'domicile' => 'Islamabad',
            'religion' => 'Islam',
            'permanent_address' => 'F-8, Islamabad',
            'mailing_address' => 'F-8, Islamabad',
            'place_of_birth' => 'Islamabad',
        ]);

        $app = Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->academicCampaign->id,
            'status' => 'INELIGIBLE',
            'disqualification_reason' => 'Degree not recognized',
        ]);

        $this->actingAs($this->applicant);

        $file = UploadedFile::fake()->create('hec_proof.pdf', 500);

        Livewire::test(\App\Livewire\Applicant\Dashboard::class)
            ->call('openAppealForm', $app->id)
            ->assertSee('Grievance appellate review stage is currently closed')
            ->set('appealText', 'Formal justification for equivalence certificate.')
            ->set('appealProof', $file)
            ->call('submitAppeal')
            ->assertSee('Grievance appellate review stage is currently closed');

        $this->assertEquals('INELIGIBLE', $app->fresh()->status);
    }

    public function test_auto_lock_profile_setting_behavior()
    {
        SystemSetting::set('auto_lock_profile_on_voucher', false);

        $basicInfo = BasicInfo::create([
            'user_id' => $this->applicant->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'David',
            'last_name' => 'LockTest',
            'cnic_number' => '33333-4444444-5',
            'date_of_birth' => '1991-03-20',
            'gender' => 'Male',
            'father_name' => 'Arthur LockTest',
            'quota' => 'OPEN_MERIT',
            'phone_number' => '+92-302-7778899',
            'nationality' => 'Pakistan',
            'marital_status' => 'Single',
            'domicile' => 'Rawalpindi',
            'religion' => 'Islam',
            'permanent_address' => 'Saddar, Rawalpindi',
            'mailing_address' => 'Saddar, Rawalpindi',
            'place_of_birth' => 'Rawalpindi',
            'is_locked' => false,
        ]);

        $this->actingAs($this->applicant);

        Livewire::test(\App\Livewire\Applicant\Dashboard::class)
            ->set('selectedCampaignIdForModal', $this->academicCampaign->id)
            ->call('submitNewApplication');

        // Should NOT be locked because auto_lock_profile_on_voucher is false
        $this->assertFalse((bool) $basicInfo->fresh()->is_locked);
    }

    public function test_document_proof_rules_enforcement_in_profile_wizard()
    {
        SystemSetting::set('require_publications_academic', true);

        $basicInfo = BasicInfo::create([
            'user_id' => $this->applicant->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Fiona',
            'last_name' => 'Scholar',
            'cnic_number' => '77777-6666666-5',
            'date_of_birth' => '1990-04-12',
            'gender' => 'Female',
            'father_name' => 'Frank Scholar',
            'quota' => 'OPEN_MERIT',
            'phone_number' => '+92-300-1122334',
            'nationality' => 'Pakistan',
            'marital_status' => 'Single',
            'domicile' => 'Islamabad',
            'religion' => 'Islam',
            'permanent_address' => 'G-9, Islamabad',
            'mailing_address' => 'G-9, Islamabad',
            'place_of_birth' => 'Islamabad',
        ]);

        \App\Models\AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'PhD',
            'degree_title' => 'PhD in Computer Science',
            'institution' => 'National University',
            'country' => 'Pakistan',
            'year' => 2022,
            'gpa' => 3.90,
            'scale' => '4.0',
            'major' => 'Computer Science',
            'board' => 'HEC',
            'division' => 'First',
        ]);

        $this->actingAs($this->applicant);

        // Attempting to generate voucher without publications for an ACADEMIC role should fail
        Livewire::test(\App\Livewire\Applicant\ProfileWizard::class)
            ->set('campaign_id', $this->academicCampaign->id)
            ->call('generateChallan')
            ->assertHasErrors(['campaign_id']);

        // Now add a publication
        Publication::create([
            'basic_info_id' => $basicInfo->id,
            'title' => 'Deep Learning in Autonomous Healthcare',
            'journal_name' => 'IEEE Transactions on Medical Imaging',
            'issn_print' => '1234-5678',
            'author_order' => 1,
            'hjrs_category' => 'W',
            'impact_factor' => 10.5,
            'publication_year' => 2024,
        ]);

        // Now generating voucher should succeed
        Livewire::test(\App\Livewire\Applicant\ProfileWizard::class)
            ->set('campaign_id', $this->academicCampaign->id)
            ->call('generateChallan')
            ->assertHasNoErrors();

        $this->assertTrue(Application::where('user_id', $this->applicant->id)->where('campaign_id', $this->academicCampaign->id)->exists());
    }

    public function test_default_application_fee_prefilled_on_new_campaign()
    {
        SystemSetting::set('default_application_fee', 4500);

        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Admin\CampaignCrud::class)
            ->assertSet('application_fee', 4500.00);
    }

    public function test_dynamic_mail_configuration_injected_at_runtime()
    {
        SystemSetting::set('mail_host', 'smtp.custom-hirely.org');
        SystemSetting::set('mail_port', 465);
        SystemSetting::set('mail_encryption', 'ssl');
        SystemSetting::set('mail_from_address', 'statutory-desk@custom-hirely.org');
        SystemSetting::set('mail_from_name', 'Hirely Central Secretariat');

        // Trigger boot logic
        app()->make(\App\Providers\AppServiceProvider::class, ['app' => app()])->boot();

        $this->assertEquals('smtp.custom-hirely.org', config('mail.mailers.smtp.host'));
        $this->assertEquals(465, config('mail.mailers.smtp.port'));
        $this->assertEquals('ssl', config('mail.mailers.smtp.encryption'));
        $this->assertEquals('statutory-desk@custom-hirely.org', config('mail.from.address'));
        $this->assertEquals('Hirely Central Secretariat', config('mail.from.name'));
    }
}
