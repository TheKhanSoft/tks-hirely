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
use App\Models\ResearchProject;
use App\Models\Patent;
use App\Models\AcademicMedal;
use App\Models\Training;
use App\Models\ProfessionalEvent;
use App\Models\Referee;
use App\Models\BillingLedger;
use App\Models\LiveEvaluation;
use App\Models\ApplicationReview;
use App\Models\ReviewerProfile;
use App\Services\MeritListService;
use App\Livewire\Admin\MeritListManagement;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationPrintAndMeritListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $registrar;
    protected User $candidate1;
    protected User $candidate2;
    protected Campaign $campaign;
    protected Application $application1;
    protected Application $application2;

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

        $this->campaign = Campaign::create([
            'job_code' => 'TEST-PROF-01',
            'title' => 'Professor Computer Science',
            'department' => 'Department of Computing',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        // Candidate 1 Details
        $bi1 = BasicInfo::create([
            'user_id' => $this->candidate1->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Turing',
            'last_name' => 'Alan',
            'father_name' => 'Julius Mathison Turing',
            'cnic_number' => '37405-1111111-1',
            'date_of_birth' => '1985-06-23',
            'gender' => 'Male',
            'quota' => 'Open Merit',
            'domicile' => 'Punjab',
            'phone_number' => '+92-300-1112233',
            'is_locked' => true,
        ]);

        AcademicDegree::create([
            'basic_info_id' => $bi1->id,
            'degree_level' => 'PHD',
            'degree_title' => 'Ph.D. in Computer Science',
            'major' => 'Computer Science',
            'board' => 'University of Cambridge',
            'institution' => 'University of Cambridge',
            'country' => 'United Kingdom',
            'year' => 2012,
            'gpa' => '3.95 / 4.00',
            'division' => '1st Division',
        ]);

        Publication::create([
            'basic_info_id' => $bi1->id,
            'title' => 'On Computable Numbers with an Application to the Entscheidungsproblem',
            'journal_name' => 'Proceedings of the London Mathematical Society',
            'issn_print' => '0024-6115',
            'hjrs_category' => 'W',
            'author_order' => 1,
            'is_corresponding_author' => true,
            'authors' => 'Alan Turing',
            'publication_year' => 2015,
        ]);

        $this->application1 = Application::create([
            'user_id' => $this->candidate1->id,
            'campaign_id' => $this->campaign->id,
            'status' => 'APPROVED',
            'submission_timestamp' => now()->subDays(2),
        ]);

        BillingLedger::create([
            'application_id' => $this->application1->id,
            'consumer_id' => '100099990001',
            'amount' => 3000.00,
            'status' => 'SETTLED',
            'transaction_id' => '1LINK-TXN-112233',
            'payment_timestamp' => now()->subDays(2),
        ]);

        // Candidate 2 Details (Ineligible)
        $bi2 = BasicInfo::create([
            'user_id' => $this->candidate2->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'father_name' => 'Richard Doe',
            'cnic_number' => '37405-2222222-2',
            'date_of_birth' => '1995-01-01',
            'gender' => 'Male',
            'quota' => 'Open Merit',
            'domicile' => 'Sindh',
            'phone_number' => '+92-300-4445566',
            'is_locked' => true,
        ]);

        AcademicDegree::create([
            'basic_info_id' => $bi2->id,
            'degree_level' => 'BACHELORS',
            'degree_title' => 'BS Computer Science',
            'major' => 'Computer Science',
            'board' => 'Local University',
            'institution' => 'Local University',
            'country' => 'Pakistan',
            'year' => 2018,
            'gpa' => '2.10 / 4.00',
            'division' => '3rd Division',
        ]);

        $this->application2 = Application::create([
            'user_id' => $this->candidate2->id,
            'campaign_id' => $this->campaign->id,
            'status' => 'INELIGIBLE',
            'submission_timestamp' => now()->subDay(),
            'disqualification_reason' => 'Candidate does not possess mandatory Terminal Ph.D. Degree and holds 3rd Division in BS.',
        ]);

        BillingLedger::create([
            'application_id' => $this->application2->id,
            'consumer_id' => '100099990002',
            'amount' => 3000.00,
            'status' => 'SETTLED',
            'transaction_id' => '1LINK-TXN-445566',
            'payment_timestamp' => now()->subDay(),
        ]);
    }

    public function test_candidate_can_print_own_application_form(): void
    {
        $this->actingAs($this->candidate1);

        $response = $this->get("/application/{$this->application1->id}/print");
        $response->assertStatus(200);
        $response->assertSee('Turing Alan');
        $response->assertSee('37405-1111111-1');
        $response->assertSee('TEST-PROF-01');
        $response->assertSee('Ph.D. in Computer Science');
        $response->assertSee('FEE SETTLED / VERIFIED');
        $response->assertSee('Solemn Statutory Undertaking');

        // Candidate must NOT see internal scrutiny administrative sign-off block
        $response->assertDontSee('CONFIDENTIAL STATUTORY SCRUTINY');
    }

    public function test_candidate_cannot_print_other_candidate_application(): void
    {
        $this->actingAs($this->candidate1);

        // Trying to access candidate2 application
        $response = $this->get("/application/{$this->application2->id}/print");
        $response->assertStatus(403);
    }

    public function test_candidate_cannot_access_admin_view_mode(): void
    {
        $this->actingAs($this->candidate1);

        // Forcing view=admin parameter
        $response = $this->get("/application/{$this->application1->id}/print?view=admin");
        $response->assertStatus(200);
        // Security logic must force candidate view for applicant
        $response->assertDontSee('CONFIDENTIAL STATUTORY SCRUTINY');
    }

    public function test_super_admin_and_registrar_can_print_any_application_dossier(): void
    {
        $this->actingAs($this->admin);

        // Admin Master Dossier view
        $response = $this->get("/application/{$this->application1->id}/print?view=admin");
        $response->assertStatus(200);
        $response->assertSee('Official Master Application Dossier');
        $response->assertSee('CONFIDENTIAL STATUTORY SCRUTINY');
        $response->assertSee('Statutory Scrutiny Committee Verification');
        $response->assertSee('Member / Incharge');
        $response->assertSee('Vice Chancellor / Chairman');

        // Registrar access
        $this->actingAs($this->registrar);
        $regResponse = $this->get("/application/{$this->application2->id}/print?view=admin");
        $regResponse->assertStatus(200);
        $regResponse->assertSee('Candidate does not possess mandatory Terminal Ph.D. Degree');
        $regResponse->assertSee('INELIGIBLE / DISQUALIFIED');
    }

    public function test_dossier_prints_user_audit_details_and_full_academic_research_portfolio(): void
    {
        $bi = $this->candidate1->basicInfo;

        // Add Research Project
        ResearchProject::create([
            'basic_info_id' => $bi->id,
            'project_title' => 'Quantum Neural Synthesis Framework',
            'funding_agency' => 'Higher Education Commission NRPU',
            'grant_amount' => 4500000.00,
            'role_in_project' => 'Principal Investigator',
            'status' => 'COMPLETED',
            'start_date' => '2023-01-01',
        ]);

        // Add Patent
        Patent::create([
            'basic_info_id' => $bi->id,
            'title' => 'Adaptive Neuromorphic Turing Engine',
            'registry_id' => 'PK-PAT-2026-9901',
            'country' => 'Pakistan',
            'date_registered' => '2024-03-15',
            'is_licensed' => true,
            'proof_path' => 'proofs/patent.pdf',
        ]);

        // Add Medal
        AcademicMedal::create([
            'basic_info_id' => $bi->id,
            'degree_title' => 'Ph.D. in Computer Science',
            'medal_type' => 'GOLD',
            'year_awarded' => 2012,
            'transcription_proof_path' => 'proofs/gold_medal.pdf',
        ]);

        // Add Training
        Training::create([
            'basic_info_id' => $bi->id,
            'title' => 'Executive Higher Education Academic Leadership',
            'body' => 'National Academy of Higher Education (NAHE)',
            'proof_path' => 'proofs/nahe_training.pdf',
        ]);

        // Add Event
        ProfessionalEvent::create([
            'basic_info_id' => $bi->id,
            'event_name' => 'International Conference on Frontier AI',
            'event_type' => 'CONFERENCE',
            'event_role' => 'PRESENTER_INTERNATIONAL',
            'paper_title' => 'Universal Computability Limits',
            'organizer' => 'IEEE Computer Society',
            'location' => 'Geneva, Switzerland',
            'start_date' => '2024-05-10',
            'end_date' => '2024-05-12',
            'certificate_proof_path' => 'proofs/conf_cert.pdf',
        ]);

        // Add Referee
        Referee::create([
            'basic_info_id' => $bi->id,
            'name' => 'Prof. Dr. Alonzo Church',
            'designation' => 'Chair of Mathematics',
            'organization' => 'Princeton University',
            'relation' => 'Doctoral Advisor',
            'email' => 'church@princeton.edu',
            'phone' => '+1-609-555-0199',
        ]);

        // 1. Candidate prints own dossier
        $this->actingAs($this->candidate1);
        $candidatePrint = $this->get("/application/{$this->application1->id}/print");
        $candidatePrint->assertStatus(200);

        // Check Requirement 1: User details (Full Name (Username) - Role & Printed/Downloaded on Date Time)
        $candidatePrint->assertSee('Printed / Downloaded by');
        $candidatePrint->assertSee('Turing Alan (candidate1@test.com)');
        $candidatePrint->assertSee('Applicant');
        $candidatePrint->assertSee('Printed / Downloaded on:');

        // Check Requirement 2: Publications, Research Projects, Patents, Medals, Events, Referees
        $candidatePrint->assertSee('Research Publications & Scholarly Output');
        $candidatePrint->assertSee('On Computable Numbers with an Application to the Entscheidungsproblem');
        $candidatePrint->assertSee('Quantum Neural Synthesis Framework');
        $candidatePrint->assertSee('Higher Education Commission NRPU');
        $candidatePrint->assertSee('4,500,000.00');
        $candidatePrint->assertSee('Adaptive Neuromorphic Turing Engine');
        $candidatePrint->assertSee('PK-PAT-2026-9901');
        $candidatePrint->assertSee('GOLD Medal');
        $candidatePrint->assertSee('Executive Higher Education Academic Leadership');
        $candidatePrint->assertSee('International Conference on Frontier AI');
        $candidatePrint->assertSee('Prof. Dr. Alonzo Church');
        $candidatePrint->assertSee('church@princeton.edu');

        // 2. Admin prints candidate dossier
        $this->actingAs($this->admin);
        $adminPrint = $this->get("/application/{$this->application1->id}/print?view=admin");
        $adminPrint->assertStatus(200);

        // Check Admin details
        $adminPrint->assertSee('admin@hirely.test');
        $adminPrint->assertSee('Super Admin');
        $adminPrint->assertSee('Printed / Downloaded on:');
        $adminPrint->assertSee('Quantum Neural Synthesis Framework');
        $adminPrint->assertSee('Adaptive Neuromorphic Turing Engine');
    }

    public function test_merit_list_management_desk_loads_and_switches_stages(): void
    {
        $this->actingAs($this->admin);

        // Load Merit List desk
        Livewire::test(MeritListManagement::class, ['campaignId' => $this->campaign->id])
            ->assertSee('Statutory Recruitment Merit Lists')
            ->assertSee('Turing Alan')
            ->assertSee('John Doe')
            // Step 5: Consolidated Gazette
            ->assertSee('SELECTED FOR APPOINTMENT (RANK 1)')
            ->assertSee('DISQUALIFIED / INELIGIBLE')
            // Switch to Step 2: Scrutiny Shortlist
            ->call('setStage', 'scrutiny')
            ->assertSet('activeStage', 'scrutiny')
            ->assertSee('Step 2: Scrutiny Shortlist')
            // Switch to Step 1: Intake Roster
            ->call('setStage', 'intake')
            ->assertSet('activeStage', 'intake')
            ->assertSee('100099990001');
    }

    public function test_official_merit_list_gazette_print_renders_all_stages(): void
    {
        $this->actingAs($this->admin);

        // 1. Consolidated Gazette Print
        $response = $this->get("/admin/merit-lists/{$this->campaign->id}/print?stage=consolidated");
        $response->assertStatus(200);
        $response->assertSee('Final Consolidated Cumulative Merit List');
        $response->assertSee('Turing Alan');
        $response->assertSee('SELECTED FOR APPOINTMENT (RANK 1)');
        $response->assertSee('Member Incharge');
        $response->assertSee('Vice Chancellor');

        // 2. Scrutiny Shortlist Print
        $scrutinyResponse = $this->get("/admin/merit-lists/{$this->campaign->id}/print?stage=scrutiny");
        $scrutinyResponse->assertStatus(200);
        $scrutinyResponse->assertSee('Scrutiny Committee Shortlist & Eligibility Merit List');
        $scrutinyResponse->assertSee('Candidate does not possess mandatory Terminal Ph.D. Degree');
    }
}
