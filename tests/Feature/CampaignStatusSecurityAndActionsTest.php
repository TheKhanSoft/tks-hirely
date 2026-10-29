<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\Application;
use App\Livewire\Admin\CampaignCrud;
use App\Livewire\Admin\AdvertisementManagement;
use App\Livewire\Admin\ApplicationManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CampaignStatusSecurityAndActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_campaign_status_badge_and_state_attributes(): void
    {
        // 1. Active campaign within closing date
        $activeCamp = Campaign::create([
            'job_code' => 'ACTIVE-01',
            'title' => 'Assistant Professor Physics',
            'department' => 'Physics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $this->assertFalse($activeCamp->is_closed);
        $this->assertFalse($activeCamp->is_completed);
        $this->assertEquals('ACTIVE POSITION', $activeCamp->status_badge['label']);

        // 2. Closed campaign (deadline passed)
        $closedCamp = Campaign::create([
            'job_code' => 'CLOSED-01',
            'title' => 'Lecturer Mathematics',
            'department' => 'Mathematics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(40),
            'closing_date' => now()->subDays(10),
            'application_fee' => 2000,
            'is_active' => true,
        ]);

        $this->assertTrue($closedCamp->is_closed);
        $this->assertStringContainsString('CLOSED', $closedCamp->status_badge['label']);

        // 3. Suspended campaign
        $suspendedCamp = Campaign::create([
            'job_code' => 'SUSP-01',
            'title' => 'Lab Assistant Chemistry',
            'department' => 'Chemistry',
            'track_type' => 'STAFF',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(15),
            'application_fee' => 1000,
            'is_active' => false,
        ]);

        $this->assertFalse($suspendedCamp->is_closed);
        $this->assertStringContainsString('SUSPENDED', $suspendedCamp->status_badge['label']);
    }

    public function test_non_super_admin_cannot_edit_closed_or_completed_campaign(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('registrar');

        $closedCamp = Campaign::create([
            'job_code' => 'CLOSED-02',
            'title' => 'Associate Professor CS',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(60),
            'closing_date' => now()->subDays(15),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        // Regular admin attempting to edit closed campaign should see security warning and form remains closed
        Livewire::test(CampaignCrud::class)
            ->call('openEditForm', $closedCamp->id)
            ->assertSet('isFormOpen', false)
            ->assertSee('Security Policy');

        // Regular admin attempting to delete closed campaign should see security warning and record remains
        Livewire::test(CampaignCrud::class)
            ->call('deleteCampaign', $closedCamp->id)
            ->assertSee('Security Policy');

        $this->assertDatabaseHas('campaigns', ['id' => $closedCamp->id]);
    }

    public function test_super_admin_can_override_and_edit_closed_campaign(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $closedCamp = Campaign::create([
            'job_code' => 'CLOSED-03',
            'title' => 'Professor Biology',
            'department' => 'Biology',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(60),
            'closing_date' => now()->subDays(15),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin);

        // Super admin can open edit form
        Livewire::test(CampaignCrud::class)
            ->call('openEditForm', $closedCamp->id)
            ->assertSet('isFormOpen', true)
            ->assertSet('isEditMode', true)
            ->assertSet('title', 'Professor Biology');

        // Super admin can delete closed campaign
        Livewire::test(CampaignCrud::class)
            ->call('deleteCampaign', $closedCamp->id);

        $this->assertDatabaseMissing('campaigns', ['id' => $closedCamp->id]);
    }

    public function test_toggle_campaign_status_security(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('registrar');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $camp = Campaign::create([
            'job_code' => 'TOGGLE-01',
            'title' => 'Software Engineer',
            'department' => 'IT Services',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 2000,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        // Regular admin can suspend active position
        Livewire::test(CampaignCrud::class)
            ->call('toggleCampaignStatus', $camp->id);

        $this->assertFalse($camp->fresh()->is_active);

        // Regular admin can reactivate since deadline is not expired
        Livewire::test(CampaignCrud::class)
            ->call('toggleCampaignStatus', $camp->id);

        $this->assertTrue($camp->fresh()->is_active);

        // Now set closing_date to the past (expired)
        $camp->update(['closing_date' => now()->subDays(5), 'is_active' => false]);

        // Regular admin cannot reactivate expired campaign
        Livewire::test(CampaignCrud::class)
            ->call('toggleCampaignStatus', $camp->id)
            ->assertSee('Security Policy');

        $this->assertFalse($camp->fresh()->is_active);

        // Super admin CAN reactivate expired campaign
        $this->actingAs($superAdmin);
        Livewire::test(CampaignCrud::class)
            ->call('toggleCampaignStatus', $camp->id);

        $this->assertTrue($camp->fresh()->is_active);
    }

    public function test_expired_advertisement_security(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('registrar');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $expiredAd = Advertisement::create([
            'advertisement_no' => 'ADV-EXP-2025/99',
            'title' => 'Expired Cycle 2025',
            'year' => 2025,
            'valid_from' => now()->subDays(90),
            'valid_to' => now()->subDays(30),
            'is_active' => false,
        ]);

        $this->actingAs($admin);

        // Regular admin cannot edit expired advertisement
        Livewire::test(AdvertisementManagement::class)
            ->call('openEditModal', $expiredAd->id)
            ->assertSet('showEditModal', false)
            ->assertSee('Security Restriction');

        // Regular admin cannot reactivate expired advertisement without corrigendum
        Livewire::test(AdvertisementManagement::class)
            ->call('toggleAdvertisementStatus', $expiredAd->id);

        $this->assertFalse($expiredAd->fresh()->is_active);

        // Super admin CAN edit expired advertisement
        $this->actingAs($superAdmin);
        Livewire::test(AdvertisementManagement::class)
            ->call('openEditModal', $expiredAd->id)
            ->assertSet('showEditModal', true);
    }

    public function test_application_security_on_closed_campaign(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('registrar');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');

        $closedCamp = Campaign::create([
            'job_code' => 'APP-SEC-01',
            'title' => 'Registrar Executive',
            'department' => 'Administration',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(50),
            'closing_date' => now()->subDays(10),
            'application_fee' => 2500,
            'is_active' => true,
        ]);

        $app = Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $closedCamp->id,
            'tracking_number' => 'APP-TEST-999',
            'status' => 'SUBMITTED',
        ]);

        $this->actingAs($admin);

        // Regular admin cannot edit application on closed campaign
        Livewire::test(ApplicationManagement::class)
            ->call('openEditApplicationForm', $app->id)
            ->assertSet('isFormOpen', false)
            ->assertSee('closed or completed');

        // Regular admin cannot delete application on closed campaign
        Livewire::test(ApplicationManagement::class)
            ->call('deleteApplication', $app->id)
            ->assertSee('Security Policy');

        $this->assertDatabaseHas('applications', ['id' => $app->id]);

        // Super admin CAN edit application
        $this->actingAs($superAdmin);
        Livewire::test(ApplicationManagement::class)
            ->call('openEditApplicationForm', $app->id)
            ->assertSet('isFormOpen', true);

        // Super admin CAN delete application
        Livewire::test(ApplicationManagement::class)
            ->call('deleteApplication', $app->id);

        $this->assertDatabaseMissing('applications', ['id' => $app->id]);
    }
}
