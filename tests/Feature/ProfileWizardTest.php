<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\BasicInfo;
use App\Livewire\Applicant\ProfileWizard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_wizard_validation_and_features(): void
    {
        // Seed roles & permissions
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        // Create user and campaign
        $user = User::factory()->create();
        $user->assignRole('applicant');

        $campaign = Campaign::create([
            'job_code' => 'TEST-001',
            'title' => 'Test Associate Professor',
            'track_type' => 'ACADEMIC',
            'department' => 'Computer Science',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(10),
            'application_fee' => 1500.00,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Test Livewire component
        Livewire::test(ProfileWizard::class)
            ->set('campaign_id', $campaign->id)
            ->set('first_name', 'Zafar')
            ->set('last_name', '') // optional last name
            ->set('cnic_number', '3740512345671') // without dashes
            ->set('date_of_birth', '1995-05-15')
            ->set('father_name', 'Muhammad Ali')
            ->set('phone_number', '03001234567')
            ->set('place_of_birth', 'Rawalpindi')
            ->set('domicile', 'Punjab (Rawalpindi)')
            ->set('permanent_address', 'House 123, Street 4, Islamabad')
            ->set('mailing_address', 'Old Address')
            ->set('same_address', true) // sync addresses
            // Trigger permanent address update to sync
            ->set('permanent_address', 'House 123, Street 4, Islamabad Updated')
            ->call('nextStep')
            ->assertHasNoErrors();

        // Retrieve created basicInfo
        $basicInfo = BasicInfo::where('user_id', $user->id)->first();
        $this->assertNotNull($basicInfo);
        $this->assertEquals('Zafar', $basicInfo->first_name);
        $this->assertNull($basicInfo->last_name);
        // Assert CNIC is formatted with dashes
        $this->assertEquals('37405-1234567-1', $basicInfo->cnic_number);

        $this->assertEquals('House 123, Street 4, Islamabad Updated', $basicInfo->permanent_address);
        $this->assertEquals('House 123, Street 4, Islamabad Updated', $basicInfo->mailing_address);
    }

    public function test_sequential_qualification_progression(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('applicant');

        // Create basicInfo for user so we can save qualifications
        BasicInfo::create([
            'user_id' => $user->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Test',
            'last_name' => 'User',
            'cnic_number' => '12345-1234567-1',
            'date_of_birth' => '1990-01-01',
        ]);

        $this->actingAs($user);

        Livewire::test(ProfileWizard::class)
            // 1. Initial State (no degrees added)
            ->assertSet('new_degree_level', 'SSC')
            ->assertSet('new_degree_country', 'Pakistan')

            // 2. Add SSC
            ->set('new_degree_title', 'Matriculation')
            ->set('new_degree_major', 'Science')
            ->set('new_degree_board', 'BISE Rawalpindi')
            ->set('new_degree_inst', 'Government High School')
            ->set('new_degree_year', 2010)
            ->set('new_degree_gpa', '850')
            ->set('new_degree_scale', '1050')
            ->set('new_degree_division', 'First Division')
            ->set('new_degree_proof', \Illuminate\Http\UploadedFile::fake()->create('transcript_ssc.pdf', 500))
            ->call('addDegree')
            ->assertHasNoErrors()

            // 3. Dropdown should automatically advance to HSSC
            ->assertSet('new_degree_level', 'HSSC')

            // 4. Add HSSC
            ->set('new_degree_title', 'FSc Pre-Engineering')
            ->set('new_degree_major', 'Pre-Engineering')
            ->set('new_degree_board', 'BISE Rawalpindi')
            ->set('new_degree_inst', 'Government College')
            ->set('new_degree_year', 2012)
            ->set('new_degree_gpa', '910')
            ->set('new_degree_scale', '1100')
            ->set('new_degree_division', 'First Division')
            ->set('new_degree_proof', \Illuminate\Http\UploadedFile::fake()->create('transcript_hssc.pdf', 500))
            ->call('addDegree')
            ->assertHasNoErrors()

            // 5. Dropdown should present options for 14_YEARS or 16_YEARS
            ->assertSet('new_degree_level', '14_YEARS') // first option of ['14_YEARS', '16_YEARS']

            // 6. Add 16_YEARS degree
            ->set('new_degree_level', '16_YEARS')
            ->set('new_degree_title', 'BS Computer Science')
            ->set('new_degree_major', 'Computer Science')
            ->set('new_degree_board', 'NUST')
            ->set('new_degree_inst', 'SEECS')
            ->set('new_degree_year', 2016)
            ->set('new_degree_gpa', '3.8')
            ->set('new_degree_scale', '4.0')
            ->set('new_degree_division', 'First Division')
            ->set('new_degree_proof', \Illuminate\Http\UploadedFile::fake()->create('transcript_bs.pdf', 500))
            ->set('new_degree_country', 'Pakistan')
            ->call('addDegree')
            ->assertHasNoErrors()

            // 7. Dropdown should present 18_YEARS (or PHD)
            ->assertSet('new_degree_level', '18_YEARS')

            // 8. Remove the 16_YEARS degree (ID 3)
            ->call('removeDegree', 3)

            // 9. Dropdown should reset to 14_YEARS (first available option of ['14_YEARS', '16_YEARS'])
            ->assertSet('new_degree_level', '14_YEARS');
    }

    public function test_phone_number_validation_rules(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('applicant');

        $campaign = Campaign::create([
            'job_code' => 'TEST-PHONE',
            'title' => 'Test Lecturer',
            'track_type' => 'ACADEMIC',
            'department' => 'CS',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(10),
            'application_fee' => 1000.00,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Invalid phone number format (text or invalid length)
        Livewire::test(ProfileWizard::class)
            ->set('campaign_id', $campaign->id)
            ->set('first_name', 'Ali')
            ->set('cnic_number', '37405-1234567-1')
            ->set('date_of_birth', '1995-05-15')
            ->set('father_name', 'Muhammad Ali')
            ->set('permanent_address', 'Address')
            ->set('mailing_address', 'Address')
            ->set('place_of_birth', 'Rawalpindi')
            ->set('phone_number', 'invalid-phone')
            ->call('nextStep')
            ->assertHasErrors(['phone_number']);

        // Valid phone number format (Pakistani mobile format)
        Livewire::test(ProfileWizard::class)
            ->set('campaign_id', $campaign->id)
            ->set('first_name', 'Ali')
            ->set('cnic_number', '37405-1234567-1')
            ->set('date_of_birth', '1995-05-15')
            ->set('father_name', 'Muhammad Ali')
            ->set('permanent_address', 'Address')
            ->set('mailing_address', 'Address')
            ->set('place_of_birth', 'Rawalpindi')
            ->set('domicile', 'Punjab')
            ->set('phone_number', '0300-1234567')
            ->set('secondary_phone', '0300-7654321')
            ->call('nextStep')
            ->assertHasNoErrors(['phone_number', 'secondary_phone']);
    }

    public function test_transcript_proof_optional_and_mandatory(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('applicant');

        BasicInfo::create([
            'user_id' => $user->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Proof',
            'last_name' => 'Tester',
            'cnic_number' => '12345-1234567-2',
            'date_of_birth' => '1990-01-01',
        ]);

        $this->actingAs($user);

        // 1. Campaign with require_transcript_proof = false
        $campaignOptional = Campaign::create([
            'job_code' => 'TEST-OPTIONAL',
            'title' => 'Support Staff',
            'track_type' => 'ACADEMIC',
            'department' => 'Admin',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(10),
            'application_fee' => 500.00,
            'is_active' => true,
            'require_transcript_proof' => false,
        ]);

        Livewire::test(ProfileWizard::class)
            ->set('campaign_id', $campaignOptional->id)
            ->set('new_degree_level', 'SSC')
            ->set('new_degree_title', 'Matric')
            ->set('new_degree_major', 'Science')
            ->set('new_degree_board', 'BISE')
            ->set('new_degree_inst', 'School')
            ->set('new_degree_year', 2010)
            ->set('new_degree_gpa', '800')
            ->set('new_degree_scale', '1050')
            ->set('new_degree_division', 'First Division')
            ->set('new_degree_country', 'Pakistan')
            ->set('new_degree_proof', null) // No proof uploaded
            ->call('addDegree')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('academic_degrees', [
            'degree_title' => 'Matric',
            'proof_path' => null,
        ]);
    }

    public function test_step4_data_persistence_publications_and_milestones(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('applicant');

        $basicInfo = BasicInfo::create([
            'user_id' => $user->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Research',
            'last_name' => 'Scholar',
            'cnic_number' => '12345-1234567-3',
            'date_of_birth' => '1990-01-01',
        ]);

        $this->actingAs($user);

        // 1. Add Academic Publication
        $component = Livewire::test(ProfileWizard::class)
            ->set('currentStep', 4)
            ->set('step4_active_tab', 'ACADEMIC')
            ->set('new_pub_title', 'Deep Learning in Distributed High-Load Systems')
            ->set('new_pub_journal', 'IEEE Transactions on Software Engineering')
            ->set('new_pub_issn', '0098-5589')
            ->set('new_pub_category', 'W')
            ->set('new_pub_impact', 3.85)
            ->set('new_pub_year', 2023)
            ->set('new_pub_authors', 'R. Scholar, K. Khan')
            ->set('new_pub_doi', '10.1109/TSE.2023.123456')
            ->call('addPublication')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('publications', [
            'basic_info_id' => $basicInfo->id,
            'title' => 'Deep Learning in Distributed High-Load Systems',
            'journal_name' => 'IEEE Transactions on Software Engineering',
            'issn_print' => '0098-5589',
        ]);

        // 2. Switch tab to ADMINISTRATIVE and add Governance Milestone
        $component->call('setStep4Tab', 'ADMINISTRATIVE')
            ->assertSet('step4_active_tab', 'ADMINISTRATIVE')
            ->set('new_ms_title', 'Enterprise Multi-Campus ERP Overhaul')
            ->set('new_ms_budget', 25.5)
            ->set('new_ms_year', 2022)
            ->set('new_ms_description', 'Direct oversight of campus automation rollout across 4 faculties.')
            ->call('addMilestone')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('admin_milestones', [
            'basic_info_id' => $basicInfo->id,
            'title' => 'Enterprise Multi-Campus ERP Overhaul',
            'budget' => 25.50,
            'year' => 2022,
        ]);

        // 3. Switch tab to SUPPORT and add Tool/Skill
        $component->call('setStep4Tab', 'SUPPORT')
            ->assertSet('step4_active_tab', 'SUPPORT')
            ->set('new_tool_name', 'High Voltage Switchgear Automation')
            ->set('new_tool_proficiency', 'Expert')
            ->set('new_tool_experience', 7)
            ->call('addTool')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('support_tools', [
            'basic_info_id' => $basicInfo->id,
            'tool_name' => 'High Voltage Switchgear Automation',
            'proficiency_level' => 'Expert',
            'experience_years' => '7',
        ]);

        // 4. Delete publication and assert removal
        $pub = \App\Models\Publication::where('title', 'Deep Learning in Distributed High-Load Systems')->first();
        $this->assertNotNull($pub);
        $component->call('removePublication', $pub->id);
        $this->assertDatabaseMissing('publications', [
            'id' => $pub->id,
        ]);
    }

    public function test_campaign_selection_moved_to_step8_and_generates_challan(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('applicant');

        $campaign = Campaign::create([
            'job_code' => 'PROF-001',
            'title' => 'Professor of Computer Science',
            'track_type' => 'ACADEMIC',
            'department' => 'Computer Science',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(15),
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        // Step 1: Does NOT require campaign_id anymore
        $component = Livewire::test(ProfileWizard::class)
            ->set('first_name', 'Hamza')
            ->set('last_name', 'Tariq')
            ->set('cnic_number', '37405-1234567-9')
            ->set('date_of_birth', '1985-04-20')
            ->set('father_name', 'Tariq Mehmood')
            ->set('phone_number', '0300-9876543')
            ->set('place_of_birth', 'Islamabad')
            ->set('domicile', 'Punjab')
            ->set('permanent_address', 'F-7/2 Islamabad')
            ->set('mailing_address', 'F-7/2 Islamabad')
            ->call('nextStep')
            ->assertHasNoErrors();

        $basicInfo = BasicInfo::where('user_id', $user->id)->first();
        $this->assertNotNull($basicInfo);
        $this->assertEquals('Hamza', $basicInfo->first_name);

        // Navigate to Step 8
        $component->set('currentStep', 8);

        // Calling generateChallan without campaign fails validation
        $component->set('campaign_id', null)
            ->call('generateChallan')
            ->assertHasErrors(['campaign_id']);

        // Now select campaign and generate challan
        $component->set('campaign_id', $campaign->id)
            ->call('generateChallan')
            ->assertHasNoErrors();

        // Verify basicInfo is locked
        $basicInfo->refresh();
        $this->assertTrue($basicInfo->is_locked);
        $this->assertEquals('ACADEMIC', $basicInfo->track_type);

        // Verify application was submitted
        $application = \App\Models\Application::where('user_id', $user->id)->where('campaign_id', $campaign->id)->first();
        $this->assertNotNull($application);
        $this->assertEquals('SUBMITTED', $application->status);

        // Verify billing ledger has consumer ID and amount
        $ledger = \App\Models\BillingLedger::where('application_id', $application->id)->first();
        $this->assertNotNull($ledger);
        $this->assertEquals(3000.00, $ledger->amount);
        $this->assertStringStartsWith('1000', $ledger->consumer_id);
        $this->assertEquals(12, strlen($ledger->consumer_id));
    }

    public function test_step4_tabs_and_governance_milestone_layout(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('applicant');

        $basicInfo = BasicInfo::create([
            'user_id' => $user->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Sara',
            'last_name' => 'Ahmed',
            'father_name' => 'Ahmed Khan',
            'gender' => 'Female',
            'date_of_birth' => '1990-05-10',
            'cnic_number' => '35201-9988776-2',
            'contact_number' => '+92-300-1234567',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ProfileWizard::class)
            ->set('currentStep', 4)
            ->assertSee('Academic Publications')
            ->assertSee('Governance Milestones')
            ->assertSee('Vocational Tools')
            ->call('setStep4Tab', 'ADMINISTRATIVE')
            ->assertSee('step4-tab-administrative')
            ->assertSee('Record Governance Milestone / Budget Managed')
            ->assertSee('Milestone Title *')
            ->assertSee('Narrative / Impact Statement')
            ->assertSee('Budget Managed (Millions PKR)')
            ->assertSee('Execution Year')
            ->assertSee('Supporting Verification File (PDF)')
            ->assertSee('Governance Milestones Log');

        $component->call('setStep4Tab', 'SUPPORT')
            ->assertSee('step4-tab-support')
            ->assertSee('Register Technical Tool / Vocational Skill')
            ->assertSee('Capabilities Log');

        $component->call('setStep4Tab', 'ACADEMIC')
            ->assertSee('step4-tab-academic')
            ->assertSee('Record Peer-Reviewed Publication')
            ->assertSee('Academic Publications Log');
    }
}
