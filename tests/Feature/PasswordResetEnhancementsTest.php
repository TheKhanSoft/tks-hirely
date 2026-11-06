<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Livewire\Admin\ReviewerManagement;
use App\Mail\AdministrativePasswordResetMail;
use App\Models\ReviewerProfile;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PasswordResetEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $targetUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'academic-reviewer']);
        Permission::firstOrCreate(['name' => 'manage-reviewers']);
        Permission::firstOrCreate(['name' => 'view-reviewer']);
        Permission::firstOrCreate(['name' => 'reset-user-password']);

        $this->adminUser = User::factory()->create([
            'email' => 'superadmin@hirely.test',
            'is_active' => true,
        ]);
        $this->adminUser->assignRole('super-admin');

        $this->targetUser = User::factory()->create([
            'email' => 'dr.sophia.rodriguez@ethz.ch',
            'password' => Hash::make('old-secret-password'),
            'is_active' => true,
        ]);
        $this->targetUser->assignRole('academic-reviewer');

        ReviewerProfile::create([
            'user_id' => $this->targetUser->id,
            'name' => 'Dr. Sophia Rodriguez',
            'subject' => 'Electrical Engineering',
            'academic_rank' => 'Senior Research Scientist',
            'university' => 'ETH Zurich',
            'country' => 'Switzerland',
            'is_active' => true,
        ]);
    }

    public function test_email_method_does_not_reveal_password_and_dispatches_securely()
    {
        $this->actingAs($this->adminUser);
        Mail::fake();

        $comp = Livewire::test(ReviewerManagement::class)
            ->call('openPasswordResetModal', $this->targetUser->id)
            ->assertSet('showPasswordResetModal', true)
            ->assertSet('resetMethod', 'email')
            ->assertSet('resetPlainPassword', '')
            // Ensure zero-knowledge notice is rendered
            ->assertSee('Zero-Knowledge Secure Email Delivery')
            // Ensure password input field is NOT shown
            ->assertDontSee('Hirely#')
            ->assertSeeHtml('Generate & Send Password to Email')
            // Execute reset
            ->call('executePasswordReset')
            ->assertHasNoErrors()
            ->assertSee('Credentials Dispatched to dr.sophia.rodriguez@ethz.ch')
            ->assertSee('In accordance with zero-knowledge security policies, the password is not revealed on screen');

        // Verify password in database was changed
        $this->targetUser->refresh();
        $this->assertFalse(Hash::check('old-secret-password', $this->targetUser->password));

        // Verify email was dispatched with credentials
        Mail::assertSent(AdministrativePasswordResetMail::class, function ($mail) {
            return $mail->hasTo('dr.sophia.rodriguez@ethz.ch')
                && $mail->includePasswordInEmail === true
                && strlen($mail->plainPassword) >= 8;
        });
    }

    public function test_screen_method_reveals_password_on_screen()
    {
        $this->actingAs($this->adminUser);
        Mail::fake();

        Livewire::test(ReviewerManagement::class)
            ->call('openPasswordResetModal', $this->targetUser->id)
            ->assertSet('showPasswordResetModal', true)
            ->set('resetMethod', 'screen')
            ->assertNotSet('resetPlainPassword', '')
            ->assertSee('New Temporary Password')
            ->assertSee('Show on Screen')
            ->call('executePasswordReset')
            ->assertHasNoErrors()
            ->assertSee('Temporary Generated Password')
            ->assertSee('Copy Password');
    }
}
