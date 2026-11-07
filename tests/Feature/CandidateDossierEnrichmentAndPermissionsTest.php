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

        // Seed sample publications, research projects, work experiences for dossier verification
        Publication::create([
            'basic_info_id' => $basicInfo->id,
            'title' => 'Deep Residual Learning for Automated Medical Screening',
            'journal_name' => 'IEEE Transactions on Neural Networks and Learning Systems',
            'issn_print' => '2162-237X',
            'publication_year' => 2024,
            'hjrs_category' => 'W',
            'impact_factor' => 10.4,
            'author_order' => 'FIRST_AUTHOR',
            'is_verified' => true,
        ]);
        Publication::create([
            'basic_info_id' => $basicInfo->id,
            'title' => 'Decentralized Consensus Protocols in Byzantine Environments',
            'journal_name' => 'ACM Computing Surveys',
            'issn_print' => '0360-0300',
            'publication_year' => 2023,
            'hjrs_category' => 'W',
            'impact_factor' => 16.6,
            'author_order' => 'CORRESPONDING',
            'is_verified' => true,
        ]);
        Publication::create([
            'basic_info_id' => $basicInfo->id,
            'title' => 'Scalable Machine Learning on Distributed GPU Clusters',
            'journal_name' => 'Journal of Parallel and Distributed Computing',
            'issn_print' => '0743-7315',
            'publication_year' => 2022,
            'hjrs_category' => 'X',
            'impact_factor' => 4.2,
            'author_order' => 'CO_AUTHOR',
            'is_verified' => true,
        ]);
        Publication::create([
            'basic_info_id' => $basicInfo->id,
            'title' => 'Fault-Tolerant Microservices in Cloud Native Environments',
            'journal_name' => 'IEEE Cloud Computing',
            'issn_print' => '2325-6095',
            'publication_year' => 2021,
            'hjrs_category' => 'Y',
            'impact_factor' => 2.8,
            'author_order' => 'FIRST_AUTHOR',
            'is_verified' => true,
        ]);

        ResearchProject::create([
            'basic_info_id' => $basicInfo->id,
            'project_title' => 'National AI Infrastructure for Genomic Healthcare',
            'funding_agency' => 'Higher Education Commission (HEC - NRPU)',
            'grant_amount' => 14500000.00,
            'role_in_project' => 'PRINCIPAL_INVESTIGATOR',
            'status' => 'COMPLETED',
            'start_date' => '2021-01-01',
            'completion_date' => '2023-12-31',
        ]);

        WorkExperience::create([
            'basic_info_id' => $basicInfo->id,
            'job_title' => 'Associate Professor of Computer Science',
            'organization' => 'National University of Sciences & Technology',
            'experience_type' => 'TEACHING',
            'start_date' => '2019-01-01',
            'end_date' => '2024-06-30',
        ]);

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $reviewerUser->id,
            'assigned_by' => $candidateUser->id,
            'status' => 'PENDING',
        ]);

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
