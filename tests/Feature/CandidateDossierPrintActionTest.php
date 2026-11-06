<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\AcademicDegree;
use App\Models\WorkExperience;
use App\Models\Publication;
use App\Livewire\Admin\CandidateProfileManagement;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidateDossierPrintActionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $registrar;
    protected User $candidate1;
    protected User $candidate2;
    protected BasicInfo $basicInfo1;
    protected BasicInfo $basicInfo2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create(['email' => 'admin@hirely.test']);
        $this->admin->assignRole('super-admin');

        $this->registrar = User::factory()->create(['email' => 'registrar@hirely.test']);
        $this->registrar->assignRole('registrar');

        $this->candidate1 = User::factory()->create(['email' => 'candidate1@test.com']);
        $this->candidate1->assignRole('applicant');

        $this->candidate2 = User::factory()->create(['email' => 'candidate2@test.com']);
        $this->candidate2->assignRole('applicant');

        $this->basicInfo1 = BasicInfo::create([
            'user_id' => $this->candidate1->id,
            'first_name' => 'Dr. Zartash',
            'last_name' => 'Afzal',
            'father_name' => 'Afzal Uzmi',
            'cnic_number' => '17301-1111111-1',
            'phone_number' => '03001234567',
            'date_of_birth' => '1985-05-15',
            'gender' => 'Male',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $this->basicInfo2 = BasicInfo::create([
            'user_id' => $this->candidate2->id,
            'first_name' => 'Sophia',
            'last_name' => 'Rodriguez',
            'cnic_number' => '17301-2222222-2',
            'phone_number' => '03007654321',
            'date_of_birth' => '1992-08-20',
            'gender' => 'Female',
            'track_type' => 'ACADEMIC',
            'is_locked' => false,
        ]);

        AcademicDegree::create([
            'basic_info_id' => $this->basicInfo1->id,
            'degree_level' => 'PHD',
            'degree_title' => 'Ph.D. in Computer Science',
            'major' => 'Computer Science',
            'board' => 'Stanford University',
            'institution' => 'Stanford University',
            'country' => 'United States',
            'year' => 2015,
            'gpa' => '3.92 / 4.00',
            'division' => 'First Division',
        ]);

        Publication::create([
            'basic_info_id' => $this->basicInfo1->id,
            'title' => 'Scalable Multi-Agent Consensus Mechanisms',
            'journal_name' => 'IEEE Transactions on Computers',
            'issn_print' => '0018-9340',
            'publication_year' => 2022,
            'hjrs_category' => 'W',
            'author_order' => 1,
            'is_corresponding_author' => true,
            'authors' => 'Dr. Zartash Afzal',
        ]);
    }

    public function test_super_admin_can_print_candidate_dossier_by_basic_info_id()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('candidate-profile.print', $this->basicInfo1->id));
        $response->assertStatus(200);
        $response->assertSee('Dr. Zartash Afzal');
        $response->assertSee('Stanford University');
        $response->assertSee('Scalable Multi-Agent Consensus Mechanisms');
        $response->assertSee('CAND-' . str_pad($this->basicInfo1->id, 6, '0', STR_PAD_LEFT));
    }

    public function test_registrar_can_print_candidate_dossier_by_user_id()
    {
        $this->actingAs($this->registrar);

        $response = $this->get(route('candidate-profile.print', $this->candidate1->id));
        $response->assertStatus(200);
        $response->assertSee('Dr. Zartash Afzal');
        $response->assertSee('candidate1@test.com');
    }

    public function test_candidate_can_print_own_dossier()
    {
        $this->actingAs($this->candidate1);

        $response = $this->get(route('candidate-profile.print', $this->basicInfo1->id));
        $response->assertStatus(200);
        $response->assertSee('Dr. Zartash Afzal');
    }

    public function test_candidate_cannot_print_other_candidate_dossier()
    {
        $this->actingAs($this->candidate2);

        $response = $this->get(route('candidate-profile.print', $this->basicInfo1->id));
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get(route('candidate-profile.print', $this->basicInfo1->id));
        $response->assertRedirect(route('login'));
    }

    public function test_candidate_with_active_applications_prints_with_campaign_details()
    {
        $campaign = Campaign::create([
            'job_code' => 'PROF-CS-2026',
            'title' => 'Chair Professor of Artificial Intelligence',
            'department' => 'Faculty of Computer Science',
            'track_type' => 'ACADEMIC',
            'application_fee' => 3000,
            'bps_scale' => 21,
            'is_active' => true,
        ]);

        $app = Application::create([
            'user_id' => $this->candidate1->id,
            'campaign_id' => $campaign->id,
            'status' => 'submitted',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('candidate-profile.print', $this->basicInfo1->id));
        $response->assertStatus(200);
        $response->assertSee('Chair Professor of Artificial Intelligence');
        $response->assertSee($app->tracking_number);
    }

    public function test_candidate_profile_management_modal_contains_dossier_print_button()
    {
        $this->actingAs($this->admin);

        Livewire::test(CandidateProfileManagement::class)
            ->call('viewBasicInfoDetails', $this->basicInfo1->id)
            ->assertSet('isDetailsOpen', true)
            ->assertSeeHtml(route('candidate-profile.print', $this->basicInfo1->id))
            ->assertSee('Download / Print Complete Dossier')
            ->assertSee('Download / Print Dossier');
    }
}
