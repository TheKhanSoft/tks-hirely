<?php

namespace Tests\Feature;

use App\Livewire\Admin\SystemSettings;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AppVersion;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Tests\TestCase;

class SystemControlledVersioningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SpatieRoleAndPermissionSeeder::class);
    }

    /**
     * Test 1: AppVersion service correctly resolves version, build, and release details.
     */
    public function test_app_version_service_resolves_configured_and_dynamic_details()
    {
        Config::set('app.version', 'v2.4.2');
        Config::set('app.build', '20261111');
        Config::set('app.release_name', 'Statutory Recruitment OS');

        $this->assertEquals('v2.4.2', AppVersion::getVersion());
        $this->assertEquals('v2.4.2', AppVersion::getShortVersion());
        $this->assertEquals('20261111', AppVersion::getBuild());
        $this->assertEquals('Statutory Recruitment OS', AppVersion::getReleaseName());
        $this->assertEquals('v2.4.2 (Build 20261111)', AppVersion::format());
        $this->assertEquals('v2.4.2 • Build 20261111', AppVersion::getBadgeString());

        $publicFooter = AppVersion::getPublicFooterString();
        $this->assertStringContainsString('Statutory Recruitment OS', $publicFooter);
        $this->assertStringContainsString('v2.4.2 (Build 20261111)', $publicFooter);

        $diag = AppVersion::getDiagnostics();
        $this->assertEquals('v2.4.2', $diag['version']);
        $this->assertEquals('20261111', $diag['build']);
        $this->assertEquals('Statutory Recruitment OS', $diag['release_name']);
        $this->assertArrayHasKey('php_version', $diag);
        $this->assertArrayHasKey('laravel_version', $diag);
    }

    /**
     * Test 2: Dynamic config changes dynamically update without database dependency.
     */
    public function test_dynamic_config_changes_update_version_without_database_dependency()
    {
        Config::set('app.version', 'v3.1.0-RC1');
        Config::set('app.build', '20270101');
        Config::set('app.release_name', 'Syndicate Governance Edition');

        $this->assertEquals('v3.1.0-RC1', AppVersion::getVersion());
        $this->assertEquals('20270101', AppVersion::getBuild());
        $this->assertEquals('v3.1.0-RC1 (Build 20270101)', AppVersion::format());

        // SystemSetting::get('app_version') dynamically delegates to AppVersion::format()
        $this->assertEquals('v3.1.0-RC1 (Build 20270101)', SystemSetting::get('app_version'));
        $this->assertEquals('v3.1.0-RC1 (Build 20270101)', SystemSetting::getAppVersion());
    }

    /**
     * Test 3: Admin branding save does not alter or overwrite system-controlled version in database.
     */
    public function test_admin_settings_does_not_permit_manual_version_override()
    {
        Config::set('app.version', 'v2.4.2');
        Config::set('app.build', '20261111');

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        Livewire::actingAs($admin)
            ->test(SystemSettings::class, ['tab' => 'branding'])
            ->assertSet('activeTab', 'branding')
            ->set('app_name', 'University of Swat Recruitment')
            ->call('saveBrandingSettings')
            ->assertHasNoErrors();

        // SystemSetting::get('app_version') remains dynamically governed
        $this->assertEquals('v2.4.2 (Build 20261111)', SystemSetting::get('app_version'));
        $this->assertEquals('University of Swat Recruitment', SystemSetting::get('app_name'));
    }

    /**
     * Test 4: Login screen renders public version and build identifier in footer.
     */
    public function test_login_screen_renders_version_in_footer()
    {
        Config::set('app.version', 'v2.4.2');
        Config::set('app.build', '20261111');
        Config::set('app.release_name', 'Statutory Recruitment OS');

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('v2.4.2 (Build 20261111)');
        $response->assertSee('Statutory Recruitment OS');
    }

    /**
     * Test 5: Authenticated layout renders short badge in sidebar and full build in footer.
     */
    public function test_authenticated_portal_renders_sidebar_badge_and_footer_build()
    {
        Config::set('app.version', 'v2.4.2');
        Config::set('app.build', '20261111');

        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');

        $response = $this->actingAs($applicant)->get('/applicant/dashboard');
        $response->assertStatus(200);

        // Sidebar brand badge shows short version
        $response->assertSee('v2.4.2');

        // Portal footer shows formatted build identifier
        $response->assertSee('v2.4.2 (Build 20261111)');
    }

    /**
     * Test 6: Dynamic auto detection resolves build date or git commit when set to auto.
     */
    public function test_automatic_dynamic_detection_from_git_and_system()
    {
        Config::set('app.version', 'auto');
        Config::set('app.build', 'auto');
        Config::set('app.release_name', 'Dynamic Statutory Engine');

        $this->assertTrue(AppVersion::isDynamic());
        $this->assertNotEmpty(AppVersion::getVersion());
        $this->assertNotEmpty(AppVersion::getBuild());
        $this->assertEquals('Dynamic Statutory Engine', AppVersion::getReleaseName());

        $formatted = AppVersion::format();
        $this->assertStringContainsString('Build', $formatted);
    }
}
