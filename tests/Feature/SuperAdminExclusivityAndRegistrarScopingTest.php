<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Services\PermissionRegistry;
use Livewire\Livewire;
use App\Livewire\Admin\RolePermissionManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\SystemSettings;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminExclusivityAndRegistrarScopingTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $registrar;
    protected User $applicant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $this->superAdmin = User::factory()->create(['email' => 'superadmin.sec@university.edu']);
        $this->superAdmin->assignRole('super-admin');

        $this->registrar = User::factory()->create(['email' => 'registrar.sec@university.edu']);
        $this->registrar->assignRole('registrar');

        $this->applicant = User::factory()->create(['email' => 'applicant.candidate@domain.com']);
        $this->applicant->assignRole('applicant');
    }

    public function test_super_admin_exclusive_permissions_are_defined(): void
    {
        $exclusive = PermissionRegistry::superAdminExclusivePermissions();

        $this->assertContains('impersonate-user', $exclusive);
        $this->assertContains('delete-audit-log', $exclusive);
        $this->assertContains('purge-audit-logs', $exclusive);
        $this->assertContains('view-permission', $exclusive);
        $this->assertContains('create-permission', $exclusive);
        $this->assertContains('edit-permission', $exclusive);
        $this->assertContains('delete-permission', $exclusive);
        $this->assertContains('manage-system-maintenance', $exclusive);
        $this->assertContains('view-system-health', $exclusive);
        $this->assertContains('manage-merit-formula', $exclusive);
    }

    public function test_registrar_role_does_not_contain_super_admin_exclusive_permissions(): void
    {
        $registrarRole = Role::findByName('registrar', 'web');
        $exclusive = PermissionRegistry::superAdminExclusivePermissions();

        foreach ($exclusive as $perm) {
            $this->assertFalse(
                $registrarRole->hasPermissionTo($perm),
                "Registrar should not have super-admin-exclusive permission: {$perm}"
            );
        }
    }

    public function test_permission_registry_catalogue_for_user_filters_exclusive_permissions_for_registrar(): void
    {
        $adminCatalogue = PermissionRegistry::catalogueForUser($this->superAdmin);
        $registrarCatalogue = PermissionRegistry::catalogueForUser($this->registrar);

        // Super Admin sees all groups including permissions and forensics
        $adminPerms = [];
        foreach ($adminCatalogue as $group => $perms) {
            foreach (array_keys($perms) as $slug) {
                $adminPerms[] = $slug;
            }
        }
        $this->assertContains('impersonate-user', $adminPerms);
        $this->assertContains('purge-audit-logs', $adminPerms);
        $this->assertContains('view-permission', $adminPerms);

        // Registrar must NOT see exclusive permissions
        $registrarPerms = [];
        foreach ($registrarCatalogue as $group => $perms) {
            foreach (array_keys($perms) as $slug) {
                $registrarPerms[] = $slug;
            }
        }
        $this->assertNotContains('impersonate-user', $registrarPerms);
        $this->assertNotContains('purge-audit-logs', $registrarPerms);
        $this->assertNotContains('delete-audit-log', $registrarPerms);
        $this->assertNotContains('view-permission', $registrarPerms);
        $this->assertNotContains('create-permission', $registrarPerms);
        $this->assertNotContains('edit-permission', $registrarPerms);
        $this->assertNotContains('delete-permission', $registrarPerms);
        $this->assertNotContains('manage-system-maintenance', $registrarPerms);
        $this->assertNotContains('view-system-health', $registrarPerms);
        $this->assertNotContains('manage-merit-formula', $registrarPerms);
    }

    public function test_registrar_cannot_access_permissions_tab_in_role_permission_management(): void
    {
        $this->actingAs($this->registrar);

        // When registrar mounts component with tab=permissions, it forces tab to roles
        $component = Livewire::test(RolePermissionManagement::class, ['tab' => 'permissions']);
        $this->assertEquals('roles', $component->get('activeSubTab'));

        // Calling setSubTab('permissions') as registrar fails with 403
        $component->call('setSubTab', 'permissions')
            ->assertStatus(403);
    }

    public function test_super_admin_can_access_permissions_tab(): void
    {
        $this->actingAs($this->superAdmin);

        $component = Livewire::test(RolePermissionManagement::class, ['tab' => 'permissions']);
        $this->assertEquals('permissions', $component->get('activeSubTab'));
        $component->assertSee('Permissions Catalog');
    }

    public function test_registrar_cannot_sneak_super_admin_permissions_into_roles(): void
    {
        $this->actingAs($this->registrar);

        Livewire::test(RolePermissionManagement::class)
            ->set('role_name_input', 'departmental-evaluator')
            ->set('selected_permissions', [
                'view-application',
                'impersonate-user',      // Super admin exclusive
                'purge-audit-logs',      // Super admin exclusive
                'manage-system-maintenance', // Super admin exclusive
            ])
            ->call('saveRole');

        $createdRole = Role::findByName('departmental-evaluator', 'web');
        $this->assertTrue($createdRole->hasPermissionTo('view-application'));
        $this->assertFalse($createdRole->hasPermissionTo('impersonate-user'));
        $this->assertFalse($createdRole->hasPermissionTo('purge-audit-logs'));
        $this->assertFalse($createdRole->hasPermissionTo('manage-system-maintenance'));
    }

    public function test_registrar_cannot_grant_super_admin_exclusive_direct_permissions_to_user(): void
    {
        $this->actingAs($this->registrar);

        $targetUser = User::factory()->create();
        $targetUser->assignRole('applicant');

        $component = Livewire::test(UserManagement::class)
            ->call('openDirectPermissionsModal', $targetUser->id);

        // Attempting to toggle an exclusive permission directly should be rejected
        $component->call('toggleDirectPermission', 'impersonate-user');
        $directPerms = $component->get('targetUserDirectPermissions');
        $this->assertNotContains('impersonate-user', $directPerms);

        // Even if injected into array, saving strips it out
        $component->set('targetUserDirectPermissions', ['submit-appeal', 'purge-audit-logs'])
            ->call('saveDirectPermissions');

        $targetUser->refresh();
        $this->assertTrue($targetUser->hasDirectPermission('submit-appeal'));
        $this->assertFalse($targetUser->hasDirectPermission('purge-audit-logs'));
    }

    public function test_registrar_cannot_start_impersonation(): void
    {
        $this->actingAs($this->registrar);

        $response = $this->get(route('impersonate.start', $this->applicant->id));
        $response->assertStatus(403);
        $this->assertFalse(session()->has('impersonated_by'));
    }

    public function test_impersonate_button_is_not_visible_to_registrar_in_user_management(): void
    {
        $this->actingAs($this->registrar);

        $response = $this->get('/registrar/users');
        $response->assertStatus(200);
        $response->assertDontSee('action-btn-impersonate');
        $response->assertDontSee('Impersonate User');
    }

    public function test_registrar_is_redirected_away_from_system_settings_tab(): void
    {
        $this->actingAs($this->registrar);

        // Mount settings component with system tab
        $component = Livewire::test(SystemSettings::class, ['tab' => 'system']);
        $this->assertEquals('pipeline', $component->get('activeTab'));

        // Calling setTab('system') as registrar forces back to pipeline
        $component->call('setTab', 'system');
        $this->assertEquals('pipeline', $component->get('activeTab'));
    }

    public function test_super_admin_can_access_system_settings_tab(): void
    {
        $this->actingAs($this->superAdmin);

        $component = Livewire::test(SystemSettings::class, ['tab' => 'system']);
        $this->assertEquals('system', $component->get('activeTab'));
    }
}
