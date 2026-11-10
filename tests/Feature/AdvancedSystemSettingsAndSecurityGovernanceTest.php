<?php

namespace Tests\Feature;

use App\Livewire\Admin\SystemSettings;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\PermissionRegistry;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdvancedSystemSettingsAndSecurityGovernanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $registrar;
    protected User $applicant;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $this->seed(SpatieRoleAndPermissionSeeder::class);
        SystemSetting::seedDefaultSettings();

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@hirely.test',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->registrar = User::factory()->create([
            'email' => 'registrar@hirely.test',
            'is_active' => true,
        ]);
        $this->registrar->assignRole('registrar');

        $this->applicant = User::factory()->create([
            'email' => 'applicant@hirely.test',
            'is_active' => true,
        ]);
        $this->applicant->assignRole('applicant');
    }

    public function test_permission_registry_includes_advanced_settings_permissions(): void
    {
        $perms = PermissionRegistry::allPermissionNames();

        $this->assertContains('manage-security-settings', $perms);
        $this->assertContains('manage-system-maintenance', $perms);
        $this->assertContains('view-system-health', $perms);
        $this->assertContains('manage-sms-settings', $perms);
        $this->assertContains('manage-merit-formula', $perms);

        $catalogue = PermissionRegistry::catalogue();
        $this->assertArrayHasKey('System & Workflow Settings', $catalogue);
        $this->assertArrayHasKey('manage-security-settings', $catalogue['System & Workflow Settings']);
    }

    public function test_system_setting_security_and_maintenance_helper_methods(): void
    {
        $this->assertFalse(SystemSetting::isMaintenanceModeEnabled());
        $this->assertTrue(SystemSetting::isRegistrationOpen());

        $security = SystemSetting::getSecurityPolicies();
        $this->assertEquals('optional', $security['two_factor_policy']);
        $this->assertEquals(60, $security['session_timeout']);
        $this->assertEquals(5, $security['max_login_attempts']);
        $this->assertEquals(15, $security['lockout_duration']);
        $this->assertEquals('open', $security['registration_status']);

        $merit = SystemSetting::getMeritFormulaWeights();
        $this->assertEquals(50, $merit['academic']);
        $this->assertEquals(5, $merit['higher_qualification']);
        $this->assertEquals(15, $merit['research']);
        $this->assertEquals(10, $merit['experience']);
        $this->assertEquals(20, $merit['interview']);
        $this->assertEquals(50, $merit['passing_aggregate']);

        $sms = SystemSetting::getSmsGatewayDetails();
        $this->assertFalse($sms['enabled']);
        $this->assertEquals('HIRELY', $sms['sender_id']);
    }

    public function test_super_admin_can_save_security_policies(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('setTab', 'security')
            ->assertSet('activeTab', 'security')
            ->set('auth_two_factor_policy', 'enforced_staff')
            ->set('auth_session_timeout_minutes', 30)
            ->set('auth_max_login_attempts', 3)
            ->set('auth_lockout_duration_minutes', 30)
            ->set('candidate_registration_status', 'closed')
            ->set('candidate_registration_closed_message', 'Intake suspended for winter break.')
            ->set('password_min_length', 12)
            ->set('max_upload_size_mb', 10)
            ->set('allowed_file_types', 'pdf, jpg, png')
            ->call('saveSecuritySettings')
            ->assertHasNoErrors();

        $this->assertEquals('enforced_staff', SystemSetting::get('auth_two_factor_policy'));
        $this->assertEquals(30, (int) SystemSetting::get('auth_session_timeout_minutes'));
        $this->assertEquals(3, (int) SystemSetting::get('auth_max_login_attempts'));
        $this->assertEquals('closed', SystemSetting::get('candidate_registration_status'));
        $this->assertFalse(SystemSetting::isRegistrationOpen());
    }

    public function test_super_admin_can_reset_security_to_default(): void
    {
        SystemSetting::set('candidate_registration_status', 'closed', 'security', 'string');
        SystemSetting::set('auth_session_timeout_minutes', 15, 'security', 'integer');

        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('setTab', 'security')
            ->call('resetSecurityToDefault')
            ->assertSet('candidate_registration_status', 'open')
            ->assertSet('auth_session_timeout_minutes', 60)
            ->assertHasNoErrors();

        $this->assertTrue(SystemSetting::isRegistrationOpen());
        $this->assertEquals(60, (int) SystemSetting::get('auth_session_timeout_minutes'));
    }

    public function test_super_admin_can_save_maintenance_mode_and_clear_caches(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('setTab', 'system')
            ->assertSet('activeTab', 'system')
            ->set('system_maintenance_mode', true)
            ->set('maintenance_notice_heading', 'Upgrading Core Database')
            ->set('maintenance_notice_message', 'Portal will be back in 15 minutes.')
            ->call('saveMaintenanceSettings')
            ->assertHasNoErrors();

        $this->assertTrue(SystemSetting::isMaintenanceModeEnabled());
        $this->assertEquals('Upgrading Core Database', SystemSetting::get('maintenance_notice_heading'));

        // Test cache flusher actions
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('clearApplicationCache')
            ->assertHasNoErrors()
            ->call('clearViewCache')
            ->assertHasNoErrors()
            ->call('clearRouteCache')
            ->assertHasNoErrors()
            ->call('clearConfigCache')
            ->assertHasNoErrors()
            ->call('optimizeSystem')
            ->assertHasNoErrors();
    }

    public function test_super_admin_can_save_sms_gateway_and_merit_weights(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('setTab', 'email')
            ->set('sms_enabled', true)
            ->set('sms_provider', 'zong')
            ->set('sms_sender_id', 'AWKUM-RECRUIT')
            ->set('sms_api_key', 'secret-key-123')
            ->call('saveSmsSettings')
            ->assertHasNoErrors();

        $this->assertTrue((bool) SystemSetting::get('sms_enabled'));
        $this->assertEquals('AWKUM-RECRUIT', SystemSetting::get('sms_sender_id'));

        // Test test sms dispatch
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->set('test_sms_recipient', '+923001234567')
            ->call('sendTestSms')
            ->assertHasNoErrors();

        // Test merit formula saving under academic criteria
        Livewire::actingAs($this->superAdmin)
            ->test(SystemSettings::class)
            ->call('setTab', 'academic_criteria')
            ->set('merit_academic_weight', 45)
            ->set('merit_higher_qualification_weight', 10)
            ->set('merit_research_weight', 15)
            ->set('merit_experience_weight', 10)
            ->set('merit_interview_weight', 20)
            ->set('merit_minimum_passing_aggregate', 55)
            ->call('saveAcademicCriteriaSettings')
            ->assertHasNoErrors();

        $this->assertEquals(45, (int) SystemSetting::get('merit_academic_weight'));
        $this->assertEquals(55, (int) SystemSetting::get('merit_minimum_passing_aggregate'));
    }

    public function test_unauthorized_user_is_strictly_blocked_from_saving_security_or_clearing_cache(): void
    {
        $this->actingAs($this->applicant);

        Livewire::test(SystemSettings::class)
            ->assertStatus(403);
    }

    public function test_two_factor_authentication_totp_qr_enrollment_and_activation(): void
    {
        $this->actingAs($this->superAdmin);

        $testable = Livewire::test(SystemSettings::class, ['tab' => 'security'])
            ->call('open2FaModal')
            ->assertSet('is2FaModalOpen', true);

        $secret = $testable->get('twoFactorSecret');
        $this->assertNotEmpty($secret);
        $this->assertEquals(32, strlen($secret));
        $this->assertStringContainsString('otpauth://totp/', $testable->get('twoFactorQrUri'));
        $this->assertCount(8, $testable->get('twoFactorRecoveryCodes'));

        // Confirm enrollment with 6-digit code
        $testable->set('twoFactorOtpCode', '654321')
            ->call('confirm2FaEnrollment')
            ->assertSet('is2FaModalOpen', false);

        $this->superAdmin->refresh();
        $this->assertTrue($this->superAdmin->hasTwoFactorEnabled());
        $this->assertNotNull($this->superAdmin->two_factor_confirmed_at);
        $this->assertNotNull($this->superAdmin->two_factor_secret);

        // Disable 2FA
        $testable->call('disable2Fa');
        $this->superAdmin->refresh();
        $this->assertFalse($this->superAdmin->hasTwoFactorEnabled());
        $this->assertNull($this->superAdmin->two_factor_secret);
    }

    public function test_dossier_sha256_cryptographic_seal_generation_and_integrity(): void
    {
        $campaign = \App\Models\Campaign::create([
            'job_code' => 'CS-DS-01',
            'title' => 'Assistant Professor of Data Science',
            'department' => 'Computer Science',
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $app = \App\Models\Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->assertFalse($app->isDossierSealed());

        $hash = $app->sealDossierSha256();

        $this->assertEquals(64, strlen($hash));
        $app->refresh();
        $this->assertTrue($app->isDossierSealed());
        $this->assertEquals($hash, $app->dossier_sha256_hash);
        $this->assertNotNull($app->dossier_sealed_at);
    }
}
