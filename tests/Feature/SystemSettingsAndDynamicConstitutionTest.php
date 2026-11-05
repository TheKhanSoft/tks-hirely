<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\SystemSetting;
use App\Livewire\Admin\SystemSettings;
use App\Livewire\Admin\ReviewerManagement;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemSettingsAndDynamicConstitutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        SystemSetting::seedDefaultSettings();
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\Config::set('mail.default', 'array');
        parent::tearDown();
    }

    public function test_super_admin_and_registrar_can_access_settings_desk(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');

        $this->actingAs($admin)->get('/admin/settings')->assertStatus(200);
        $this->actingAs($registrar)->get('/registrar/settings')->assertStatus(200);

        // Applicant barred
        $this->actingAs($applicant)->get('/admin/settings')->assertStatus(403);
    }

    public function test_settings_can_toggle_pipeline_and_documents(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(SystemSettings::class)
            ->set('activeTab', 'pipeline')
            ->set('enable_peer_review_step', false)
            ->call('savePipelineSettings')
            ->assertHasNoErrors();

        $this->assertFalse(SystemSetting::isPipelineStepEnabled('peer_review'));

        // Toggle documents
        Livewire::test(SystemSettings::class)
            ->set('activeTab', 'documents')
            ->set('require_noc_employed', true)
            ->call('saveDocumentSettings')
            ->assertHasNoErrors();

        $this->assertTrue(SystemSetting::isDocumentRequired('noc'));
    }

    public function test_statutory_constitution_presets_and_peer_quota_for_assistant_professor(): void
    {
        // 1. Assistant Professor Campaign
        $asstProfCampaign = Campaign::create([
            'job_code' => 'CS-AP-101',
            'title' => 'Assistant Professor Computer Science',
            'department' => 'Department of Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        // Must dynamically require exactly 3 peer review experts
        $this->assertEquals(3, $asstProfCampaign->getRequiredPeerReviewersCount());

        // Committee constitution must specify: 6 total, 5 internal (Dean, Dept Head, 3 Faculty), 1 external (Professor other university)
        $constitution = $asstProfCampaign->getCommitteeConstitution();
        $this->assertEquals(6, $constitution['required_total']);
        $this->assertEquals(5, $constitution['required_internal']);
        $this->assertEquals(1, $constitution['required_external']);
        $this->assertEquals(0, $constitution['required_international']);
    }

    public function test_statutory_constitution_for_corporate_and_public_sector(): void
    {
        // Corporate Executive Campaign
        $corpCampaign = Campaign::create([
            'job_code' => 'EXEC-DIR-01',
            'title' => 'Executive Director Technology Operations',
            'department' => 'Enterprise Cloud Division',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 5000.00,
            'is_active' => true,
        ]);

        // Corporate requires 0 peer reviewers
        $this->assertEquals(0, $corpCampaign->getRequiredPeerReviewersCount());

        // Board: 5 total, 3 internal, 2 external industry specialists
        $corpConst = $corpCampaign->getCommitteeConstitution();
        $this->assertEquals(5, $corpConst['required_total']);
        $this->assertEquals(3, $corpConst['required_internal']);
        $this->assertEquals(2, $corpConst['required_external']);
    }

    public function test_strict_peer_review_quota_enforcement_when_enabled(): void
    {
        SystemSetting::set('enforce_strict_peer_review_quorum', true, 'pipeline', 'boolean');

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $campaign = Campaign::create([
            'job_code' => 'CS-ASST-PROF-2026',
            'title' => 'Assistant Professor Computer Science',
            'department' => 'Department of Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $candidate = User::factory()->create();
        $candidate->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'date_of_birth' => '1990-12-09',
            'cnic_number' => '37405-3344556-7',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $reviewer1 = User::factory()->create(['email' => 'rev1@univ.edu']);
        $reviewer1->assignRole('academic-reviewer');

        $this->actingAs($registrar);

        // Selecting only 1 reviewer when statutory quota is 3 should trigger statutory quota error
        Livewire::test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->call('toggleReviewerSelection', $reviewer1->id)
            ->call('dispatchApplicationToReviewers')
            ->assertHasErrors(['selectedReviewerIds']);
    }

    public function test_organization_profile_settings_and_sector_generalization(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(SystemSettings::class)
            ->set('activeTab', 'organization')
            ->set('org_name', 'Federal Innovation & Research Authority')
            ->set('org_type', 'PUBLIC_SECTOR_GOVT')
            ->set('sector_label', 'Directorate / Division')
            ->set('currency_symbol', 'PKR')
            ->set('default_application_fee', 2500)
            ->call('saveOrganizationProfile')
            ->assertHasNoErrors();

        $this->assertEquals('Federal Innovation & Research Authority', SystemSetting::get('org_name'));
        $this->assertEquals('PUBLIC_SECTOR_GOVT', SystemSetting::get('org_type'));
        $this->assertEquals('Directorate / Division', SystemSetting::get('sector_label'));
    }

    public function test_dedicated_modular_settings_routes_and_slug_mapping(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        // Subpages by dedicated semantic URLs
        $this->get('/admin/settings/workflows')->assertStatus(200)->assertSee('Workflow Pipeline Stages');
        $this->get('/admin/settings/documents')->assertStatus(200)->assertSee('Mandatory Candidate Document Proofs');
        $this->get('/admin/settings/candidate-rules')->assertStatus(200)->assertSee('Candidate Eligibility, Submission');
        $this->get('/admin/settings/organization')->assertStatus(200)->assertSee('Organization Profile, Institutional Sector');
        $this->get('/admin/settings/academic-criteria')->assertStatus(200)->assertSee('Academic Division Criteria');

        // Aliases and slug variants
        $this->get('/admin/settings/workflow')->assertStatus(200);
        $this->get('/admin/settings/document-proofs')->assertStatus(200);
        $this->get('/admin/settings/candidates')->assertStatus(200);

        // Registrar dedicated URLs
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');
        $this->actingAs($registrar);
        $this->get('/registrar/settings/workflows')->assertStatus(200);
        $this->get('/registrar/settings/documents')->assertStatus(200);

        // Livewire component slug resolution & tab switching event
        Livewire::test(SystemSettings::class, ['tab' => 'candidate-rules'])
            ->assertSet('activeTab', 'candidate_rules')
            ->call('setTab', 'documents')
            ->assertSet('activeTab', 'documents')
            ->assertDispatched('url-changed');
    }

    public function test_branding_settings_can_be_saved(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(SystemSettings::class, ['tab' => 'branding'])
            ->assertSet('activeTab', 'branding')
            ->set('app_name', 'EduHire Pakistan')
            ->set('app_tagline', 'National Higher Education Talent Grid')
            ->set('app_description', 'Official Recruitment Platform for Higher Education Institutions.')
            ->set('app_version', 'v3.0 Sovereign')
            ->set('theme_accent_color', '#0d9488')
            ->set('portal_announcement_enabled', true)
            ->set('portal_announcement_type', 'warning')
            ->set('portal_announcement_message', 'Scheduled maintenance this Sunday at 02:00 AM PKT.')
            ->set('footer_copyright_text', '© 2026 EduHire Pakistan. All rights reserved.')
            ->call('saveBrandingSettings')
            ->assertHasNoErrors();

        $this->assertEquals('EduHire Pakistan', SystemSetting::getAppName());
        $this->assertEquals('EduHire Pakistan', SystemSetting::get('app_name'));
        $this->assertEquals('National Higher Education Talent Grid', SystemSetting::get('app_tagline'));
        $this->assertEquals('v3.0 Sovereign', SystemSetting::get('app_version'));
        $this->assertEquals('#0d9488', SystemSetting::get('theme_accent_color'));
        $this->assertTrue((bool) SystemSetting::get('portal_announcement_enabled'));
        $this->assertEquals('warning', SystemSetting::get('portal_announcement_type'));
        $this->assertEquals('Scheduled maintenance this Sunday at 02:00 AM PKT.', SystemSetting::get('portal_announcement_message'));
        $this->assertEquals('© 2026 EduHire Pakistan. All rights reserved.', SystemSetting::get('footer_copyright_text'));

        // Check layout reflection
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('EDUHIRE PAKISTAN');
        $response->assertSee('v3.0 Sovereign');
        $response->assertSee('Scheduled maintenance this Sunday at 02:00 AM PKT.');
    }

    public function test_contact_settings_can_be_saved(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(SystemSettings::class, ['tab' => 'contact'])
            ->assertSet('activeTab', 'contact')
            ->set('support_email', 'helpdesk@university.edu.pk')
            ->set('support_phone', '+92 51 900 1122')
            ->set('support_whatsapp', '+92 300 1234567')
            ->set('registrar_hotline', '+92 51 900 3344')
            ->set('support_hours', 'Monday - Friday: 08:30 AM - 04:30 PM PKT')
            ->set('helpdesk_location', 'Facilitation Center, Ground Floor, Admin Block, Islamabad')
            ->set('org_website', 'https://university.edu.pk')
            ->call('saveContactSettings')
            ->assertHasNoErrors();

        $this->assertEquals('helpdesk@university.edu.pk', SystemSetting::get('support_email'));
        $this->assertEquals('+92 51 900 1122', SystemSetting::get('support_phone'));
        $this->assertEquals('+92 300 1234567', SystemSetting::get('support_whatsapp'));
        $this->assertEquals('+92 51 900 3344', SystemSetting::get('registrar_hotline'));
        $this->assertEquals('Monday - Friday: 08:30 AM - 04:30 PM PKT', SystemSetting::get('support_hours'));
        $this->assertEquals('Facilitation Center, Ground Floor, Admin Block, Islamabad', SystemSetting::get('helpdesk_location'));
        $this->assertEquals('https://university.edu.pk', SystemSetting::get('org_website'));
    }

    public function test_email_settings_can_be_saved_and_tested(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(SystemSettings::class, ['tab' => 'email'])
            ->assertSet('activeTab', 'email')
            ->set('mail_from_name', 'University Recruitment Directorate')
            ->set('mail_from_address', 'recruitment@university.edu.pk')
            ->set('mail_reply_to', 'support@university.edu.pk')
            ->set('mail_mailer', 'smtp')
            ->set('mail_host', 'smtp.mailtrap.io')
            ->set('mail_port', 2525)
            ->set('mail_encryption', 'tls')
            ->set('mail_username', 'sandbox_user_test')
            ->set('mail_password', 'secret_password_123')
            ->call('saveEmailSettings')
            ->assertHasNoErrors();

        $this->assertEquals('University Recruitment Directorate', SystemSetting::get('mail_from_name'));
        $this->assertEquals('recruitment@university.edu.pk', SystemSetting::get('mail_from_address'));
        $this->assertEquals('support@university.edu.pk', SystemSetting::get('mail_reply_to'));
        $this->assertEquals('smtp', SystemSetting::get('mail_mailer'));
        $this->assertEquals('smtp.mailtrap.io', SystemSetting::get('mail_host'));
        $this->assertEquals(2525, SystemSetting::get('mail_port'));
        $this->assertEquals('tls', SystemSetting::get('mail_encryption'));
        $this->assertEquals('sandbox_user_test', SystemSetting::get('mail_username'));
        $this->assertEquals('secret_password_123', SystemSetting::get('mail_password'));

        // Test sending with array/log transport
        \Illuminate\Support\Facades\Mail::fake();

        Livewire::test(SystemSettings::class, ['tab' => 'email'])
            ->set('test_email_recipient', 'candidate.test@example.com')
            ->set('mail_mailer', 'array')
            ->call('sendTestEmail')
            ->assertHasNoErrors();
    }

    public function test_dedicated_settings_routes_for_branding_contact_email(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $this->get('/admin/settings/branding')->assertStatus(200)->assertSee('Application Identity, Logos, Favicon', false);
        $this->get('/admin/settings/contact')->assertStatus(200)->assertSee('Contact Options, Helpdesk Channels', false);
        $this->get('/admin/settings/email')->assertStatus(200)->assertSee('Email Delivery, SMTP Protocol', false);
        $this->get('/admin/settings/smtp')->assertStatus(200)->assertSee('Email Delivery, SMTP Protocol', false);
    }
}
