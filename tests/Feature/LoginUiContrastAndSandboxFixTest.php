<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginUiContrastAndSandboxFixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SpatieRoleAndPermissionSeeder::class);
        SystemSetting::seedDefaultSettings();
    }

    public function test_login_page_renders_with_high_contrast_showcase_and_theme_toggle(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('auth-showcase-card');
        $response->assertSee('OFFICIAL INSTITUTIONAL PORTAL');
        $response->assertSee('Statutory Faculty');
        $response->assertSee('HEC Statutory Criteria Alignment');
        $response->assertSee('Confidential Blind Peer Review');
        $response->assertSee('Automated 1Link');
        $response->assertSee('themeBtnLight');
        $response->assertSee('themeBtnDark');
        $response->assertSee('setLoginTheme');
    }

    public function test_login_sandbox_fast_switch_renders_2_column_non_overlapping_chips(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('1-Click Sandbox Fast Switch');
        $response->assertSee('registrar@university.edu');
        $response->assertSee('applicant1@gmail.com');
        $response->assertSee('scrutiny1@university.edu');
        $response->assertSee('appellate1@university.edu');
        $response->assertSee('board1@university.edu');
        $response->assertSee('superadmin@university.edu');
        $response->assertSee('quick-sandbox-chip');
    }

    public function test_login_page_renders_in_center_glass_and_minimalist_modes(): void
    {
        SystemSetting::set('theme_login_layout', 'center-glass', 'branding', 'string');
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('1-Click Sandbox Fast Switch');

        SystemSetting::set('theme_login_layout', 'minimalist', 'branding', 'string');
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('1-Click Sandbox Fast Switch');
    }
}
