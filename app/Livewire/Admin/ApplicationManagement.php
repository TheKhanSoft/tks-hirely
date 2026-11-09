<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\User;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\ReviewerProfile;
use App\Models\WorkflowStatus;
use App\Models\SystemSetting;
use App\Services\AcademicDisciplineMatcher;
use App\Services\CountryService;
use App\Mail\ApplicationAssignedReviewerMail;
use App\Mail\ReviewerCredentialsMail;
use App\Services\MailNotificationService;
use App\Services\Sms\SmsGatewayService;
use App\Models\SecurityAuditLog;
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

    // Filters & Search
    #[Url(as: 'q', except: '')]
    public $search = '';

    #[Url(as: 'status', except: '')]
    public $statusFilter = '';

    #[Url(as: 'camp', except: '')]
    public $campaignFilter = '';

    #[Url(as: 'ad', except: 'ALL')]
    public $advertisementFilter = '';

    #[Url(as: 'voucher', except: '')]
    public $voucherFilter = '';

    public $sortBy = 'created_at';
    public $sortDir = 'desc';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

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

    // =========================================================================
    // 📜 DIRECT ACTION 5: STATUTORY APPOINTMENT & OFFER LETTER
    // =========================================================================
    public $showAppointmentLetterModal = false;
    public $appointmentApplication = null;

    public function openAppointmentLetterModal($appId)
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->can('edit-application') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $this->appointmentApplication = Application::with([
            'user.basicInfo.degrees',
            'campaign.advertisement',
            'evaluations',
        ])->findOrFail($appId);
        $this->showAppointmentLetterModal = true;
    }

    public function closeAppointmentLetterModal()
    {
        $this->showAppointmentLetterModal = false;
        $this->appointmentApplication = null;
    }

    // =========================================================================
    // ✉️ DIRECT ACTION 6: STATUTORY INTERVIEW CALL LETTER GENERATOR & DISPATCH
    // =========================================================================
    public $showCallLetterModal = false;
    public $callLetterAppId = null;
    public $callLetterApplication = null;
    public $callLetterDate = '';
    public $callLetterTime = '10:00';
    public $callLetterReportingTime = '09:00';
    public $callLetterVenue = 'Main Administration Block, University Secretariat';
    public $callLetterCommitteeRoom = 'Syndicate & Selection Board Conference Room, 2nd Floor';
    public $callLetterInstructions = 'Bring original CNIC, degrees, transcripts, NOC (if employed), and publication reprints.';
    public $callLetterSendEmail = true;
    public $callLetterSendSms = true;

    public function openCallLetterModal($appId)
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        if (!SystemSetting::isFeatureEnabled('interview_call_letters')) {
            session()->flash('error', 'Statutory Interview Call Letter module is currently disabled in System Settings.');
            return;
        }

        $this->callLetterAppId = $appId;
        $this->callLetterApplication = Application::with(['user.basicInfo', 'campaign'])->findOrFail($appId);

        $this->callLetterDate = $this->callLetterApplication->interview_date ? $this->callLetterApplication->interview_date->format('Y-m-d') : now()->addDays(7)->format('Y-m-d');
        $this->callLetterTime = $this->callLetterApplication->interview_time ?: '10:00';
        $this->callLetterReportingTime = $this->callLetterApplication->interview_reporting_time ?: '09:00';
        $this->callLetterVenue = $this->callLetterApplication->interview_venue ?: 'Main Administration Block, University Secretariat';
        $this->callLetterCommitteeRoom = $this->callLetterApplication->interview_committee_room ?: 'Syndicate & Selection Board Conference Room, 2nd Floor';
        $this->callLetterInstructions = $this->callLetterApplication->interview_instructions ?: 'Bring original CNIC, degrees, transcripts, NOC (if employed), and publication reprints.';
        $this->callLetterSendEmail = true;
        $this->callLetterSendSms = true;
        $this->showCallLetterModal = true;
    }

    public function closeCallLetterModal()
    {
        $this->showCallLetterModal = false;
        $this->callLetterAppId = null;
        $this->callLetterApplication = null;
    }

    public function issueCallLetter()
    {
        abort_unless(Auth::user()->can('change-application-status') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        if (!SystemSetting::isFeatureEnabled('interview_call_letters')) {
            session()->flash('error', 'Statutory Interview Call Letter module is currently disabled in System Settings.');
            return;
        }

        $this->validate([
            'callLetterDate' => 'required|date',
            'callLetterTime' => 'required|string',
            'callLetterReportingTime' => 'required|string',
            'callLetterVenue' => 'required|string|max:255',
            'callLetterCommitteeRoom' => 'required|string|max:255',
            'callLetterInstructions' => 'nullable|string|max:2000',
        ]);

        $app = Application::with(['user.basicInfo', 'campaign'])->findOrFail($this->callLetterAppId);

        $targetStatus = in_array($app->status, ['DISQUALIFIED', 'REJECTED']) ? $app->status : 'INTERVIEW_SCHEDULED';

        $app->update([
            'interview_date' => $this->callLetterDate,
            'interview_time' => $this->callLetterTime,
            'interview_reporting_time' => $this->callLetterReportingTime,
            'interview_venue' => $this->callLetterVenue,
            'interview_committee_room' => $this->callLetterCommitteeRoom,
            'interview_instructions' => $this->callLetterInstructions,
            'interview_call_letter_issued_at' => now(),
            'status' => $targetStatus,
        ]);

        // Email Dispatch
        if ($this->callLetterSendEmail && $app->user?->email) {
            MailNotificationService::triggerInterviewCall($app, [
                'date' => \Carbon\Carbon::parse($this->callLetterDate)->format('l, d F Y'),
                'time' => $this->callLetterTime,
                'reporting_time' => $this->callLetterReportingTime,
                'venue' => $this->callLetterVenue . ' (' . $this->callLetterCommitteeRoom . ')',
                'docs' => $this->callLetterInstructions,
            ]);
        }

        // SMS Dispatch
        $mobile = $app->user?->basicInfo?->mobile_number;
        if ($this->callLetterSendSms && $mobile) {
            $smsMsg = "Interview Call: You are scheduled for Selection Board interview for {$app->campaign?->title} on " . \Carbon\Carbon::parse($this->callLetterDate)->format('d-M-Y') . " at {$this->callLetterTime}. Venue: {$this->callLetterVenue}. Download official call letter from your portal.";
            SmsGatewayService::send($mobile, $smsMsg);
        }

        SecurityAuditLog::record(
            'INTERVIEW_CALL_LETTER_ISSUED',
            'Application',
            $app->id,
            [
                'interview_date' => $this->callLetterDate,
                'interview_time' => $this->callLetterTime,
                'candidate' => $app->user?->name,
                'tracking' => $app->tracking_number,
                'email_dispatched' => $this->callLetterSendEmail,
                'sms_dispatched' => $this->callLetterSendSms,
            ],
            null,
            Auth::id()
        );

        session()->flash('message', "Statutory Interview Call Letter successfully issued and dispatched for candidate {$app->tracking_number}!");
        $this->closeCallLetterModal();
    }

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'admin'])
        ), 403);

        // Default to the latest advertisement if none was explicitly provided
        if (empty($this->advertisementFilter)) {
            $lastAd = Advertisement::chronologicalDesc()->first();
            if ($lastAd) {
                $this->advertisementFilter = (string)$lastAd->id;
            }
        }

        $this->loadAuxiliaryData();
        $this->dispatchReviewDueDate = now()->addDays(14)->format('Y-m-d');
    }

    public function updatedAdvertisementFilter()
    {
        $this->resetPage();

        // Only reset campaignFilter if the currently selected campaign does not belong to the newly selected ad.
        // If switching to 'ALL', keep the active campaignFilter so user doesn't lose their context.
        if (!empty($this->campaignFilter) && !empty($this->advertisementFilter) && $this->advertisementFilter !== 'ALL') {
            $camp = Campaign::find($this->campaignFilter);
            if ($camp && (string)$camp->advertisement_id !== (string)$this->advertisementFilter) {
                $this->campaignFilter = '';
            }
        }

        $this->loadAuxiliaryData();
    }

    public function updatedCampaignFilter()
    {
        $this->resetPage();

        // If a campaign is selected and its parent advertisement is known, sync the advertisementFilter to match
        if (!empty($this->campaignFilter)) {
            $camp = Campaign::find($this->campaignFilter);
            if ($camp && $camp->advertisement_id) {
                if ((string)$this->advertisementFilter !== (string)$camp->advertisement_id) {
                    $this->advertisementFilter = (string)$camp->advertisement_id;
                }
            }
        }

        $this->loadAuxiliaryData();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedVoucherFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Toggle a specific status filter or reset to empty if clicked again.
     */
    public function toggleStatusFilter($status)
    {
        $this->statusFilter = ($this->statusFilter === $status) ? '' : $status;
        $this->resetPage();
    }

    /**
     * Clear all active application filters and reload datasets.
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->campaignFilter = '';
        $this->statusFilter = '';
        $this->voucherFilter = '';
        $this->advertisementFilter = 'ALL';
        $this->sortBy = 'created_at';
        $this->sortDir = 'desc';
        $this->loadAuxiliaryData();
        $this->resetPage();
    }

    public function loadAuxiliaryData()
    {
        $this->users = User::role('applicant')->get();
        $campaignQuery = Campaign::query();

        if (!empty($this->advertisementFilter) && $this->advertisementFilter !== 'ALL') {
            $campaignQuery->where(function ($q) {
                $q->where('advertisement_id', $this->advertisementFilter);
                // Retain currently selected campaign in options so browser <select> doesn't lose selection on DOM patch
                if (!empty($this->campaignFilter)) {
                    $q->orWhere('id', $this->campaignFilter);
                }
            });
        }

        $this->campaigns = $campaignQuery->orderBy('id', 'desc')->get();
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
        $app = Application::with('campaign')->findOrFail($id);
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            session()->flash('error', 'Recruitment for this position has closed or completed. Modifying applications requires Super Admin privileges.');
            return;
        }

        $this->resetForm();
        $this->app_id = $id;
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
            $app = Application::with('campaign')->findOrFail($this->app_id);
            if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
                session()->flash('error', 'Recruitment for this position has closed or completed. Modifying applications requires Super Admin privileges.');
                return;
            }

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
        $app = Application::with('campaign')->findOrFail($id);

        if (!Auth::user()->hasRole('super-admin')) {
            if ($app->campaign?->is_completed || $app->campaign?->is_closed) {
                session()->flash('error', 'Security Policy: Applications in closed or completed recruitment cycles cannot be deleted.');
                return;
            }
            if (in_array($app->status, ['APPOINTED', 'OFFERED', 'SELECTED'])) {
                session()->flash('error', 'Security Policy: Finalized or appointed applications cannot be deleted. Contact Super Admin for emergency adjustments.');
                return;
            }
        }

        $app->delete();
        session()->flash('message', 'Application record deleted successfully.');
    }

    public function viewApplicationDetails($id)
    {
        abort_unless(Auth::user()->can('view-application') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->selectedRecordId = $id;
        $this->selectedRecordDetails = Application::with('user.basicInfo.degrees', 'campaign.advertisement', 'campaign.constitution', 'billingLedger', 'evaluations.boardMember', 'reviews.reviewer.reviewerProfile')->findOrFail($id);
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
        if (!Auth::user()->hasRole('super-admin') && ($this->dispatchApplication->campaign?->is_completed || $this->dispatchApplication->campaign?->is_closed)) {
            abort(403, 'Reviewers cannot be dispatched for a closed or completed campaign.');
        }

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
        abort_unless(Auth::user()->can('create-reviewer') || Auth::user()->can('manage-reviewers') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

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
                $review = ApplicationReview::create([
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
                    MailNotificationService::triggerReviewerDossierAssigned($review);
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
        $app = Application::with('campaign')->findOrFail($applicationId);
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            abort(403, 'Application dossier status cannot be altered for a closed or completed campaign.');
        }

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

        $app = Application::with('campaign')->findOrFail($this->incompleteAppId);
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            abort(403, 'Application dossier status cannot be altered for a closed or completed campaign.');
        }

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
        $app = Application::with('campaign')->findOrFail($applicationId);
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            abort(403, 'Application cannot be rejected or disqualified for a closed or completed campaign.');
        }

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

        $app = Application::with('campaign')->findOrFail($this->rejectAppId);
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            abort(403, 'Application cannot be rejected or disqualified for a closed or completed campaign.');
        }

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
        $app = Application::with('campaign')->findOrFail($applicationId);
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            abort(403, 'Application eligibility cannot be altered for a closed or completed campaign.');
        }

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
        if (!Auth::user()->hasRole('super-admin') && ($this->transitionApplication->campaign?->is_completed || $this->transitionApplication->campaign?->is_closed)) {
            abort(403, 'Workflow transition is locked for closed or completed campaigns.');
        }

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
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            abort(403, 'Workflow transition is locked for closed or completed campaigns.');
        }

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
        if (!Auth::user()->hasRole('super-admin') && ($app->campaign?->is_completed || $app->campaign?->is_closed)) {
            abort(403, 'Peer review evaluation is locked for closed or completed campaigns.');
        }
        
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
        abort_unless(Auth::user()->can('view-application') || Auth::user()->can('export-application'), 403);
        $applicationsQuery = Application::with('user.basicInfo', 'campaign', 'billingLedger', 'reviews')
            ->orderBy('created_at', 'desc');

        if (!empty($this->statusFilter)) {
            $applicationsQuery->where('status', $this->statusFilter);
        }

        if (!empty($this->campaignFilter)) {
            $applicationsQuery->where('campaign_id', $this->campaignFilter);
        }

        if (!empty($this->advertisementFilter) && $this->advertisementFilter !== 'ALL') {
            $applicationsQuery->whereHas('campaign', fn($c) => $c->where('advertisement_id', $this->advertisementFilter));
        }

        if (!empty($this->voucherFilter)) {
            if ($this->voucherFilter === 'SETTLED') {
                $applicationsQuery->whereHas('billingLedger', fn($b) => $b->where('status', 'SETTLED'));
            } elseif ($this->voucherFilter === 'PENDING') {
                $applicationsQuery->where(function ($q) {
                    $q->whereHas('billingLedger', fn($b) => $b->where('status', '!=', 'SETTLED'))
                      ->orWhereDoesntHave('billingLedger');
                });
            }
        }

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $applicationsQuery->where(function ($q) use ($searchTerm) {
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

        $applications = $applicationsQuery->get();

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
        $query = Application::with('user.basicInfo.degrees', 'campaign.advertisement', 'billingLedger', 'reviews.reviewer.reviewerProfile');

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->campaignFilter)) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if (!empty($this->advertisementFilter) && $this->advertisementFilter !== 'ALL') {
            $query->whereHas('campaign', fn($c) => $c->where('advertisement_id', $this->advertisementFilter));
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

        // Dynamic Column Sorting
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
        } elseif ($this->sortBy === 'status') {
            $query->orderBy('applications.status', $this->sortDir);
        } elseif ($this->sortBy === 'submission_timestamp') {
            $query->orderBy('applications.submission_timestamp', $this->sortDir);
        } else {
            $query->orderBy('applications.created_at', $this->sortDir);
        }

        $applications = $query->paginate($this->perPage);

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

        // Dynamic Context-Scoped System KPI Metrics (Scoped strictly to active contextual scope: Advertisement and Campaign)
        $baseStatsQuery = Application::query();
        if (!empty($this->advertisementFilter) && $this->advertisementFilter !== 'ALL') {
            $baseStatsQuery->whereHas('campaign', fn($c) => $c->where('advertisement_id', $this->advertisementFilter));
        }
        if (!empty($this->campaignFilter)) {
            $baseStatsQuery->where('campaign_id', $this->campaignFilter);
        }

        $selectedAdModel = (!empty($this->advertisementFilter) && $this->advertisementFilter !== 'ALL')
            ? Advertisement::find($this->advertisementFilter)
            : null;

        $selectedCampaignModel = (!empty($this->campaignFilter))
            ? Campaign::find($this->campaignFilter)
            : null;

        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'paid' => (clone $baseStatsQuery)->whereHas('billingLedger', fn($b) => $b->where('status', 'SETTLED'))->count(),
            'eligible' => (clone $baseStatsQuery)->where('status', 'ELIGIBLE')->count(),
            'under_review' => (clone $baseStatsQuery)->whereIn('status', ['UNDER_REVIEW', 'APPEALED'])->count(),
            'approved' => (clone $baseStatsQuery)->where('status', 'APPROVED')->count(),
            'overall_system_total' => Application::count(),
        ];

        // All active workflow statuses for filter dropdown
        $workflowStatuses = WorkflowStatus::forScope('APPLICATION')->where('is_active', true)->orderBy('order')->get();

        return view('livewire.admin.application-management', [
            'applications' => $applications,
            'stats' => $stats,
            'selectedAdModel' => $selectedAdModel,
            'selectedCampaignModel' => $selectedCampaignModel,
            'fieldMatchedReviewers' => $fieldMatchedReviewers,
            'availableDisciplines' => $availableDisciplines,
            'workflowStatuses' => $workflowStatuses,
            'countries' => CountryService::all(),
            'advertisements' => Advertisement::where('is_active', true)->chronologicalDesc()->get(),
        ])->layout('layouts.app', ['title' => 'Applications Desk - Hirely']);
    }
}
