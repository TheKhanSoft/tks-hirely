<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\User;
use App\Models\Campaign;
use App\Models\ReviewerProfile;
use App\Models\WorkflowStatus;
use App\Models\SystemSetting;
use App\Services\AcademicDisciplineMatcher;
use App\Services\CountryService;
use App\Mail\ApplicationAssignedReviewerMail;
use App\Mail\ReviewerCredentialsMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Livewire\Traits\WithDynamicListing;

class ApplicationManagement extends Component
{
    use WithDynamicListing;

    // View state
    public $isFormOpen = false;
    public $isEditMode = false;
    public $isDetailsOpen = false;
    public $selectedRecordId;
    public $selectedRecordDetails = null;

    // Default per-page for card grid (16 items)
    public $perPage = 16;

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

    // =========================================================================
    // 🚀 DIRECT ACTION 1: DISPATCH TO PEER REVIEWERS DIRECTLY FROM /APPLICATIONS
    // =========================================================================
    public $showDispatchModal = false;
    public $dispatchAppId = null;
    public $dispatchApplication = null;
    public $selectedReviewerIds = [];
    public $dispatchReviewDueDate = null;
    public $dispatchReviewNotes = '';
    public $dispatchTargetRequiredExperts = 3;
    public $dispatchAlreadyAssignedCount = 0;
    public $dispatchRemainingRequiredExperts = 3;
    public $dispatchReviewerSearch = '';
    public $dispatchDisciplineFilter = '';
    public $filterByFieldOnly = true;

    // Direct Empanel Modal within Dispatch
    public $showEmpanelModal = false;
    public $empanelName = '';
    public $empanelEmail = '';
    public $empanelUniversity = '';
    public $empanelSubject = '';
    public $empanelRank = 'Professor';
    public $empanelCountry = 'Pakistan';
    public $empanelPhone = '';
    public $empanelSpecialization = '';

    // =========================================================================
    // ⚠️ DIRECT ACTION 2: MARK AS INCOMPLETE DOSSIER
    // =========================================================================
    public $showIncompleteModal = false;
    public $incompleteAppId = null;
    public $incompleteDeficiencyNotes = '';

    // =========================================================================
    // 🚫 DIRECT ACTION 3: REJECT / DISQUALIFY APPLICATION
    // =========================================================================
    public $showRejectModal = false;
    public $rejectAppId = null;
    public $rejectReason = '';

    // =========================================================================
    // 🔄 DIRECT ACTION 4: ADVANCE DYNAMIC STATUS TRANSITION
    // =========================================================================
    public $showTransitionModal = false;
    public $transitionAppId = null;
    public $transitionApplication = null;
    public $transitionTargetStatus = '';
    public $transitionNotes = '';
    public $transitionValidationErrors = [];

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->loadAuxiliaryData();
        $this->dispatchReviewDueDate = now()->addDays(14)->format('Y-m-d');
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
        abort_unless(Auth::user()->can('create-application') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isFormOpen = true;
    }

    public function openEditApplicationForm($id)
    {
        abort_unless(Auth::user()->can('edit-application') || Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
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
            abort_unless(Auth::user()->can('edit-application') || Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
            $app = Application::findOrFail($this->app_id);
            $app->update([
                'status' => $this->app_status,
                'disqualification_reason' => $this->app_disqualification_reason,
            ]);
            session()->flash('message', 'Application status updated successfully.');
        } else {
            abort_unless(Auth::user()->can('create-application') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
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
        abort_unless(Auth::user()->can('delete-application') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        Application::destroy($id);
        session()->flash('message', 'Application record deleted successfully.');
    }

    public function viewApplicationDetails($id)
    {
        abort_unless(Auth::user()->can('view-application') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->selectedRecordId = $id;
        $this->selectedRecordDetails = Application::with('user.basicInfo', 'campaign', 'billingLedger', 'evaluations.boardMember', 'reviews.reviewer.reviewerProfile')->findOrFail($id);
        $this->isDetailsOpen = true;
    }

    // =========================================================================
    // 🚀 DISPATCH TO REVIEWERS MODAL LOGIC DIRECT ON /APPLICATIONS
    // =========================================================================
    public function openDispatchModal($applicationId)
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        
        $this->dispatchAppId = $applicationId;
        $this->dispatchApplication = Application::with(['campaign', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])->findOrFail($applicationId);
        $this->selectedReviewerIds = [];
        $this->dispatchTargetRequiredExperts = $this->dispatchApplication->campaign->getRequiredPeerReviewersCount();
        $this->dispatchAlreadyAssignedCount = $this->dispatchApplication->reviews->count();
        $this->dispatchRemainingRequiredExperts = max(0, $this->dispatchTargetRequiredExperts - $this->dispatchAlreadyAssignedCount);
        $this->dispatchReviewNotes = "Please evaluate the candidate's research publications, impact, and suitability for the position of {$this->dispatchApplication->campaign->title} under statutory appointment criteria.";
        $this->dispatchReviewDueDate = now()->addDays(14)->format('Y-m-d');
        $this->filterByFieldOnly = true;
        $this->dispatchReviewerSearch = '';
        $this->dispatchDisciplineFilter = '';
        $this->resetErrorBag();
        $this->showDispatchModal = true;
    }

    public function closeDispatchModal()
    {
        $this->showDispatchModal = false;
        $this->showEmpanelModal = false;
        $this->dispatchAppId = null;
        $this->dispatchApplication = null;
        $this->selectedReviewerIds = [];
        $this->resetErrorBag();
    }

    public function toggleReviewerSelection($userId)
    {
        if (in_array($userId, $this->selectedReviewerIds)) {
            $this->selectedReviewerIds = array_values(array_diff($this->selectedReviewerIds, [$userId]));
        } else {
            $this->selectedReviewerIds[] = $userId;
        }
    }

    public function openEmpanelModal()
    {
        $this->reset(['empanelName', 'empanelEmail', 'empanelUniversity', 'empanelPhone', 'empanelSpecialization']);
        $rawSubject = $this->dispatchApplication?->campaign?->department ?? '';
        $this->empanelSubject = AcademicDisciplineMatcher::normalizeDepartment($rawSubject);
        $this->empanelRank = 'Professor';
        $this->empanelCountry = 'Pakistan';
        $this->resetErrorBag();
        $this->showEmpanelModal = true;
    }

    public function closeEmpanelModal()
    {
        $this->showEmpanelModal = false;
        $this->resetErrorBag();
    }

    public function createAndAssignReviewer()
    {
        abort_unless(Auth::user()->can('create-reviewer') || Auth::user()->can('manage-reviewers') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $this->validate([
            'empanelName' => 'required|string|min:3|max:150',
            'empanelEmail' => 'required|email|unique:users,email',
            'empanelUniversity' => 'required|string|min:2|max:200',
            'empanelSubject' => 'required|string|min:2|max:150',
            'empanelRank' => 'required|string',
            'empanelCountry' => 'required|string',
        ], [
            'empanelName.required' => 'Please specify expert name.',
            'empanelEmail.required' => 'Please provide academic email.',
            'empanelUniversity.required' => 'Please specify expert institution.',
        ]);

        $plainPassword = 'Rev@' . rand(100000, 999999);

        $user = User::create([
            'email' => trim($this->empanelEmail),
            'password' => Hash::make($plainPassword),
            'is_active' => true,
        ]);

        $user->assignRole('academic-reviewer');

        $profile = ReviewerProfile::create([
            'user_id' => $user->id,
            'name' => trim($this->empanelName),
            'subject' => trim($this->empanelSubject),
            'academic_rank' => trim($this->empanelRank),
            'university' => trim($this->empanelUniversity),
            'country' => trim($this->empanelCountry),
            'phone' => trim($this->empanelPhone),
            'specialization' => trim($this->empanelSpecialization),
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        // Dispatch welcome email
        try {
            Mail::to($user->email)->send(new ReviewerCredentialsMail(
                $profile->name,
                $user->email,
                $plainPassword,
                $profile->subject,
                $profile->academic_rank,
                $profile->university,
                url('/login')
            ));
        } catch (\Exception $e) {
            Log::warning("Could not dispatch reviewer email: " . $e->getMessage());
        }

        // Auto select this newly empanelled expert
        if (!in_array($user->id, $this->selectedReviewerIds)) {
            $this->selectedReviewerIds[] = $user->id;
        }

        $this->showEmpanelModal = false;
        session()->flash('message', "Academic Subject Expert '{$profile->name}' empanelled successfully and assigned to this dispatch!");
    }

    public function dispatchApplicationToReviewers()
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $this->validate([
            'selectedReviewerIds' => 'required|array|min:1',
            'dispatchReviewDueDate' => 'nullable|date|after_or_equal:today',
            'dispatchReviewNotes' => 'nullable|string|max:1000',
        ], [
            'selectedReviewerIds.required' => 'Please select at least one field-related academic reviewer.',
            'selectedReviewerIds.min' => 'Please select at least one field-related academic reviewer.',
        ]);

        $application = Application::with(['campaign.constitution', 'user.basicInfo'])->findOrFail($this->dispatchAppId);
        $assignedCount = 0;

        $constitution = $application->campaign->constitution;
        $enforceStrict = ($constitution && $constitution->enforce_strict_quota) || SystemSetting::get('enforce_strict_peer_review_quorum', false);
        $selectedCount = count($this->selectedReviewerIds);

        if ($enforceStrict && $this->dispatchTargetRequiredExperts > 0) {
            $totalProjected = $this->dispatchAlreadyAssignedCount + $selectedCount;
            if ($totalProjected < $this->dispatchTargetRequiredExperts) {
                $needed = $this->dispatchTargetRequiredExperts - $this->dispatchAlreadyAssignedCount;
                $this->addError('selectedReviewerIds', "Statutory Quota: Exactly {$this->dispatchTargetRequiredExperts} external peer experts are required for {$application->campaign->title}. Please select {$needed} expert(s) (currently selected: {$selectedCount}).");
                return;
            } elseif ($totalProjected > $this->dispatchTargetRequiredExperts) {
                $maxSelectable = max(0, $this->dispatchTargetRequiredExperts - $this->dispatchAlreadyAssignedCount);
                $this->addError('selectedReviewerIds', "Statutory Quota Exceeded: At most {$maxSelectable} additional expert(s) can be selected to fulfill the statutory quorum of {$this->dispatchTargetRequiredExperts}.");
                return;
            }
        }

        foreach ($this->selectedReviewerIds as $reviewerUserId) {
            $reviewerUser = User::with('reviewerProfile')->find($reviewerUserId);
            if (!$reviewerUser) continue;

            $exists = ApplicationReview::where('application_id', $application->id)
                ->where('reviewer_id', $reviewerUserId)
                ->exists();

            if (!$exists) {
                ApplicationReview::create([
                    'application_id' => $application->id,
                    'reviewer_id' => $reviewerUserId,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                    'due_date' => $this->dispatchReviewDueDate ?: now()->addDays(14),
                    'confidential_notes' => $this->dispatchReviewNotes,
                    'status' => 'PENDING',
                ]);

                $assignedCount++;

                // Notify reviewer
                try {
                    Mail::to($reviewerUser->email)->send(new ApplicationAssignedReviewerMail(
                        $reviewerUser->reviewerProfile?->name ?? 'Colleague',
                        $application->user->basicInfo ? trim($application->user->basicInfo->first_name . ' ' . $application->user->basicInfo->last_name) : 'Academic Candidate',
                        $application->campaign->title,
                        $application->campaign->department,
                        $this->dispatchReviewDueDate,
                        $this->dispatchReviewNotes,
                        url('/reviewer/dashboard')
                    ));
                } catch (\Exception $e) {
                    Log::warning("Could not dispatch review assignment email: " . $e->getMessage());
                }
            }
        }

        // Transition application to UNDER_REVIEW
        $application->update(['status' => 'UNDER_REVIEW']);

        session()->flash('message', "Application for '{$application->campaign->title}' successfully dispatched to {$assignedCount} subject expert(s) for external peer review!");
        $this->closeDispatchModal();
    }

    // =========================================================================
    // ⚠️ DIRECT ACTION 2: MARK INCOMPLETE DOSSIER
    // =========================================================================
    public function openIncompleteModal($applicationId)
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->incompleteAppId = $applicationId;
        $app = Application::findOrFail($applicationId);
        $this->incompleteDeficiencyNotes = $app->disqualification_reason ?: 'Candidate dossier is missing certified transcripts or statutory proofs.';
        $this->showIncompleteModal = true;
    }

    public function closeIncompleteModal()
    {
        $this->showIncompleteModal = false;
        $this->incompleteAppId = null;
        $this->incompleteDeficiencyNotes = '';
    }

    public function markAsIncompleteDossier()
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->validate([
            'incompleteDeficiencyNotes' => 'required|string|min:5|max:1000',
        ], [
            'incompleteDeficiencyNotes.required' => 'Please provide specific deficiency details explaining what documents or data are missing.',
        ]);

        $app = Application::findOrFail($this->incompleteAppId);
        $app->update([
            'status' => 'INCOMPLETE_DOSSIER',
            'disqualification_reason' => trim($this->incompleteDeficiencyNotes),
        ]);

        session()->flash('message', "Application for '{$app->campaign->title}' marked as Incomplete Dossier. Deficiency notes recorded.");
        $this->closeIncompleteModal();
    }

    // =========================================================================
    // 🚫 DIRECT ACTION 3: REJECT / DISQUALIFY APPLICATION
    // =========================================================================
    public function openRejectModal($applicationId)
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->rejectAppId = $applicationId;
        $app = Application::findOrFail($applicationId);
        $this->rejectReason = $app->disqualification_reason ?: '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectAppId = null;
        $this->rejectReason = '';
    }

    public function rejectApplication()
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->validate([
            'rejectReason' => 'required|string|min:5|max:1000',
        ], [
            'rejectReason.required' => 'Please provide the formal justification reason for disqualification or rejection.',
        ]);

        $app = Application::findOrFail($this->rejectAppId);
        $app->update([
            'status' => 'DISQUALIFIED',
            'disqualification_reason' => trim($this->rejectReason),
        ]);

        session()->flash('message', "Application for '{$app->campaign->title}' has been rejected / disqualified. Justification recorded.");
        $this->closeRejectModal();
    }

    // =========================================================================
    // ✅ DIRECT ACTION 4: MARK AS ELIGIBLE
    // =========================================================================
    public function markAsEligible($applicationId)
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $app = Application::findOrFail($applicationId);
        $app->update([
            'status' => 'ELIGIBLE',
            'disqualification_reason' => null,
        ]);
        session()->flash('message', "Application for '{$app->campaign->title}' successfully marked as Eligible for Selection Board!");
    }

    // =========================================================================
    // 🔄 DIRECT ACTION 5: DYNAMIC STATUS TRANSITION MODAL
    // =========================================================================
    public function openTransitionModal($applicationId)
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->transitionAppId = $applicationId;
        $this->transitionApplication = Application::with(['campaign', 'user.basicInfo', 'billingLedger', 'reviews'])->findOrFail($applicationId);
        $this->transitionTargetStatus = '';
        $this->transitionNotes = '';
        $this->transitionValidationErrors = [];
        $this->showTransitionModal = true;
    }

    public function closeTransitionModal()
    {
        $this->showTransitionModal = false;
        $this->transitionAppId = null;
        $this->transitionApplication = null;
        $this->transitionTargetStatus = '';
        $this->transitionNotes = '';
        $this->transitionValidationErrors = [];
    }

    public function executeStatusTransition()
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->validate([
            'transitionTargetStatus' => 'required|string',
        ]);

        $app = Application::with(['campaign', 'billingLedger', 'reviews'])->findOrFail($this->transitionAppId);

        // Dynamic gate validation check
        $validation = WorkflowStatus::validateApplicationTransition($app, $this->transitionTargetStatus, $this->transitionNotes);
        if (!$validation['allowed']) {
            $this->transitionValidationErrors = $validation['errors'];
            return;
        }

        $app->update([
            'status' => $this->transitionTargetStatus,
            'disqualification_reason' => $this->transitionNotes ?: $app->disqualification_reason,
        ]);

        session()->flash('message', "Application successfully transitioned to '{$app->status_label}'.");
        $this->closeTransitionModal();
    }

    public function evaluatePeerReviewResults($applicationId)
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $app = Application::with(['campaign.constitution', 'campaign.processFlow', 'reviews'])->findOrFail($applicationId);
        
        $verdict = $app->evaluatePeerReviewVerdict();
        
        if (!$verdict['is_complete']) {
            session()->flash('error', "Cannot evaluate yet: {$verdict['total_submitted']} of {$verdict['required_count']} expert review(s) completed.");
            return;
        }

        if ($verdict['passed']) {
            $app->update([
                'status' => 'ELIGIBLE',
            ]);
            session()->flash('message', "🎉 Candidate passed Academic Peer Review! Positive recommendations: {$verdict['positive_count']}/{$verdict['total_submitted']} (Threshold: {$verdict['min_positive_needed']}). Candidate advanced to ELIGIBLE.");
        } else {
            $app->update([
                'status' => 'REJECTED',
            ]);
            session()->flash('warning', "❌ Candidate failed Academic Peer Review criteria. Positive recommendations: {$verdict['positive_count']}/{$verdict['total_submitted']} (Required: {$verdict['min_positive_needed']}). Candidate marked as REJECTED (Appellate / Grievance Right Open).");
        }
    }

    public function exportCsv()
    {
        abort_unless(Auth::user()->can('view-application'), 403);
        $applications = Application::with('user.basicInfo', 'campaign', 'billingLedger', 'reviews')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = ['Tracking #', 'Applicant Name', 'Email', 'CNIC', 'Campaign Title', 'Job Code', 'Career Track', 'Challan Status', 'Processing Stage', 'Submission Date'];
        $rows = [];
        foreach ($applications as $app) {
            $name = $app->user?->basicInfo 
                ? ($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name)
                : 'N/A';
            $isPaid = $app->billingLedger?->status === 'SETTLED';
            $rows[] = [
                $app->tracking_number,
                $name,
                $app->user?->email ?? 'N/A',
                $app->user?->basicInfo?->cnic_number ?? 'N/A',
                $app->campaign?->title ?? 'N/A',
                $app->campaign?->job_code ?? 'N/A',
                $app->campaign?->track_type ?? 'N/A',
                $isPaid ? 'Settled / Paid' : 'Pending Payment',
                $app->status_label ?? $app->status,
                $app->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->streamCsvDownload('job-applications-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function render()
    {
        $query = Application::with('user.basicInfo', 'campaign', 'billingLedger', 'reviews.reviewer.reviewerProfile');

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

        $applications = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        // Field-matched reviewers for dispatch modal if open
        $fieldMatchedReviewers = collect();
        if ($this->showDispatchModal && $this->dispatchApplication) {
            $dept = trim($this->dispatchApplication->campaign->department);
            $campTitle = trim($this->dispatchApplication->campaign->title ?? '');

            $fieldMatchedReviewers = AcademicDisciplineMatcher::queryForCandidacy(
                $dept,
                $campTitle,
                $this->dispatchReviewerSearch,
                $this->dispatchDisciplineFilter,
                $this->filterByFieldOnly
            )->get();
        }

        // Distinct disciplines for dispatch filter
        $availableDisciplines = ReviewerProfile::distinct()->pluck('subject')->filter()->values();

        // Dynamic System KPI Metrics
        $stats = [
            'total' => Application::count(),
            'paid' => Application::whereHas('billingLedger', fn($b) => $b->where('status', 'SETTLED'))->count(),
            'eligible' => Application::where('status', 'ELIGIBLE')->count(),
            'under_review' => Application::whereIn('status', ['UNDER_REVIEW', 'APPEALED'])->count(),
            'approved' => Application::where('status', 'APPROVED')->count(),
        ];

        // All active workflow statuses for filter dropdown
        $workflowStatuses = WorkflowStatus::forScope('APPLICATION')->where('is_active', true)->orderBy('order')->get();

        return view('livewire.admin.application-management', [
            'applications' => $applications,
            'stats' => $stats,
            'fieldMatchedReviewers' => $fieldMatchedReviewers,
            'availableDisciplines' => $availableDisciplines,
            'workflowStatuses' => $workflowStatuses,
            'countries' => CountryService::all(),
        ])->layout('layouts.app', ['title' => 'Applications Desk - Hirely']);
    }
}
