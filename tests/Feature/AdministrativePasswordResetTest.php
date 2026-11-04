<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\ReviewerProfile;
use App\Models\SecurityAuditLog;
use App\Mail\AdministrativePasswordResetMail;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\ReviewerManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

class AdministrativePasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_super_admin_can_reset_candidate_password_via_email()
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $candidate = User::factory()->create([
            'email' => 'candidate.test@hirely.edu.pk',
            'password' => Hash::make('OldCandidatePassword123!'),
        ]);
        $candidate->assignRole('applicant');

        BasicInfo::create([
            'user_id' => $candidate->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Ahmad',
            'last_name' => 'Khan',
            'cnic_number' => '17301-1234567-1',
            'date_of_birth' => '1990-01-01',
            'phone_number' => '03001234567',
        ]);

        Livewire::test(CandidateProfileManagement::class)
            ->call('openPasswordResetModal', $candidate->id)
            ->assertSet('showPasswordResetModal', true)
            ->assertSet('resetTargetUserId', $candidate->id)
            ->set('resetMethod', 'email')
            ->set('resetPlainPassword', 'TemporarySecret#2026')
            ->call('executePasswordReset')
            ->assertSet('resetSuccessResult.status', 'success');

        // Verify database password updated
        $candidate->refresh();
        $this->assertTrue(Hash::check('TemporarySecret#2026', $candidate->password));

        // Verify Security Notification & Credentials Email dispatched
        Mail::assertSent(AdministrativePasswordResetMail::class, function ($mail) use ($candidate) {
            return $mail->hasTo($candidate->email) && $mail->method === 'email';
        });

        // Verify Security Audit Log
        $this->assertDatabaseHas('security_audit_logs', [
            'user_id' => $admin->id,
            'action_type' => 'ADMIN_PASSWORD_RESET',
        ]);
    }

    public function test_registrar_can_reset_expert_password_via_whatsapp()
    {
        Mail::fake();

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');
        $this->actingAs($registrar);

        $expertUser = User::factory()->create([
            'email' => 'expert.referee@oxford.edu',
            'password' => Hash::make('OldExpertPassword999!'),
        ]);
        $expertUser->assignRole('academic-reviewer');

        ReviewerProfile::create([
            'user_id' => $expertUser->id,
            'name' => 'Prof. Dr. Tariq Mahmood',
            'academic_rank' => 'Professor',
            'subject' => 'Computer Science',
            'university' => 'Oxford University',
            'country' => 'United Kingdom',
            'phone' => '0333 5557788',
            'is_active' => true,
        ]);

        $test = Livewire::test(ReviewerManagement::class)
            ->call('openPasswordResetModal', $expertUser->id)
            ->assertSet('showPasswordResetModal', true)
            ->set('resetMethod', 'whatsapp')
            ->set('resetPlainPassword', 'ExpertAccess#8822')
            ->call('executePasswordReset')
            ->assertSet('resetSuccessResult.status', 'success');

        // Verify WhatsApp dispatch URL was generated with normalized country code (0333... -> 92333...)
        $result = $test->get('resetSuccessResult');
        $this->assertNotNull($result['whatsapp']);
        $this->assertEquals('923335557788', $result['whatsapp']['formatted_phone']);
        $this->assertStringContainsString('https://wa.me/923335557788', $result['whatsapp']['url']);

        // Verify password updated
        $expertUser->refresh();
        $this->assertTrue(Hash::check('ExpertAccess#8822', $expertUser->password));

        // Always-on security audit email dispatched
        Mail::assertSent(AdministrativePasswordResetMail::class, function ($mail) use ($expertUser) {
            return $mail->hasTo($expertUser->email) && $mail->method === 'whatsapp';
        });
    }

    public function test_reset_via_screen_handover_mode()
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        $staffUser = User::factory()->create([
            'email' => 'staff.internal@hirely.edu.pk',
            'password' => Hash::make('OldStaffPassword1!'),
        ]);

        $test = Livewire::test(UserManagement::class)
            ->call('openPasswordResetModal', $staffUser->id)
            ->set('resetMethod', 'screen')
            ->set('resetPlainPassword', 'ScreenPass#4567')
            ->call('executePasswordReset')
            ->assertSet('resetSuccessResult.status', 'success');

        $result = $test->get('resetSuccessResult');
        $this->assertEquals('ScreenPass#4567', $result['temporary_password']);

        // Password hash updated
        $staffUser->refresh();
        $this->assertTrue(Hash::check('ScreenPass#4567', $staffUser->password));

        // Security notification email still dispatched
        Mail::assertSent(AdministrativePasswordResetMail::class, function ($mail) use ($staffUser) {
            return $mail->hasTo($staffUser->email) && $mail->method === 'screen';
        });
    }

    public function test_unauthorized_user_cannot_access_user_management()
    {
        $candidate = User::factory()->create();
        $candidate->assignRole('applicant');
        $this->actingAs($candidate);

        Livewire::test(UserManagement::class)
            ->assertStatus(403);
    }

    public function test_user_without_reset_permission_aborts_when_calling_reset_modal()
    {
        $viewer = User::factory()->create();
        $viewer->givePermissionTo('view-user'); // Can view users, but has no reset-user-password permission
        $this->actingAs($viewer);

        $target = User::factory()->create();

        Livewire::test(UserManagement::class)
            ->call('openPasswordResetModal', $target->id)
            ->assertForbidden();
    }

    public function test_non_super_admin_cannot_reset_super_admin_password()
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');
        $this->actingAs($registrar);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        Livewire::test(UserManagement::class)
            ->call('openPasswordResetModal', $superAdmin->id)
            ->assertSet('resetErrorMessage', 'Security Policy: Only Super Administrators can reset Super Administrator credentials.');
    }

    public function test_admin_cannot_reset_own_password_via_administrative_tool()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin);

        Livewire::test(UserManagement::class)
            ->call('openPasswordResetModal', $admin->id)
            ->assertSet('resetErrorMessage', 'You cannot reset your own password using the administrative override tool. Please use standard profile settings.');
    }
}
