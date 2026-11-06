<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\SystemSetting;
use App\Livewire\Admin\SystemSettings;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class OrganizationNomenclatureAndLetterheadTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $applicant;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $manageSettings = Permission::firstOrCreate(['name' => 'manage-settings']);
        $viewApp = Permission::firstOrCreate(['name' => 'view-application']);
        
        $adminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole->givePermissionTo([$manageSettings, $viewApp]);

        $applicantRole = Role::firstOrCreate(['name' => 'applicant']);
        $applicantRole->givePermissionTo($viewApp);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super-admin');

        $this->applicant = User::factory()->create();
        $this->applicant->assignRole('applicant');

        $this->unauthorizedUser = User::factory()->create();

        SystemSetting::seedDefaultSettings();
    }

    public function test_organization_full_and_short_name_defaults_and_helpers(): void
    {
        $this->assertEquals('Abdul Wali Khan University Mardan', SystemSetting::getOrgName());
        $this->assertEquals('AWKUM', SystemSetting::getOrgShortName());

        SystemSetting::set('org_name', 'University of Peshawar', 'organization', 'string');
        SystemSetting::set('org_short_name', 'UOP', 'organization', 'string');

        $this->assertEquals('University of Peshawar', SystemSetting::getOrgName());
        $this->assertEquals('UOP', SystemSetting::getOrgShortName());
    }

    public function test_super_admin_can_save_organization_dual_nomenclature(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->set('activeTab', 'organization')
            ->set('org_name', 'Abdul Wali Khan University Mardan')
            ->set('org_short_name', 'AWKUM')
            ->set('org_location', 'Khyber Pakhtunkhwa, Pakistan')
            ->set('org_office_title', 'Office of the Registrar')
            ->set('org_type', 'HIGHER_EDUCATION')
            ->set('sector_label', 'Faculty / Department')
            ->set('currency_symbol', 'PKR')
            ->set('default_application_fee', 3500)
            ->call('saveOrganizationProfile')
            ->assertHasNoErrors();

        $this->assertEquals('Abdul Wali Khan University Mardan', SystemSetting::get('org_name'));
        $this->assertEquals('AWKUM', SystemSetting::get('org_short_name'));
        $this->assertEquals('Khyber Pakhtunkhwa, Pakistan', SystemSetting::get('org_location'));
        $this->assertEquals('Office of the Registrar', SystemSetting::get('org_office_title'));
        $this->assertEquals(3500, SystemSetting::get('default_application_fee'));
    }

    public function test_super_admin_can_customize_official_letterhead(): void
    {
        Storage::fake('public');
        $fakeLogo = UploadedFile::fake()->image('awkum-emblem.png', 200, 200);

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->set('activeTab', 'organization')
            ->set('letterhead_heading', 'Abdul Wali Khan University Mardan')
            ->set('letterhead_subheading', 'Khyber Pakhtunkhwa, Pakistan')
            ->set('letterhead_office', 'Office of the Registrar')
            ->set('letterhead_custom_details', "Main Campus, Toru Road, Mardan\nPhone: +92-937-9230611\nEmail: registrar@awkum.edu.pk")
            ->set('letterhead_layout', 'classic_left')
            ->set('letterheadLogoUpload', $fakeLogo)
            ->call('saveLetterheadSettings')
            ->assertHasNoErrors();

        $details = SystemSetting::getLetterheadDetails();
        $this->assertEquals('Abdul Wali Khan University Mardan', $details['heading']);
        $this->assertEquals('Khyber Pakhtunkhwa, Pakistan', $details['subheading']);
        $this->assertEquals('Office of the Registrar', $details['office']);
        $this->assertStringContainsString('+92-937-9230611', $details['custom_details']);
        $this->assertNotEmpty($details['logo']);
    }

    public function test_multi_portal_footers_and_letter_endorsements(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->set('activeTab', 'branding')
            ->set('app_name', 'Hirely OS')
            ->set('footer_copyright_text', '© 2026 AWKUM Recruitment. All rights reserved.')
            ->set('footer_candidate_text', 'Custom Candidate Helpdesk: Call 0937-9230611 for voucher queries.')
            ->set('footer_admin_text', 'AWKUM Registrar Secretariat • Authorized Personnel Only.')
            ->set('footer_board_text', 'Selection Board Confidential Record • Statutory Code 2026.')
            ->set('footer_reviewer_text', 'External Referee Blind Peer Review Portal.')
            ->set('letter_footer_text', 'Official Computer-Generated Document issued under AWKUM Seal.')
            ->set('letter_footer_contact', 'Registrar Office, AWKUM, Mardan')
            ->set('letter_show_audit_trail', true)
            ->set('letter_show_verify_ref', true)
            ->call('saveBrandingSettings')
            ->assertHasNoErrors();

        $this->assertEquals('Custom Candidate Helpdesk: Call 0937-9230611 for voucher queries.', SystemSetting::getPortalFooter('candidate'));
        $this->assertEquals('AWKUM Registrar Secretariat • Authorized Personnel Only.', SystemSetting::getPortalFooter('admin'));
        $this->assertEquals('Selection Board Confidential Record • Statutory Code 2026.', SystemSetting::getPortalFooter('board'));
        $this->assertEquals('External Referee Blind Peer Review Portal.', SystemSetting::getPortalFooter('reviewer'));

        $letterFooter = SystemSetting::getLetterFooter();
        $this->assertEquals('Official Computer-Generated Document issued under AWKUM Seal.', $letterFooter['disclaimer']);
        $this->assertEquals('Registrar Office, AWKUM, Mardan', $letterFooter['contact']);
        $this->assertTrue($letterFooter['show_audit']);
        $this->assertTrue($letterFooter['show_verify_ref']);
    }

    public function test_candidate_dossier_print_renders_customized_letterhead_and_footer(): void
    {
        SystemSetting::set('letterhead_heading', 'Abdul Wali Khan University Mardan', 'organization', 'string');
        SystemSetting::set('letterhead_subheading', 'Khyber Pakhtunkhwa, Pakistan', 'organization', 'string');
        SystemSetting::set('letterhead_office', 'Office of the Registrar', 'organization', 'string');
        SystemSetting::set('letterhead_custom_details', 'Direct Intake Secretariat • Tel: +92-937-9230611', 'organization', 'string');
        SystemSetting::set('letter_footer_text', 'Official Document issued under authority of AWKUM.', 'branding', 'string');

        $campaign = Campaign::create([
            'title' => 'Assistant Professor in Software Engineering',
            'job_code' => 'AP-SE-2026',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'status' => 'OPEN',
            'application_fee' => 3000,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(20),
        ]);

        $app = Application::create([
            'campaign_id' => $campaign->id,
            'user_id' => $this->applicant->id,
            'tracking_number' => 'AWKUM-2026-TEST-99',
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get("/admin/applications/{$app->id}/print");

        $response->assertStatus(200);
        $response->assertSee('Abdul Wali Khan University Mardan');
        $response->assertSee('Khyber Pakhtunkhwa, Pakistan');
        $response->assertSee('Office of the Registrar');
        $response->assertSee('Direct Intake Secretariat • Tel: +92-937-9230611');
        $response->assertSee('Official Document issued under authority of AWKUM.');
        $response->assertSee('Document Audit Trail:');
    }

    public function test_sidebar_renders_short_name_and_full_name_tooltip(): void
    {
        SystemSetting::set('org_name', 'Abdul Wali Khan University Mardan', 'organization', 'string');
        SystemSetting::set('org_short_name', 'AWKUM', 'organization', 'string');

        $response = $this->actingAs($this->superAdmin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('AWKUM');
        $response->assertSee('title="Abdul Wali Khan University Mardan"', false);
    }

    public function test_unauthorized_user_cannot_save_organization_or_letterhead(): void
    {
        $this->actingAs($this->unauthorizedUser);

        Livewire::test(SystemSettings::class)
            ->assertStatus(403);
    }
}
