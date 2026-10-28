<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\BillingLedger;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class PaymentManagement extends Component
{
    // View state
    public $isFormOpen = false;
    public $isEditMode = false;
    public $selectedRecordId;

    // Filters & Search
    public $search = '';
    public $statusFilter = '';

    // Collections
    public $availableApplications = [];

    // Form fields
    public $pay_id;
    public $pay_app_id;
    public $pay_consumer_id;
    public $pay_amount;
    public $pay_status = 'PENDING';
    public $pay_transaction_id;
    public $pay_timestamp;

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->can('view-payment'), 403);
        $this->loadAuxiliaryData();
    }

    public function loadAuxiliaryData()
    {
        $this->availableApplications = Application::doesntHave('billingLedger')->with('user', 'campaign')->get();
    }

    public function resetForm()
    {
        $this->reset([
            'pay_id',
            'pay_app_id',
            'pay_consumer_id',
            'pay_amount',
            'pay_transaction_id',
            'pay_timestamp',
        ]);
        $this->pay_status = 'PENDING';
        $this->isFormOpen = false;
        $this->isEditMode = false;
    }

    public function openCreatePaymentForm()
    {
        abort_unless(Auth::user()->can('create-payment'), 403);
        $this->resetForm();
        $this->loadAuxiliaryData();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditPaymentForm($id)
    {
        abort_unless(Auth::user()->can('edit-payment'), 403);
        $this->resetForm();
        $this->pay_id = $id;
        $ledger = BillingLedger::findOrFail($id);
        $this->pay_app_id = $ledger->application_id;
        $this->pay_consumer_id = $ledger->consumer_id;
        $this->pay_amount = $ledger->amount;
        $this->pay_status = $ledger->status;
        $this->pay_transaction_id = $ledger->transaction_id;
        $this->pay_timestamp = $ledger->payment_timestamp?->format('Y-m-d\TH:i');
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function savePayment()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-payment'), 403);
            $this->validate([
                'pay_amount' => 'required|numeric',
                'pay_status' => 'required|in:PENDING,AUTHORIZED,SETTLED,EXPIRED',
            ]);
            $ledger = BillingLedger::findOrFail($this->pay_id);
            $ledger->update([
                'amount' => $this->pay_amount,
                'status' => $this->pay_status,
                'transaction_id' => $this->pay_transaction_id,
                'payment_timestamp' => $this->pay_timestamp,
            ]);

            // Reconcile associated application if settled
            if ($this->pay_status === 'SETTLED' && $ledger->application) {
                $ledger->application->update(['status' => 'PAID']);
            }

            session()->flash('message', 'Payment & voucher details updated.');
        } else {
            abort_unless(Auth::user()->can('create-payment'), 403);
            $this->validate([
                'pay_app_id' => 'required|exists:applications,id|unique:billing_ledger,application_id',
                'pay_consumer_id' => 'required|string|unique:billing_ledger,consumer_id',
                'pay_amount' => 'required|numeric',
                'pay_status' => 'required|in:PENDING,AUTHORIZED,SETTLED,EXPIRED',
            ]);

            $ledger = BillingLedger::create([
                'application_id' => $this->pay_app_id,
                'consumer_id' => $this->pay_consumer_id,
                'amount' => $this->pay_amount,
                'status' => $this->pay_status,
                'transaction_id' => $this->pay_transaction_id,
                'payment_timestamp' => $this->pay_timestamp,
            ]);

            if ($this->pay_status === 'SETTLED' && $ledger->application) {
                $ledger->application->update(['status' => 'PAID']);
            }

            session()->flash('message', 'Billing voucher logged successfully.');
        }

        $this->resetForm();
        $this->loadAuxiliaryData();
    }

    public function markSettled($id)
    {
        abort_unless(Auth::user()->can('reconcile-payment') || Auth::user()->can('edit-payment'), 403);
        $ledger = BillingLedger::findOrFail($id);
        $ledger->update([
            'status' => 'SETTLED',
            'payment_timestamp' => now(),
            'transaction_id' => $ledger->transaction_id ?: 'RECON-' . strtoupper(bin2hex(random_bytes(4))),
        ]);

        if ($ledger->application) {
            $ledger->application->update(['status' => 'PAID']);
        }

        session()->flash('message', "Voucher #{$ledger->consumer_id} marked as SETTLED. Application reconciled to PAID.");
    }

    public function deletePayment($id)
    {
        abort_unless(Auth::user()->can('delete-payment'), 403);
        BillingLedger::destroy($id);
        session()->flash('message', 'Billing record deleted.');
        $this->loadAuxiliaryData();
    }

    public function render()
    {
        $query = BillingLedger::with('application.user.basicInfo', 'application.campaign');

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('consumer_id', 'like', $searchTerm)
                  ->orWhere('transaction_id', 'like', $searchTerm)
                  ->orWhereHas('application', function ($aq) use ($searchTerm) {
                      $aq->whereHas('user', function ($uq) use ($searchTerm) {
                          $uq->where('email', 'like', $searchTerm)
                             ->orWhereHas('basicInfo', function ($bq) use ($searchTerm) {
                                 $bq->where('first_name', 'like', $searchTerm)
                                    ->orWhere('last_name', 'like', $searchTerm);
                             });
                      })->orWhereHas('campaign', function ($cq) use ($searchTerm) {
                          $cq->where('title', 'like', $searchTerm)
                             ->orWhere('job_code', 'like', $searchTerm);
                      });
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        return view('livewire.admin.payment-management', [
            'payments' => $payments,
        ])->layout('layouts.app', ['title' => 'Vouchers & Payments - Hirely']);
    }
}
