<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\ReviewerProfile;
use App\Models\RecruitmentProcessFlow;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use App\Livewire\Admin\ReviewerManagement;
use App\Livewire\Admin\ConstitutionCriteriaManagement;

class RecruitmentPipelineAndCardStatusTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $registrar;
    protected RecruitmentProcessFlow $flow;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles & Permissions
        $adminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $registrarRole = Role::firstOrCreate(['name' => 'registrar']);

        $perms = [
            'view-campaign', 'create-campaign', 'edit-campaign', 'delete-campaign',
            'view-committee', 'assign-committee', 'remove-committee', 'tune-constitution',
            'manage-reviewers', 'assign-reviewer', 'view-review-reports',
            'view-application', 'change-application-status',
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }
        $adminRole->syncPermissions(Permission::all());
        $registrarRole->syncPermissions(Permission::all());

        // Users
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@system.gov',
            'is_active' => true,
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->registrar = User::factory()->create([
            'email' => 'registrar@system.gov',
            'is_active' => true,
        ]);
        $this->registrar->assignRole('registrar');

        // Create a test pipeline
        $this->flow = RecruitmentProcessFlow::create([
            'public_id' => 'pipe_test12345',
            'name' => 'Standard Research Faculty Pipeline',
            'sector' => 'ACADEMIC',
            'description' => 'Test research faculty hiring blueprint.',
            'stages' => RecruitmentProcessFlow::defaultAcademicPipeline(),
            'is_default' => false,
            'is_active' => true,
            'created_by' => $this->registrar->id,
        ]);
    }

    public function test_card_view_displays_status_strip_above_actions_and_applies_status_background_classes(): void
    {
        $this->actingAs($this->registrar);

        $reviewerUser = User::factory()->create(['email' => 'prof.smith@oxford.edu']);
        $reviewer = ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Prof. Dr. Arthur Pendelton',
            'academic_rank' => 'Professor',
            'subject' => 'Artificial Intelligence & Robotics',
            'university' => 'University of Oxford',
            'country' => 'United Kingdom',
            'is_active' => true,
        ]);

        // 1. Active Reviewer Card: renders status-active, full name, prefix, and status-toggle-btn
        Livewire::test(ReviewerManagement::class)
            ->assertSee('status-active')
            ->assertSee('Prof. Dr.')
            ->assertSee('Arthur Pendelton')
            ->assertSee('ACTIVE')
            ->assertSee('Artificial Intelligence & Robotics')
            ->assertSee('Click to toggle')
            ->call('toggleReviewerStatus', $reviewer->id);

        $this->assertFalse($reviewer->fresh()->is_active);

        // 2. Inactive Reviewer Card: renders status-inactive background class and INACTIVE badge
        Livewire::test(ReviewerManagement::class)
            ->assertSee('status-inactive')
            ->assertSee('INACTIVE');
    }

    public function test_dedicated_pipeline_routes_accessible_by_public_id_and_slug(): void
    {
        $this->actingAs($this->registrar);

        // 1. Direct Pipeline Visual Designer via /pipeline/{public_id}
        $resp1 = $this->get('/pipeline/' . $this->flow->public_id);
        $resp1->assertStatus(200);
        $resp1->assertSee('Standard Research Faculty Pipeline');
        $resp1->assertSee('Back to All Pipelines');

        // 2. Direct Pipeline Visual Designer via /admin/pipeline/{public_id}
        $resp2 = $this->get('/admin/pipeline/' . $this->flow->public_id);
        $resp2->assertStatus(200);
        $resp2->assertSee('Standard Research Faculty Pipeline');

        // 3. Direct Pipeline via /criteria/{public_id}
        $resp3 = $this->get('/criteria/' . $this->flow->public_id);
        $resp3->assertStatus(200);
        $resp3->assertSee('Standard Research Faculty Pipeline');

        // 4. Pipelines Listing Desk via /admin/pipelines
        $resp4 = $this->get('/admin/pipelines');
        $resp4->assertStatus(200);
        $resp4->assertSee('Recruitment Pipeline Flows');
        $resp4->assertSee('Standard Research Faculty Pipeline');
    }

    public function test_recruitment_pipeline_can_be_renamed_and_configured(): void
    {
        $this->actingAs($this->registrar);

        Livewire::test(ConstitutionCriteriaManagement::class, ['pipeline' => $this->flow->public_id])
            ->assertSet('pipelineViewMode', 'design')
            ->assertSee('Standard Research Faculty Pipeline')
            ->call('openEditPipelineModal', $this->flow->id)
            ->assertSet('showEditPipelineModal', true)
            ->assertSet('edit_flow_name', 'Standard Research Faculty Pipeline')
            ->set('edit_flow_name', 'Tenured Professorial Track Pipeline')
            ->set('edit_flow_sector', 'ACADEMIC')
            ->set('edit_flow_description', 'Updated statutory criteria with strict viva board.')
            ->call('savePipelineInfo')
            ->assertHasNoErrors()
            ->assertSet('showEditPipelineModal', false);

        $this->assertEquals('Tenured Professorial Track Pipeline', $this->flow->fresh()->name);
        $this->assertEquals('Updated statutory criteria with strict viva board.', $this->flow->fresh()->description);
    }

    public function test_deleting_pipeline_with_attached_campaigns_shows_warning_modal(): void
    {
        $this->actingAs($this->registrar);

        // Attach flow to a Campaign
        $campaign = Campaign::create([
            'title' => 'Associate Professor Computer Science',
            'job_code' => 'CS-PROF-2026',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'application_fee' => 2500,
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(20),
            'process_flow_id' => $this->flow->id,
            'is_active' => true,
        ]);

        $this->assertEquals(1, $this->flow->campaigns()->count());

        // Deleting triggers warning modal listing the attached campaign
        $component = Livewire::test(ConstitutionCriteriaManagement::class)
            ->call('requestDeleteProcessFlow', $this->flow->id)
            ->assertSet('showDeleteWarningModal', true)
            ->assertSet('confirmingDeleteFlowId', $this->flow->id)
            ->assertSee('CS-PROF-2026')
            ->assertSee('Associate Professor Computer Science')
            ->assertSee('Detach & Delete Pipeline');

        // Confirming detachment and deletion
        $component->call('confirmDeleteProcessFlow')
            ->assertHasNoErrors()
            ->assertSet('showDeleteWarningModal', false);

        $this->assertNull(RecruitmentProcessFlow::find($this->flow->id));
        $this->assertNull($campaign->fresh()->process_flow_id);
    }

    public function test_deleting_default_system_pipeline_is_blocked(): void
    {
        $this->actingAs($this->registrar);

        $defaultFlow = RecruitmentProcessFlow::create([
            'public_id' => 'pipe_default001',
            'name' => 'Default Institution Pipeline',
            'sector' => 'GENERAL',
            'stages' => RecruitmentProcessFlow::defaultAcademicPipeline(),
            'is_default' => true,
            'is_active' => true,
        ]);

        Livewire::test(ConstitutionCriteriaManagement::class)
            ->call('requestDeleteProcessFlow', $defaultFlow->id)
            ->assertSet('showDeleteWarningModal', false);

        $this->assertNotNull(RecruitmentProcessFlow::find($defaultFlow->id));
    }
}
