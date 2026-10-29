<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SecurityAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\PermissionRegistry;

class SuperAdminImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $registrar;
    protected $applicant;
    protected $reviewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        PermissionRegistry::syncAll();

        $this->superAdmin = User::where('email', 'superadmin@university.edu')->first();
        $this->registrar = User::where('email', 'registrar@university.edu')->first();
        $this->applicant = User::where('email', 'applicant1@gmail.com')->first();
        $this->reviewer = User::role('academic-reviewer')->first();
    }

    public function test_super_admin_can_successfully_impersonate_applicant()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('impersonate.start', $this->applicant->id));
        $response->assertRedirect('/');

        // Session must record impersonator details
        $this->assertEquals($this->superAdmin->id, session('impersonated_by'));

        // Current authenticated user must now be the applicant
        $this->assertEquals($this->applicant->id, auth()->id());

        // Security Audit Log must be created
        $this->assertDatabaseHas('security_audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action_type' => 'IMPERSONATE_START',
            'record_id' => $this->applicant->id,
        ]);
    }

    public function test_super_admin_can_impersonate_academic_reviewer_and_view_desk()
    {
        $this->actingAs($this->superAdmin);

        $this->get(route('impersonate.start', $this->reviewer->id))
            ->assertRedirect('/');

        $this->assertEquals($this->reviewer->id, auth()->id());
        $this->assertEquals($this->superAdmin->id, session('impersonated_by'));

        // Reviewer dashboard should render successfully and display the impersonation banner
        $response = $this->get('/reviewer/dashboard');
        $response->assertOk();
        $response->assertSee('You are impersonating', false);
        $response->assertSee('Exit Impersonation', false);
    }

    public function test_non_super_admin_roles_are_strictly_forbidden_from_impersonating()
    {
        // 1. Registrar is forbidden
        $this->actingAs($this->registrar);
        $this->get(route('impersonate.start', $this->applicant->id))
            ->assertForbidden();

        // 2. Pure Applicant is forbidden
        $this->actingAs($this->applicant);
        $this->get(route('impersonate.start', $this->registrar->id))
            ->assertForbidden();

        // 3. Guest is redirected to login
        auth()->logout();
        $this->get(route('impersonate.start', $this->applicant->id))
            ->assertRedirect('/login');
    }

    public function test_super_admin_cannot_impersonate_themselves()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('impersonate.start', $this->superAdmin->id));
        $response->assertSessionHasErrors(['impersonate']);
        $this->assertEquals($this->superAdmin->id, auth()->id());
        $this->assertFalse(session()->has('impersonated_by'));
    }

    public function test_super_admin_cannot_impersonate_another_super_admin()
    {
        // Create a secondary super admin
        $secondAdmin = User::factory()->create([
            'email' => 'admin2@university.edu',
        ]);
        $secondAdmin->assignRole('super-admin');

        $this->actingAs($this->superAdmin);

        $response = $this->get(route('impersonate.start', $secondAdmin->id));
        $response->assertSessionHasErrors(['impersonate']);
        $this->assertEquals($this->superAdmin->id, auth()->id());
        $this->assertFalse(session()->has('impersonated_by'));
    }

    public function test_super_admin_can_leave_impersonation_and_restore_session()
    {
        $this->actingAs($this->superAdmin);

        // Start impersonation
        $this->get(route('impersonate.start', $this->applicant->id));
        $this->assertEquals($this->applicant->id, auth()->id());
        $this->assertTrue(session()->has('impersonated_by'));

        // Leave impersonation
        $leaveResponse = $this->get(route('impersonate.leave'));
        $leaveResponse->assertRedirect('/admin/users');

        // Session flags must be cleared
        $this->assertFalse(session()->has('impersonated_by'));

        // Auth must be restored to original Super Admin
        $this->assertEquals($this->superAdmin->id, auth()->id());

        // Security Audit Log must record termination
        $this->assertDatabaseHas('security_audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action_type' => 'IMPERSONATE_STOP',
            'record_id' => $this->applicant->id,
        ]);
    }
}
