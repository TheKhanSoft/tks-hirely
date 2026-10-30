<?php

namespace App\Livewire\Applicant;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\BillingLedger;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithFileUploads;

    public $appealText;
    public $appealProof;
    public $showAppealForm = false;
    public $selectedApplicationId;

    // Apply for additional position modal properties
    public $showApplyModal = false;
    public $selectedCampaignIdForModal = null;
    public $campaignSearch = '';
    public $campaignYearFilter = '';
    public $campaignAdFilter = '';

    // Print-ready official challan slip modal
    public $showSlipModal = false;
    public $selectedSlipApp = null;

    // Filter for applications table
    public $statusFilter = 'ALL';

    public function setStatusFilter($filter)
    {
        $this->statusFilter = $filter;
    }

    public function openAppealForm($applicationId)
    {
        $this->selectedApplicationId = $applicationId;
        $this->showAppealForm = true;
    }

    public function submitAppeal()
    {
        abort_unless(Auth::user()->can('submit-appeal') || Auth::user()->can('submit-applications'), 403);

        $this->validate([
            'appealText' => 'required|string|min:20',
            'appealProof' => 'required|file|max:2048',
        ]);

        $application = Application::findOrFail($this->selectedApplicationId);

        // Ensure user owns this application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        // Handle upload
        $path = $this->appealProof->store('appeals', 'public');

        // Update application state
        $application->update([
            'status' => 'APPEALED',
            'appeal_letter' => $this->appealText,
            'appeal_proof_path' => $path,
        ]);

        session()->flash('message', 'Your formal appeal has been submitted successfully and queued for grievance committee review.');
        
        $this->showAppealForm = false;
        $this->appealText = '';
        $this->appealProof = null;
    }

    public function openApplyModal()
    {
        abort_unless(Auth::user()->can('submit-applications'), 403);

        $user = Auth::user();
        if (!$user->basicInfo) {
            return redirect()->to('/applicant/wizard');
        }

        $this->selectedCampaignIdForModal = null;
        $this->campaignSearch = '';
        $this->showApplyModal = true;
    }

    public function closeApplyModal()
    {
        $this->showApplyModal = false;
        $this->selectedCampaignIdForModal = null;
        $this->campaignSearch = '';
        $this->campaignYearFilter = '';
        $this->campaignAdFilter = '';
        $this->resetErrorBag();
    }

    public function resetCampaignFilters()
    {
        $this->campaignSearch = '';
        $this->campaignYearFilter = '';
        $this->campaignAdFilter = '';
    }

    public function selectCampaignForModal($campaignId)
    {
        // Check if user has already applied for this campaign
        $alreadyApplied = Auth::user()->applications()->where('campaign_id', $campaignId)->exists();
        if ($alreadyApplied) {
            $this->addError('selectedCampaignIdForModal', 'You have already applied for this position. Candidates cannot submit duplicate applications for the same campaign.');
            return;
        }

        $this->resetErrorBag('selectedCampaignIdForModal');
        $this->selectedCampaignIdForModal = $campaignId;
    }

    public function submitNewApplication()
    {
        abort_unless(Auth::user()->can('submit-applications'), 403);

        $this->validate([
            'selectedCampaignIdForModal' => 'required|exists:campaigns,id',
        ], [
            'selectedCampaignIdForModal.required' => 'Please select an open position to submit your application.',
        ]);

        $user = Auth::user();
        $basicInfo = $user->basicInfo;

        if (!$basicInfo) {
            return redirect()->to('/applicant/wizard');
        }

        $campaign = Campaign::findOrFail($this->selectedCampaignIdForModal);

        // Strict duplicate check: prevent reapplying for the same campaign
        $alreadyApplied = $user->applications()->where('campaign_id', $campaign->id)->exists();
        if ($alreadyApplied) {
            $this->addError('selectedCampaignIdForModal', 'You have already applied for this position. Duplicate applications for the same position are not allowed.');
            return;
        }

        // Lock basicInfo if not locked
        if (!$basicInfo->is_locked) {
            $basicInfo->update(['is_locked' => true]);
        }

        // Generate a unique 12-digit 1Link / Kuickpay consumer reference
        $consumerId = '1000' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        while (BillingLedger::where('consumer_id', $consumerId)->exists()) {
            $consumerId = '1000' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        }

        // Create new Application record
        $application = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        // Create Billing Ledger for this application
        BillingLedger::create([
            'application_id' => $application->id,
            'consumer_id' => $consumerId,
            'amount' => $campaign->application_fee,
            'status' => 'PENDING',
        ]);

        session()->flash('message', "Application for {$campaign->title} ({$campaign->job_code}) submitted successfully! 1Link Consumer ID #{$consumerId} generated.");

        $this->showApplyModal = false;
        $this->selectedCampaignIdForModal = null;
    }

    public function openSlipModal($applicationId)
    {
        $app = Application::with(['campaign', 'billingLedger', 'user.basicInfo'])->findOrFail($applicationId);

        if ($app->user_id !== Auth::id()) {
            abort(403);
        }

        $this->selectedSlipApp = $app;
        $this->showSlipModal = true;
    }

    public function closeSlipModal()
    {
        $this->showSlipModal = false;
        $this->selectedSlipApp = null;
    }

    public function render()
    {
        $user = Auth::user();
        $basicInfo = $user->basicInfo;

        $allApplications = $user->applications()->with(['campaign', 'billingLedger'])->latest()->get();
        $appliedCampaignIds = $allApplications->pluck('campaign_id')->toArray();

        // Filter applications based on statusFilter
        $filteredApplications = $allApplications->filter(function ($app) {
            if ($this->statusFilter === 'ALL') {
                return true;
            }
            if ($this->statusFilter === 'PENDING') {
                return $app->status === 'SUBMITTED';
            }
            if ($this->statusFilter === 'PAID') {
                return $app->status === 'PAID';
            }
            if ($this->statusFilter === 'ELIGIBLE') {
                return in_array($app->status, ['ELIGIBLE', 'APPROVED']);
            }
            if ($this->statusFilter === 'INELIGIBLE') {
                return in_array($app->status, ['INELIGIBLE', 'APPEALED', 'REJECTED']);
            }
            return true;
        });

        // Query active campaigns for the Apply Modal
        $activeCampaignsQuery = Campaign::with('advertisement')->where('is_active', true);
        if (!empty($this->campaignSearch)) {
            $search = '%' . trim($this->campaignSearch) . '%';
            $activeCampaignsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('job_code', 'like', $search)
                  ->orWhere('department', 'like', $search);
            });
        }
        if (!empty($this->campaignYearFilter)) {
            $y = (int)$this->campaignYearFilter;
            $activeCampaignsQuery->where(function ($q) use ($y) {
                $q->whereHas('advertisement', fn($aq) => $aq->where('year', $y))
                  ->orWhere(function($oq) use ($y) {
                      $oq->whereNull('advertisement_id')->whereYear('opening_date', $y);
                  });
            });
        }
        if (!empty($this->campaignAdFilter)) {
            $activeCampaignsQuery->where('advertisement_id', $this->campaignAdFilter);
        }
        $availableCampaigns = $activeCampaignsQuery->orderBy('title')->get();

        $applicantAdYears = Advertisement::distinct()->whereNotNull('year')->pluck('year')->all();
        $applicantYears = collect(array_unique(array_merge($applicantAdYears, [(int)date('Y')])))->sortDesc()->values();

        $adQuery = Advertisement::where('is_active', true)->where('valid_to', '>=', now())->withCount('campaigns');
        if (!empty($this->campaignYearFilter)) {
            $adQuery->where('year', (int)$this->campaignYearFilter);
        }
        $applicantAdvertisements = $adQuery->orderBy('year', 'desc')->orderBy('advertisement_no')->get();

        // Calculate dossier completeness percentage
        $completion = 0;
        if ($basicInfo) {
            if ($basicInfo->is_locked) {
                $completion = 100;
            } else {
                $completion += 30; // Bio-Data filled
                if ($basicInfo->degrees()->count() > 0) $completion += 25;
                if ($basicInfo->experiences()->count() > 0) $completion += 20;
                if ($basicInfo->publications()->count() > 0 || $basicInfo->tools()->count() > 0 || $basicInfo->milestones()->count() > 0) $completion += 15;
                if ($allApplications->count() > 0) $completion += 10;
                $completion = min(100, $completion);
            }
        }

        return view('livewire.applicant.dashboard', [
            'basicInfo' => $basicInfo,
            'applications' => $filteredApplications,
            'allApplications' => $allApplications,
            'appliedCampaignIds' => $appliedCampaignIds,
            'availableCampaigns' => $availableCampaigns,
            'applicantYears' => $applicantYears,
            'applicantAdvertisements' => $applicantAdvertisements,
            'completion' => $completion,
            'totalApplications' => $allApplications->count(),
            'paidCount' => $allApplications->whereIn('status', ['PAID', 'ELIGIBLE', 'APPROVED'])->count(),
            'pendingCount' => $allApplications->where('status', 'SUBMITTED')->count(),
            'eligibleCount' => $allApplications->where('status', 'ELIGIBLE')->count(),
            'ineligibleCount' => $allApplications->where('status', 'INELIGIBLE')->count(),
        ])->layout('layouts.app');
    }
}
