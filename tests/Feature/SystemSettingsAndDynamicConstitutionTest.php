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
}
