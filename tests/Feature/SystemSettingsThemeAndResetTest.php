<?php

namespace Tests\Feature;

use App\Livewire\Admin\SystemSettings;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AcademicEligibilityEvaluator;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SystemSettingsThemeAndResetTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularCandidate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SpatieRoleAndPermissionSeeder::class);
        SystemSetting::seedDefaultSettings();

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@hirely.test',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->regularCandidate = User::factory()->create([
            'email' => 'candidate@hirely.test',
            'is_active' => true,
        ]);
        $this->regularCandidate->assignRole('applicant');
    }

    public function test_system_setting_render_theme_css_compiles_valid_css(): void
    {
        SystemSetting::set('theme_accent_color', '#059669', 'branding', 'string');
        SystemSetting::set('theme_secondary_color', '#d97706', 'branding', 'string');
        SystemSetting::set('theme_radius_style', 'pill', 'branding', 'string');
        SystemSetting::set('theme_font_family', 'outfit', 'branding', 'string');
        SystemSetting::set('theme_card_elevation', 'floating', 'branding', 'string');

        $css = SystemSetting::renderThemeCss();

        $this->assertStringContainsString('--primary: #059669 !important;', $css);
        $this->assertStringContainsString('--secondary: #d97706 !important;', $css);
        $this->assertStringContainsString('--radius-md: 24px !important;', $css);
        $this->assertStringContainsString("'Outfit'", $css);
        $this->assertStringContainsString('--shadow-sm:', $css);
    }

    public function test_super_admin_can_apply_theme_presets(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('applyThemePreset', 'cambridge_emerald')
            ->assertSet('theme_accent_color', '#059669')
            ->assertSet('theme_secondary_color', '#0d9488')
            ->assertSet('theme_font_family', 'jakarta')
            ->assertHasNoErrors();

        // Verify DB was updated
        $this->assertEquals('#059669', SystemSetting::get('theme_accent_color'));
        $this->assertEquals('#0d9488', SystemSetting::get('theme_secondary_color'));

        // Test another preset
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('applyThemePreset', 'harvard_crimson')
            ->assertSet('theme_accent_color', '#be123c')
            ->assertSet('theme_secondary_color', '#f59e0b')
            ->assertSet('theme_font_family', 'academic');

        $this->assertEquals('#be123c', SystemSetting::get('theme_accent_color'));
    }

    public function test_super_admin_can_reset_pipeline_to_default(): void
    {
        // First mutate
        SystemSetting::set('enable_scrutiny_step', false, 'workflows', 'boolean');
        SystemSetting::set('enable_peer_review_step', false, 'workflows', 'boolean');
        SystemSetting::set('enforce_strict_committee_quorum', false, 'workflows', 'boolean');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetPipelineToDefault')
            ->assertSet('enable_scrutiny_step', true)
            ->assertSet('enable_peer_review_step', true)
            ->assertSet('enable_board_interview_step', true)
            ->assertSet('enable_appellate_step', true)
            ->assertSet('enforce_strict_committee_quorum', true)
            ->assertSet('enforce_strict_peer_review_quorum', false);

        $this->assertTrue((bool) SystemSetting::get('enable_scrutiny_step'));
        $this->assertTrue((bool) SystemSetting::get('enable_peer_review_step'));
        $this->assertTrue((bool) SystemSetting::get('enforce_strict_committee_quorum'));
    }

    public function test_super_admin_can_reset_documents_to_default(): void
    {
        SystemSetting::set('require_transcript_proof', false, 'documents', 'boolean');
        SystemSetting::set('require_cnic_upload', false, 'documents', 'boolean');
        SystemSetting::set('require_publications_academic', false, 'documents', 'boolean');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetDocumentsToDefault')
            ->assertSet('require_transcript_proof', true)
            ->assertSet('require_cnic_upload', true)
            ->assertSet('require_challan_receipt_upload', false)
            ->assertSet('require_publications_academic', true)
            ->assertSet('require_noc_employed', false)
            ->assertSet('require_experience_proof', true);

        $this->assertTrue((bool) SystemSetting::get('require_transcript_proof'));
        $this->assertTrue((bool) SystemSetting::get('require_cnic_upload'));
        $this->assertTrue((bool) SystemSetting::get('require_publications_academic'));
    }

    public function test_super_admin_can_reset_candidate_rules_to_default(): void
    {
        SystemSetting::set('challan_grace_days', 45, 'candidate_rules', 'integer');
        SystemSetting::set('allow_multiple_applications', false, 'candidate_rules', 'boolean');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetCandidateRulesToDefault')
            ->assertSet('challan_grace_days', 14)
            ->assertSet('allow_multiple_applications', true)
            ->assertSet('prevent_duplicate_campaign_applications', true)
            ->assertSet('auto_lock_profile_on_voucher', true);

        $this->assertEquals(14, SystemSetting::get('challan_grace_days'));
        $this->assertTrue((bool) SystemSetting::get('allow_multiple_applications'));
    }

    public function test_super_admin_can_reset_organization_to_default(): void
    {
        SystemSetting::set('org_name', 'Custom Test Academy', 'organization', 'string');
        SystemSetting::set('currency_symbol', 'USD', 'organization', 'string');
        SystemSetting::set('default_application_fee', 5000, 'organization', 'integer');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetOrganizationToDefault')
            ->assertSet('org_name', 'Hirely')
            ->assertSet('currency_symbol', 'PKR')
            ->assertSet('sector_label', 'Department / Division')
            ->assertSet('default_application_fee', 3000);

        $this->assertEquals('Hirely', SystemSetting::get('org_name'));
        $this->assertEquals('PKR', SystemSetting::get('currency_symbol'));
    }

    public function test_super_admin_can_reset_academic_criteria_to_default(): void
    {
        AcademicEligibilityEvaluator::setGlobalCriteria([
            'disallow_third_division' => false,
            'disallow_third_in_terminal' => false,
            'disallow_two_second_divisions' => false,
            'scope' => 'GLOBAL',
        ]);
        SystemSetting::set('asst_prof_required_peer_reviewers', 1, 'academic_criteria', 'integer');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetAcademicCriteriaToDefault')
            ->assertSet('global_disallow_third_division', true)
            ->assertSet('global_disallow_third_in_terminal', true)
            ->assertSet('global_disallow_two_second_divisions', true)
            ->assertSet('asst_prof_required_peer_reviewers', 3)
            ->assertSet('prof_required_peer_reviewers', 3)
            ->assertSet('lecturer_required_peer_reviewers', 0);

        $global = AcademicEligibilityEvaluator::getGlobalCriteria();
        $this->assertTrue((bool) $global['disallow_third_division']);
        $this->assertEquals(3, SystemSetting::get('asst_prof_required_peer_reviewers'));
    }

    public function test_super_admin_can_reset_theme_to_default(): void
    {
        SystemSetting::set('theme_accent_color', '#ff0000', 'branding', 'string');
        SystemSetting::set('theme_radius_style', 'sharp', 'branding', 'string');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetThemeToDefault')
            ->assertSet('theme_accent_color', '#4f46e5')
            ->assertSet('theme_secondary_color', '#06b6d4')
            ->assertSet('theme_radius_style', 'modern')
            ->assertSet('theme_font_family', 'jakarta')
            ->assertSet('theme_card_elevation', 'bordered');

        $this->assertEquals('#4f46e5', SystemSetting::get('theme_accent_color'));
        $this->assertEquals('modern', SystemSetting::get('theme_radius_style'));
    }

    public function test_super_admin_can_reset_contact_and_email_to_default(): void
    {
        SystemSetting::set('support_email', 'random@test.com', 'contact', 'string');
        SystemSetting::set('mail_host', 'mail.testserver.com', 'email', 'string');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetContactToDefault')
            ->assertSet('support_email', 'support@hirely.app');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('resetEmailToDefault')
            ->assertSet('mail_mailer', 'smtp')
            ->assertSet('mail_from_name', 'Hirely Recruitment Desk')
            ->assertSet('mail_from_address', 'noreply@hirely.app');

        $this->assertEquals('support@hirely.app', SystemSetting::get('support_email'));
        $this->assertEquals('smtp', SystemSetting::get('mail_mailer'));
    }

    public function test_all_settings_subviews_render_reset_buttons(): void
    {
        $test = Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class);

        // 1. Workflows
        $test->call('setTab', 'pipeline')
            ->assertSee('Reset to Defaults')
            ->assertSee('Reset Pipeline to Defaults')
            ->assertSee('Visual Candidate Lifecycle Pipeline Flow');

        // 2. Documents
        $test->call('setTab', 'documents')
            ->assertSee('Reset Document Rules')
            ->assertSee('Candidate Submission Enforcement Matrix');

        // 3. Candidate Rules
        $test->call('setTab', 'candidate_rules')
            ->assertSee('Reset Candidate Rules')
            ->assertSee('Live Challan Payment Grace Window Calculator');

        // 4. Organization
        $test->call('setTab', 'organization')
            ->assertSee('Reset Organization Defaults')
            ->assertSee('Live Candidate Dossier');

        // 5. Academic Criteria
        $test->call('setTab', 'academic_criteria')
            ->assertSee('Reset HEC Criteria')
            ->assertSee('HEC Academic Division Classification');

        // 6. Branding
        $test->call('setTab', 'branding')
            ->assertSee('Reset Theme Defaults')
            ->assertSee('Institutional Theme Presets');

        // 7. Contact
        $test->call('setTab', 'contact')
            ->assertSee('Reset Contact Channels')
            ->assertSee('Live Candidate Facilitation Card Preview');

        // 8. Email
        $test->call('setTab', 'email')
            ->assertSee('Reset SMTP Defaults')
            ->assertSee('Active SMTP Dispatch Gateway Summary');
    }

    public function test_login_page_renders_with_dynamic_theme_and_layout(): void
    {
        SystemSetting::set('theme_accent_color', '#6366f1', 'branding', 'string');
        SystemSetting::set('theme_login_layout', 'split-modern', 'branding', 'string');

        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('1-Click Sandbox Fast Switch');
        $response->assertSee('Candidate Helpdesk');

        // Test center glass layout
        SystemSetting::set('theme_login_layout', 'center-glass', 'branding', 'string');
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('1-Click Sandbox Fast Switch');
    }
}
