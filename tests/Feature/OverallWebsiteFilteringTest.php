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

    public function test_application_management_filters_remain_intact_and_do_not_auto_clear()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad1 = Advertisement::create([
            'advertisement_no' => 'ADV-CYCLE-1',
            'title' => 'Cycle 1',
            'year' => 2025,
            'valid_from' => now()->subYear(),
            'valid_to' => now()->subMonths(10),
            'is_active' => true,
        ]);

        $ad2 = Advertisement::create([
            'advertisement_no' => 'ADV-CYCLE-2',
            'title' => 'Cycle 2',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        $camp1 = Campaign::create([
            'advertisement_id' => $ad1->id,
            'job_code' => 'JOB-CAMP-1',
            'title' => 'Camp 1',
            'department' => 'CS',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subYear(),
            'closing_date' => now()->subMonths(10),
            'application_fee' => 1000,
            'is_active' => true,
        ]);

        $camp2 = Campaign::create([
            'advertisement_id' => $ad2->id,
            'job_code' => 'JOB-CAMP-2',
            'title' => 'Camp 2',
            'department' => 'EE',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 1500,
            'is_active' => true,
        ]);

        $test = Livewire::test(ApplicationManagement::class)
            ->set('advertisementFilter', (string)$ad2->id)
            ->set('campaignFilter', (string)$camp2->id)
            ->set('statusFilter', 'PAID')
            ->set('voucherFilter', 'SETTLED')
            ->set('search', 'Dr Alice');

        // Verify all 5 filters are set
        $test->assertSet('advertisementFilter', (string)$ad2->id)
             ->assertSet('campaignFilter', (string)$camp2->id)
             ->assertSet('statusFilter', 'PAID')
             ->assertSet('voucherFilter', 'SETTLED')
             ->assertSet('search', 'Dr Alice');

        // Changing statusFilter must NOT clear search, campaignFilter, advertisementFilter, or voucherFilter
        $test->set('statusFilter', 'ELIGIBLE')
             ->assertSet('statusFilter', 'ELIGIBLE')
             ->assertSet('campaignFilter', (string)$camp2->id)
             ->assertSet('advertisementFilter', (string)$ad2->id)
             ->assertSet('voucherFilter', 'SETTLED')
             ->assertSet('search', 'Dr Alice');

        // Changing search must NOT clear filters
        $test->set('search', 'Dr Bob')
             ->assertSet('search', 'Dr Bob')
             ->assertSet('statusFilter', 'ELIGIBLE')
             ->assertSet('campaignFilter', (string)$camp2->id)
             ->assertSet('advertisementFilter', (string)$ad2->id);

        // Switching advertisement to ALL must NOT wipe campaignFilter
        $test->set('advertisementFilter', 'ALL')
             ->assertSet('advertisementFilter', 'ALL')
             ->assertSet('campaignFilter', (string)$camp2->id);

        // Selecting camp1 must auto-synchronize advertisementFilter to ad1
        $test->set('campaignFilter', (string)$camp1->id)
             ->assertSet('advertisementFilter', (string)$ad1->id);
    }

    public function test_application_management_kpi_statistics_scope_to_advertisement_and_campaign()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $ad1 = Advertisement::create([
            'advertisement_no' => 'ADV-METRICS-1',
            'title' => 'Metrics Cycle 1',
            'year' => 2025,
            'valid_from' => now()->subYear(),
            'valid_to' => now()->subMonths(10),
            'is_active' => true,
        ]);

        $ad2 = Advertisement::create([
            'advertisement_no' => 'ADV-METRICS-2',
            'title' => 'Metrics Cycle 2',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(25),
            'is_active' => true,
        ]);

        $camp1 = Campaign::create([
            'advertisement_id' => $ad1->id,
            'job_code' => 'CAMP-STAT-1',
            'title' => 'Math Lecturer',
            'department' => 'Math',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subYear(),
            'closing_date' => now()->subMonths(10),
            'application_fee' => 1000,
            'is_active' => true,
        ]);

        $camp2 = Campaign::create([
            'advertisement_id' => $ad2->id,
            'job_code' => 'CAMP-STAT-2',
            'title' => 'CS Professor',
            'department' => 'CS',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 2000,
            'is_active' => true,
        ]);

        // Create 2 applications in ad1 (1 paid, 1 submitted)
        $u1 = User::factory()->create();
        $u1->assignRole('applicant');
        $app1 = Application::create(['user_id' => $u1->id, 'campaign_id' => $camp1->id, 'status' => 'ELIGIBLE']);
        BillingLedger::create(['application_id' => $app1->id, 'consumer_id' => '2000000001', 'amount' => 1000, 'status' => 'SETTLED']);

        $u2 = User::factory()->create();
        $u2->assignRole('applicant');
        Application::create(['user_id' => $u2->id, 'campaign_id' => $camp1->id, 'status' => 'SUBMITTED']);

        // Create 3 applications in ad2 (3 paid, 2 eligible, 1 approved)
        $u3 = User::factory()->create();
        $u3->assignRole('applicant');
        $app3 = Application::create(['user_id' => $u3->id, 'campaign_id' => $camp2->id, 'status' => 'ELIGIBLE']);
        BillingLedger::create(['application_id' => $app3->id, 'consumer_id' => '2000000003', 'amount' => 2000, 'status' => 'SETTLED']);

        $u4 = User::factory()->create();
        $u4->assignRole('applicant');
        $app4 = Application::create(['user_id' => $u4->id, 'campaign_id' => $camp2->id, 'status' => 'ELIGIBLE']);
        BillingLedger::create(['application_id' => $app4->id, 'consumer_id' => '2000000004', 'amount' => 2000, 'status' => 'SETTLED']);

        $u5 = User::factory()->create();
        $u5->assignRole('applicant');
        $app5 = Application::create(['user_id' => $u5->id, 'campaign_id' => $camp2->id, 'status' => 'APPROVED']);
        BillingLedger::create(['application_id' => $app5->id, 'consumer_id' => '2000000005', 'amount' => 2000, 'status' => 'SETTLED']);

        // Mount defaults to ad2 (latest ad). Verify stats are scoped to ad2!
        $component = Livewire::test(ApplicationManagement::class)
            ->assertSet('advertisementFilter', (string)$ad2->id);

        $statsAd2 = $component->viewData('stats');
        $this->assertEquals(3, $statsAd2['total'], 'Ad2 should have 3 applications');
        $this->assertEquals(3, $statsAd2['paid'], 'Ad2 should have 3 paid applications');
        $this->assertEquals(2, $statsAd2['eligible'], 'Ad2 should have 2 eligible');
        $this->assertEquals(1, $statsAd2['approved'], 'Ad2 should have 1 approved');
        $this->assertEquals(5, $statsAd2['overall_system_total'], 'System wide total should be 5');

        // Switch to Ad1
        $component->set('advertisementFilter', (string)$ad1->id);
        $statsAd1 = $component->viewData('stats');
        $this->assertEquals(2, $statsAd1['total'], 'Ad1 should have 2 applications');
        $this->assertEquals(1, $statsAd1['paid'], 'Ad1 should have 1 paid');
        $this->assertEquals(1, $statsAd1['eligible'], 'Ad1 should have 1 eligible');

        // Switch to ALL
        $component->set('advertisementFilter', 'ALL');
        $statsAll = $component->viewData('stats');
        $this->assertEquals(5, $statsAll['total']);
        $this->assertEquals(4, $statsAll['paid']);

        // Interactive toggleStatusFilter
        $component->call('toggleStatusFilter', 'PAID')
            ->assertSet('statusFilter', 'PAID');
        // Clicking same status again toggles off
        $component->call('toggleStatusFilter', 'PAID')
            ->assertSet('statusFilter', '');
    }
}
