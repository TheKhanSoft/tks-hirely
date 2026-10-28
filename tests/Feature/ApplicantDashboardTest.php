<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Livewire\Applicant\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ApplicantDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_dashboard_renders_for_new_user(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('applicant');

        $this->actingAs($user);

        Livewire::test(Dashboard::class)
            ->assertSee('Welcome to Hirely Recruitment Portal')
            ->assertSee('Launch Application Dossier Wizard');
    }

    public function test_applicant_dashboard_displays_executive_metrics_and_applications(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('applicant');

        $basicInfo = BasicInfo::create([
            'user_id' => $user->id,
            'first_name' => 'Sara',
            'last_name' => 'Ahmed',
            'father_name' => 'Ahmed Khan',
            'gender' => 'Female',
            'date_of_birth' => '1990-05-15',
            'cnic_number' => '37405-1234567-9',
            'phone_number' => '0300-1234567',
            'place_of_birth' => 'Islamabad',
            'nationality' => 'Pakistan',
            'religion' => 'Islam',
            'domicile' => 'Islamabad',
            'permanent_address' => 'House 123, Sector F-8, Islamabad',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'CS-PROF-101',
            'title' => 'Professor Computer Science',
            'track_type' => 'ACADEMIC',
            'department' => 'Computing',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(15),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $ledger = BillingLedger::create([
            'application_id' => $application->id,
            'consumer_id' => '100012345678',
            'amount' => 3000.00,
            'status' => 'PENDING',
        ]);

        $this->actingAs($user);

        Livewire::test(Dashboard::class)
            ->assertSee('Sara Ahmed')
            ->assertSee('37405-1234567-9')
            ->assertSee('ACADEMIC TRACK')
            ->assertSee('100012345678')
            ->assertSee('Pending Payment')
            ->assertSee('CS-PROF-101')
            ->assertSee('Professor Computer Science');
    }

    public function test_applicant_can_submit_appeal_for_ineligible_application(): void
    {
        Storage::fake('public');

        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('applicant');

        $basicInfo = BasicInfo::create([
            'user_id' => $user->id,
            'first_name' => 'Tariq',
            'last_name' => 'Mahmood',
            'father_name' => 'Mahmood Khan',
            'gender' => 'Male',
            'date_of_birth' => '1985-08-20',
            'cnic_number' => '37405-7654321-1',
            'phone_number' => '0300-7654321',
            'place_of_birth' => 'Lahore',
            'nationality' => 'Pakistan',
            'religion' => 'Islam',
            'domicile' => 'Punjab',
            'permanent_address' => 'House 45, Gulberg, Lahore',
            'track_type' => 'ADMINISTRATIVE',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'ADMIN-DIR-002',
            'title' => 'Director Administration',
            'track_type' => 'ADMINISTRATIVE',
            'department' => 'Registrar Office',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(15),
            'application_fee' => 2500.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'status' => 'INELIGIBLE',
            'disqualification_reason' => 'Missing attested experience equivalence.',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('equivalence_proof.pdf', 500, 'application/pdf');

        Livewire::test(Dashboard::class)
            ->call('openAppealForm', $application->id)
            ->assertSet('showAppealForm', true)
            ->set('appealText', 'I am submitting the duly attested HEC equivalence certificate along with employer experience certificate.')
            ->set('appealProof', $file)
            ->call('submitAppeal')
            ->assertSet('showAppealForm', false);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'APPEALED',
            'appeal_letter' => 'I am submitting the duly attested HEC equivalence certificate along with employer experience certificate.',
        ]);
    }

    public function test_applicant_can_apply_for_multiple_positions_with_unique_consumer_ids(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('applicant');

        $basicInfo = BasicInfo::create([
            'user_id' => $user->id,
            'first_name' => 'Usman',
            'last_name' => 'Ghani',
            'father_name' => 'Abdul Ghani',
            'gender' => 'Male',
            'date_of_birth' => '1992-03-10',
            'cnic_number' => '37405-9988776-5',
            'phone_number' => '0300-9988776',
            'place_of_birth' => 'Peshawar',
            'nationality' => 'Pakistan',
            'religion' => 'Islam',
            'domicile' => 'KPK',
            'permanent_address' => 'University Town, Peshawar',
            'track_type' => 'ACADEMIC',
            'is_locked' => false,
        ]);

        $camp1 = Campaign::create([
            'job_code' => 'CS-LEC-01',
            'title' => 'Lecturer Computer Science',
            'track_type' => 'ACADEMIC',
            'department' => 'CS',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1500.00,
            'is_active' => true,
        ]);

        $camp2 = Campaign::create([
            'job_code' => 'SE-AP-02',
            'title' => 'Assistant Professor Software Eng',
            'track_type' => 'ACADEMIC',
            'department' => 'SE',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(20),
            'application_fee' => 2000.00,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // 1. Apply for Position 1
        Livewire::test(Dashboard::class)
            ->call('openApplyModal')
            ->assertSet('showApplyModal', true)
            ->call('selectCampaignForModal', $camp1->id)
            ->assertSet('selectedCampaignIdForModal', $camp1->id)
            ->call('submitNewApplication')
            ->assertSet('showApplyModal', false);

        // 2. Apply for Position 2
        Livewire::test(Dashboard::class)
            ->call('openApplyModal')
            ->assertSet('showApplyModal', true)
            ->call('selectCampaignForModal', $camp2->id)
            ->assertSet('selectedCampaignIdForModal', $camp2->id)
            ->call('submitNewApplication')
            ->assertSet('showApplyModal', false);

        // Assert 2 applications exist
        $this->assertCount(2, $user->applications);

        $app1 = $user->applications()->where('campaign_id', $camp1->id)->first();
        $app2 = $user->applications()->where('campaign_id', $camp2->id)->first();

        $this->assertNotNull($app1);
        $this->assertNotNull($app2);

        // Assert unique consumer references
        $this->assertNotEmpty($app1->billingLedger->consumer_id);
        $this->assertNotEmpty($app2->billingLedger->consumer_id);
        $this->assertNotEquals($app1->billingLedger->consumer_id, $app2->billingLedger->consumer_id);

        // Assert basicInfo was locked
        $basicInfo->refresh();
        $this->assertTrue($basicInfo->is_locked);
    }

    public function test_applicant_cannot_reapply_for_same_position_duplicate_prevention(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('applicant');

        BasicInfo::create([
            'user_id' => $user->id,
            'first_name' => 'Ayesha',
            'last_name' => 'Malik',
            'father_name' => 'Malik Tariq',
            'gender' => 'Female',
            'date_of_birth' => '1994-06-25',
            'cnic_number' => '37405-5544332-1',
            'phone_number' => '0300-5544332',
            'place_of_birth' => 'Multan',
            'nationality' => 'Pakistan',
            'religion' => 'Islam',
            'domicile' => 'Punjab',
            'permanent_address' => 'Cantt, Multan',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'BIO-LEC-05',
            'title' => 'Lecturer Biotechnology',
            'track_type' => 'ACADEMIC',
            'department' => 'Bio Sciences',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1500.00,
            'is_active' => true,
        ]);

        // First application
        Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($user);

        // Attempting to select or submit for the same campaign in Dashboard modal fails
        Livewire::test(Dashboard::class)
            ->call('openApplyModal')
            ->call('selectCampaignForModal', $campaign->id)
            ->assertHasErrors(['selectedCampaignIdForModal']);

        $this->assertEquals(1, $user->applications()->where('campaign_id', $campaign->id)->count());
    }

    public function test_applicant_can_open_official_3_part_bank_slip_modal(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('applicant');

        BasicInfo::create([
            'user_id' => $user->id,
            'first_name' => 'Kamran',
            'last_name' => 'Akram',
            'father_name' => 'Akram Khan',
            'gender' => 'Male',
            'date_of_birth' => '1991-09-12',
            'cnic_number' => '37405-1122334-9',
            'phone_number' => '0300-1122334',
            'place_of_birth' => 'Rawalpindi',
            'nationality' => 'Pakistan',
            'religion' => 'Islam',
            'domicile' => 'Punjab',
            'permanent_address' => 'Saddar, Rawalpindi',
            'track_type' => 'SUPPORT',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'SUPP-TECH-01',
            'title' => 'Lab Technician',
            'track_type' => 'SUPPORT',
            'department' => 'Physics',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(15),
            'application_fee' => 1000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        BillingLedger::create([
            'application_id' => $application->id,
            'consumer_id' => '100077889900',
            'amount' => 1000.00,
            'status' => 'PENDING',
        ]);

        $this->actingAs($user);

        Livewire::test(Dashboard::class)
            ->call('openSlipModal', $application->id)
            ->assertSet('showSlipModal', true)
            ->assertSee('Official Bank Challan / Deposit Slip')
            ->assertSee('100077889900')
            ->assertSee('1. CANDIDATE COPY')
            ->assertSee('2. BANK BRANCH COPY')
            ->assertSee('3. UNIVERSITY TREASURY COPY')
            ->call('closeSlipModal')
            ->assertSet('showSlipModal', false);
    }
}
