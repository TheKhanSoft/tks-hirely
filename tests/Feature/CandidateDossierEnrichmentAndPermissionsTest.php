<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\Publication;
use App\Models\ResearchProject;
use App\Models\Patent;
use App\Models\ProfessionalEvent;
use App\Models\WorkExperience;
use App\Models\SystemSetting;
use App\Livewire\Admin\SystemSettings;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidateDossierEnrichmentAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        SystemSetting::seedDefaultSettings();
    }

    public function test_migration_enriches_candidate_publications_projects_and_reviews(): void
    {
        // 1. Create campaign, candidate, application
        $campaign = Campaign::create([
            'job_code' => 'TEST-ENRICH-PROF',
            'title' => 'Professor Computer Science',
            'department' => 'Department of Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(10),
            'closing_date' => now()->addDays(20),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $candidateUser = User::factory()->create(['email' => 'scholar.candidate@gmail.com']);
        $candidateUser->assignRole('applicant');

        $basicInfo = BasicInfo::create([
            'user_id' => $candidateUser->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Dr. Arslan',
            'last_name' => 'Tariq',
            'cnic_number' => '61101-9988776-1',
            'date_of_birth' => '1985-04-12',
            'is_locked' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidateUser->id,
            'campaign_id' => $campaign->id,
            'status' => 'UNDER_REVIEW',
            'submission_timestamp' => now()->subDays(5),
        ]);

        $reviewerUser = User::factory()->create(['email' => 'dr.kenji.sato@u-tokyo.ac.jp']);
        $reviewerUser->assignRole('academic-reviewer');

        // Execute the enrichment migration up()
        $migration = require database_path('migrations/2026_11_05_235000_enrich_candidate_dossiers_publications_and_projects.php');
        $migration->up();

        // 2. Verify candidate now has rich publications
        $publications = Publication::where('basic_info_id', $basicInfo->id)->get();
        $this->assertGreaterThanOrEqual(4, $publications->count());
        $this->assertTrue($publications->contains('hjrs_category', 'W'));
        $this->assertNotEmpty($publications->first()->journal_name);
        $this->assertNotEmpty($publications->first()->issn_print);

        // 3. Verify candidate now has funded research projects
        $projects = ResearchProject::where('basic_info_id', $basicInfo->id)->get();
        $this->assertGreaterThanOrEqual(1, $projects->count());
        $this->assertGreaterThan(1000000, $projects->first()->grant_amount);
        $this->assertNotEmpty($projects->first()->funding_agency);
        $this->assertContains($projects->first()->status, ['COMPLETED', 'ONGOING']);

        // 4. Verify candidate has work experiences
        $experiences = WorkExperience::where('basic_info_id', $basicInfo->id)->get();
        $this->assertGreaterThanOrEqual(1, $experiences->count());

        // 5. Verify peer review created for application
        $reviews = ApplicationReview::where('application_id', $application->id)->get();
        $this->assertNotEmpty($reviews);
        $this->assertContains($reviews->first()->status, ['PENDING', 'SUBMITTED']);

        // 6. Test Dossier Print View reflects the newly enriched publications & projects
        $this->actingAs($candidateUser);
        $response = $this->get('/applicant/applications/' . $application->id . '/print');
        $response->assertStatus(200);
        $response->assertSee($publications->first()->title);
        $response->assertSee($projects->first()->project_title);
    }

    public function test_settings_save_actions_strictly_enforce_manage_settings_permission(): void
    {
        $nonAdmin = User::factory()->create();
        $nonAdmin->assignRole('applicant');

        $this->actingAs($nonAdmin);

        // Direct component test is forbidden without manage-settings
        Livewire::test(SystemSettings::class)
            ->assertForbidden();

        // Grant permission to an administrative user
        $admin = User::factory()->create();
        $admin->givePermissionTo('manage-settings');
        $this->actingAs($admin);

        Livewire::test(SystemSettings::class)
            ->assertOk()
            ->set('app_name', 'Hirely Higher Education')
            ->call('saveBrandingSettings')
            ->assertHasNoErrors();

        $this->assertEquals('Hirely Higher Education', SystemSetting::getAppName());
    }
}
