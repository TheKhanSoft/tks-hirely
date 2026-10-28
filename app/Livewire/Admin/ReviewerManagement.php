<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\ReviewerProfile;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Mail\ReviewerCredentialsMail;
use App\Mail\ApplicationAssignedReviewerMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\CountryService;
use App\Services\AcademicDisciplineMatcher;
use App\Models\Campaign;

class ReviewerManagement extends Component
{
    use WithPagination;

    // Active Navigation Tab: 'reviewers' or 'dispatch'
    public $activeTab = 'reviewers';

    // Reviewer Creation Form Properties
    public $showCreateModal = false;
    public $name;
    public $email;
    public $subject;
    public $academic_rank = 'Professor';
    public $university;
    public $country = 'Pakistan';
    public $phone;
    public $specialization;

    // Tab 1: Reviewer Directory Filter & Search Properties
    public $search = '';
    public $subjectFilter = '';
    public $rankFilter = '';
    public $countryFilter = '';

    // Tab 2: Academic Candidacies Filter & Search Properties
    public $candidacySearch = '';
    public $candidacyCampaignFilter = '';
    public $candidacyStatusFilter = 'ALL'; // ALL, NEEDS_DISPATCH, UNDER_REVIEW, COMPLETED

    // Application Review Dispatch Modal Properties
    public $showDispatchModal = false;
    public $showEmpanelAndAssignModal = false;
    public $showInlineEmpanel = false;
    public $targetApplicationId;
    public $targetApplication;
    public $selectedReviewerIds = [];
    public $reviewDueDate;
    public $reviewNotes;
    public $filterByFieldOnly = true;
    public $dispatchReviewerSearch = '';
    public $dispatchDisciplineFilter = '';
    public $targetRequiredExperts = 3;
    public $alreadyAssignedCount = 0;
    public $remainingRequiredExperts = 3;

    // View Review Reports Modal Properties
    public $showReportsModal = false;
    public $viewingApplication;
    public $applicationReviews = [];

    protected $rules = [
        'name' => 'required|string|min:3|max:150',
        'email' => 'required|email|unique:users,email',
        'subject' => 'required|string|min:2|max:100',
        'academic_rank' => 'required|string',
        'university' => 'required|string|min:2|max:150',
        'country' => 'required|string',
        'phone' => 'nullable|string|max:30',
        'specialization' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->can('manage-reviewers'), 403);
        $this->reviewDueDate = now()->addDays(14)->format('Y-m-d');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openCreateModal($prefilledSubject = null)
    {
        abort_unless(Auth::user()->can('create-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $this->reset(['name', 'email', 'university', 'phone', 'specialization']);
        
        if (!empty($prefilledSubject)) {
            $this->subject = $prefilledSubject;
        } elseif ($this->showDispatchModal && $this->targetApplication && $this->targetApplication->campaign) {
            $this->subject = AcademicDisciplineMatcher::normalizeDepartment($this->targetApplication->campaign->department);
        } else {
            $this->subject = '';
        }

        $this->academic_rank = 'Professor';
        $this->country = 'Pakistan';
        $this->resetErrorBag();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetErrorBag();
    }

    public function createReviewer()
    {
        abort_unless(Auth::user()->can('create-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $this->validate();

        // 1. Generate a secure temporary password
        $plainPassword = 'Rev@' . rand(100000, 999999);

        // 2. Create User account
        $user = User::create([
            'email' => trim($this->email),
            'password' => Hash::make($plainPassword),
            'is_active' => true,
        ]);

        // 3. Assign academic-reviewer role
        $user->assignRole('academic-reviewer');

        // 4. Create Reviewer Profile
        $profile = ReviewerProfile::create([
            'user_id' => $user->id,
            'name' => trim($this->name),
            'subject' => trim($this->subject),
            'academic_rank' => trim($this->academic_rank),
            'university' => trim($this->university),
            'country' => trim($this->country),
            'phone' => trim($this->phone),
            'specialization' => trim($this->specialization),
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        // 5. Send login credentials & guidance email to the reviewer
        $loginUrl = url('/login');
        try {
            Mail::to($user->email)->send(new ReviewerCredentialsMail(
                $profile->name,
                $user->email,
                $plainPassword,
                $profile->subject,
                $profile->academic_rank,
                $profile->university,
                $loginUrl
            ));
            Log::info("Reviewer credentials email sent to {$user->email} for {$profile->name}");
        } catch (\Exception $e) {
            Log::warning("Could not dispatch reviewer credentials email via SMTP: " . $e->getMessage());
        }

        // If empanelled while in Dispatch modal or via Empanel & Assign modal, auto-select this new reviewer
        if ($this->showEmpanelAndAssignModal || ($this->showDispatchModal && $this->targetApplication)) {
            if (!in_array($user->id, $this->selectedReviewerIds)) {
                $this->selectedReviewerIds[] = $user->id;
            }
            session()->flash('message', "Academic Subject Expert '{$profile->name}' empanelled successfully and assigned to this review dispatch! Credentials dispatched to {$user->email}.");
        } else {
            session()->flash('message', "Academic Reviewer '{$profile->name}' empanelled successfully! Credentials and evaluation guidance dispatched to {$user->email}. (Temporary Password: {$plainPassword})");
        }

        $this->showCreateModal = false;
        $this->showEmpanelAndAssignModal = false;
        $this->showInlineEmpanel = false;
    }

    public function openEmpanelAndAssignModal($prefilledSubject = null)
    {
        $this->reset(['name', 'email', 'university', 'phone', 'specialization']);
        $rawSubject = $prefilledSubject ?: ($this->targetApplication?->campaign?->department ?? '');
        $this->subject = AcademicDisciplineMatcher::normalizeDepartment($rawSubject);
        $this->academic_rank = 'Professor';
        $this->country = 'Pakistan';
        $this->resetErrorBag();
        $this->showEmpanelAndAssignModal = true;
    }

    public function closeEmpanelAndAssignModal()
    {
        $this->showEmpanelAndAssignModal = false;
        $this->resetErrorBag();
    }

    public function toggleInlineEmpanel($prefilledSubject = null)
    {
        $this->openEmpanelAndAssignModal($prefilledSubject);
    }

    public function toggleReviewerStatus($profileId)
    {
        abort_unless(Auth::user()->can('edit-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $profile = ReviewerProfile::findOrFail($profileId);
        $profile->is_active = !$profile->is_active;
        $profile->save();

        if ($profile->user) {
            $profile->user->is_active = $profile->is_active;
            $profile->user->save();
        }

        session()->flash('message', "Reviewer '{$profile->name}' status updated to " . ($profile->is_active ? 'Active' : 'Inactive') . ".");
    }

    public function openDispatchModal($applicationId)
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $this->targetApplicationId = $applicationId;
        $this->targetApplication = Application::with(['campaign', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])->findOrFail($applicationId);
        $this->selectedReviewerIds = [];
        $this->targetRequiredExperts = $this->targetApplication->campaign->getRequiredPeerReviewersCount();
        $this->alreadyAssignedCount = $this->targetApplication->reviews->count();
        $this->remainingRequiredExperts = max(0, $this->targetRequiredExperts - $this->alreadyAssignedCount);
        $this->reviewNotes = "Please evaluate the candidate's research publications, impact, and suitability for the position of {$this->targetApplication->campaign->title} under statutory appointment criteria.";
        $this->reviewDueDate = now()->addDays(14)->format('Y-m-d');
        $this->filterByFieldOnly = true;
        $this->dispatchReviewerSearch = '';
        $this->dispatchDisciplineFilter = '';
        $this->resetErrorBag();
        $this->showDispatchModal = true;
    }

    public function closeDispatchModal()
    {
        $this->showDispatchModal = false;
        $this->showEmpanelAndAssignModal = false;
        $this->showInlineEmpanel = false;
        $this->targetApplicationId = null;
        $this->targetApplication = null;
        $this->selectedReviewerIds = [];
        $this->dispatchReviewerSearch = '';
        $this->dispatchDisciplineFilter = '';
        $this->resetErrorBag();
    }

    public function toggleReviewerSelection($userId)
    {
        if (in_array($userId, $this->selectedReviewerIds)) {
            $this->selectedReviewerIds = array_diff($this->selectedReviewerIds, [$userId]);
        } else {
            $this->selectedReviewerIds[] = $userId;
        }
    }

    public function dispatchApplicationToReviewers()
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $this->validate([
            'selectedReviewerIds' => 'required|array|min:1',
            'reviewDueDate' => 'nullable|date|after_or_equal:today',
            'reviewNotes' => 'nullable|string|max:1000',
        ], [
            'selectedReviewerIds.required' => 'Please select at least one field-related academic reviewer.',
            'selectedReviewerIds.min' => 'Please select at least one field-related academic reviewer.',
        ]);

        $application = Application::with(['campaign', 'user.basicInfo'])->findOrFail($this->targetApplicationId);
        $assignedCount = 0;

        $enforceStrict = \App\Models\SystemSetting::get('enforce_strict_peer_review_quorum', false);
        $selectedCount = count($this->selectedReviewerIds);

        if ($enforceStrict && $this->targetRequiredExperts > 0) {
            $totalProjected = $this->alreadyAssignedCount + $selectedCount;
            if ($totalProjected < $this->targetRequiredExperts) {
                $needed = $this->targetRequiredExperts - $this->alreadyAssignedCount;
                $this->addError('selectedReviewerIds', "Statutory Quota: Exactly {$this->targetRequiredExperts} external peer experts are required by statutory criteria for {$application->campaign->title}. Please select {$needed} expert(s) to satisfy the statutory quorum (currently selected: {$selectedCount}).");
                return;
            } elseif ($totalProjected > $this->targetRequiredExperts) {
                $maxSelectable = max(0, $this->targetRequiredExperts - $this->alreadyAssignedCount);
                $this->addError('selectedReviewerIds', "Statutory Quota Exceeded: At most {$maxSelectable} additional expert(s) can be selected to fulfill the statutory quorum of {$this->targetRequiredExperts}.");
                return;
            }
        }

        foreach ($this->selectedReviewerIds as $reviewerUserId) {
            $reviewerUser = User::with('reviewerProfile')->find($reviewerUserId);
            if (!$reviewerUser) continue;

            // Check if already assigned
            $exists = ApplicationReview::where('application_id', $application->id)
                ->where('reviewer_id', $reviewerUserId)
                ->exists();

            if (!$exists) {
                ApplicationReview::create([
                    'application_id' => $application->id,
                    'reviewer_id' => $reviewerUserId,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                    'due_date' => $this->reviewDueDate ?: now()->addDays(14),
                    'confidential_notes' => $this->reviewNotes,
                    'status' => 'PENDING',
                ]);

                $assignedCount++;

                // Notify reviewer via email
                $evalUrl = url('/reviewer/dashboard');
                try {
                    Mail::to($reviewerUser->email)->send(new ApplicationAssignedReviewerMail(
                        $reviewerUser->reviewerProfile?->name ?? 'Colleague',
                        $application->user->basicInfo ? trim($application->user->basicInfo->first_name . ' ' . $application->user->basicInfo->last_name) : 'Academic Candidate',
                        $application->campaign->title,
                        $application->campaign->department,
                        $this->reviewDueDate,
                        $this->reviewNotes,
                        $evalUrl
                    ));
                    Log::info("Review assignment email sent to {$reviewerUser->email}");
                } catch (\Exception $e) {
                    Log::warning("Could not dispatch review assignment email: " . $e->getMessage());
                }
            }
        }

        // Update application status to UNDER_REVIEW
        $application->update(['status' => 'UNDER_REVIEW']);

        session()->flash('message', "Application for '{$application->campaign->title}' successfully sent to {$assignedCount} field expert(s) for academic evaluation!");

        $this->showDispatchModal = false;
        $this->selectedReviewerIds = [];
    }

    public function openReportsModal($applicationId)
    {
        abort_unless(Auth::user()->can('view-review-reports') || Auth::user()->can('manage-reviewers'), 403);
        $this->viewingApplication = Application::with(['campaign', 'user.basicInfo'])->findOrFail($applicationId);
        $this->applicationReviews = ApplicationReview::with(['reviewer.reviewerProfile', 'assigner'])
            ->where('application_id', $applicationId)
            ->get();
        $this->showReportsModal = true;
    }

    public function closeReportsModal()
    {
        $this->showReportsModal = false;
        $this->viewingApplication = null;
        $this->applicationReviews = [];
    }

    public function render()
    {
        // 1. Query Reviewer Profiles (Tab 1)
        $reviewersQuery = ReviewerProfile::with(['user', 'creator'])
            ->whereDoesntHave('user.roles', fn($rq) => $rq->where('name', 'super-admin'))
            ->withCount('reviews');

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $reviewersQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('university', 'like', $s)
                  ->orWhere('subject', 'like', $s)
                  ->orWhere('specialization', 'like', $s)
                  ->orWhereHas('user', fn($uq) => $uq->where('email', 'like', $s));
            });
        }

        if (!empty($this->subjectFilter)) {
            $reviewersQuery->where('subject', $this->subjectFilter);
        }

        if (!empty($this->rankFilter)) {
            $reviewersQuery->where('academic_rank', $this->rankFilter);
        }

        if (!empty($this->countryFilter)) {
            $reviewersQuery->where('country', $this->countryFilter);
        }

        $reviewers = $reviewersQuery->latest()->paginate(10);

        // 2. Query Academic Applications for Review Dispatch (Tab 2)
        $candidaciesQuery = Application::with(['campaign', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])
            ->withCount('reviews')
            ->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'));

        if (!empty($this->candidacySearch)) {
            $cs = '%' . trim($this->candidacySearch) . '%';
            $candidaciesQuery->where(function ($q) use ($cs) {
                $q->where('tracking_number', 'like', $cs)
                  ->orWhereHas('user', function ($uq) use ($cs) {
                      $uq->where('email', 'like', $cs)
                         ->orWhereHas('basicInfo', function ($bq) use ($cs) {
                             $bq->where('first_name', 'like', $cs)
                                ->orWhere('last_name', 'like', $cs)
                                ->orWhere('cnic_number', 'like', $cs);
                         });
                  });
            });
        }

        if (!empty($this->candidacyCampaignFilter)) {
            $candidaciesQuery->where('campaign_id', $this->candidacyCampaignFilter);
        }

        if ($this->candidacyStatusFilter === 'NEEDS_DISPATCH') {
            $candidaciesQuery->doesntHave('reviews');
        } elseif ($this->candidacyStatusFilter === 'UNDER_REVIEW') {
            $candidaciesQuery->where('status', 'UNDER_REVIEW');
        } elseif ($this->candidacyStatusFilter === 'COMPLETED') {
            $candidaciesQuery->whereHas('reviews', fn($q) => $q->where('status', 'SUBMITTED'));
        }

        $academicApplications = $candidaciesQuery->latest()->get();

        // 3. For the dispatch modal: retrieve matching reviewers using AcademicDisciplineMatcher
        $fieldMatchedReviewers = collect();
        if ($this->targetApplication) {
            $dept = trim($this->targetApplication->campaign->department);
            $campTitle = trim($this->targetApplication->campaign->title ?? '');

            $fieldMatchedReviewers = AcademicDisciplineMatcher::queryForCandidacy(
                $dept,
                $campTitle,
                $this->dispatchReviewerSearch,
                $this->dispatchDisciplineFilter,
                $this->filterByFieldOnly
            )->get();
        }

        // 4. Distinct Subjects, Ranks & Campaigns for dropdown filters
        $availableSubjects = ReviewerProfile::distinct()->pluck('subject')->filter()->values();
        $availableRanks = ReviewerProfile::distinct()->pluck('academic_rank')->filter()->values();
        $availableAcademicCampaigns = Campaign::where('track_type', 'ACADEMIC')->orderBy('title')->get();

        // 5. System KPI Metrics
        $stats = [
            'totalReviewers' => ReviewerProfile::count(),
            'activeReviewers' => ReviewerProfile::where('is_active', true)->count(),
            'totalAcademicCampaigns' => Campaign::where('track_type', 'ACADEMIC')->count(),
            'totalAcademicApplications' => Application::whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'))->count(),
            'inReviewApplications' => Application::where('status', 'UNDER_REVIEW')->count(),
            'completedReviewsCount' => ApplicationReview::where('status', 'SUBMITTED')->count(),
        ];

        return view('livewire.admin.reviewer-management', [
            'reviewers' => $reviewers,
            'academicApplications' => $academicApplications,
            'fieldMatchedReviewers' => $fieldMatchedReviewers,
            'availableSubjects' => $availableSubjects,
            'availableRanks' => $availableRanks,
            'availableAcademicCampaigns' => $availableAcademicCampaigns,
            'stats' => $stats,
            'countries' => CountryService::all(),
            'priorityCountries' => CountryService::priority(),
        ])->layout('layouts.app');
    }
}
