<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\CampaignCommittee;
use App\Models\ReviewerProfile;
use App\Models\BasicInfo;
use Livewire\Livewire;
use App\Livewire\Admin\CampaignCrud;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class CampaignCommitteeAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected $registrar;
    protected $superAdmin;
    protected $campaign;
    protected $internalUser;
    protected $externalUser;
    protected $internationalUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->registrar = User::where('email', 'registrar@university.edu')->first();
        $this->superAdmin = User::where('email', 'superadmin@university.edu')->first();
        $this->campaign = Campaign::where('job_code', 'CS-FAC-2026')->first();

        // Internal member
        $this->internalUser = User::where('email', 'scrutiny1@university.edu')->first();

        // External expert from LUMS (Pakistan)
        $this->externalUser = User::where('email', 'prof.ahmad.khan@lums.edu.pk')->first();

        // International specialist from MIT (United States)
        $this->internationalUser = User::where('email', 'dr.sarah.connor@mit.edu')->first();
    }

    public function test_super_admin_is_strictly_excluded_from_candidate_pool()
    {
        $this->actingAs($this->registrar);

        $component = Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees');

        $candidateUsers = $component->get('committee_users');
        $emails = collect($candidateUsers)->pluck('email')->all();

        // Assert superadmin is NOT in the candidate list
        $this->assertNotContains('superadmin@university.edu', $emails);
        $this->assertFalse(collect($candidateUsers)->contains('category', 'super-admin'));

        // Assert internal, external, and international candidates ARE present
        $this->assertContains('scrutiny1@university.edu', $emails);
        $this->assertContains('prof.ahmad.khan@lums.edu.pk', $emails);
        $this->assertContains('dr.sarah.connor@mit.edu', $emails);
    }

    public function test_attempting_to_assign_super_admin_is_blocked_by_validation()
    {
        $this->actingAs($this->registrar);

        Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->set('selected_campaign_id', $this->campaign->id)
            ->set('selected_user_id', $this->superAdmin->id)
            ->set('member_category', 'INTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember')
            ->assertHasErrors(['selected_user_id']);

        $this->assertDatabaseMissing('campaign_committees', [
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_candidates_are_categorized_into_sections()
    {
        $this->actingAs($this->registrar);

        $component = Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees');

        $candidates = collect($component->get('committee_users'));

        $internal = $candidates->firstWhere('email', 'scrutiny1@university.edu');
        $this->assertEquals('INTERNAL', $internal['category']);

        $external = $candidates->firstWhere('email', 'prof.ahmad.khan@lums.edu.pk');
        $this->assertEquals('EXTERNAL', $external['category']);
        $this->assertEquals('Lahore University of Management Sciences (LUMS)', $external['affiliation']);

        $intl = $candidates->firstWhere('email', 'dr.sarah.connor@mit.edu');
        $this->assertEquals('INTERNATIONAL', $intl['category']);
        $this->assertEquals('United States', $intl['country']);
    }

    public function test_selecting_candidate_populates_category_and_details()
    {
        $this->actingAs($this->registrar);

        Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->call('selectCandidate', $this->internationalUser->id)
            ->assertSet('selected_user_id', $this->internationalUser->id)
            ->assertSet('member_category', 'INTERNATIONAL')
            ->assertSet('country', 'United States')
            ->assertSet('affiliation', 'Massachusetts Institute of Technology (MIT)');
    }

    public function test_exact_quota_enforcement_prevents_exceeding_category_slots()
    {
        $this->actingAs($this->registrar);

        // For Academic track, default constitution is: 2 Internal, 2 External, 1 International (Total 5)
        $scrutiny2 = User::where('email', 'scrutiny2@university.edu')->first();
        $board1 = User::where('email', 'board1@university.edu')->first();

        // 1. Assign 1st internal member -> OK
        Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->set('selected_campaign_id', $this->campaign->id)
            ->set('selected_user_id', $this->internalUser->id)
            ->set('member_category', 'INTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember')
            ->assertHasNoErrors();

        // 2. Assign 2nd internal member -> OK (Now internal quota 2/2 is full)
        Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->set('selected_campaign_id', $this->campaign->id)
            ->set('selected_user_id', $scrutiny2->id)
            ->set('member_category', 'INTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember')
            ->assertHasNoErrors();

        // 3. Attempt to assign a 3rd internal member -> Exceeds internal quota of 2!
        Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->set('selected_campaign_id', $this->campaign->id)
            ->set('selected_user_id', $board1->id)
            ->set('member_category', 'INTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember')
            ->assertHasErrors(['member_category']);

        // Assert 3rd internal member was NOT saved
        $this->assertDatabaseMissing('campaign_committees', [
            'campaign_id' => $this->campaign->id,
            'user_id' => $board1->id,
        ]);
    }

    public function test_assigning_across_dynamic_categories_satisfies_statutory_quorum()
    {
        $this->actingAs($this->registrar);

        $scrutiny2 = User::where('email', 'scrutiny2@university.edu')->first();
        $external2 = User::where('email', 'engr.tariq.aziz@telecom.gov.pk')->first();

        $component = Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->set('selected_campaign_id', $this->campaign->id);

        // Assign 2 Internal
        $component->set('selected_user_id', $this->internalUser->id)
            ->set('member_category', 'INTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember');

        $component->set('selected_user_id', $scrutiny2->id)
            ->set('member_category', 'INTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember');

        // Assign 2 External
        $component->set('selected_user_id', $this->externalUser->id)
            ->set('member_category', 'EXTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember');

        $component->set('selected_user_id', $external2->id)
            ->set('member_category', 'EXTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember');

        // Assign 1 International
        $component->set('selected_user_id', $this->internationalUser->id)
            ->set('member_category', 'INTERNATIONAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember');

        // Total 5 members now empanelled
        $metrics = $this->campaign->fresh()->getCommitteeMetrics();
        $this->assertEquals(5, $metrics['total_assigned']);
        $this->assertTrue($metrics['is_complete']);

        // Attempting to assign a 6th member fails quorum check
        $board2 = User::where('email', 'board2@university.edu')->first();
        $component->set('selected_user_id', $board2->id)
            ->set('member_category', 'INTERNAL')
            ->set('is_appellate_panel', false)
            ->call('assignCommitteeMember')
            ->assertHasErrors(['selected_user_id']);
    }

    public function test_registrar_can_tune_statutory_constitution()
    {
        $this->actingAs($this->registrar);

        Livewire::test(CampaignCrud::class)
            ->set('activeTab', 'committees')
            ->set('selected_campaign_id', $this->campaign->id)
            ->set('constitution_title', 'Special International Search Committee')
            ->set('constitution_required_total', 6)
            ->set('constitution_required_internal', 2)
            ->set('constitution_required_external', 2)
            ->set('constitution_required_international', 2)
            ->set('constitution_required_independent', 0)
            ->call('saveConstitution')
            ->assertHasNoErrors();

        $metrics = $this->campaign->fresh()->getCommitteeMetrics();
        $this->assertEquals(6, $metrics['required_total']);
        $this->assertEquals(2, $metrics['categories']['INTERNATIONAL']['required']);
    }
}

