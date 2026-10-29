<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\AdvertisementDateTrack;
use App\Models\BasicInfo;
use App\Models\AcademicDegree;
use App\Models\Application;
use App\Models\SystemSetting;
use App\Services\AcademicEligibilityEvaluator;
use App\Livewire\Admin\AdvertisementManagement;
use App\Livewire\Admin\CampaignCrud;
use App\Livewire\Admin\ApplicationVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;

class AdvertisementAndAcademicEligibilityCriteriaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_advertisement_can_be_published_with_default_current_year_and_validity_dates(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $this->actingAs($registrar);

        $now = now();
        $closing = now()->addDays(30);

        Livewire::test(AdvertisementManagement::class)
            ->call('openCreateModal')
            ->assertSet('year', (int)date('Y'))
            ->set('advertisement_no', 'ADV-2026/01')
            ->set('title', 'Annual Faculty Recruitment 2026')
            ->set('year', (int)date('Y'))
            ->set('valid_from', $now->format('Y-m-d\TH:i'))
            ->set('valid_to', $closing->format('Y-m-d\TH:i'))
            ->set('newspaper_reference', 'Daily Dawn & Daily Jang dated 15-Oct-2026')
            ->set('details', 'Applications are invited from Pakistani nationals for faculty positions.')
            ->call('createAdvertisement')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('advertisements', [
            'advertisement_no' => 'ADV-2026/01',
            'title' => 'Annual Faculty Recruitment 2026',
            'year' => (int)date('Y'),
            'newspaper_reference' => 'Daily Dawn & Daily Jang dated 15-Oct-2026',
            'is_active' => true,
        ]);

        $ad = Advertisement::where('advertisement_no', 'ADV-2026/01')->first();
        $this->assertNotNull($ad);
        $this->assertFalse($ad->is_extended);
        $this->assertFalse($ad->is_expired);
        $this->assertEquals('ACTIVE', $ad->status_badge['label']);
    }

    public function test_advertisement_date_can_be_extended_and_tracks_all_previous_dates_in_database(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $initialClosing = now()->addDays(15);
        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026/02',
            'title' => 'Officer & Staff Recruitment',
            'year' => (int)date('Y'),
            'valid_from' => now()->subDays(5),
            'valid_to' => $initialClosing,
            'is_active' => true,
            'created_by' => $registrar->id,
        ]);

        // Create 2 jobs under this advertisement
        $job1 = Campaign::create([
            'advertisement_id' => $ad->id,
            'job_code' => 'CS-PROF-2026',
            'title' => 'Professor Computer Science',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => $initialClosing,
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $job2 = Campaign::create([
            'advertisement_id' => $ad->id,
            'job_code' => 'REG-OFF-2026',
            'title' => 'Assistant Registrar',
            'department' => 'Administration',
            'track_type' => 'ADMINISTRATIVE',
            'opening_date' => now()->subDays(5),
            'closing_date' => $initialClosing,
            'application_fee' => 2000.00,
            'is_active' => true,
        ]);

        $this->actingAs($registrar);

        // First Extension via Corrigendum 1
        $newClosing1 = now()->addDays(25);
        $track1 = $ad->extendValidity(
            $newClosing1,
            'Extended due to gazetted public holidays and requests from overseas candidates.',
            'Corrigendum No. 1',
            $registrar->id,
            true
        );

        $this->assertEquals(1, $ad->dateTracks()->count());
        $this->assertEquals($initialClosing->toDateTimeString(), $track1->previous_valid_to->toDateTimeString());
        $this->assertEquals($newClosing1->toDateTimeString(), $track1->extended_valid_to->toDateTimeString());
        $this->assertEquals('Corrigendum No. 1', $track1->corrigendum_no);

        // Assert job closing dates were synchronized
        $this->assertEquals($newClosing1->toDateTimeString(), $job1->fresh()->closing_date->toDateTimeString());
        $this->assertEquals($newClosing1->toDateTimeString(), $job2->fresh()->closing_date->toDateTimeString());

        // Second Extension via Corrigendum 2
        $newClosing2 = now()->addDays(40);
        $track2 = $ad->extendValidity(
            $newClosing2,
            'Final 15-day extension approved by the Vice Chancellor / Competent Authority.',
            'Corrigendum No. 2 (Final Notice)',
            $registrar->id,
            true
        );

        $this->assertEquals(2, $ad->dateTracks()->count());
        $this->assertTrue($ad->fresh()->is_extended);
        $this->assertStringContainsString('EXTENDED (x2)', $ad->fresh()->status_badge['label']);

        // Assert audit trail preserved both chronological records
        $history = $ad->fresh()->dateTracks;
        $this->assertEquals($newClosing2->toDateTimeString(), $history[0]->extended_valid_to->toDateTimeString());
        $this->assertEquals('Corrigendum No. 2 (Final Notice)', $history[0]->corrigendum_no);
        $this->assertEquals($newClosing1->toDateTimeString(), $history[1]->extended_valid_to->toDateTimeString());
        $this->assertEquals('Corrigendum No. 1', $history[1]->corrigendum_no);
    }

    public function test_advertisement_management_desk_extension_modal_works(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026/03',
            'title' => 'Engineering Positions',
            'year' => (int)date('Y'),
            'valid_from' => now()->subDay(),
            'valid_to' => now()->addDays(10),
            'is_active' => true,
        ]);

        $this->actingAs($registrar);

        $newDate = now()->addDays(20);

        Livewire::test(AdvertisementManagement::class)
            ->call('openExtendModal', $ad->id)
            ->assertSet('showExtendModal', true)
            ->set('extend_new_valid_to', $newDate->format('Y-m-d\TH:i'))
            ->set('extend_corrigendum_no', 'Corrigendum 1')
            ->set('extend_reason', 'Extended by Registrar Office')
            ->set('extend_sync_jobs', true)
            ->call('extendAdvertisementValidity')
            ->assertHasNoErrors()
            ->assertSet('showExtendModal', false);

        $this->assertEquals(1, $ad->dateTracks()->count());
        $this->assertTrue($ad->fresh()->is_extended);
    }

    public function test_jobs_belong_under_advertisement_and_can_be_mapped(): void
    {
        $ad = Advertisement::create([
            'advertisement_no' => 'ADV-2026/04',
            'title' => 'Medical College Faculty',
            'year' => 2026,
            'valid_from' => now(),
            'valid_to' => now()->addDays(30),
            'is_active' => true,
        ]);

        $job = Campaign::create([
            'advertisement_id' => $ad->id,
            'job_code' => 'MED-ASST-2026',
            'title' => 'Assistant Professor Pathology',
            'department' => 'Pathology',
            'track_type' => 'ACADEMIC',
            'opening_date' => now(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $this->assertEquals($ad->id, $job->advertisement->id);
        $this->assertEquals('ADV-2026/04', $job->advertisement->advertisement_no);
        $this->assertTrue($ad->campaigns->contains($job));
    }

    public function test_academic_criteria_disallows_third_division_when_configured(): void
    {
        $candidate = User::factory()->create();
        $basicInfo = BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Ali',
            'last_name' => 'Khan',
            'date_of_birth' => '1990-01-01',
            'cnic_number' => '37405-1111111-1',
            'track_type' => 'ACADEMIC',
        ]);

        // Degrees: Matric with 3rd Division, Bachelors with 1st Division
        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Matric',
            'degree_title' => 'SSC Science',
            'institution' => 'BISE Rawalpindi',
            'country' => 'Pakistan',
            'major' => 'Science',
            'board' => 'BISE Rawalpindi',
            'year' => 2006,
            'division' => 'Third Division',
            'gpa' => 450,
            'scale' => 1050,
        ]);

        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Bachelor (16 Years) / BS / Professional',
            'degree_title' => 'BS Computer Science',
            'institution' => 'NUST',
            'country' => 'Pakistan',
            'major' => 'Computer Science',
            'board' => 'NUST Islamabad',
            'year' => 2012,
            'division' => 'First Division',
            'gpa' => 3.5,
            'scale' => 4.0,
        ]);

        // Case A: Rule strictly disallowing 3rd division
        $criteriaDisallowThird = [
            'disallow_third_division' => true,
            'max_allowed_third_divisions' => 0,
            'disallow_third_in_terminal' => true,
            'disallow_two_second_divisions' => false,
        ];

        $evaluationA = AcademicEligibilityEvaluator::evaluateCandidate($basicInfo, null, $criteriaDisallowThird);
        $this->assertFalse($evaluationA['is_eligible']);
        $this->assertNotEmpty($evaluationA['violations']);
        $this->assertStringContainsString('3rd Division is not permitted', $evaluationA['violations'][0]);
        $this->assertEquals(1, $evaluationA['third_division_count']);

        // Case B: Rule allowing 1 third division (provided not terminal)
        $criteriaAllowOneThird = [
            'disallow_third_division' => false,
            'max_allowed_third_divisions' => 1,
            'disallow_third_in_terminal' => true,
            'disallow_two_second_divisions' => false,
        ];

        $evaluationB = AcademicEligibilityEvaluator::evaluateCandidate($basicInfo, null, $criteriaAllowOneThird);
        $this->assertTrue($evaluationB['is_eligible']);
        $this->assertEmpty($evaluationB['violations']);
    }

    public function test_academic_criteria_two_second_divisions_not_allowed(): void
    {
        $candidate = User::factory()->create();
        $basicInfo = BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Fatima',
            'last_name' => 'Zahra',
            'date_of_birth' => '1992-05-15',
            'cnic_number' => '37405-2222222-2',
            'track_type' => 'ACADEMIC',
        ]);

        // Matric (2nd Div), Intermediate (2nd Div), Masters (1st Div)
        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Matric',
            'degree_title' => 'SSC Science',
            'institution' => 'BISE Lahore',
            'country' => 'Pakistan',
            'major' => 'Science',
            'board' => 'BISE Lahore',
            'year' => 2008,
            'division' => 'Second Division',
            'gpa' => 580,
            'scale' => 1100,
        ]);

        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Intermediate',
            'degree_title' => 'FSc Pre-Engineering',
            'institution' => 'BISE Lahore',
            'country' => 'Pakistan',
            'major' => 'Pre-Engineering',
            'board' => 'BISE Lahore',
            'year' => 2010,
            'division' => 'Second Division',
            'gpa' => 600,
            'scale' => 1100,
        ]);

        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'MS / M.Phil (18 Years)',
            'degree_title' => 'MS Computer Science',
            'institution' => 'FAST NUCES',
            'country' => 'Pakistan',
            'major' => 'Computer Science',
            'board' => 'FAST NUCES Islamabad',
            'year' => 2016,
            'division' => 'First Division',
            'gpa' => 3.6,
            'scale' => 4.0,
        ]);

        // Rule: Two second divisions NOT allowed
        $criteriaDisallowTwoSeconds = [
            'disallow_third_division' => false,
            'disallow_two_second_divisions' => true,
            'max_allowed_second_divisions' => 1,
            'disallow_third_in_terminal' => true,
        ];

        $evaluation = AcademicEligibilityEvaluator::evaluateCandidate($basicInfo, null, $criteriaDisallowTwoSeconds);
        $this->assertFalse($evaluation['is_eligible']);
        $this->assertEquals(2, $evaluation['second_division_count']);
        $this->assertStringContainsString('Two or more 2nd divisions are not allowed', $evaluation['violations'][0]);
    }

    public function test_academic_criteria_marks_cutoff_xx_out_of_nn_marks_not_allowed(): void
    {
        $candidate = User::factory()->create();
        $basicInfo = BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Usman',
            'last_name' => 'Tariq',
            'date_of_birth' => '1991-03-20',
            'cnic_number' => '37405-3333333-3',
            'track_type' => 'ACADEMIC',
        ]);

        // Degree with 520 out of 1100 marks = 47.27%
        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Intermediate',
            'degree_title' => 'FSc',
            'institution' => 'BISE Multan',
            'country' => 'Pakistan',
            'major' => 'Pre-Medical',
            'board' => 'BISE Multan',
            'year' => 2009,
            'division' => 'Second Division',
            'gpa' => 520,
            'scale' => 1100,
        ]);

        // Terminal degree with 3.2 CGPA
        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Ph.D. / Terminal Degree',
            'degree_title' => 'PhD Physics',
            'institution' => 'QAU Islamabad',
            'country' => 'Pakistan',
            'major' => 'Physics',
            'board' => 'Quaid-i-Azam University',
            'year' => 2018,
            'division' => 'First Division',
            'gpa' => 3.2,
            'scale' => 4.0,
        ]);

        // Rule: Minimum 60% marks across all qualifications
        $criteriaMinMarks = [
            'min_marks_percentage' => 60.0,
            'disallow_third_in_terminal' => true,
        ];

        $evaluation = AcademicEligibilityEvaluator::evaluateCandidate($basicInfo, null, $criteriaMinMarks);
        $this->assertFalse($evaluation['is_eligible']);
        $this->assertStringContainsString('Minimum 60% marks required', $evaluation['violations'][0]);
        $this->assertStringContainsString('520/1100', $evaluation['violations'][0]);
    }

    public function test_campaign_can_define_specific_academic_criteria_that_overrides_global(): void
    {
        // Global setting: allow 3rd division
        AcademicEligibilityEvaluator::setGlobalCriteria([
            'disallow_third_division' => false,
            'max_allowed_third_divisions' => 2,
        ]);

        // Specific Position: Assistant Professor CS disallows 3rd division strictly
        $campaign = Campaign::create([
            'job_code' => 'CS-SPEC-2026',
            'title' => 'Assistant Professor CS (Strict)',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 3000.00,
            'is_active' => true,
            'scoring_rules' => [
                'academic_criteria' => [
                    'disallow_third_division' => true,
                    'max_allowed_third_divisions' => 0,
                    'disallow_two_second_divisions' => true,
                    'scope' => 'POSITION',
                ]
            ]
        ]);

        $effective = $campaign->getEffectiveAcademicCriteria();
        $this->assertTrue($effective['disallow_third_division']);
        $this->assertEquals(0, $effective['max_allowed_third_divisions']);
        $this->assertTrue($effective['disallow_two_second_divisions']);
    }

    public function test_application_verification_autofills_academic_disqualification_reason(): void
    {
        $scrutinyOfficer = User::factory()->create();
        $scrutinyOfficer->assignRole('scrutiny-committee');

        $candidate = User::factory()->create();
        $basicInfo = BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Hamza',
            'last_name' => 'Abbasi',
            'date_of_birth' => '1989-11-12',
            'cnic_number' => '37405-4444444-4',
            'track_type' => 'ACADEMIC',
        ]);

        // Violates 3rd division rule
        AcademicDegree::create([
            'basic_info_id' => $basicInfo->id,
            'degree_level' => 'Matric',
            'degree_title' => 'Matric Arts',
            'institution' => 'BISE Peshawar',
            'country' => 'Pakistan',
            'major' => 'Arts',
            'board' => 'BISE Peshawar',
            'year' => 2005,
            'division' => 'Third Division',
            'gpa' => 420,
            'scale' => 1050,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'MATH-LECT-2026',
            'title' => 'Lecturer Mathematics',
            'department' => 'Mathematics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 2000.00,
            'is_active' => true,
            'scoring_rules' => [
                'academic_criteria' => [
                    'disallow_third_division' => true,
                ]
            ]
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($scrutinyOfficer);

        Livewire::test(ApplicationVerification::class, ['id' => $application->id])
            ->call('autoFillAcademicDisqualification')
            ->assertSet('showDisqualificationModal', true)
            ->assertSet('disqualification_reason', function ($reason) {
                return str_contains($reason, 'Statutory Academic Eligibility Violations')
                    && str_contains($reason, '3rd Division is not permitted');
            })
            ->assertSee('Statutory Academic Eligibility Violations')
            ->assertSee('3rd Division is not permitted');
    }
}
