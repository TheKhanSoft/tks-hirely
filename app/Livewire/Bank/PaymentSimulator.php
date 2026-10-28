<?php

namespace App\Livewire\Bank;

use Livewire\Component;
use App\Models\BillingLedger;
use Illuminate\Support\Facades\Http;

class PaymentSimulator extends Component
{
    public $consumer_id;
    public $foundLedger;
    public $statusMessage;
    public $statusType = 'info';

    public function searchVoucher()
    {
        $this->validate([
            'consumer_id' => 'required|string',
        ]);

        $this->foundLedger = BillingLedger::where('consumer_id', $this->consumer_id)->first();

        if (!$this->foundLedger) {
            $this->statusMessage = "Challan/Voucher not found in system.";
            $this->statusType = 'danger';
        } else {
            $this->statusMessage = "Voucher found! Ready to simulate payment.";
            $this->statusType = 'success';
        }
    }

    public function simulatePayment()
    {
        if (!$this->foundLedger) {
            return;
        }

        // Generate a random transaction ID
        $transactionId = 'TXN-' . strtoupper(strval(rand(100000, 999999))) . '-' . date('YmdHis');

        // Send a POST request to our webhook URL to simulate the real bank integration!
        $webhookUrl = url('/api/webhooks/kuickpay');

        try {
            $response = Http::post($webhookUrl, [
                'consumer_id' => $this->foundLedger->consumer_id,
                'transaction_id' => $transactionId,
                'amount' => $this->foundLedger->amount,
                'status' => 'PAID'
            ]);

            if ($response->successful()) {
                $this->statusMessage = "Success! Payment webhook resolved successfully. Status: " . $response->json('message');
                $this->statusType = 'success';
                // Reload ledger state
                $this->foundLedger = BillingLedger::where('consumer_id', $this->consumer_id)->first();
            } else {
                $this->statusMessage = "Webhook failed with status " . $response->status() . ": " . $response->body();
                $this->statusType = 'danger';
            }
        } catch (\Exception $e) {
            $this->statusMessage = "Connection failed: " . $e->getMessage();
            $this->statusType = 'danger';
        }
    }

    public function render()
    {
        return view('livewire.bank.payment-simulator')
            ->layout('layouts.app');
    }
}
