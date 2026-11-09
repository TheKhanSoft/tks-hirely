<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Livewire\Admin\SystemSettings;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsMasterSidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $registrar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create(['email' => 'superadmin@awkum.edu.pk']);
        $this->admin->assignRole('super-admin');

        $this->registrar = User::factory()->create(['email' => 'registrar@awkum.edu.pk']);
        $this->registrar->assignRole('registrar');
    }

    public function test_sidebar_renders_with_institution_and_identity_at_the_top(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/settings/organization');
        $response->assertStatus(200);

        $content = $response->getContent();

        // 1. Verify "Institution & Identity" is at the top before "Recruitment & Academic Governance"
        $posInstitution = strpos($content, 'Institution &amp; Identity');
        if ($posInstitution === false) {
            $posInstitution = strpos($content, 'Institution & Identity');
        }
        $posRecruitment = strpos($content, 'Recruitment &amp; Academic Governance');
        if ($posRecruitment === false) {
            $posRecruitment = strpos($content, 'Recruitment & Academic Governance');
        }
        $posComms = strpos($content, 'Communications &amp; Messaging');
        if ($posComms === false) {
            $posComms = strpos($content, 'Communications & Messaging');
        }
        $posGovernance = strpos($content, 'Platform &amp; Governance');
        if ($posGovernance === false) {
            $posGovernance = strpos($content, 'Platform & Governance');
        }

        $this->assertNotFalse($posInstitution, 'Institution & Identity header must exist');
        $this->assertNotFalse($posRecruitment, 'Recruitment header must exist');
        $this->assertNotFalse($posComms, 'Communications header must exist');
        $this->assertNotFalse($posGovernance, 'Platform & Governance header must exist');

        // Verify strictly chronological priority: Institution < Recruitment < Comms < Governance
        $this->assertTrue($posInstitution < $posRecruitment, 'Institution & Identity must appear before Recruitment');
        $this->assertTrue($posRecruitment < $posComms, 'Recruitment must appear before Communications');
        $this->assertTrue($posComms < $posGovernance, 'Communications must appear before Platform Governance');

        // 2. Verify Search Filter is rendered
        $response->assertSee('Search settings, CGPA, AWKUM, fees...');

        // 3. Verify Active tab state for Organization
        $response->assertSee('Institution Profile & Acronym');
    }

    public function test_task_grouped_main_navbar_renders_with_collapsible_elements_and_tooltips(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);

        // 1. Operational Task Groups
        $response->assertSee('Campaigns &amp; Intake', false);
        $response->assertSee('Candidacies &amp; Dossiers', false);
        $response->assertSee('Statutory Governance', false);
        $response->assertSee('Administration', false);

        // 2. Collapsible rail toggle buttons in top header & desktop topbar
        $response->assertSee('sidebar-header-box', false);
        $response->assertSee('sidebar-header-toggle-btn', false);
        $response->assertSee('id="sidebarRailToggle"', false);
        $response->assertSee('id="sidebarDesktopToggle"', false);

        // 3. Floating Tooltips (data-tooltip) & Global Tooltip Container
        $response->assertSee('id="sidebarRailTooltip"', false);
        $response->assertSee('data-tooltip="Operations Desk"', false);
        $response->assertSee('data-tooltip="Candidate Profiles"', false);
        $response->assertSee('data-tooltip="Academic Reviewers"', false);
        $response->assertSee('data-tooltip="Settings &amp; Workflows"', false);

        // 4. Governance Engine and bottom beacon removed
        $response->assertDontSee('sidebar-status-beacon-box', false);
        $response->assertDontSee('Operational • Live Quorum', false);
        $response->assertDontSee('Governance Engine: Operational', false);
    }

    public function test_universal_search_palette_indexes_deep_settings(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\UniversalSearchPalette::class)
            ->set('isOpen', true)
            ->set('query', 'CGPA')
            ->assertSee('Terminal Degree Minimum CGPA')
            ->set('query', 'AWKUM')
            ->assertSee('Short Name / Acronym (AWKUM)')
            ->set('query', 'Mailpit')
            ->assertSee('SMTP Port');
    }

    public function test_switching_tabs_in_master_detail_sidebar(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SystemSettings::class, ['tab' => 'organization'])
            ->assertSet('activeTab', 'organization')
            ->call('setTab', 'academic-criteria')
            ->assertSet('activeTab', 'academic_criteria')
            ->call('setTab', 'branding')
            ->assertSet('activeTab', 'branding')
            ->call('setTab', 'email-templates')
            ->assertSet('activeTab', 'email_templates');
    }

    public function test_registrar_can_access_master_sidebar_settings(): void
    {
        $this->actingAs($this->registrar);

        $response = $this->get('/registrar/settings/organization');
        $response->assertStatus(200);
        $response->assertSee('Institution Profile & Acronym');
    }

    public function test_bento_hub_grid_and_view_mode_switcher_rendered_in_settings(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/settings/organization');
        $response->assertStatus(200);

        // 1. View Mode Switcher Controls
        $response->assertSee('Bento Hub');
        $response->assertSee('Master Sidebar');
        $response->assertSee('hirely_settings_view_mode');

        // 2. Bento Grid 11 Cards
        $response->assertSee('settingsBentoHub');
        $response->assertSee('Workflow Pipelines');
        $response->assertSee('Document Proofs');
        $response->assertSee('Candidate Policies');
        $response->assertSee('Academic Benchmarks');
        $response->assertSee('Institution Profile & Acronym');
        $response->assertSee('Theme Studio & Design System');
        $response->assertSee('Contact & Helpdesk');
        $response->assertSee('Email & Delivery');
        $response->assertSee('Email Templates & Triggers');
        $response->assertSee('Security & Access');
        $response->assertSee('System & Health');

        // 3. Bento Card Header Layout (Title inline in front of Icon, category badges removed)
        $response->assertSee('settings-tab-card-header');
        $response->assertSee('settings-tab-icon-box');
        $response->assertSee('settings-tab-title');
        $response->assertDontSee('GATES &amp; QUORUM', false);
        $response->assertDontSee('settings-tab-badge');
    }
}
