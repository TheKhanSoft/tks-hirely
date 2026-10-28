<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Application;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class ApplicationManagement extends Component
{
    // View state
    public $isFormOpen = false;
    public $isEditMode = false;
    public $isDetailsOpen = false;
    public $selectedRecordId;
    public $selectedRecordDetails = null;

    // Filters & Search
    public $search = '';
    public $statusFilter = '';
    public $campaignFilter = '';
    public $voucherFilter = '';

    // Collections
    public $users;
    public $campaigns;

    // Form fields
    public $app_id;
    public $app_user_id;
    public $app_campaign_id;
    public $app_status = 'DRAFT';
    public $app_disqualification_reason;

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->loadAuxiliaryData();
    }

    public function loadAuxiliaryData()
    {
        $this->users = User::role('applicant')->get();
        $this->campaigns = Campaign::where('is_active', true)->get();
    }

    public function resetForm()
    {
        $this->reset([
            'app_id',
            'app_user_id',
            'app_campaign_id',
            'app_disqualification_reason',
        ]);
        $this->app_status = 'DRAFT';
        $this->isFormOpen = false;
        $this->isEditMode = false;
    }

    public function openCreateApplicationForm()
    {
        abort_unless(Auth::user()->can('create-application'), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditApplicationForm($id)
    {
        abort_unless(Auth::user()->can('edit-application') || Auth::user()->can('change-application-status'), 403);
        $this->resetForm();
        $this->app_id = $id;
        $app = Application::findOrFail($id);
        $this->app_user_id = $app->user_id;
        $this->app_campaign_id = $app->campaign_id;
        $this->app_status = $app->status;
        $this->app_disqualification_reason = $app->disqualification_reason;
        $this->isEditMode = true;
        $this->isFormOpen = true;
    }

    public function saveApplication()
    {
        if ($this->isEditMode) {
            abort_unless(Auth::user()->can('edit-application') || Auth::user()->can('change-application-status'), 403);
            $app = Application::findOrFail($this->app_id);
            $app->update([
                'status' => $this->app_status,
                'disqualification_reason' => $this->app_disqualification_reason,
            ]);
            session()->flash('message', 'Application status updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-application'), 403);
            $this->validate([
                'app_user_id' => 'required|exists:users,id',
                'app_campaign_id' => 'required|exists:campaigns,id',
                'app_status' => 'required',
            ]);
            Application::create([
                'user_id' => $this->app_user_id,
                'campaign_id' => $this->app_campaign_id,
                'status' => $this->app_status,
                'disqualification_reason' => $this->app_disqualification_reason,
            ]);
            session()->flash('message', 'Application registered successfully.');
        }

        $this->resetForm();
    }

    public function deleteApplication($id)
    {
        abort_unless(Auth::user()->can('delete-application'), 403);
        Application::destroy($id);
        session()->flash('message', 'Application record deleted successfully.');
    }

    public function viewApplicationDetails($id)
    {
        abort_unless(Auth::user()->can('view-application'), 403);
        $this->selectedRecordId = $id;
        $this->selectedRecordDetails = Application::with('user.basicInfo', 'campaign', 'billingLedger', 'evaluations.boardMember', 'reviews.reviewer.reviewerProfile')->findOrFail($id);
        $this->isDetailsOpen = true;
    }

    public function render()
    {
        $query = Application::with('user.basicInfo', 'campaign', 'billingLedger');

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->campaignFilter)) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if (!empty($this->voucherFilter)) {
            if ($this->voucherFilter === 'SETTLED') {
                $query->whereHas('billingLedger', fn($b) => $b->where('status', 'SETTLED'));
            } elseif ($this->voucherFilter === 'PENDING') {
                $query->where(function ($q) {
                    $q->whereHas('billingLedger', fn($b) => $b->where('status', '!=', 'SETTLED'))
                      ->orWhereDoesntHave('billingLedger');
                });
            }
        }

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($uq) use ($searchTerm) {
                    $uq->where('email', 'like', $searchTerm)
                       ->orWhereHas('basicInfo', function ($bq) use ($searchTerm) {
                           $bq->where('first_name', 'like', $searchTerm)
                              ->orWhere('last_name', 'like', $searchTerm)
                              ->orWhere('cnic_number', 'like', $searchTerm);
                       });
                })->orWhereHas('campaign', function ($cq) use ($searchTerm) {
                    $cq->where('title', 'like', $searchTerm)
                       ->orWhere('job_code', 'like', $searchTerm);
                });
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->get();

        // Dynamic System KPI Metrics
        $stats = [
            'total' => Application::count(),
            'paid' => Application::whereHas('billingLedger', fn($b) => $b->where('status', 'SETTLED'))->count(),
            'eligible' => Application::where('status', 'ELIGIBLE')->count(),
            'under_review' => Application::whereIn('status', ['UNDER_REVIEW', 'APPEALED'])->count(),
            'approved' => Application::where('status', 'APPROVED')->count(),
        ];

        return view('livewire.admin.application-management', [
            'applications' => $applications,
            'stats' => $stats,
        ])->layout('layouts.app', ['title' => 'Applications Desk - Hirely']);
    }
}
