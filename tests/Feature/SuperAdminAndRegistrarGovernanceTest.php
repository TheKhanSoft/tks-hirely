<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\ReviewerProfile;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\RolePermissionManagement;
use App\Livewire\Admin\CampaignCrud;
use App\Services\AcademicDisciplineMatcher;
use Livewire\Livewire;
use Illuminate\Support\Facades\Gate;

class SuperAdminAndRegistrarGovernanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    /**
     * Item 1: Topbar renders theme switcher & user avatar/role/email in topbar header.
     */
    public function test_item_1_topbar_renders_theme_mode_and_user_profile_detail(): void
    {
        $registrar = User::factory()->create(['email' => 'registrar.governance@university.edu']);
        $registrar->assignRole('registrar');
        BasicInfo::create([
            'user_id' => $registrar->id,
            'first_name' => 'Dr. Registrar',
            'last_name' => 'Governance',
            'father_name' => 'Father',
            'gender' => 'Male',
            'date_of_birth' => '1985-05-15',
            'cnic_number' => '42101-1111111-1',
            'track_type' => 'ADMINISTRATIVE',
        ]);

        $response = $this->actingAs($registrar)->get('/registrar/dashboard');
        $response->assertStatus(200);

        // Verify theme switcher is present in topbar
        $response->assertSee('data-theme-val="light"', false);
        $response->assertSee('data-theme-val="dark"', false);
        $response->assertSee('data-theme-val="system"', false);

        // Verify user profile detail is present in topbar (avatar, name/email, role)
        $response->assertSee('topbar-user-badge', false);
        $response->assertSee('Dr. Registrar Governance', false);
        $response->assertSee('registrar', false);

        // Verify old "System Online" beacon and "ROLE:" label are removed
        $response->assertDontSee('System Online');
        $response->assertDontSee('ROLE:');
    }

    /**
     * Item 2: CandidateProfileManagement metrics are strictly scoped to pure applicants.
     */
    public function test_item_2_candidate_profile_metrics_consistent_with_applicant_pool(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        // Create internal staff with BasicInfo (Academic track, locked)
        $internalStaff = User::factory()->create(['email' => 'dean.engineering@university.edu']);
        $internalStaff->assignRole('selection-board-member');
        BasicInfo::create([
            'user_id' => $internalStaff->id,
            'first_name' => 'Prof. Dr. Jamshed',
            'last_name' => 'Anwar',
            'father_name' => 'Father Anwar',
            'gender' => 'Male',
            'date_of_birth' => '1975-03-20',
            'cnic_number' => '42201-5555555-1',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        // Create genuine applicants
        $applicant1 = User::factory()->create(['email' => 'candidate1@applicant.com']);
        $applicant1->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $applicant1->id,
            'first_name' => 'Candidate',
            'last_name' => 'One',
            'father_name' => 'Father One',
            'gender' => 'Female',
            'date_of_birth' => '1992-06-10',
            'cnic_number' => '42101-2222222-1',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $applicant2 = User::factory()->create(['email' => 'candidate2@applicant.com']);
        $applicant2->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $applicant2->id,
            'first_name' => 'Candidate',
            'last_name' => 'Two',
            'father_name' => 'Father Two',
            'gender' => 'Male',
            'date_of_birth' => '1995-11-25',
            'cnic_number' => '42101-3333333-1',
            'track_type' => 'ACADEMIC',
            'is_locked' => false,
        ]);

        $this->actingAs($admin);

        Livewire::test(CandidateProfileManagement::class)
            ->assertViewHas('stats', function ($stats) {
                // Total applicant pool must be exactly 2 (internalStaff excluded)
                // Academic applicant pool must be exactly 2
                // Locked applicant pool must be exactly 1
                return $stats['total'] === 2 &&
                       $stats['academic'] === 2 &&
                       $stats['locked'] === 1;
            });
    }

    /**
     * Item 3: Registrar holds administrative operational permissions, but lacks committee evaluation permissions.
     */
    public function test_item_3_registrar_permissions_separation(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        // Allowed: Operational administration
        $this->assertTrue($registrar->can('view-campaign'));
        $this->assertTrue($registrar->can('create-campaign'));
        $this->assertTrue($registrar->can('view-application'));
        $this->assertTrue($registrar->can('view-basic-info'));
        $this->assertTrue($registrar->can('view-payment'));
        $this->assertTrue($registrar->can('manage-reviewers'));
        $this->assertTrue($registrar->can('view-user'));
        $this->assertTrue($registrar->can('view-role'));

        // Disallowed: Role-specific committee evaluation permissions
        $this->assertFalse($registrar->can('evaluate-academic-dossier'));
        $this->assertFalse($registrar->can('scrutinize-application'));
        $this->assertFalse($registrar->can('process-appeal'));
        $this->assertFalse($registrar->can('access-board-grading'));
        $this->assertFalse($registrar->can('submit-board-score'));
    }

    /**
     * Item 4: Super admin is not visible in committee member selection.
     */
    public function test_item_4_super_admin_excluded_from_committee_and_reviewer_selection(): void
    {
        $superAdmin = User::factory()->create(['email' => 'superadmin.ops@university.edu']);
        $superAdmin->assignRole('super-admin');

        $staff = User::factory()->create(['email' => 'professor.staff@university.edu']);
        $staff->assignRole('selection-board-member');

        $this->actingAs($superAdmin);

        $component = Livewire::test(CampaignCrud::class);
        $committeeUsers = $component->get('committee_users');

        $emails = collect($committeeUsers)->pluck('email')->toArray();
        $this->assertNotContains('superadmin.ops@university.edu', $emails);
        $this->assertContains('professor.staff@university.edu', $emails);
    }

    /**
     * Item 5: Super Admin always has all permissions via Gate::before.
     */
    public function test_item_5_super_admin_has_implicit_master_access(): void
    {
        $superAdmin = User::factory()->create(['email' => 'chief.superadmin@university.edu']);
        $superAdmin->assignRole('super-admin');

        $this->assertTrue(Gate::forUser($superAdmin)->allows('any-arbitrary-permission'));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('evaluate-academic-dossier'));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('scrutinize-application'));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('manage-settings'));
    }

    /**
     * Item 6: Super Admin user and role are hidden from non-super-admins in User & Role Management.
     */
    public function test_item_6_super_admin_user_and_role_hidden_from_registrar(): void
    {
        $superAdmin = User::factory()->create(['email' => 'superadmin.hidden@university.edu']);
        $superAdmin->assignRole('super-admin');

        $registrar = User::factory()->create(['email' => 'registrar.viewer@university.edu']);
        $registrar->assignRole('registrar');

        $this->actingAs($registrar);

        // 1. In User Management: Super-admin user should not be visible in user listing
        Livewire::test(UserManagement::class)
            ->assertDontSee('superadmin.hidden@university.edu')
            ->assertViewHas('roles', function ($roles) {
                // Super-admin role should NOT be in the assignable roles dropdown
                return !$roles->contains('name', 'super-admin');
            });

        // 2. In Role Management: Super-admin role should not be listed
        Livewire::test(RolePermissionManagement::class)
            ->assertViewHas('roles', function ($roles) {
                return !$roles->contains('name', 'super-admin');
            });

        // 3. Registrar attempting to edit super-admin account directly must be blocked (403)
        Livewire::test(UserManagement::class)
            ->call('openEditUserForm', $superAdmin->id)
            ->assertStatus(403);
    }
}
