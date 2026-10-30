<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Advertisement;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Appeal;
use App\Models\Position;
use App\Models\Department;
use App\Models\WorkflowState;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use App\Livewire\Admin\AdvertisementManagement;
use App\Livewire\Admin\AppellateReview;
use App\Livewire\Admin\ApplicationVerification;
use App\Livewire\Admin\ConstitutionCriteriaManagement;
use App\Livewire\Admin\SystemSettings;
use App\Livewire\Board\LiveGradingPanel;
use App\Livewire\Applicant\Dashboard as ApplicantDashboard;
use App\Services\PermissionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActionPermissionsCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        PermissionRegistry::syncAll();
    }

    public function test_advertisement_create_action_requires_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view-advertisement');

        $this->actingAs($user);

        // Calling openCreateModal without create-advertisement is forbidden
        Livewire::test(AdvertisementManagement::class)
            ->call('openCreateModal')
            ->assertForbidden();

        // Calling createAdvertisement without create-advertisement is forbidden
        Livewire::test(AdvertisementManagement::class)
            ->call('createAdvertisement')
            ->assertForbidden();

        // Grant create-advertisement permission and verify access is allowed
        $user->givePermissionTo('create-advertisement');

        Livewire::test(AdvertisementManagement::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true);
    }

    public function test_advertisement_edit_and_toggle_status_requires_permission()
    {
        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-TEST-2026-01',
            'title' => 'Test Advertisement 2026',
            'valid_from' => now(),
            'valid_to' => now()->addDays(30),
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo('view-advertisement');

        $this->actingAs($user);

        // Forbidden without edit-advertisement
        Livewire::test(AdvertisementManagement::class)
            ->call('openEditModal', $ad->id)
            ->assertForbidden();

        Livewire::test(AdvertisementManagement::class)
            ->call('toggleAdvertisementStatus', $ad->id)
            ->assertForbidden();

        // Grant edit-advertisement
        $user->givePermissionTo('edit-advertisement');

        Livewire::test(AdvertisementManagement::class)
            ->call('openEditModal', $ad->id)
            ->assertSet('showEditModal', true);
    }

    public function test_advertisement_extend_validity_requires_permission()
    {
        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-TEST-2026-02',
            'title' => 'Test Extend Ad',
            'valid_from' => now(),
            'valid_to' => now()->addDays(30),
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo('view-advertisement');

        $this->actingAs($user);

        Livewire::test(AdvertisementManagement::class)
            ->call('openExtendModal', $ad->id)
            ->assertForbidden();

        // Grant extend-advertisement
        $user->givePermissionTo('extend-advertisement');

        Livewire::test(AdvertisementManagement::class)
            ->call('openExtendModal', $ad->id)
            ->assertSet('showExtendModal', true);
    }

    public function test_appellate_review_actions_require_process_appeal_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view-appeal');

        $this->actingAs($user);

        // Mounting AppellateReview succeeds with view-appeal
        Livewire::test(AppellateReview::class)
            ->assertOk();

        // Calling approveAppealToPaid is strictly forbidden without process-appeal
        Livewire::test(AppellateReview::class)
            ->call('approveAppealToPaid', 999)
            ->assertForbidden();

        // Calling rejectAppeal is strictly forbidden without process-appeal
        Livewire::test(AppellateReview::class)
            ->call('rejectAppeal', 999)
            ->assertForbidden();

        // Grant process-appeal permission
        $user->givePermissionTo('process-appeal');

        $this->assertTrue($user->can('process-appeal'));
    }

    public function test_application_verification_scrutiny_actions_require_permission()
    {
        $applicant = User::factory()->create();
        $campaign = Campaign::create([
            'job_code' => 'TEST-SCRUT-01',
            'title' => 'Associate Professor',
            'track_type' => 'ACADEMIC',
            'department' => 'CS',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1000,
            'is_active' => true,
        ]);

        $app = Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-SCRUT-TEST-001',
            'status' => 'SUBMITTED',
            'submitted_at' => now(),
        ]);

        $user = User::factory()->create();
        $user->givePermissionTo('view-application');

        $this->actingAs($user);

        // Unauthorized scrutiny member cannot mark eligible
        Livewire::test(ApplicationVerification::class, ['id' => $app->id])
            ->call('markEligible', $app->id)
            ->assertForbidden();

        // Unauthorized scrutiny member cannot mark ineligible
        Livewire::test(ApplicationVerification::class, ['id' => $app->id])
            ->call('markIneligible', $app->id)
            ->assertForbidden();

        // Unauthorized scrutiny member cannot autofill disqualification
        Livewire::test(ApplicationVerification::class, ['id' => $app->id])
            ->call('autoFillAcademicDisqualification')
            ->assertForbidden();

        // Grant scrutinize-application permission
        $user->givePermissionTo('scrutinize-application');

        Livewire::test(ApplicationVerification::class, ['id' => $app->id])
            ->assertOk();
    }

    public function test_system_settings_actions_require_manage_settings_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view-campaign');

        $this->actingAs($user);

        // Mount is forbidden without manage-settings
        Livewire::test(SystemSettings::class)
            ->assertForbidden();

        // Grant manage-settings
        $user->givePermissionTo('manage-settings');

        Livewire::test(SystemSettings::class)
            ->assertOk()
            ->call('toggleSetting', 'enable_recaptcha')
            ->assertOk();
    }

    public function test_constitution_criteria_management_actions_require_permission()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view-campaign');

        $this->actingAs($user);

        // Mounting ConstitutionCriteriaManagement is forbidden without tune-constitution or manage-constitutions
        Livewire::test(ConstitutionCriteriaManagement::class)
            ->assertForbidden();

        // Grant tune-constitution
        $user->givePermissionTo('tune-constitution');

        Livewire::test(ConstitutionCriteriaManagement::class)
            ->assertOk()
            ->call('openCreateBoardModal')
            ->assertSet('showBoardModal', true);
    }

    public function test_board_live_grading_panel_requires_permission()
    {
        $applicant = User::factory()->create();
        $campaign = Campaign::create([
            'job_code' => 'TEST-BOARD-01',
            'title' => 'Professor Computer Science',
            'track_type' => 'ACADEMIC',
            'department' => 'CS',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 1000,
            'is_active' => true,
        ]);

        $app = Application::create([
            'user_id' => $applicant->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-BOARD-TEST-001',
            'status' => 'SHORTLISTED',
            'submitted_at' => now(),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user);

        // Unauthorized user cannot mount LiveGradingPanel
        Livewire::test(LiveGradingPanel::class, ['id' => $app->id])
            ->assertForbidden();

        // User with access-board-grading can mount
        $user->givePermissionTo('access-board-grading');

        Livewire::test(LiveGradingPanel::class, ['id' => $app->id])
            ->assertOk();
    }
}
