<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use App\Livewire\Admin\CampaignCrud;
use App\Livewire\Admin\UserManagement;
use App\Services\PermissionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GranularPermissionsAndDirectUserAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        PermissionRegistry::syncAll();
    }

    public function test_super_admin_bypasses_all_granular_permission_gates()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin);

        // Super admin passes any can check
        $this->assertTrue($superAdmin->can('create-campaign'));
        $this->assertTrue($superAdmin->can('delete-user'));
        $this->assertTrue($superAdmin->can('reconcile-payment'));
        $this->assertTrue($superAdmin->can('tune-constitution'));

        // Can mount and open create form on CampaignCrud without 403
        Livewire::test(CampaignCrud::class)
            ->call('openCreateForm')
            ->assertSet('isFormOpen', true);
    }

    public function test_user_without_permission_is_strictly_blocked_with_403()
    {
        $role = Role::firstOrCreate(['name' => 'department-chair', 'guard_name' => 'web']);
        $role->syncPermissions(['view-campaign']);

        $user = User::factory()->create();
        $user->assignRole('department-chair');

        $this->actingAs($user);

        $this->assertTrue($user->can('view-campaign'));
        $this->assertFalse($user->can('create-campaign'));

        // Mounting CampaignCrud succeeds because view-campaign is granted
        $component = Livewire::test(CampaignCrud::class);
        $component->assertSeeHtml('Active Job Campaigns');
        $component->assertDontSeeHtml('Create Campaign');

        // Calling openCreateForm directly returns 403 Forbidden
        $component->call('openCreateForm')
            ->assertForbidden();
    }

    public function test_direct_extra_permissions_granted_to_specific_user_overrides_role_restrictions()
    {
        $role = Role::firstOrCreate(['name' => 'faculty-lead', 'guard_name' => 'web']);
        $role->syncPermissions(['view-campaign']);

        // User A and User B share the exact same role
        $userA = User::factory()->create();
        $userA->assignRole('faculty-lead');

        $userB = User::factory()->create();
        $userB->assignRole('faculty-lead');

        // Give User A direct permission to create campaigns without changing their role
        $userA->givePermissionTo('create-campaign');

        // User A can now create campaigns
        $this->actingAs($userA);
        $this->assertTrue($userA->can('create-campaign'));

        Livewire::test(CampaignCrud::class)
            ->assertSeeHtml('Create Campaign')
            ->call('openCreateForm')
            ->assertSet('isFormOpen', true);

        // User B is still strictly blocked
        $this->actingAs($userB);
        $this->assertFalse($userB->can('create-campaign'));

        Livewire::test(CampaignCrud::class)
            ->assertDontSeeHtml('Create Campaign');
    }

    public function test_direct_permissions_modal_in_user_management_syncs_extra_permissions()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $targetUser = User::factory()->create();
        $targetUser->assignRole('applicant');

        $this->actingAs($admin);

        // Open modal in UserManagement Livewire component
        $component = Livewire::test(UserManagement::class)
            ->call('openDirectPermissionsModal', $targetUser->id)
            ->assertSet('isDirectPermissionsModalOpen', true)
            ->assertSet('targetUserIdForPermissions', $targetUser->id);

        // Toggle 'tune-constitution' and save
        $component->call('toggleDirectPermission', 'tune-constitution')
            ->call('saveDirectPermissions')
            ->assertSet('isDirectPermissionsModalOpen', false);

        // Verify targetUser received the extra direct permission
        $targetUser->refresh();
        $this->assertTrue($targetUser->hasDirectPermission('tune-constitution'));
        $this->assertTrue($targetUser->can('tune-constitution'));
    }
}
