<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Livewire\Traits\WithDynamicListing;
use App\Models\Application;
use App\Models\Advertisement;
use App\Models\Campaign;
use App\Models\SecurityAuditLog;
use App\Models\SystemSetting;
use App\Services\MailNotificationService;
use App\Services\Sms\SmsGatewayService;
use Illuminate\Support\Facades\Auth;

class AppellateReview extends Component
{
    use WithDynamicListing;

    public $search = '';
    public $campaignFilter = '';
    public $advertisementFilter = 'ALL';
    public $statusFilter = 'APPEALED';
    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public $appeals;
    public $selectedAppealId;
    public $selectedAppeal;

    // Split screen document viewer
    public $viewingDocName = 'No Document Selected';
    public $viewingDocPath = '';

    // Statutory Decree Modal
    public $isResolutionModalOpen = false;
    public $resolutionType = ''; // 'ELIGIBLE', 'PAID', 'REJECTED'
    public $statutoryOrderNotes = '';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->loadAppeals();
    }

    public function updatedSearch()
    {
        $this->loadAppeals();
    }

    public function updatedCampaignFilter()
    {
        $this->loadAppeals();
    }

    public function updatedAdvertisementFilter()
    {
        $this->loadAppeals();
    }

    public function updatedStatusFilter()
    {
        $this->loadAppeals();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->campaignFilter = '';
        $this->advertisementFilter = 'ALL';
        $this->statusFilter = 'APPEALED';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->loadAppeals();
    }

    public function exportCsv()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('export-application') ||
            Auth::user()->can('view-appeal') ||
            Auth::user()->can('process-appeal') ||
            Auth::user()->hasAnyRole(['super-admin', 'appellate-committee', 'appellate', 'registrar'])
        ), 403);

        $query = Application::with(['user.basicInfo', 'campaign.advertisement']);

        if (!empty($this->statusFilter) && $this->statusFilter !== 'ALL') {
            $query->where('status', $this->statusFilter);
        } else {
            $query->whereIn('status', ['APPEALED', 'ELIGIBLE', 'PAID', 'REJECTED']);
        }

        if ($this->advertisementFilter !== 'ALL') {
            $query->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        if ($this->campaignFilter) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $rawId = preg_replace('/[^0-9]/', '', $this->search);
            $query->where(function ($q) use ($s, $rawId) {
                if (!empty($rawId)) {
                    $q->where('applications.id', (int)$rawId);
                }
                $q->orWhereHas('user', function ($uq) use ($s) {
                    $uq->where('email', 'like', $s)
                       ->orWhereHas('basicInfo', function ($bq) use ($s) {
                           $bq->where('first_name', 'like', $s)
                              ->orWhere('last_name', 'like', $s)
                              ->orWhere('cnic_number', 'like', $s);
                       });
                })->orWhereHas('campaign', function ($cq) use ($s) {
                    $cq->where('title', 'like', $s)->orWhere('job_code', 'like', $s);
                });
            });
        }

        $records = $query->latest()->get();

        $headers = [
            'Dossier Ref',
            'Appellant Name',
            'Father Name',
            'CNIC Number',
            'Candidate Email',
            'Position Applied',
            'Job Code',
            'Department',
            'Current Status',
            'Initial Disqualification Reason',
            'Appeal Grounds / Letter',
            'Proof Attached',
            'Appeal Date',
        ];

        $rows = [];
        foreach ($records as $app) {
            $bi = $app->user?->basicInfo;
            $name = $bi ? trim($bi->first_name . ' ' . $bi->last_name) : ($app->user?->email ?? 'N/A');

            $rows[] = [
                $app->tracking_number,
                $name,
                $bi?->father_name ?? 'N/A',
                $bi?->cnic_number ?? 'N/A',
                $app->user?->email ?? '',
                $app->campaign?->title ?? '',
                $app->campaign?->job_code ?? '',
                $app->campaign?->department ?? '',
                $app->status,
                $app->disqualification_reason ?? 'None',
                $app->appeal_letter ?? 'No text provided',
                $app->appeal_proof_path ? 'Yes (Document Uploaded)' : 'No',
                $app->updated_at ? $app->updated_at->format('d M Y, h:i A') : $app->created_at->format('d M Y, h:i A'),
            ];
        }

        return $this->streamCsvDownload('appellate-tribunal-docket-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function openResolutionModal($type)
    {
        abort_unless(Auth::user()->can('process-appeal'), 403);
        $this->resolutionType = $type;
        $this->statutoryOrderNotes = match($type) {
            'ELIGIBLE' => 'The Appellate Tribunal, having examined the supplementary documentary evidence and equivalence certificates, finds the candidate fully qualified and hereby condones the initial scrutiny deficiency. The candidate is unconditionally restored as ELIGIBLE.',
            'PAID' => 'The Appellate Tribunal hereby remands the dossier back to the Scrutiny Committee for de novo review in light of the clarified credential evidence submitted by the appellant.',
            'REJECTED' => 'The Appellate Tribunal, upon rigorous judicial review of the statutory criteria, affirms the initial scrutiny decision. The grievance is hereby dismissed without merit.',
            default => '',
        };
        $this->isResolutionModalOpen = true;
    }

    public function closeResolutionModal()
    {
        $this->isResolutionModalOpen = false;
        $this->resolutionType = '';
        $this->statutoryOrderNotes = '';
    }

    public function executeAppellateDecree()
    {
        abort_unless(Auth::user()->can('process-appeal') || Auth::user()->hasAnyRole(['super-admin', 'registrar', 'appellate-committee']), 403);

        if (!SystemSetting::isFeatureEnabled('appellate_workbench')) {
            session()->flash('error', 'Grievance Appellate Adjudication Workbench is currently disabled by university administration.');
            return;
        }

        if (!$this->selectedAppeal) {
            return;
        }

        $app = $this->selectedAppeal;
        $orderNotes = trim($this->statutoryOrderNotes) ?: 'Statutory decree issued by Appellate Committee.';
        $deciderId = Auth::id();
        $decidedAt = now();

        if ($this->resolutionType === 'ELIGIBLE') {
            $app->update([
                'status' => 'ELIGIBLE',
                'disqualification_reason' => null,
                'appeal_decision_remarks' => $orderNotes,
                'appeal_decided_by' => $deciderId,
                'appeal_decided_at' => $decidedAt,
            ]);

            MailNotificationService::triggerAppealDecision(
                $app,
                'APPROVED_DECLARED_ELIGIBLE',
                $orderNotes
            );

            session()->flash('message', 'Formal Appellate Decree Issued: Candidate restored to ELIGIBLE.');
        } elseif ($this->resolutionType === 'PAID') {
            $app->update([
                'status' => 'PAID',
                'disqualification_reason' => null,
                'appeal_decision_remarks' => $orderNotes,
                'appeal_decided_by' => $deciderId,
                'appeal_decided_at' => $decidedAt,
            ]);

            MailNotificationService::triggerAppealDecision(
                $app,
                'APPROVED_RETURNED_TO_SCRUTINY',
                $orderNotes
            );

            session()->flash('message', 'Formal Appellate Remand: Candidate remanded to PAID for re-scrutiny.');
        } elseif ($this->resolutionType === 'REJECTED') {
            $app->update([
                'status' => 'REJECTED',
                'appeal_decision_remarks' => $orderNotes,
                'appeal_decided_by' => $deciderId,
                'appeal_decided_at' => $decidedAt,
            ]);

            MailNotificationService::triggerAppealDecision(
                $app,
                'REJECTED',
                $orderNotes
            );

            session()->flash('message', 'Formal Appellate Decree: Appeal dismissed and candidacy marked REJECTED.');
        }

        // Candidate SMS Notification
        $mobile = $app->user?->basicInfo?->mobile_number;
        if ($mobile) {
            $smsMsg = "Appellate Decision: Your statutory appeal for {$app->campaign?->title} has been decided ({$this->resolutionType}). Please check candidate portal for official decree.";
            SmsGatewayService::send($mobile, $smsMsg);
        }

        // Security Audit Log
        SecurityAuditLog::record(
            'APPELLATE_DECREE_EXECUTED',
            'Application',
            $app->id,
            [
                'decree' => $this->resolutionType,
                'remarks' => $orderNotes,
                'candidate' => $app->user?->name,
                'tracking' => $app->tracking_number,
            ],
            null,
            $deciderId
        );

        $this->closeResolutionModal();
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-appeal') ||
            Auth::user()->can('process-appeal') ||
            Auth::user()->hasAnyRole(['super-admin', 'appellate-committee', 'appellate', 'registrar'])
        ), 403);
        $this->loadAppeals();
    }

    public function loadAppeals()
    {
        $query = Application::with('user.basicInfo', 'campaign.advertisement');

        if (!empty($this->statusFilter) && $this->statusFilter !== 'ALL') {
            $query->where('status', $this->statusFilter);
        } else {
            $query->whereIn('status', ['APPEALED', 'ELIGIBLE', 'PAID', 'REJECTED']);
        }

        if ($this->advertisementFilter !== 'ALL') {
            $query->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        if ($this->campaignFilter) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $rawId = preg_replace('/[^0-9]/', '', $this->search);
            $query->where(function ($q) use ($s, $rawId) {
                if (!empty($rawId)) {
                    $q->where('applications.id', (int)$rawId);
                }
                $q->orWhereHas('user', function ($uq) use ($s) {
                    $uq->where('email', 'like', $s)
                       ->orWhereHas('basicInfo', function ($bq) use ($s) {
                           $bq->where('first_name', 'like', $s)
                              ->orWhere('last_name', 'like', $s)
                              ->orWhere('cnic_number', 'like', $s);
                       });
                })->orWhereHas('campaign', function ($cq) use ($s) {
                    $cq->where('title', 'like', $s)->orWhere('job_code', 'like', $s);
                });
            });
        }

        // Sorting
        if ($this->sortBy === 'candidate_name') {
            $query->join('users', 'applications.user_id', '=', 'users.id')
                ->leftJoin('basic_infos', 'users.id', '=', 'basic_infos.user_id')
                ->orderBy('basic_infos.first_name', $this->sortDir)
                ->select('applications.*');
        } elseif ($this->sortBy === 'tracking_number') {
            $query->orderBy('applications.id', $this->sortDir);
        } elseif ($this->sortBy === 'campaign_title') {
            $query->join('campaigns', 'applications.campaign_id', '=', 'campaigns.id')
                ->orderBy('campaigns.title', $this->sortDir)
                ->select('applications.*');
        } else {
            $query->orderBy('applications.updated_at', $this->sortDir);
        }

        $this->appeals = $query->get();
            
        if ($this->selectedAppealId && $this->appeals->contains('id', $this->selectedAppealId)) {
            $this->selectAppeal($this->selectedAppealId);
        } elseif ($this->appeals->isNotEmpty()) {
            $this->selectAppeal($this->appeals->first()->id);
        } else {
            $this->selectedAppeal = null;
            $this->viewingDocName = 'No Document Selected';
            $this->viewingDocPath = '';
        }
    }

    public function selectAppeal($id)
    {
        abort_unless(
            Auth::user()->can('view-appeal') ||
            Auth::user()->can('process-appeal') ||
            Auth::user()->hasAnyRole(['super-admin', 'appellate-committee', 'appellate', 'registrar']),
            403
        );

        $this->selectedAppealId = $id;
        $this->selectedAppeal = Application::with('user.basicInfo', 'campaign')->findOrFail($id);
        
        if ($this->selectedAppeal->appeal_proof_path) {
            $this->viewingDocName = 'Appeal Grievance Proof';
            $this->viewingDocPath = $this->selectedAppeal->appeal_proof_path;
        } else {
            $this->viewingDocName = 'No Document Selected';
            $this->viewingDocPath = '';
        }
    }

    public function approveAppealToPaid()
    {
        abort_unless(Auth::user()->can('process-appeal'), 403);

        if (!$this->selectedAppeal) {
            return;
        }

        // Shifting status back to PAID, clearing disqualification reason
        $this->selectedAppeal->update([
            'status' => 'PAID',
            'disqualification_reason' => null,
        ]);

        MailNotificationService::triggerAppealDecision(
            $this->selectedAppeal,
            'APPROVED_RETURNED_TO_SCRUTINY',
            'Appeal upheld by the Appellate Committee. Application returned to scrutiny pipeline.'
        );

        session()->flash('message', 'Appeal approved. Candidate status reset to PAID and returned to Scrutiny Pipeline.');
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function approveAppealToEligible()
    {
        abort_unless(Auth::user()->can('process-appeal'), 403);

        if (!$this->selectedAppeal) {
            return;
        }

        // Shifting status directly to ELIGIBLE
        $this->selectedAppeal->update([
            'status' => 'ELIGIBLE',
            'disqualification_reason' => null,
        ]);

        MailNotificationService::triggerAppealDecision(
            $this->selectedAppeal,
            'APPROVED_DECLARED_ELIGIBLE',
            'Appeal upheld by the Appellate Committee. Candidate declared directly ELIGIBLE.'
        );

        session()->flash('message', 'Appeal approved. Candidate status upgraded directly to ELIGIBLE.');
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function rejectAppeal()
    {
        abort_unless(Auth::user()->can('process-appeal'), 403);

        if (!$this->selectedAppeal) {
            return;
        }

        // Shifting status to REJECTED (appeal finalized and dismissed)
        $this->selectedAppeal->update([
            'status' => 'REJECTED',
        ]);

        MailNotificationService::triggerAppealDecision(
            $this->selectedAppeal,
            'REJECTED',
            'Appeal reviewed and dismissed by the Statutory Appellate Committee.'
        );

        session()->flash('message', 'Appeal rejected. Candidate status permanently updated to REJECTED.');
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function render()
    {
        $campaignsQuery = Campaign::select('id', 'title', 'job_code', 'department', 'advertisement_id')->orderBy('title');
        if ($this->advertisementFilter !== 'ALL') {
            $campaignsQuery->where('advertisement_id', (int)$this->advertisementFilter);
        }
        $campaigns = $campaignsQuery->get();

        $advertisements = Advertisement::chronologicalDesc()->get();

        return view('livewire.admin.appellate-review', [
            'campaigns' => $campaigns,
            'advertisements' => $advertisements,
            'isFeatureDisabled' => !SystemSetting::isFeatureEnabled('appellate_workbench'),
        ])->layout('layouts.app');
    }
}
