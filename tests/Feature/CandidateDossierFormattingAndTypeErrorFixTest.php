<?php

namespace Tests\Feature;

use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\EntityManagement;
use App\Livewire\Applicant\ProfileWizard;
use App\Models\AcademicDegree;
use App\Models\Advertisement;
use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\WorkExperience;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CandidateDossierFormattingAndTypeErrorFixTest extends TestCase
{
    use RefreshDatabase;

    protected User $registrar;
    protected User $applicant;
    protected BasicInfo $basicInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SpatieRoleAndPermissionSeeder::class);

        $this->registrar = User::factory()->create([
            'email' => 'registrar@university.edu',
        ]);
        $this->registrar->assignRole('registrar');

        $this->applicant = User::factory()->create([
            'email' => 'researcher.applicant@gmail.com',
        ]);
        $this->applicant->assignRole('applicant');

        $this->basicInfo = BasicInfo::create([
            'user_id' => $this->applicant->id,
            'first_name' => 'Ayesha',
            'last_name' => 'Tariq',
            'father_name' => 'Tariq Mehmood',
            'cnic_number' => '17301-9988776-1',
            'date_of_birth' => '1988-04-12',
            'gender' => 'Female',
            'phone_number' => '+923001234567',
            'domicile_district' => 'Mardan',
            'domicile_province' => 'Khyber Pakhtunkhwa',
            'mailing_address' => 'House 42, Sector F, Islamabad',
            'permanent_address' => 'House 42, Sector F, Islamabad',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        // Add degrees
        AcademicDegree::create([
            'basic_info_id' => $this->basicInfo->id,
            'degree_level' => 'PhD',
            'degree_title' => 'PhD in Computer Science',
            'major' => 'Computer Science',
            'board' => 'NUST',
            'institution' => 'National University of Sciences & Technology (NUST)',
            'year' => 2021,
            'gpa' => '3.85',
            'scale' => '4.00',
            'division' => 'First Division',
        ]);

        // Add experiences with diverse string and numeric salary values
        WorkExperience::create([
            'basic_info_id' => $this->basicInfo->id,
            'job_title' => 'Postdoctoral Research Associate',
            'organization' => 'Imperial College London',
            'start_date' => '2021-09-01',
            'end_date' => '2023-08-31',
            'experience_type' => 'TEACHING_RESEARCH',
            'sector' => 'International Academic',
            'salary' => 'GBP 38,000 / year', // String salary with currency and tenure
            'description' => 'Conducted deep learning research on quantum computer architectures.',
        ]);

        WorkExperience::create([
            'basic_info_id' => $this->basicInfo->id,
            'job_title' => 'Assistant Professor',
            'organization' => 'COMSATS University',
            'start_date' => '2023-09-01',
            'end_date' => null,
            'experience_type' => 'TEACHING_RESEARCH',
            'sector' => 'Public Sector Higher Education',
            'salary' => 'PKR 350,000 / month', // String salary with slash
            'description' => 'Tenure track faculty in software engineering.',
        ]);

        WorkExperience::create([
            'basic_info_id' => $this->basicInfo->id,
            'job_title' => 'Visiting Lecturer',
            'organization' => 'Federal Urdu University',
            'start_date' => '2020-01-01',
            'end_date' => '2021-06-30',
            'experience_type' => 'TEACHING',
            'sector' => 'Public Sector Higher Education',
            'salary' => '95000', // Pure numeric string
            'description' => 'Taught algorithms and data structures.',
        ]);

        // Add research project with decimal grant amount
        ResearchProject::create([
            'basic_info_id' => $this->basicInfo->id,
            'project_title' => 'Autonomous Quantum Error Correction using Deep Reinforcement Learning',
            'funding_agency' => 'Higher Education Commission (HEC - NRPU)',
            'grant_amount' => 14500000.00,
            'start_date' => '2024-01-15',
            'role_in_project' => 'Principal Investigator (PI)',
            'turnitin_similarity_percentage' => 4.50,
            'status' => 'ONGOING',
        ]);
    }

    public function test_work_experience_model_formatted_salary_accessor(): void
    {
        $expStringGbp = WorkExperience::where('job_title', 'Postdoctoral Research Associate')->first();
        $this->assertEquals('GBP 38,000 / year', $expStringGbp->formatted_salary);

        $expStringPkr = WorkExperience::where('job_title', 'Assistant Professor')->first();
        $this->assertEquals('PKR 350,000 / month', $expStringPkr->formatted_salary);

        $expNumeric = WorkExperience::where('job_title', 'Visiting Lecturer')->first();
        $this->assertEquals('PKR 95,000.00', $expNumeric->formatted_salary);

        $emptyExp = new WorkExperience(['salary' => null]);
        $this->assertEquals('N/A', $emptyExp->formatted_salary);

        $blankExp = new WorkExperience(['salary' => '']);
        $this->assertEquals('N/A', $emptyExp->formatted_salary);
    }

    public function test_research_project_model_formatted_grant_amount_accessor(): void
    {
        $project = ResearchProject::first();
        $this->assertEquals('PKR 14,500,000.00', $project->formatted_grant_amount);

        $emptyProject = new ResearchProject(['grant_amount' => null]);
        $this->assertEquals('N/A', $emptyProject->formatted_grant_amount);
    }

    public function test_candidate_profile_management_view_details_does_not_throw_number_format_type_error(): void
    {
        $this->actingAs($this->registrar);

        // Opening details modal for candidate with string salaries and decimal project grants must NOT throw TypeError
        Livewire::test(CandidateProfileManagement::class)
            ->call('viewBasicInfoDetails', $this->basicInfo->id)
            ->assertSet('isDetailsOpen', true)
            ->assertSet('selectedRecordId', $this->basicInfo->id)
            ->assertSee('Ayesha')
            ->assertSee('GBP 38,000 / year')
            ->assertSee('PKR 350,000 / month')
            ->assertSee('PKR 95,000.00')
            ->assertSee('Autonomous Quantum Error Correction')
            ->assertSee('PKR 14,500,000.00');
    }

    public function test_entity_management_view_details_does_not_throw_type_error(): void
    {
        $this->actingAs($this->registrar);

        Livewire::test(EntityManagement::class)
            ->call('setTab', 'basic_infos')
            ->call('viewBasicInfoDetails', $this->basicInfo->id)
            ->assertSet('isDetailsOpen', true)
            ->assertSee('GBP 38,000 / year')
            ->assertSee('PKR 350,000 / month');
    }

    public function test_profile_wizard_renders_experiences_with_string_salary_safely(): void
    {
        $this->actingAs($this->applicant);

        Livewire::test(ProfileWizard::class)
            ->set('currentStep', 3)
            ->assertSee('GBP 38,000 / year')
            ->assertSee('PKR 350,000 / month')
            ->assertSee('PKR 95,000.00');
    }
}
