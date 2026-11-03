<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Advertisement;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BillingLedger;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\ApplicationManagement;
use App\Livewire\Admin\AdvertisementManagement;
use App\Livewire\Admin\CampaignCrud;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\PaymentManagement;
use App\Livewire\Scrutiny\Dashboard as ScrutinyDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class OverallWebsiteFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_user_management_filtering_by_search_role_and_status()
    {
        $admin = User::factory()->create(['email' => 'admin_test@university.edu']);
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $applicant = User::factory()->create(['email' => 'candidate_unique@gmail.com', 'is_active' => true]);
        $applicant->assignRole('applicant');

        $registrar = User::factory()->create(['email' => 'registrar_personnel@university.edu', 'is_active' => false]);
        $registrar->assignRole('registrar');

        // Test search filter
        Livewire::test(UserManagement::class)
            ->set('search', 'candidate_unique')
            ->assertSee('candidate_unique@gmail.com')
            ->assertDontSee('registrar_personnel@university.edu');

        // Test role filter
        Livewire::test(UserManagement::class)
            ->set('roleFilter', 'registrar')
            ->assertSee('registrar_personnel@university.edu')
            ->assertDontSee('candidate_unique@gmail.com');

        // Test status filter
        Livewire::test(UserManagement::class)
            ->set('statusFilter', '0')
            ->assertSee('registrar_personnel@university.edu')
            ->assertDontSee('candidate_unique@gmail.com');
    }

    public function test_advertisement_management_filtering_by_search_year_and_status()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad2024 = Advertisement::create([
            'advertisement_no' => 'ADV-2024/SPEC',
            'title' => 'Special Faculty Search 2024',
            'year' => 2024,
            'valid_from' => now()->subYears(2),
            'valid_to' => now()->subYear(),
            'is_active' => false,
        ]);

        $ad2026 = Advertisement::create([
            'advertisement_no' => 'ADV-2026/REG',
            'title' => 'Regular Faculty Search 2026',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        // Test search filter
        Livewire::test(AdvertisementManagement::class)
            ->set('search', 'Special Faculty')
            ->assertSee('ADV-2024/SPEC')
            ->assertDontSee('ADV-2026/REG');

        // Test year filter
        Livewire::test(AdvertisementManagement::class)
            ->set('yearFilter', '2024')
            ->assertSee('ADV-2024/SPEC')
            ->assertDontSee('ADV-2026/REG');

        // Test status filter
        Livewire::test(AdvertisementManagement::class)
            ->set('statusFilter', 'ACTIVE')
            ->assertSee('ADV-2026/REG')
            ->assertDontSee('ADV-2024/SPEC');
    }

    public function test_campaign_crud_filtering_by_search_and_track()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $campAcademic = Campaign::create([
            'job_code' => 'MATH-ACAD-01',
            'title' => 'Associate Professor Pure Math',
            'department' => 'Mathematics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1500,
            'is_active' => true,
        ]);

        $campAdmin = Campaign::create([
            'job_code' => 'ACC-ADMIN-01',
            'title' => 'Deputy Treasurer Finance',
            'department' => 'Finance',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1500,
            'is_active' => true,
        ]);

        // Test search filter
        Livewire::test(CampaignCrud::class)
            ->set('search', 'Pure Math')
            ->assertSee('MATH-ACAD-01')
            ->assertDontSee('ACC-ADMIN-01');

        // Test track filter
        Livewire::test(CampaignCrud::class)
            ->set('trackFilter', 'ADMINISTRATIVE')
            ->assertSee('ACC-ADMIN-01')
            ->assertDontSee('MATH-ACAD-01');
    }

    public function test_payment_management_filtering_by_status()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $camp = Campaign::create([
            'job_code' => 'TEST-01',
            'title' => 'Test Position',
            'department' => 'CS',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1000,
            'is_active' => true,
        ]);

        $u1 = User::factory()->create();
        $u1->assignRole('applicant');
        $app1 = Application::create(['user_id' => $u1->id, 'campaign_id' => $camp->id, 'status' => 'PAID']);
        $p1 = BillingLedger::create([
            'application_id' => $app1->id,
            'consumer_id' => '1000000001',
            'amount' => 1000,
            'status' => 'SETTLED',
            'transaction_id' => 'TXN-99991',
        ]);

        $u2 = User::factory()->create();
        $u2->assignRole('applicant');
        $app2 = Application::create(['user_id' => $u2->id, 'campaign_id' => $camp->id, 'status' => 'SUBMITTED']);
        $p2 = BillingLedger::create([
            'application_id' => $app2->id,
            'consumer_id' => '1000000002',
            'amount' => 1000,
            'status' => 'PENDING',
        ]);

        Livewire::test(PaymentManagement::class)
            ->set('statusFilter', 'SETTLED')
            ->assertSee('TXN-99991')
            ->assertDontSee('1000000002');

        Livewire::test(PaymentManagement::class)
            ->set('statusFilter', 'PENDING')
            ->assertSee('1000000002')
            ->assertDontSee('TXN-99991');
    }
}
