<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Livewire\Admin\CampaignCrud;
use App\Livewire\Applicant\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CampaignFilteringByAdvertisementAndYearTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_campaign_crud_can_filter_jobs_by_advertisement(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad1 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/01',
            'title' => 'Annual Faculty Recruitment 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        $ad2 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/02',
            'title' => 'Staff Recruitment 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        $job1 = Campaign::create([
            'advertisement_id' => $ad1->id,
            'job_code' => 'CS-PROF-01',
            'title' => 'Professor Computer Science',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $job2 = Campaign::create([
            'advertisement_id' => $ad2->id,
            'job_code' => 'ADMIN-DIR-01',
            'title' => 'Director Administration',
            'department' => 'Central Admin',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 2000,
            'is_active' => true,
        ]);

        $standaloneJob = Campaign::create([
            'advertisement_id' => null,
            'job_code' => 'DIRECT-LECT-01',
            'title' => 'Direct Lecturer Physics',
            'department' => 'Physics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 1500,
            'is_active' => true,
        ]);

        // Unfiltered should contain all 3
        Livewire::test(CampaignCrud::class)
            ->assertSee('Professor Computer Science')
            ->assertSee('Director Administration')
            ->assertSee('Direct Lecturer Physics');

        // Filter by Advertisement 1
        Livewire::test(CampaignCrud::class)
            ->set('advertisementFilter', (string)$ad1->id)
            ->assertSee('Professor Computer Science')
            ->assertDontSee('Director Administration')
            ->assertDontSee('Direct Lecturer Physics');

        // Filter by Advertisement 2
        Livewire::test(CampaignCrud::class)
            ->set('advertisementFilter', (string)$ad2->id)
            ->assertDontSee('Professor Computer Science')
            ->assertSee('Director Administration')
            ->assertDontSee('Direct Lecturer Physics');

        // Filter by Standalone (No Advertisement)
        Livewire::test(CampaignCrud::class)
            ->set('advertisementFilter', 'NONE')
            ->assertDontSee('Professor Computer Science')
            ->assertDontSee('Director Administration')
            ->assertSee('Direct Lecturer Physics');
    }

    public function test_campaign_crud_can_filter_jobs_by_year(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad2025 = Advertisement::create([
            'advertisement_no' => 'ADV-2025/99',
            'title' => 'Old Recruitment Drive 2025',
            'year' => 2025,
            'valid_from' => '2025-01-01',
            'valid_to' => '2025-02-01',
            'is_active' => false,
        ]);

        $ad2026 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/01',
            'title' => 'Current Faculty Drive 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        $job2025 = Campaign::create([
            'advertisement_id' => $ad2025->id,
            'job_code' => 'HIST-2025',
            'title' => 'Assistant Professor History 2025',
            'department' => 'History',
            'track_type' => 'ACADEMIC',
            'opening_date' => '2025-01-01',
            'closing_date' => '2025-02-01',
            'application_fee' => 2500,
            'is_active' => false,
        ]);

        $job2026 = Campaign::create([
            'advertisement_id' => $ad2026->id,
            'job_code' => 'DATA-2026',
            'title' => 'Associate Professor Data Science 2026',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 3500,
            'is_active' => true,
        ]);

        // Filter by Year 2025
        Livewire::test(CampaignCrud::class)
            ->set('yearFilter', '2025')
            ->assertSee('Assistant Professor History 2025')
            ->assertDontSee('Associate Professor Data Science 2026');

        // Filter by Year 2026
        Livewire::test(CampaignCrud::class)
            ->set('yearFilter', '2026')
            ->assertDontSee('Assistant Professor History 2025')
            ->assertSee('Associate Professor Data Science 2026');
    }

    public function test_campaign_crud_resets_advertisement_filter_when_switching_to_incompatible_year(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad2025 = Advertisement::create([
            'advertisement_no' => 'ADV-2025/01',
            'title' => 'Drive 2025',
            'year' => 2025,
            'valid_from' => '2025-01-01',
            'valid_to' => '2025-02-01',
            'is_active' => false,
        ]);

        Livewire::test(CampaignCrud::class)
            ->set('advertisementFilter', (string)$ad2025->id)
            ->assertSet('advertisementFilter', (string)$ad2025->id)
            ->set('yearFilter', '2026')
            ->assertSet('advertisementFilter', ''); // Reset automatically because ad2025 belongs to 2025!
    }

    public function test_campaign_crud_reset_filters_helper(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(CampaignCrud::class)
            ->set('search', 'Professor')
            ->set('yearFilter', '2026')
            ->set('advertisementFilter', '1')
            ->set('trackFilter', 'ACADEMIC')
            ->set('statusFilter', 'ACTIVE')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('yearFilter', '')
            ->assertSet('advertisementFilter', '')
            ->assertSet('trackFilter', 'ALL')
            ->assertSet('statusFilter', 'ALL');
    }

    public function test_applicant_dashboard_can_filter_modal_positions_by_advertisement_and_year(): void
    {
        $user = User::factory()->create();
        $user->assignRole('applicant');
        $this->actingAs($user);

        $ad1 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/F1',
            'title' => 'Faculty Recruitment 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(2),
            'valid_to' => now()->addDays(20),
            'is_active' => true,
        ]);

        $ad2 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/A1',
            'title' => 'Admin Recruitment 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(2),
            'valid_to' => now()->addDays(20),
            'is_active' => true,
        ]);

        $camp1 = Campaign::create([
            'advertisement_id' => $ad1->id,
            'job_code' => 'FAC-01',
            'title' => 'Lecturer Mathematics',
            'department' => 'Mathematics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(2),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1500,
            'is_active' => true,
        ]);

        $camp2 = Campaign::create([
            'advertisement_id' => $ad2->id,
            'job_code' => 'ADM-01',
            'title' => 'Assistant Registrar Examination',
            'department' => 'Examination Branch',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(2),
            'closing_date' => now()->addDays(20),
            'application_fee' => 2000,
            'is_active' => true,
        ]);

        Livewire::test(Dashboard::class)
            ->set('showApplyModal', true)
            ->set('campaignAdFilter', (string)$ad1->id)
            ->assertSee('Lecturer Mathematics')
            ->assertDontSee('Assistant Registrar Examination')
            ->set('campaignAdFilter', (string)$ad2->id)
            ->assertDontSee('Lecturer Mathematics')
            ->assertSee('Assistant Registrar Examination')
            ->call('resetCampaignFilters')
            ->assertSet('campaignAdFilter', '')
            ->assertSee('Lecturer Mathematics')
            ->assertSee('Assistant Registrar Examination');
    }

    public function test_campaign_crud_mounts_with_advertisement_id_query_parameter(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026/SPEC',
            'title' => 'Specialized Engineering Cadre',
            'year' => 2026,
            'valid_from' => now()->subDays(1),
            'valid_to' => now()->addDays(20),
            'is_active' => true,
        ]);

        $job = Campaign::create([
            'advertisement_id' => $ad->id,
            'job_code' => 'ENG-01',
            'title' => 'Lecturer Electrical Engineering',
            'department' => 'Electrical Engineering',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(1),
            'closing_date' => now()->addDays(20),
            'application_fee' => 2000,
            'is_active' => true,
        ]);

        $otherJob = Campaign::create([
            'advertisement_id' => null,
            'job_code' => 'OTH-01',
            'title' => 'Medical Officer',
            'department' => 'Health Services',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(1),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1000,
            'is_active' => true,
        ]);

        // Simulate visiting with ?advertisement_id=X
        $this->get('/admin/campaigns?advertisement_id=' . $ad->id)
            ->assertOk()
            ->assertSee('Lecturer Electrical Engineering')
            ->assertDontSee('Medical Officer');
    }

    public function test_campaign_crud_can_filter_by_track_and_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $academicActive = Campaign::create([
            'job_code' => 'ACAD-ACT',
            'title' => 'Active Professor CS',
            'department' => 'CS',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(2),
            'closing_date' => now()->addDays(20),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $adminClosed = Campaign::create([
            'job_code' => 'ADM-CLS',
            'title' => 'Closed Assistant Treasurer',
            'department' => 'Accounts',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(20),
            'closing_date' => now()->subDays(2),
            'application_fee' => 2000,
            'is_active' => false,
        ]);

        // Filter track ACADEMIC
        Livewire::test(CampaignCrud::class)
            ->set('trackFilter', 'ACADEMIC')
            ->assertSee('Active Professor CS')
            ->assertDontSee('Closed Assistant Treasurer');

        // Filter track ADMINISTRATIVE
        Livewire::test(CampaignCrud::class)
            ->set('trackFilter', 'ADMINISTRATIVE')
            ->assertDontSee('Active Professor CS')
            ->assertSee('Closed Assistant Treasurer');

        // Filter status ACTIVE
        Livewire::test(CampaignCrud::class)
            ->set('statusFilter', 'ACTIVE')
            ->assertSee('Active Professor CS')
            ->assertDontSee('Closed Assistant Treasurer');

        // Filter status CLOSED
        Livewire::test(CampaignCrud::class)
            ->set('statusFilter', 'CLOSED')
            ->assertDontSee('Active Professor CS')
            ->assertSee('Closed Assistant Treasurer');
    }
}
