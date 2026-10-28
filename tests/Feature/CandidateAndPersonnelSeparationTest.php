<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\Application;
use Livewire\Livewire;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\UserManagement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CandidateAndPersonnelSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected $registrar;
    protected $applicant;
    protected $scrutinyMember;
    protected $boardMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->registrar = User::where('email', 'registrar@university.edu')->first();
        $this->applicant = User::where('email', 'applicant1@gmail.com')->first();
        $this->scrutinyMember = User::where('email', 'scrutiny1@university.edu')->first();
        $this->boardMember = User::where('email', 'board1@university.edu')->first();

        // Ensure applicant has basicInfo
        if (!$this->applicant->basicInfo) {
            BasicInfo::create([
                'user_id' => $this->applicant->id,
                'track_type' => 'ACADEMIC',
                'first_name' => 'Ali',
                'last_name' => 'Raza',
                'cnic_number' => '42101-9999999-1',
                'date_of_birth' => '1995-01-01',
                'gender' => 'Male',
                'father_name' => 'Father',
                'marital_status' => 'Single',
                'phone_number' => '+92-300-9999999',
                'permanent_address' => 'Karachi',
                'mailing_address' => 'Karachi',
                'same_address' => true,
                'is_locked' => false,
            ]);
        }
    }

    public function test_candidate_profiles_strictly_excludes_employees_and_committee_members()
    {
        $this->actingAs($this->registrar);

        // Even though scrutiny1, board1, registrar have basicInfo records in DB:
        $this->assertDatabaseHas('basic_infos', ['user_id' => $this->scrutinyMember->id]);
        $this->assertDatabaseHas('basic_infos', ['user_id' => $this->boardMember->id]);
        $this->assertDatabaseHas('basic_infos', ['user_id' => $this->registrar->id]);

        $component = Livewire::test(CandidateProfileManagement::class);

        $profiles = $component->viewData('profiles');
        $profileUserEmails = $profiles->pluck('user.email')->all();

        // Must contain applicant
        $this->assertContains('applicant1@gmail.com', $profileUserEmails);

        // Must NOT contain employees or committee members
        $this->assertNotContains('scrutiny1@university.edu', $profileUserEmails);
        $this->assertNotContains('scrutiny2@university.edu', $profileUserEmails);
        $this->assertNotContains('board1@university.edu', $profileUserEmails);
        $this->assertNotContains('registrar@university.edu', $profileUserEmails);
        $this->assertNotContains('superadmin@university.edu', $profileUserEmails);
    }

    public function test_user_management_categorizes_personnel_and_renders_dossier()
    {
        $this->actingAs($this->registrar);

        // Test filtering by category tabs
        $component = Livewire::test(UserManagement::class);

        // 1. Staff filter
        $component->call('filterCategory', 'STAFF');
        $staffUsers = $component->viewData('users')->pluck('email')->all();
        $this->assertContains('registrar@university.edu', $staffUsers);
        $this->assertNotContains('applicant1@gmail.com', $staffUsers);

        // 2. Committee filter
        $component->call('filterCategory', 'COMMITTEES');
        $committeeUsers = $component->viewData('users')->pluck('email')->all();
        $this->assertContains('scrutiny1@university.edu', $committeeUsers);
        $this->assertContains('board1@university.edu', $committeeUsers);
        $this->assertNotContains('applicant1@gmail.com', $committeeUsers);

        // 3. Reviewer filter
        $component->call('filterCategory', 'REVIEWERS');
        $reviewers = $component->viewData('users')->pluck('email')->all();
        $this->assertContains('dr.sarah.connor@mit.edu', $reviewers);
        $this->assertNotContains('applicant1@gmail.com', $reviewers);

        // 4. View Personnel Dossier Modal
        $component->call('viewPersonnelDetails', $this->scrutinyMember->id)
            ->assertSet('isPersonnelModalOpen', true)
            ->assertSee('Dr. Usman Qureshi')
            ->assertSee('Main Campus / Head Office');
    }
}

