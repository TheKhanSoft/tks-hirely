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
}
