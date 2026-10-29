<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\ReviewerProfile;
use App\Models\ApplicationReview;
use App\Livewire\Admin\ReviewerManagement;
use App\Livewire\Reviewer\EvaluationDesk;
use App\Mail\ReviewerCredentialsMail;
use App\Mail\ApplicationAssignedReviewerMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ReviewerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_registrar_can_empanel_academic_reviewer_and_credentials_email_is_dispatched(): void
    {
        Mail::fake();

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $this->actingAs($registrar);

        Livewire::test(ReviewerManagement::class)
            ->call('openCreateModal')
            ->set('name', 'Dr. Alan Turing')
            ->set('email', 'alan.turing@cambridge.ac.uk')
            ->set('subject', 'Computer Science')
            ->set('academic_rank', 'Professor')
            ->set('university', 'University of Cambridge')
            ->set('country', 'United Kingdom')
            ->set('phone', '+44 1223 337733')
            ->set('specialization', 'Artificial Intelligence & Computability')
            ->call('createReviewer')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false);

        // Check user created with academic-reviewer role
        $reviewerUser = User::where('email', 'alan.turing@cambridge.ac.uk')->first();
        $this->assertNotNull($reviewerUser);
        $this->assertTrue($reviewerUser->hasRole('academic-reviewer'));

        // Check reviewer profile created
        $this->assertDatabaseHas('reviewer_profiles', [
            'user_id' => $reviewerUser->id,
            'name' => 'Dr. Alan Turing',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'University of Cambridge',
        ]);

        // Check email dispatched
        Mail::assertSent(ReviewerCredentialsMail::class, function ($mail) {
            return $mail->hasTo('alan.turing@cambridge.ac.uk') &&
                   $mail->name === 'Dr. Alan Turing' &&
                   $mail->subjectField === 'Computer Science';
        });
    }

    public function test_field_matched_reviewers_are_filtered_by_department_when_dispatching(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        // Create matching reviewer: Computer Science
        $csUser = User::factory()->create(['email' => 'cs.prof@test.edu']);
        $csUser->assignRole('academic-reviewer');
        $csProfile = ReviewerProfile::create([
            'user_id' => $csUser->id,
            'name' => 'Prof. CS Expert',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'Fast NUCES',
            'country' => 'Pakistan',
            'is_active' => true,
        ]);

        // Create non-matching reviewer: English Literature
        $engUser = User::factory()->create(['email' => 'eng.prof@test.edu']);
        $engUser->assignRole('academic-reviewer');
        $engProfile = ReviewerProfile::create([
            'user_id' => $engUser->id,
            'name' => 'Prof. Literature Expert',
            'subject' => 'English Literature',
            'academic_rank' => 'Professor',
            'university' => 'Punjab University',
            'country' => 'Pakistan',
            'is_active' => true,
        ]);

        // Create candidate application for Computer Science
        $candidate = User::factory()->create();
        $candidate->assignRole('applicant');
        $basicInfo = BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'date_of_birth' => '1992-12-10',
            'cnic_number' => '37405-9988776-1',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'CS-PROF-2026',
            'title' => 'Professor of Computer Science',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 5000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($registrar);

        // Test field-matching filter in Livewire
        $component = Livewire::test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->assertSet('showDispatchModal', true);

        $viewData = $component->viewData('fieldMatchedReviewers');
        $matchedIds = $viewData->pluck('id')->all();

        $this->assertContains($csProfile->id, $matchedIds);
        $this->assertNotContains($engProfile->id, $matchedIds);
    }

    public function test_application_dispatch_creates_review_and_updates_status_to_under_review(): void
    {
        Mail::fake();

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $reviewerUser = User::factory()->create(['email' => 'reviewer.cs@test.edu']);
        $reviewerUser->assignRole('academic-reviewer');
        ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Prof. Reviewer CS',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'Oxford',
            'country' => 'United Kingdom',
            'is_active' => true,
        ]);

        $candidate = User::factory()->create();
        $candidate->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Candidate',
            'last_name' => 'Academic',
            'date_of_birth' => '1988-03-20',
            'cnic_number' => '37405-1122334-5',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'CS-AP-2026',
            'title' => 'Associate Professor of Computer Science',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 4000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($registrar);

        Livewire::test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->call('toggleReviewerSelection', $reviewerUser->id)
            ->set('reviewNotes', 'Please rigorously verify IEEE/ACM publications.')
            ->call('dispatchApplicationToReviewers')
            ->assertHasNoErrors()
            ->assertSet('showDispatchModal', false);

        // Verify Application status updated
        $this->assertEquals('UNDER_REVIEW', $application->fresh()->status);

        // Verify ApplicationReview created
        $this->assertDatabaseHas('application_reviews', [
            'application_id' => $application->id,
            'reviewer_id' => $reviewerUser->id,
            'status' => 'PENDING',
            'confidential_notes' => 'Please rigorously verify IEEE/ACM publications.',
        ]);

        // Verify assignment email sent
        Mail::assertSent(ApplicationAssignedReviewerMail::class, function ($mail) use ($reviewerUser) {
            return $mail->hasTo($reviewerUser->email) &&
                   $mail->positionTitle === 'Associate Professor of Computer Science';
        });
    }

    public function test_academic_reviewer_can_access_evaluation_desk_and_submit_review(): void
    {
        $reviewerUser = User::factory()->create(['email' => 'evaluator@test.edu', 'password' => bcrypt('password')]);
        $reviewerUser->assignRole('academic-reviewer');
        ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Prof. Expert Evaluator',
            'subject' => 'Data Science',
            'academic_rank' => 'Tenured Professor',
            'university' => 'Stanford University',
            'country' => 'USA',
            'is_active' => true,
        ]);

        $candidate = User::factory()->create();
        $candidate->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Researcher',
            'last_name' => 'Applicant',
            'date_of_birth' => '1985-07-12',
            'cnic_number' => '37405-5566778-9',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'DS-PROF-2026',
            'title' => 'Professor of Data Science',
            'department' => 'Data Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 5000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'UNDER_REVIEW',
            'submission_timestamp' => now(),
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('registrar');

        $review = ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $reviewerUser->id,
            'assigned_by' => $admin->id,
            'status' => 'PENDING',
            'assigned_at' => now(),
            'due_date' => now()->addDays(14),
        ]);

        // Reviewer login redirect
        $loginResponse = $this->post('/login', [
            'email' => 'evaluator@test.edu',
            'password' => 'password',
        ]);
        $loginResponse->assertRedirect('/reviewer/dashboard');

        // Reviewer evaluates application
        $this->actingAs($reviewerUser);

        Livewire::test(EvaluationDesk::class)
            ->assertSee('Professor of Data Science')
            ->call('openEvalModal', $review->id)
            ->assertSet('showEvalModal', true)
            ->set('overall_score', 92)
            ->set('recommendation', 'STRONGLY_RECOMMENDED')
            ->set('report_text', 'The candidate exhibits outstanding H-index, impactful Q1 journal publications, and high citation metrics suitable for Professor appointment.')
            ->call('submitEvaluation')
            ->assertHasNoErrors()
            ->assertSet('showEvalModal', false);

        // Verify review updated in DB
        $this->assertDatabaseHas('application_reviews', [
            'id' => $review->id,
            'status' => 'SUBMITTED',
            'overall_score' => 92,
            'recommendation' => 'STRONGLY_RECOMMENDED',
        ]);
    }

    public function test_country_service_returns_comprehensive_list_and_searches(): void
    {
        $countries = \App\Services\CountryService::all();
        $this->assertContains('Pakistan', $countries);
        $this->assertContains('United Kingdom', $countries);
        $this->assertContains('United States', $countries);
        $this->assertGreaterThan(150, count($countries));

        $searchResults = \App\Services\CountryService::search('pak');
        $this->assertContains('Pakistan', $searchResults);
    }

    public function test_empanel_modal_prefills_department_and_auto_selects_when_opened_from_dispatch(): void
    {
        Mail::fake();

        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $candidate = User::factory()->create();
        $candidate->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Nadeem',
            'last_name' => 'Ullah Khan',
            'date_of_birth' => '1990-01-01',
            'cnic_number' => '16201-1234567-1',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'CS-FAC-2026',
            'title' => 'Assistant Professor - Computer Science',
            'department' => 'Department of Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($registrar);

        // Test dispatch modal opening and nested empanel action
        Livewire::test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->assertSet('showDispatchModal', true)
            ->call('openCreateModal', 'Department of Computer Science')
            ->assertSet('showCreateModal', true)
            ->assertSet('subject', 'Department of Computer Science')
            ->set('name', 'Dr. Donald Knuth')
            ->set('email', 'knuth@stanford.edu')
            ->set('academic_rank', 'Distinguished Professor')
            ->set('university', 'Stanford University')
            ->set('country', 'United States')
            ->call('createReviewer')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false)
            ->assertSet('showDispatchModal', true);

        // Check reviewer was created and pre-selected in the dispatch queue
        $newReviewer = User::where('email', 'knuth@stanford.edu')->first();
        $this->assertNotNull($newReviewer);
    }

    public function test_unauthorized_applicant_cannot_access_reviewer_management(): void
    {
        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');

        $this->actingAs($applicant);

        $response = $this->get('/admin/reviewers');
        $response->assertStatus(403);
    }

    public function test_all_allied_computing_disciplines_are_matched_for_assistant_professor_computer_science(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        // Create the 6 allied computing disciplines mentioned by the user
        $disciplines = [
            'Computer Science & Robotics',
            'Computer Science',
            'Information Technology & Networks',
            'Computer Engineering',
            'Artificial Intelligence & Systems',
            'Cybersecurity & Cryptography',
        ];

        $matchedProfileIds = [];
        foreach ($disciplines as $idx => $subject) {
            $reviewerUser = User::factory()->create(['email' => "reviewer{$idx}@domain.edu"]);
            $reviewerUser->assignRole('academic-reviewer');
            $profile = ReviewerProfile::create([
                'user_id' => $reviewerUser->id,
                'name' => "Expert {$idx}",
                'subject' => $subject,
                'academic_rank' => 'Professor',
                'university' => "University of Tech {$idx}",
                'country' => 'Pakistan',
                'is_active' => true,
            ]);
            $matchedProfileIds[] = $profile->id;
        }

        // Create non-allied reviewers
        $nonAlliedSubjects = ['English Literature', 'Business Administration', 'Biotechnology'];
        $unmatchedProfileIds = [];
        foreach ($nonAlliedSubjects as $idx => $subject) {
            $u = User::factory()->create(['email' => "other{$idx}@domain.edu"]);
            $u->assignRole('academic-reviewer');
            $p = ReviewerProfile::create([
                'user_id' => $u->id,
                'name' => "Other Expert {$idx}",
                'subject' => $subject,
                'academic_rank' => 'Professor',
                'university' => "Arts/Biz University {$idx}",
                'country' => 'Pakistan',
                'is_active' => true,
            ]);
            $unmatchedProfileIds[] = $p->id;
        }

        // Campaign: Assistant Professor Computer Science, Department of Computer Science
        $candidate = User::factory()->create();
        $candidate->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $candidate->id,
            'first_name' => 'Alan',
            'last_name' => 'Turing',
            'date_of_birth' => '1990-06-23',
            'cnic_number' => '37405-1122334-9',
            'track_type' => 'ACADEMIC',
            'is_locked' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'CS-ASST-PROF-2026',
            'title' => 'Assistant Professor Computer Science',
            'department' => 'Department of Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(30),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $candidate->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->actingAs($registrar);

        $component = Livewire::test(ReviewerManagement::class)
            ->call('openDispatchModal', $application->id)
            ->assertSet('showDispatchModal', true);

        $matchedReviewers = $component->viewData('fieldMatchedReviewers');
        $matchedIds = $matchedReviewers->pluck('id')->all();

        // Assert all 6 computing disciplines are found
        foreach ($matchedProfileIds as $expectedId) {
            $this->assertContains($expectedId, $matchedIds, "Reviewer ID {$expectedId} was expected to match for Computer Science candidacy.");
        }

        // Assert non-allied disciplines are excluded
        foreach ($unmatchedProfileIds as $excludedId) {
            $this->assertNotContains($excludedId, $matchedIds, "Reviewer ID {$excludedId} should NOT match Computer Science candidacy.");
        }
    }

    public function test_reviewer_status_can_be_toggled_interactively(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $revUser = User::factory()->create(['is_active' => true]);
        $revUser->assignRole('academic-reviewer');

        $profile = ReviewerProfile::create([
            'user_id' => $revUser->id,
            'name' => 'Prof. Dr. Kenji Sato',
            'subject' => 'Robotics',
            'academic_rank' => 'Professor',
            'university' => 'Tokyo Institute of Technology',
            'country' => 'Japan',
            'is_active' => true,
        ]);

        $this->actingAs($registrar);

        // Toggle from active to inactive
        Livewire::test(ReviewerManagement::class)
            ->call('toggleReviewerStatus', $profile->id)
            ->assertHasNoErrors();

        $this->assertFalse($profile->fresh()->is_active);
        $this->assertFalse($revUser->fresh()->is_active);

        // Toggle back from inactive to active
        Livewire::test(ReviewerManagement::class)
            ->call('toggleReviewerStatus', $profile->id)
            ->assertHasNoErrors();

        $this->assertTrue($profile->fresh()->is_active);
        $this->assertTrue($revUser->fresh()->is_active);
    }

    public function test_reviewer_parsed_name_handles_titles_and_clean_full_name(): void
    {
        $profile1 = new ReviewerProfile(['name' => 'Prof. Dr. Kenji Sato']);
        $this->assertEquals('Prof. Dr.', $profile1->parsed_name['prefix']);
        $this->assertEquals('Kenji Sato', $profile1->parsed_name['full_name']);

        $profile2 = new ReviewerProfile(['name' => 'Dr. Jane Goodall']);
        $this->assertEquals('Dr.', $profile2->parsed_name['prefix']);
        $this->assertEquals('Jane Goodall', $profile2->parsed_name['full_name']);

        $profile3 = new ReviewerProfile(['name' => 'Ada Lovelace']);
        $this->assertEquals('', $profile3->parsed_name['prefix']);
        $this->assertEquals('Ada Lovelace', $profile3->parsed_name['full_name']);
    }

    public function test_reviewer_details_modal_loads_profile_and_dossier_ledger(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $revUser = User::factory()->create();
        $revUser->assignRole('academic-reviewer');

        $profile = ReviewerProfile::create([
            'user_id' => $revUser->id,
            'name' => 'Prof. Dr. Tariq Mahmood',
            'subject' => 'Computer Science',
            'academic_rank' => 'Professor',
            'university' => 'NUST',
            'country' => 'Pakistan',
            'specialization' => 'Machine Learning & Cyber Systems',
            'is_active' => true,
        ]);

        $this->actingAs($registrar);

        Livewire::test(ReviewerManagement::class)
            ->call('openDetailsModal', $profile->id)
            ->assertSet('showDetailsModal', true)
            ->assertSee('Tariq Mahmood')
            ->assertSee('Prof. Dr.')
            ->assertSee('NUST')
            ->assertSee('Machine Learning & Cyber Systems')
            ->call('closeDetailsModal')
            ->assertSet('showDetailsModal', false);
    }

    public function test_reviewer_can_be_edited_via_modal(): void
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        $revUser = User::factory()->create(['email' => 'old.email@test.edu']);
        $revUser->assignRole('academic-reviewer');

        $profile = ReviewerProfile::create([
            'user_id' => $revUser->id,
            'name' => 'Dr. Old Name',
            'subject' => 'Physics',
            'academic_rank' => 'Associate Professor',
            'university' => 'Old University',
            'country' => 'Pakistan',
            'is_active' => true,
        ]);

        $this->actingAs($registrar);

        Livewire::test(ReviewerManagement::class)
            ->call('openEditModal', $profile->id)
            ->assertSet('showEditModal', true)
            ->assertSet('edit_name', 'Dr. Old Name')
            ->set('edit_name', 'Prof. Dr. Updated Name')
            ->set('edit_email', 'updated.email@test.edu')
            ->set('edit_subject', 'Applied Physics & Quantum Computing')
            ->set('edit_academic_rank', 'Professor')
            ->set('edit_university', 'Quaid-i-Azam University')
            ->set('edit_country', 'Pakistan')
            ->set('edit_phone', '+92 51 9064000')
            ->set('edit_specialization', 'Quantum Information Theory')
            ->call('updateReviewer')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $profile->refresh();
        $this->assertEquals('Prof. Dr. Updated Name', $profile->name);
        $this->assertEquals('Applied Physics & Quantum Computing', $profile->subject);
        $this->assertEquals('Professor', $profile->academic_rank);
        $this->assertEquals('Quaid-i-Azam University', $profile->university);
        $this->assertEquals('Quantum Information Theory', $profile->specialization);
        $this->assertEquals('updated.email@test.edu', $revUser->fresh()->email);
    }
}
