<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\ReviewerProfile;
use App\Models\BillingLedger;
use App\Models\CommitteeConstitution;
use App\Models\RecruitmentProcessFlow;
use App\Livewire\UniversalSearchPalette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class UniversalSearchPaletteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);
    }

    public function test_palette_component_mounts_and_toggles_state()
    {
        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');

        $this->actingAs($applicant);

        Livewire::test(UniversalSearchPalette::class)
            ->assertSet('isOpen', false)
            ->call('openPalette')
            ->assertSet('isOpen', true)
            ->set('query', 'professor')
            ->call('closePalette')
            ->assertSet('isOpen', false)
            ->assertSet('query', '')
            ->dispatch('open-search-palette')
            ->assertSet('isOpen', true);
    }

    public function test_applicant_only_sees_candidate_space_and_their_own_applications()
    {
        $applicant1 = User::factory()->create(['email' => 'applicant1@university.edu']);
        $applicant1->assignRole('applicant');

        $applicant2 = User::factory()->create(['email' => 'applicant2@university.edu']);
        $applicant2->assignRole('applicant');

        $activeCampaign = Campaign::create([
            'job_code' => 'CS-PROF-01',
            'title' => 'Professor of Quantum Computing',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addMonth(),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $closedCampaign = Campaign::create([
            'job_code' => 'PHYS-LEC-09',
            'title' => 'Lecturer in Astro Physics',
            'department' => 'Physics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subMonth(),
            'closing_date' => now()->subDay(),
            'application_fee' => 2000,
            'is_active' => false,
        ]);

        $app1 = Application::create([
            'user_id' => $applicant1->id,
            'campaign_id' => $activeCampaign->id,
            'status' => 'SUBMITTED',
        ]);

        $app2 = Application::create([
            'user_id' => $applicant2->id,
            'campaign_id' => $activeCampaign->id,
            'status' => 'APPROVED',
        ]);

        // Reviewer & Payment records that applicant must NEVER see
        $reviewerUser = User::factory()->create(['email' => 'reviewer.secret@university.edu']);
        ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Dr. Secret Reviewer',
            'subject' => 'Cryptography',
            'academic_rank' => 'Professor',
            'university' => 'Oxford',
            'country' => 'UK',
            'is_active' => true,
        ]);

        BillingLedger::create([
            'application_id' => $app2->id,
            'consumer_id' => '1000998877',
            'amount' => 5000,
            'status' => 'SETTLED',
        ]);

        $this->actingAs($applicant1);

        $test = Livewire::test(UniversalSearchPalette::class)
            ->call('openPalette')
            ->set('query', 'Quantum');

        // Applicant can see the active campaign
        $results = $test->instance()->getSearchResults();
        $titles = collect($results)->pluck('title')->toArray();

        $this->assertContains('Professor of Quantum Computing', $titles);

        // Applicant searching closed campaign should NOT get it
        $test->set('query', 'Astro Physics');
        $results = $test->instance()->getSearchResults();
        $titles = collect($results)->pluck('title')->toArray();
        $this->assertNotContains('Lecturer in Astro Physics', $titles);

        // Applicant searching applications can ONLY see their own
        $test->set('query', 'Application');
        $results = $test->instance()->getSearchResults();
        $titles = collect($results)->pluck('title')->toArray();

        $this->assertTrue(collect($titles)->contains(fn($t) => str_contains($t, "My Application #{$app1->id}")));
        $this->assertFalse(collect($titles)->contains(fn($t) => str_contains($t, "#{$app2->id}")));

        // Applicant searching reviewers or payments gets 0 results
        $test->set('query', 'Dr. Secret Reviewer');
        $results = $test->instance()->getSearchResults();
        $this->assertEmpty($results);

        $test->set('query', '1000998877');
        $results = $test->instance()->getSearchResults();
        $this->assertEmpty($results);
    }

    public function test_registrar_can_search_across_all_permitted_modules()
    {
        $registrar = User::factory()->create(['email' => 'registrar@university.edu']);
        $registrar->assignRole('registrar');

        $adv = Advertisement::create([
            'advertisement_no' => 'ADV-REG-99',
            'title' => 'Statutory Spring Recruitment Drive',
            'year' => 2026,
            'valid_from' => now()->subDays(5),
            'valid_to' => now()->addDays(30),
            'is_active' => true,
        ]);

        $campaign = Campaign::create([
            'job_code' => 'MATH-AP-01',
            'title' => 'Associate Professor in Applied Mathematics',
            'department' => 'Mathematics',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(25),
            'application_fee' => 3500,
            'advertisement_id' => $adv->id,
            'is_active' => true,
        ]);

        $candUser = User::factory()->create(['email' => 'candidate.math@university.edu']);
        $candUser->assignRole('applicant');

        $candProfile = BasicInfo::create([
            'user_id' => $candUser->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Zubair',
            'last_name' => 'Ahmad',
            'cnic_number' => '17301-8899001-3',
            'date_of_birth' => '1990-01-01',
            'phone_number' => '0300-1234567',
        ]);

        $app = Application::create([
            'user_id' => $candUser->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
        ]);

        $reviewerUser = User::factory()->create(['email' => 'turing@cambridge.edu']);
        $reviewer = ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Prof. Alan Turing',
            'subject' => 'Applied Mathematics',
            'academic_rank' => 'Professor Emeritus',
            'university' => 'Cambridge',
            'country' => 'UK',
            'is_active' => true,
        ]);

        $voucher = BillingLedger::create([
            'application_id' => $app->id,
            'consumer_id' => '1000223344',
            'amount' => 4000,
            'status' => 'SETTLED',
        ]);

        $this->actingAs($registrar);

        // Test 1: Search advertisement number
        $test = Livewire::test(UniversalSearchPalette::class)
            ->call('openPalette')
            ->set('query', 'ADV-REG-99');

        $results = $test->instance()->getSearchResults();
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['title'], 'ADV-REG-99')));
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['url'], '/registrar/advertisements')));

        // Test 2: Search candidate by CNIC
        $test->set('query', '17301-8899001-3');
        $results = $test->instance()->getSearchResults();
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['subtitle'], '17301-8899001-3')));
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['url'], '/registrar/candidates')));

        // Test 3: Search reviewer by name
        $test->set('query', 'Alan Turing');
        $results = $test->instance()->getSearchResults();
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['title'], 'Alan Turing')));
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['url'], '/registrar/reviewers')));

        // Test 4: Search payment voucher by consumer ID
        $test->set('query', '1000223344');
        $results = $test->instance()->getSearchResults();
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['title'], '1000223344')));
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['url'], '/registrar/payments')));
    }

    public function test_category_filtering_restricts_results_to_chosen_category()
    {
        $registrar = User::factory()->create();
        $registrar->assignRole('registrar');

        Campaign::create([
            'job_code' => 'BIO-01',
            'title' => 'Professor of Biochemistry',
            'department' => 'Biochemistry',
            'track_type' => 'ACADEMIC',
            'opening_date' => now()->subDays(5),
            'closing_date' => now()->addDays(20),
            'application_fee' => 3000,
            'is_active' => true,
        ]);

        $reviewerUser = User::factory()->create();
        ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Prof. Bio Scientist',
            'subject' => 'Biochemistry',
            'academic_rank' => 'Professor',
            'university' => 'MIT',
            'country' => 'USA',
            'is_active' => true,
        ]);

        $this->actingAs($registrar);

        // Searching 'Bio' with activeCategory = 'campaigns' only returns campaigns
        $test = Livewire::test(UniversalSearchPalette::class)
            ->call('openPalette')
            ->call('selectCategory', 'campaigns')
            ->set('query', 'Bio');

        $results = $test->instance()->getSearchResults();
        $categoryKeys = collect($results)->pluck('category_key')->unique()->toArray();

        $this->assertEquals(['campaigns'], $categoryKeys);
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['title'], 'Biochemistry')));

        // Switching category to 'reviewers' only returns reviewers
        $test->call('selectCategory', 'reviewers');
        $results = $test->instance()->getSearchResults();
        $categoryKeys = collect($results)->pluck('category_key')->unique()->toArray();

        $this->assertEquals(['reviewers'], $categoryKeys);
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['title'], 'Bio Scientist')));
    }

    public function test_scrutiny_committee_only_searches_scrutiny_and_nav_not_reviewers_or_payments()
    {
        $scrutinyUser = User::factory()->create();
        $scrutinyUser->assignRole('scrutiny-committee');

        $reviewerUser = User::factory()->create();
        ReviewerProfile::create([
            'user_id' => $reviewerUser->id,
            'name' => 'Dr. Review Expert',
            'subject' => 'Civil Law',
            'academic_rank' => 'Professor',
            'university' => 'Harvard',
            'country' => 'USA',
            'is_active' => true,
        ]);

        $this->actingAs($scrutinyUser);

        $test = Livewire::test(UniversalSearchPalette::class)
            ->call('openPalette');

        $categories = $test->instance()->getAvailableCategories();

        $this->assertArrayHasKey('navigation', $categories);
        $this->assertArrayHasKey('applications', $categories);
        $this->assertArrayNotHasKey('reviewers', $categories);
        $this->assertArrayNotHasKey('payments', $categories);
        $this->assertArrayNotHasKey('users', $categories);

        $test->set('query', 'Review Expert');
        $results = $test->instance()->getSearchResults();
        $this->assertEmpty($results);
    }

    public function test_academic_reviewer_only_sees_peer_review_desk()
    {
        $reviewerUser = User::factory()->create(['email' => 'reviewer@university.edu']);
        $reviewerUser->assignRole('academic-reviewer');

        $this->actingAs($reviewerUser);

        $test = Livewire::test(UniversalSearchPalette::class)
            ->call('openPalette');

        $categories = $test->instance()->getAvailableCategories();

        $this->assertArrayHasKey('navigation', $categories);
        $this->assertArrayNotHasKey('reviewers', $categories);
        $this->assertArrayNotHasKey('candidates', $categories);
        $this->assertArrayNotHasKey('payments', $categories);
        $this->assertArrayNotHasKey('users', $categories);

        $test->set('query', 'Review');
        $results = $test->instance()->getSearchResults();
        $this->assertTrue(collect($results)->contains(fn($r) => str_contains($r['url'], '/reviewer/dashboard')));
    }

    public function test_super_admin_has_universal_search_across_all_modules()
    {
        $superAdmin = User::factory()->create(['email' => 'superadmin@university.edu']);
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin);

        $test = Livewire::test(UniversalSearchPalette::class)
            ->call('openPalette');

        $categories = $test->instance()->getAvailableCategories();

        $this->assertArrayHasKey('navigation', $categories);
        $this->assertArrayHasKey('campaigns', $categories);
        $this->assertArrayHasKey('advertisements', $categories);
        $this->assertArrayHasKey('applications', $categories);
        $this->assertArrayHasKey('candidates', $categories);
        $this->assertArrayHasKey('reviewers', $categories);
        $this->assertArrayHasKey('payments', $categories);
        $this->assertArrayHasKey('constitutions', $categories);
        $this->assertArrayHasKey('users', $categories);

        // Super admin links use /admin
        $test->set('query', 'Operations Desk');
        $results = $test->instance()->getSearchResults();
        $this->assertTrue(collect($results)->contains(fn($r) => $r['url'] === '/admin/dashboard'));
    }
}
