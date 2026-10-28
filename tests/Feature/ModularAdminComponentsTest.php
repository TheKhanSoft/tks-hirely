<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Models\SecurityAuditLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Livewire\Admin\ApplicationManagement;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\PaymentManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\RolePermissionManagement;
use App\Livewire\Admin\AuditLogManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModularAdminComponentsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $applicant;
    protected $campaign;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('registrar');

        $this->applicant = User::factory()->create(['email' => 'applicant@test.edu']);
        $this->applicant->assignRole('applicant');

        $this->campaign = Campaign::create([
            'job_code' => 'PROF-CS-101',
            'title' => 'Professor of Computer Science',
            'track_type' => 'ACADEMIC',
            'department' => 'Computer Science',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 1500.00,
            'is_active' => true,
        ]);
    }

    public function test_application_management_crud_and_status_update(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Application via Livewire
        Livewire::test(ApplicationManagement::class)
            ->call('openCreateApplicationForm')
            ->set('app_user_id', $this->applicant->id)
            ->set('app_campaign_id', $this->campaign->id)
            ->set('app_status', 'SUBMITTED')
            ->call('saveApplication')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->campaign->id,
            'status' => 'SUBMITTED',
        ]);

        $app = Application::where('user_id', $this->applicant->id)->first();

        // 2. Edit Application Status
        Livewire::test(ApplicationManagement::class)
            ->call('openEditApplicationForm', $app->id)
            ->set('app_status', 'ELIGIBLE')
            ->call('saveApplication')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status' => 'ELIGIBLE',
        ]);

        // 3. View Details Modal
        Livewire::test(ApplicationManagement::class)
            ->call('viewApplicationDetails', $app->id)
            ->assertSet('isDetailsOpen', true)
            ->assertSet('selectedRecordId', $app->id);

        // 4. Delete Application
        Livewire::test(ApplicationManagement::class)
            ->call('deleteApplication', $app->id);

        $this->assertDatabaseMissing('applications', ['id' => $app->id]);
    }

    public function test_candidate_profile_management_crud_and_lock_toggle(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Profile
        Livewire::test(CandidateProfileManagement::class)
            ->call('openCreateBasicInfoForm')
            ->set('bi_user_id', $this->applicant->id)
            ->set('bi_first_name', 'Zain')
            ->set('bi_last_name', 'Ahmed')
            ->set('bi_cnic_number', '42101-1234567-9')
            ->set('bi_dob', '1992-05-15')
            ->call('saveBasicInfo')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('basic_infos', [
            'user_id' => $this->applicant->id,
            'first_name' => 'Zain',
            'cnic_number' => '42101-1234567-9',
            'is_locked' => false,
        ]);

        $profile = BasicInfo::where('user_id', $this->applicant->id)->first();

        // 2. Toggle Lock Status
        Livewire::test(CandidateProfileManagement::class)
            ->call('toggleLock', $profile->id);

        $this->assertDatabaseHas('basic_infos', [
            'id' => $profile->id,
            'is_locked' => true,
        ]);

        // 3. View Full Dossier Modal
        Livewire::test(CandidateProfileManagement::class)
            ->call('viewBasicInfoDetails', $profile->id)
            ->assertSet('isDetailsOpen', true);
    }

    public function test_payment_management_reconciliation(): void
    {
        $this->actingAs($this->admin);

        $app = Application::create([
            'user_id' => $this->applicant->id,
            'campaign_id' => $this->campaign->id,
            'status' => 'SUBMITTED',
        ]);

        // 1. Log Payment
        Livewire::test(PaymentManagement::class)
            ->call('openCreatePaymentForm')
            ->set('pay_app_id', $app->id)
            ->set('pay_consumer_id', '999912345678')
            ->set('pay_amount', 1500.00)
            ->set('pay_status', 'PENDING')
            ->call('savePayment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('billing_ledger', [
            'application_id' => $app->id,
            'consumer_id' => '999912345678',
            'status' => 'PENDING',
        ]);

        $ledger = BillingLedger::where('application_id', $app->id)->first();

        // 2. Quick Reconcile Paid
        Livewire::test(PaymentManagement::class)
            ->call('markSettled', $ledger->id);

        $this->assertDatabaseHas('billing_ledger', [
            'id' => $ledger->id,
            'status' => 'SETTLED',
        ]);

        // Reconciled application should automatically be updated to PAID
        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status' => 'PAID',
        ]);
    }

    public function test_user_management_crud_and_status_toggle(): void
    {
        $this->actingAs($this->admin);

        // 1. Register User
        Livewire::test(UserManagement::class)
            ->call('openCreateUserForm')
            ->set('email', 'officer@university.edu')
            ->set('password', 'secret123')
            ->set('role_name', 'scrutiny-committee')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'officer@university.edu']);
        $user = User::where('email', 'officer@university.edu')->first();
        $this->assertTrue($user->hasRole('scrutiny-committee'));

        // 2. Toggle Status
        Livewire::test(UserManagement::class)
            ->call('toggleUserStatus', $user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);

        // 3. Safety Gate: cannot delete own account
        Livewire::test(UserManagement::class)
            ->call('deleteUser', $this->admin->id)
            ->assertSee('Safety gate');
    }

    public function test_role_permission_management_workflows(): void
    {
        $this->actingAs($this->admin);

        // 1. Create Role
        Livewire::test(RolePermissionManagement::class)
            ->call('openCreateRoleForm')
            ->set('role_name_input', 'dean-faculty')
            ->set('selected_permissions', ['view-campaign', 'view-application'])
            ->call('saveRole')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('roles', ['name' => 'dean-faculty']);
        $role = Role::where('name', 'dean-faculty')->first();
        $this->assertTrue($role->hasPermissionTo('view-campaign'));

        // 2. Create Permission
        Livewire::test(RolePermissionManagement::class)
            ->call('setSubTab', 'permissions')
            ->call('openCreatePermissionForm')
            ->set('permission_name_input', 'export-roster')
            ->call('savePermission')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('permissions', ['name' => 'export-roster']);
    }

    public function test_audit_log_management_and_purge(): void
    {
        $this->actingAs($this->admin);

        $log = SecurityAuditLog::create([
            'user_id' => $this->admin->id,
            'action_type' => 'UPDATED',
            'table_name' => 'campaigns',
            'record_id' => 1,
            'state_before' => ['title' => 'Old Title'],
            'state_after' => ['title' => 'New Title'],
            'ip_address' => '127.0.0.1',
            'logged_at' => now(),
        ]);

        // View Diff Modal
        Livewire::test(AuditLogManagement::class)
            ->call('viewLogDetails', $log->id)
            ->assertSet('isDetailsOpen', true);

        // Purge Logs
        Livewire::test(AuditLogManagement::class)
            ->call('clearAuditLogs');

        $this->assertDatabaseCount('security_audit_logs', 0);
    }

    public function test_unauthorized_applicant_blocked_from_admin_modules(): void
    {
        $this->actingAs($this->applicant);

        $this->get('/admin/applications')->assertStatus(403);
        $this->get('/admin/candidates')->assertStatus(403);
        $this->get('/admin/payments')->assertStatus(403);
        $this->get('/admin/users')->assertStatus(403);
        $this->get('/admin/roles')->assertStatus(403);
        $this->get('/admin/audit-logs')->assertStatus(403);
    }

    public function test_legacy_management_redirect_routes_properly(): void
    {
        $this->actingAs($this->admin);

        $this->get('/admin/management?tab=applications')->assertRedirect('/admin/applications');
        $this->get('/admin/management?tab=basic_infos')->assertRedirect('/admin/candidates');
        $this->get('/admin/management?tab=payments')->assertRedirect('/admin/payments');
        $this->get('/admin/management?tab=campaigns')->assertRedirect('/admin/campaigns');
        $this->get('/admin/management?tab=logs')->assertRedirect('/admin/audit-logs');
        $this->get('/admin/management')->assertRedirect('/admin/users');
    }
}
