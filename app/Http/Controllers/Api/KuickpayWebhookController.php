<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BillingLedger;
use App\Models\Application;
use Illuminate\Support\Facades\Log;

class KuickpayWebhookController extends Controller
{
    /**
     * Handle the billing switch webhook hit.
     */
    public function handle(Request $request)
    {
        // 1. Validate the payload
        $request->validate([
            'consumer_id' => 'required|string',
            'transaction_id' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $consumerId = $request->input('consumer_id');
        $transactionId = $request->input('transaction_id');
        $amount = $request->input('amount');
        $status = $request->input('status');

        Log::info("Kuickpay Webhook Hit: Consumer ID = {$consumerId}, Transaction = {$transactionId}, Amount = {$amount}");

        // 2. Locate the billing ledger record
        $ledger = BillingLedger::where('consumer_id', $consumerId)->first();

        if (!$ledger) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Consumer ID not found in system.'
            ], 404);
        }

        // 3. Reconcile transaction
        if ($status === 'PAID' || $status === 'SETTLED') {
            $ledger->update([
                'status' => 'SETTLED',
                'transaction_id' => $transactionId,
                'payment_timestamp' => now(),
                'raw_response_log' => $request->all(),
            ]);

            // Update parent application state to PAID
            $application = $ledger->application;
            if ($application) {
                $application->update([
                    'status' => 'PAID'
                ]);
                Log::info("Application ID {$application->id} status updated to PAID via Webhook.");
            }

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Payment reconciled and application unlocked successfully.'
            ], 200);
        }

        return response()->json([
            'status' => 'ERROR',
            'message' => 'Invalid transaction status payload.'
        ], 400);
    }
}
