<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\SystemSetting;
use Livewire\Livewire;
use App\Livewire\Admin\ConstitutionCriteriaManagement;
use App\Livewire\Admin\CampaignCrud;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\PermissionRegistry;

class ConstitutionCriteriaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $registrar;
    protected $superAdmin;
    protected $applicant;
    protected $campaign;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        PermissionRegistry::syncAll();

        $this->registrar = User::where('email', 'registrar@university.edu')->first();
        $this->superAdmin = User::where('email', 'superadmin@university.edu')->first();
        $this->applicant = User::where('email', 'applicant1@gmail.com')->first();
        $this->campaign = Campaign::where('job_code', 'CS-FAC-2026')->first();
    }

    public function test_super_admin_and_registrar_can_access_criteria_management_desk()
    {
        $this->actingAs($this->superAdmin);
        $this->get('/admin/criteria')->assertOk();

        $this->actingAs($this->registrar);
        $this->get('/registrar/criteria')->assertOk();
    }

    public function test_unauthorized_user_is_strictly_blocked_from_criteria_desk()
    {
        $this->actingAs($this->applicant);
        $this->get('/admin/criteria')->assertForbidden();
        $this->get('/registrar/criteria')->assertForbidden();
    }

    public function test_criteria_desk_loads_active_campaign_and_rules()
    {
        $this->actingAs($this->registrar);

        Livewire::test(ConstitutionCriteriaManagement::class, ['campaign_id' => $this->campaign->id])
            ->assertSet('selected_campaign_id', $this->campaign->id)
            ->assertSee('Statutory Criteria', false)
            ->assertSee('ONE-CLICK STATUTORY PRESET TEMPLATES');
    }

    public function test_dynamic_rule_addition_removal_and_count_updates()
    {
        $this->actingAs($this->superAdmin);

        $test = Livewire::test(ConstitutionCriteriaManagement::class, ['campaign_id' => $this->campaign->id])
            ->call('applyPreset', 'blank');

        $initialCount = count($test->get('rules'));
        $this->assertEquals(1, $initialCount);

        // Add a new dynamic rule slot
        $test->call('addRule');
        $this->assertCount(2, $test->get('rules'));

        // Update count for the first rule slot
        $test->call('updateRuleCount', 0, 2);
        $rules = $test->get('rules');
        $this->assertEquals(3, $rules[0]['count']);

        // Remove the second rule slot
        $test->call('removeRule', 1);
        $this->assertCount(1, $test->get('rules'));
    }

    public function test_preset_templates_populate_dynamic_rules_and_quotas()
    {
        $this->actingAs($this->registrar);

        // Test Assistant Professor preset
        $test = Livewire::test(ConstitutionCriteriaManagement::class, ['campaign_id' => $this->campaign->id])
            ->call('applyPreset', 'asst_prof');

        $this->assertEquals(6, $test->get('totalQuorum'));
        $this->assertEquals(5, $test->get('internalCount'));
        $this->assertEquals(1, $test->get('externalCount'));
        $this->assertEquals(3, $test->get('required_peer_reviewers'));

        // Test Corporate Executive Panel preset
        $test->call('applyPreset', 'corporate_board');
        $this->assertEquals(5, $test->get('totalQuorum'));
        $this->assertEquals(3, $test->get('internalCount'));
        $this->assertEquals(2, $test->get('externalCount'));
        $this->assertEquals(0, $test->get('required_peer_reviewers'));
    }

    public function test_saving_dynamic_constitution_persists_to_campaign_and_metrics()
    {
        $this->actingAs($this->superAdmin);

        $customRules = [
            [
                'id' => 'rule_dean',
                'name' => 'Dean of Computing Faculty (Convener)',
                'count' => 1,
                'category' => 'INTERNAL',
                'cadre' => 'FACULTY',
                'role_nature' => 'CONVENER',
                'is_mandatory' => true,
                'notes' => 'Faculty Dean',
            ],
            [
                'id' => 'rule_hod',
                'name' => 'Head of CS Department',
                'count' => 1,
                'category' => 'INTERNAL',
                'cadre' => 'FACULTY',
                'role_nature' => 'CONVENER',
                'is_mandatory' => true,
                'notes' => 'Department Head',
            ],
            [
                'id' => 'rule_faculty',
                'name' => 'Internal CS Professors (Same University)',
                'count' => 3,
                'category' => 'INTERNAL',
                'cadre' => 'FACULTY',
                'role_nature' => 'MEMBER',
                'is_mandatory' => false,
                'notes' => 'Senior faculty',
            ],
            [
                'id' => 'rule_external',
                'name' => 'External Professor (Other University)',
                'count' => 1,
                'category' => 'EXTERNAL',
                'cadre' => 'FACULTY',
                'role_nature' => 'MEMBER',
                'is_mandatory' => false,
                'notes' => 'External professor',
            ],
            [
                'id' => 'rule_intl',
                'name' => 'Overseas AI Specialist',
                'count' => 1,
                'category' => 'INTERNATIONAL',
                'cadre' => 'EXPERT',
                'role_nature' => 'EXPERT',
                'is_mandatory' => false,
                'notes' => 'International expert',
            ],
        ];

        Livewire::test(ConstitutionCriteriaManagement::class, ['campaign_id' => $this->campaign->id])
            ->set('constitution_title', 'Advanced Computing Statutory Search Committee')
            ->set('required_peer_reviewers', 3)
            ->set('rules', $customRules)
            ->call('saveConstitution')
            ->assertHasNoErrors();

        $metrics = $this->campaign->fresh()->getCommitteeMetrics();
        // Total should be 1 + 1 + 3 + 1 + 1 = 7
        $this->assertEquals(7, $metrics['required_total']);
        $this->assertEquals(5, $metrics['categories']['INTERNAL']['required']);
        $this->assertEquals(1, $metrics['categories']['EXTERNAL']['required']);
        $this->assertEquals(1, $metrics['categories']['INTERNATIONAL']['required']);
        $this->assertNotEmpty($metrics['rules']);
    }

    public function test_campaign_crud_embeds_dynamic_rules_in_constitution_modal()
    {
        $this->actingAs($this->registrar);

        Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->set('selected_campaign_id', $this->campaign->id)
            ->call('openConstitutionModal')
            ->assertSet('isConstitutionModalOpen', true)
            ->call('applyConstitutionPreset', 'asst_prof')
            ->assertSet('constitution_required_total', 6)
            ->assertSet('constitution_required_internal', 5)
            ->assertSet('constitution_required_external', 1)
            ->call('addConstitutionRule')
            ->call('saveConstitution')
            ->assertHasNoErrors();
    }
}
