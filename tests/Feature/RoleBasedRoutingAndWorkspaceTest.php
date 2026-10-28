<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\ReviewerProfile;
use App\Livewire\Admin\ReviewerManagement;
use App\Livewire\Scrutiny\Dashboard as ScrutinyDashboard;
use App\Livewire\Board\Dashboard as BoardDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleBasedRoutingAndWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_scrutiny_committee_root_redirect_and_access()
    {
        $scrutinyUser = User::factory()->create();
        $scrutinyUser->assignRole('scrutiny-committee');

        // Root redirect goes to /scrutiny/dashboard
        $this->actingAs($scrutinyUser)
            ->get('/')
            ->assertRedirect('/scrutiny/dashboard');

        // Dedicated scrutiny dashboard accessible
        $this->actingAs($scrutinyUser)
            ->get('/scrutiny/dashboard')
            ->assertStatus(200);

        // Sidebar does NOT show unauthorized operations
        $response = $this->actingAs($scrutinyUser)->get('/scrutiny/dashboard');
        $response->assertDontSee('/admin/campaigns');
        $response->assertDontSee('/admin/payments');
        $response->assertDontSee('/admin/audit-logs');
        $response->assertDontSee('Security Audit Logs');
    }

    public function test_selection_board_member_root_redirect_and_access()
    {
        $boardUser = User::factory()->create();
        $boardUser->assignRole('selection-board-member');

        // Root redirect goes to /board/dashboard
        $this->actingAs($boardUser)
            ->get('/')
            ->assertRedirect('/board/dashboard');

        // Dedicated board dashboard accessible
        $this->actingAs($boardUser)
            ->get('/board/dashboard')
            ->assertStatus(200);

        // Board member cannot view campaigns crud or users crud
        $this->actingAs($boardUser)
            ->get('/admin/campaigns')
            ->assertStatus(403);
    }

    public function test_appellate_committee_root_redirect_and_access()
    {
        $appellateUser = User::factory()->create();
        $appellateUser->assignRole('appellate-committee');

        // Root redirect goes to /appellate/dashboard
        $this->actingAs($appellateUser)
            ->get('/')
            ->assertRedirect('/appellate/dashboard');

        $this->actingAs($appellateUser)
            ->get('/appellate/dashboard')
            ->assertStatus(200);
    }

    public function test_registrar_can_access_registrar_and_admin_routes()
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $this->actingAs($registrar)
            ->get('/')
            ->assertRedirect('/registrar/dashboard');

        $this->actingAs($registrar)
            ->get('/registrar/dashboard')
            ->assertStatus(200);

        $this->actingAs($registrar)
            ->get('/registrar/reviewers')
            ->assertStatus(200);

        // Direct /admin routes also work seamlessly for backwards compatibility
        $this->actingAs($registrar)
            ->get('/admin/dashboard')
            ->assertStatus(200);

        $this->actingAs($registrar)
            ->get('/admin/reviewers')
            ->assertStatus(200);
    }

    public function test_empanel_and_assign_modal_creates_reviewer_and_auto_selects()
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $candidateUser = User::factory()->create();
        $candidateUser->assignRole('applicant');

        $campaign = Campaign::create([
            'job_code' => 'CS-PROF-001',
            'title' => 'Professor in Computer Science',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidateUser->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-CS-789',
            'status' => 'ELIGIBLE',
            'submitted_at' => now(),
        ]);

        Livewire::actingAs($registrar)
            ->test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->assertSet('showDispatchModal', true)
            ->call('openEmpanelAndAssignModal', 'Computer Science')
            ->assertSet('showEmpanelAndAssignModal', true)
            ->assertSet('subject', 'Computer Science')
            ->set('name', 'Prof. Dr. Zahid Hussain')
            ->set('email', 'zahid.hussain@stanford.edu')
            ->set('academic_rank', 'Professor')
            ->set('university', 'Stanford University')
            ->set('country', 'United States')
            ->set('specialization', 'Machine Learning, Neural Networks')
            ->call('createReviewer')
            ->assertSet('showEmpanelAndAssignModal', false)
            ->assertSet('showDispatchModal', true);

        // Verify reviewer was created and assigned role
        $createdUser = User::where('email', 'zahid.hussain@stanford.edu')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole('academic-reviewer'));

        $profile = ReviewerProfile::where('user_id', $createdUser->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('Prof. Dr. Zahid Hussain', $profile->name);
        $this->assertEquals('Stanford University', $profile->university);
        $this->assertEquals('United States', $profile->country);
    }

    public function test_country_select_does_not_contain_searchable_list_or_priority_labels()
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $response = $this->actingAs($registrar)->get('/admin/reviewers');
        $response->assertStatus(200);
        $response->assertDontSee('Searchable List');
        $response->assertDontSee('>Priority<');
    }
}
