<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Advertisement;
use App\Models\Campaign;
use App\Models\Application;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\ApplicationManagement;
use App\Livewire\Admin\AdvertisementManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class CandidatesFilteredByAdvertisementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_candidate_profile_management_defaults_to_last_advertisement()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        // Create older advertisement
        $oldAd = Advertisement::create([
            'advertisement_no' => 'ADV-2025/01',
            'title' => 'Faculty Recruitment 2025',
            'year' => 2025,
            'valid_from' => now()->subYear(),
            'valid_to' => now()->subMonths(10),
            'is_active' => true,
        ]);

        // Create last / latest advertisement
        $latestAd = Advertisement::create([
            'advertisement_no' => 'ADV-2026/01',
            'title' => 'Annual Faculty Recruitment 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        // When mounted without specific ?ad= param, it defaults to latest advertisement
        Livewire::test(CandidateProfileManagement::class)
            ->assertSet('advertisementFilter', (string)$latestAd->id);
    }

    public function test_candidate_profile_management_shows_candidates_for_selected_advertisement()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        // Advertisements
        $ad2025 = Advertisement::create([
            'advertisement_no' => 'ADV-2025/01',
            'title' => 'Faculty Recruitment 2025',
            'year' => 2025,
            'valid_from' => now()->subYear(),
            'valid_to' => now()->subMonths(10),
            'is_active' => true,
        ]);

        $ad2026 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/01',
            'title' => 'Annual Faculty Recruitment 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        // Campaigns
        $job2025 = Campaign::create([
            'advertisement_id' => $ad2025->id,
            'job_code' => 'MATH-LECT-2025',
            'title' => 'Lecturer Mathematics 2025',
            'department' => 'Mathematics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subYear(),
            'closing_date' => now()->subMonths(10),
            'application_fee' => 1000,
            'is_active' => false,
        ]);

        $job2026 = Campaign::create([
            'advertisement_id' => $ad2026->id,
            'job_code' => 'CS-PROF-2026',
            'title' => 'Professor Computer Science 2026',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        // Candidates
        $candidate2025 = User::factory()->create(['email' => 'old_candidate@test.com']);
        $candidate2025->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $candidate2025->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Old',
            'last_name' => 'Applicant',
            'cnic_number' => '42101-1111111-1',
            'date_of_birth' => '1990-01-01',
        ]);
        Application::create([
            'user_id' => $candidate2025->id,
            'campaign_id' => $job2025->id,
            'status' => 'SUBMITTED',
        ]);

        $candidate2026 = User::factory()->create(['email' => 'current_candidate@test.com']);
        $candidate2026->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $candidate2026->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Current',
            'last_name' => 'Applicant',
            'cnic_number' => '42101-2222222-2',
            'date_of_birth' => '1992-05-15',
        ]);
        Application::create([
            'user_id' => $candidate2026->id,
            'campaign_id' => $job2026->id,
            'status' => 'PAID',
        ]);

        // By default, it selects the last advertisement (2026)
        $component = Livewire::test(CandidateProfileManagement::class);
        $component->assertSet('advertisementFilter', (string)$ad2026->id);

        $profiles = $component->viewData('profiles');
        $emails = $profiles->pluck('user.email')->all();

        // Must show candidate for 2026
        $this->assertContains('current_candidate@test.com', $emails);
        // Must NOT show candidate from 2025
        $this->assertNotContains('old_candidate@test.com', $emails);

        // When switching to the 2025 advertisement:
        $component->set('advertisementFilter', (string)$ad2025->id);
        $profiles2025 = $component->viewData('profiles');
        $emails2025 = $profiles2025->pluck('user.email')->all();

        $this->assertContains('old_candidate@test.com', $emails2025);
        $this->assertNotContains('current_candidate@test.com', $emails2025);

        // When switching to ALL advertisements:
        $component->set('advertisementFilter', 'ALL');
        $allProfiles = $component->viewData('profiles');
        $allEmails = $allProfiles->pluck('user.email')->all();

        $this->assertContains('current_candidate@test.com', $allEmails);
        $this->assertContains('old_candidate@test.com', $allEmails);
    }

    public function test_candidate_profile_management_export_respects_selected_advertisement()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026/EXP',
            'title' => 'Export Cycle Advertisement',
            'year' => 2026,
            'valid_from' => now()->subDays(2),
            'valid_to' => now()->addDays(20),
            'is_active' => true,
        ]);

        Livewire::test(CandidateProfileManagement::class)
            ->set('advertisementFilter', (string)$ad->id)
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    public function test_advertisement_management_modal_shows_candidates()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026/ROSTER',
            'title' => 'Faculty Roster 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        $job = Campaign::create([
            'advertisement_id' => $ad->id,
            'job_code' => 'PHY-PROF-01',
            'title' => 'Professor Physics',
            'department' => 'Physics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $applicant = User::factory()->create(['email' => 'physicist@test.com']);
        $applicant->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $applicant->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Albert',
            'last_name' => 'Einstein',
            'cnic_number' => '42101-3333333-3',
            'date_of_birth' => '1985-03-14',
        ]);
        Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $job->id,
            'status' => 'PAID',
        ]);

        Livewire::test(AdvertisementManagement::class)
            ->call('openCandidatesModal', $ad->id)
            ->assertSet('showCandidatesModal', true)
            ->assertSee('Albert Einstein')
            ->assertSee('Professor Physics')
            ->call('closeCandidatesModal')
            ->assertSet('showCandidatesModal', false);
    }

    public function test_application_management_defaults_to_last_advertisement_and_filters()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad1 = Advertisement::create([
            'advertisement_no' => 'ADV-2025/APP',
            'title' => 'Older Cycle',
            'year' => 2025,
            'valid_from' => now()->subYear(),
            'valid_to' => now()->subMonths(10),
            'is_active' => true,
        ]);

        $ad2 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/APP',
            'title' => 'Latest Cycle',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        $job1 = Campaign::create([
            'advertisement_id' => $ad1->id,
            'job_code' => 'OLD-JOB-01',
            'title' => 'Old Job Position',
            'track_type' => 'ACADEMIC',
            'department' => 'CS',
            'opening_date' => now()->subYear(),
            'closing_date' => now()->subMonths(10),
            'application_fee' => 1000,
            'is_active' => false,
        ]);

        $job2 = Campaign::create([
            'advertisement_id' => $ad2->id,
            'job_code' => 'NEW-JOB-01',
            'title' => 'New Job Position',
            'track_type' => 'ACADEMIC',
            'department' => 'CS',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 2000,
            'is_active' => true,
        ]);

        $user1 = User::factory()->create(['email' => 'old_candidate_app@test.com']);
        $user1->assignRole('applicant');
        $app1 = Application::create([
            'user_id' => $user1->id,
            'campaign_id' => $job1->id,
            'status' => 'SUBMITTED',
        ]);

        $user2 = User::factory()->create(['email' => 'new_candidate_app@test.com']);
        $user2->assignRole('applicant');
        $app2 = Application::create([
            'user_id' => $user2->id,
            'campaign_id' => $job2->id,
            'status' => 'SUBMITTED',
        ]);

        // Default mounts to ad2 (the last one)
        Livewire::test(ApplicationManagement::class)
            ->assertSet('advertisementFilter', (string)$ad2->id)
            ->assertSee('New Job Position')
            ->assertDontSee('Old Job Position')
            // Switch to ad1
            ->set('advertisementFilter', (string)$ad1->id)
            ->assertSee('Old Job Position')
            ->assertDontSee('New Job Position')
            // Switch to ALL
            ->set('advertisementFilter', 'ALL')
            ->assertSee('Old Job Position')
            ->assertSee('New Job Position');
    }
}
