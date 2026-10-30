<?php

namespace App\Livewire\Bank;

use Livewire\Component;
use App\Models\BillingLedger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\KuickpayWebhookController;

class PaymentSimulator extends Component
{
    public $consumer_id;
    public $foundLedger;
    public $statusMessage;
    public $statusType = 'info';

    public function mount()
    {
        // Auto-load if consumer_id is passed via query parameter (e.g. from applicant dashboard or wizard)
        if (request()->has('consumer_id') && !empty(request()->query('consumer_id'))) {
            $this->consumer_id = trim(request()->query('consumer_id'));
            $this->searchVoucher();
        }
    }

    public function searchVoucher()
    {
        $this->validate([
            'consumer_id' => 'required|string',
        ]);

        $this->foundLedger = BillingLedger::where('consumer_id', trim($this->consumer_id))->first();

        if (!$this->foundLedger) {
            $this->statusMessage = "Challan / Voucher reference not found in system.";
            $this->statusType = 'danger';
        } else {
            $this->statusMessage = "Voucher found! Ready to simulate payment switch settlement.";
            $this->statusType = 'success';
        }
    }

    public function simulatePayment()
    {
        if (!$this->foundLedger) {
            return;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $isOwner = $this->foundLedger->application?->user_id === $user->id;
            $hasPerm = $user->can('simulate-payment') || $user->can('reconcile-payment');
            if (!$isOwner && !$hasPerm) {
                abort(403, 'Unauthorized to simulate settlement for this voucher.');
            }
        }

        // Generate a random 1Link transaction reference
        $transactionId = 'TXN-' . strtoupper(strval(rand(100000, 999999))) . '-' . date('YmdHis');

        $payload = [
            'consumer_id' => $this->foundLedger->consumer_id,
            'transaction_id' => $transactionId,
            'amount' => $this->foundLedger->amount,
            'status' => 'PAID',
        ];

        // When running under single-threaded servers (e.g. 'php artisan serve' / cli-server),
        // self-calling via HTTP results in a deadlock and cURL error 28 timeout.
        // In local/testing or cli-server environments, we dispatch directly to the controller.
        if (php_sapi_name() === 'cli-server' || app()->environment('local', 'testing')) {
            $this->dispatchInternally($payload);
            return;
        }

        // For multi-threaded production servers (Apache, Nginx + PHP-FPM)
        try {
            $webhookUrl = url('/api/webhooks/kuickpay');
            $response = Http::timeout(5)->post($webhookUrl, $payload);

            if ($response->successful()) {
                $this->statusMessage = "Success! Payment webhook resolved successfully: " . $response->json('message');
                $this->statusType = 'success';
                $this->foundLedger = BillingLedger::where('consumer_id', $this->consumer_id)->first();
            } else {
                $this->statusMessage = "Webhook failed with status " . $response->status() . ": " . $response->body();
                $this->statusType = 'danger';
            }
        } catch (\Exception $e) {
            // Graceful fallback to internal dispatch if loopback or network fails
            $this->dispatchInternally($payload, "(Dispatched via internal engine due to loopback timeout)");
        }
    }

    protected function dispatchInternally(array $payload, string $fallbackNote = '')
    {
        try {
            $request = Request::create('/api/webhooks/kuickpay', 'POST', $payload);
            $response = app(KuickpayWebhookController::class)->handle($request);
            $responseData = json_decode($response->getContent(), true);

            if ($response->getStatusCode() === 200) {
                $msg = $responseData['message'] ?? 'Payment reconciled and application unlocked successfully.';
                $this->statusMessage = "Success! Payment settled instantly. " . $msg . ($fallbackNote ? " $fallbackNote" : '');
                $this->statusType = 'success';
                $this->foundLedger = BillingLedger::where('consumer_id', $this->consumer_id)->first();
            } else {
                $msg = $responseData['message'] ?? 'Payment reconciliation failed.';
                $this->statusMessage = "Webhook failed with status " . $response->getStatusCode() . ": " . $msg;
                $this->statusType = 'danger';
            }
        } catch (\Exception $ex) {
            $this->statusMessage = "Internal dispatch error: " . $ex->getMessage();
            $this->statusType = 'danger';
        }
    }

    public function render()
    {
        return view('livewire.bank.payment-simulator')
            ->layout('layouts.app');
    }
}
