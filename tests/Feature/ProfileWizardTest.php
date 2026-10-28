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

        $fakePdf = \Illuminate\Http\UploadedFile::fake()->create('transcript.pdf', 500);

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
            ->set('new_degree_proof', $fakePdf)
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
            ->set('new_degree_proof', $fakePdf)
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
            ->set('new_degree_proof', $fakePdf)
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
}
