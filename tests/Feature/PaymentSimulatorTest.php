<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\BillingLedger;
use App\Livewire\Bank\PaymentSimulator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentSimulatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_simulator_resolves_ledger_and_application_status(): void
    {
        $this->seed(\Database\Seeders\SpatieRoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('applicant');

        $campaign = Campaign::create([
            'job_code' => 'TEST-SIM-001',
            'title' => 'Software Engineer',
            'track_type' => 'SUPPORT',
            'department' => 'IT Services',
            'opening_date' => now()->subDay(),
            'closing_date' => now()->addDays(10),
            'application_fee' => 2000.00,
            'is_active' => true,
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $ledger = BillingLedger::create([
            'application_id' => $application->id,
            'consumer_id' => '100099887766',
            'amount' => 2000.00,
            'status' => 'PENDING',
            'due_date' => now()->addDays(5),
        ]);

        $this->actingAs($user);

        Livewire::test(PaymentSimulator::class)
            ->set('consumer_id', '100099887766')
            ->call('searchVoucher')
            ->assertSet('statusType', 'success')
            ->assertSee('100099887766')
            ->call('simulatePayment')
            ->assertSet('statusType', 'success')
            ->assertSee('Success! Payment settled instantly.');

        // Verify ledger updated
        $this->assertDatabaseHas('billing_ledger', [
            'id' => $ledger->id,
            'status' => 'SETTLED',
        ]);

        // Verify application updated to PAID
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'PAID',
        ]);
    }
}
