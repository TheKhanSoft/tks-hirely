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
use App\Models\Advertisement;
use App\Livewire\Traits\WithDynamicListing;
use App\Livewire\Traits\WithPasswordReset;

class ReviewerManagement extends Component
{
    use WithDynamicListing, WithPasswordReset;

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
    public $advertisementFilter = 'ALL';
    public $candidacyCampaignFilter = '';
    public $candidacyStatusFilter = 'ALL'; // ALL, NEEDS_DISPATCH, UNDER_REVIEW, COMPLETED, STRONGLY_RECOMMENDED, NOT_RECOMMENDED
    public $candidacyAppStatusFilter = 'ALL'; // ALL, SUBMITTED, UNDER_REVIEW, ELIGIBLE, REJECTED, SHORTLISTED
    public $candidacySortBy = 'created_at';
    public $candidacySortDir = 'desc';
    public $candidacyPerPage = 20;

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

    // Reviewer Full Profile Details Modal
    public $showDetailsModal = false;
    public $selectedReviewerForDetails = null;

    // Reviewer Edit Modal
    public $showEditModal = false;
    public $editingReviewerId = null;
    public $edit_name;
    public $edit_email;
    public $edit_subject;
    public $edit_academic_rank = 'Professor';
    public $edit_university;
    public $edit_country = 'Pakistan';
    public $edit_phone;
    public $edit_specialization;

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

    public function updatedSubjectFilter()
    {
        $this->resetPage();
    }

    public function updatedRankFilter()
    {
        $this->resetPage();
    }

    public function updatedCountryFilter()
    {
        $this->resetPage();
    }

    public function updatedCandidacySearch()
    {
        $this->resetPage();
    }

    public function updatedAdvertisementFilter()
    {
        $this->candidacyCampaignFilter = '';
        $this->resetPage();
    }

    public function updatedCandidacyCampaignFilter()
    {
        $this->resetPage();
    }

    public function updatedCandidacyStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCandidacyAppStatusFilter()
    {
        $this->resetPage();
    }

    public function sortCandidacies($column)
    {
        if ($this->candidacySortBy === $column) {
            $this->candidacySortDir = $this->candidacySortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->candidacySortBy = $column;
            $this->candidacySortDir = 'asc';
        }
        $this->resetPage();
    }

    public function resetCandidacyFilters()
    {
        $this->candidacySearch = '';
        $this->advertisementFilter = 'ALL';
        $this->candidacyCampaignFilter = '';
        $this->candidacyStatusFilter = 'ALL';
        $this->candidacyAppStatusFilter = 'ALL';
        $this->candidacySortBy = 'created_at';
        $this->candidacySortDir = 'desc';
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->subjectFilter = '';
        $this->rankFilter = '';
        $this->countryFilter = '';
        $this->candidacySearch = '';
        $this->advertisementFilter = 'ALL';
        $this->candidacyCampaignFilter = '';
        $this->candidacyStatusFilter = 'ALL';
        $this->candidacyAppStatusFilter = 'ALL';
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
        abort_unless(Auth::user()->can('create-reviewer') || Auth::user()->can('manage-reviewers'), 403);

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
        abort_unless(Auth::user()->can('create-reviewer') || Auth::user()->can('manage-reviewers'), 403);
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

        if ($this->showDetailsModal && $this->selectedReviewerForDetails?->id === $profile->id) {
            $this->selectedReviewerForDetails->refresh();
        }

        session()->flash('message', "Reviewer '{$profile->name}' status updated to " . ($profile->is_active ? 'Active' : 'Inactive') . ".");
    }

    public function openDetailsModal($profileId)
    {
        abort_unless(Auth::user()->can('view-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $this->selectedReviewerForDetails = ReviewerProfile::with(['user', 'creator', 'reviews.application.campaign', 'reviews.application.user.basicInfo'])->findOrFail($profileId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedReviewerForDetails = null;
    }

    public function openEditModal($profileId)
    {
        abort_unless(Auth::user()->can('edit-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $profile = ReviewerProfile::with('user')->findOrFail($profileId);
        $this->editingReviewerId = $profile->id;
        $this->edit_name = $profile->name;
        $this->edit_email = $profile->user?->email ?? '';
        $this->edit_subject = $profile->subject;
        $this->edit_academic_rank = $profile->academic_rank;
        $this->edit_university = $profile->university;
        $this->edit_country = $profile->country ?: 'Pakistan';
        $this->edit_phone = $profile->phone;
        $this->edit_specialization = $profile->specialization;
        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingReviewerId = null;
        $this->resetErrorBag();
    }

    public function updateReviewer()
    {
        abort_unless(Auth::user()->can('edit-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $profile = ReviewerProfile::with('user')->findOrFail($this->editingReviewerId);

        $this->validate([
            'edit_name' => 'required|string|min:3|max:150',
            'edit_email' => 'required|email|unique:users,email,' . ($profile->user_id ?? 'NULL'),
            'edit_subject' => 'required|string|min:2|max:100',
            'edit_academic_rank' => 'required|string',
            'edit_university' => 'required|string|min:2|max:150',
            'edit_country' => 'required|string',
            'edit_phone' => 'nullable|string|max:30',
            'edit_specialization' => 'nullable|string|max:500',
        ]);

        $profile->update([
            'name' => trim($this->edit_name),
            'subject' => trim($this->edit_subject),
            'academic_rank' => trim($this->edit_academic_rank),
            'university' => trim($this->edit_university),
            'country' => trim($this->edit_country),
            'phone' => trim($this->edit_phone),
            'specialization' => trim($this->edit_specialization),
        ]);

        if ($profile->user && $profile->user->email !== trim($this->edit_email)) {
            $profile->user->update(['email' => trim($this->edit_email)]);
        }

        session()->flash('message', "Reviewer '{$profile->name}' profile updated successfully.");
        $this->closeEditModal();
        if ($this->showDetailsModal && $this->selectedReviewerForDetails?->id === $profile->id) {
            $this->openDetailsModal($profile->id);
        }
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

        $application = Application::with(['campaign.constitution', 'user.basicInfo'])->findOrFail($this->targetApplicationId);
        $assignedCount = 0;

        $constitution = $application->campaign->constitution;
        $enforceStrict = ($constitution && $constitution->enforce_strict_quota) || \App\Models\SystemSetting::get('enforce_strict_peer_review_quorum', false);
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

    public function evaluatePeerReviewResults($applicationId)
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers'), 403);
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

    public function sendReviewerReminder($reviewId)
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $review = ApplicationReview::with(['reviewer.reviewerProfile', 'application.campaign', 'application.user.basicInfo'])->findOrFail($reviewId);

        if ($review->status === 'SUBMITTED') {
            session()->flash('warning', 'Evaluation report has already been submitted by this referee.');
            return;
        }

        $reviewerUser = $review->reviewer;
        $application = $review->application;

        if ($reviewerUser && $reviewerUser->email) {
            $evalUrl = url('/reviewer/dashboard');
            try {
                Mail::to($reviewerUser->email)->send(new ApplicationAssignedReviewerMail(
                    $reviewerUser->reviewerProfile?->name ?? 'Colleague',
                    $application->user->basicInfo ? trim($application->user->basicInfo->first_name . ' ' . $application->user->basicInfo->last_name) : 'Academic Candidate',
                    $application->campaign->title,
                    $application->campaign->department,
                    $review->due_date ? $review->due_date->format('Y-m-d') : now()->addDays(7)->format('Y-m-d'),
                    "REMINDER: Statutory confidential peer evaluation for this dossier is awaiting your evaluation report. Kindly submit your expert score and remarks at your earliest convenience.",
                    $evalUrl
                ));
                Log::info("Reviewer reminder email dispatched to {$reviewerUser->email}");
                session()->flash('message', "Official reminder email sent to {$reviewerUser->reviewerProfile?->name} ({$reviewerUser->email}).");
            } catch (\Exception $e) {
                Log::warning("Could not dispatch reviewer reminder: " . $e->getMessage());
                session()->flash('warning', "Notice logged: Referee reminder generated. (Email transport note: {$e->getMessage()})");
            }
        }

        if ($this->viewingApplication) {
            $this->applicationReviews = ApplicationReview::with(['reviewer.reviewerProfile', 'assigner'])
                ->where('application_id', $this->viewingApplication->id)
                ->get();
        }
    }

    public function extendReviewDeadline($reviewId, $days = 7)
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $review = ApplicationReview::findOrFail($reviewId);

        $currentDue = $review->due_date ?: now();
        $newDue = $currentDue->copy()->addDays((int)$days);
        $review->update(['due_date' => $newDue]);

        session()->flash('message', "Review deadline extended by {$days} days (New Due Date: {$newDue->format('d M Y')}).");

        if ($this->viewingApplication) {
            $this->applicationReviews = ApplicationReview::with(['reviewer.reviewerProfile', 'assigner'])
                ->where('application_id', $this->viewingApplication->id)
                ->get();
        }
    }

    public function removeReviewerAssignment($reviewId)
    {
        abort_unless(Auth::user()->can('assign-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $review = ApplicationReview::findOrFail($reviewId);

        if ($review->status === 'SUBMITTED') {
            session()->flash('error', 'Cannot remove an assignment that already has an evaluation report submitted.');
            return;
        }

        $appId = $review->application_id;
        $reviewerName = $review->reviewer?->reviewerProfile?->name ?? 'External Reviewer';
        $review->delete();

        $remainingCount = ApplicationReview::where('application_id', $appId)->count();
        if ($remainingCount === 0) {
            Application::where('id', $appId)->where('status', 'UNDER_REVIEW')->update(['status' => 'PAID']);
        }

        session()->flash('message', "Referee assignment for {$reviewerName} has been revoked.");

        if ($this->viewingApplication) {
            $this->applicationReviews = ApplicationReview::with(['reviewer.reviewerProfile', 'assigner'])
                ->where('application_id', $this->viewingApplication->id)
                ->get();
            $this->viewingApplication->refresh();
        }
    }

    public function exportDispatchCsv()
    {
        abort_unless(Auth::user()->can('export-reviewer') || Auth::user()->can('view-reviewer') || Auth::user()->can('manage-reviewers'), 403);

        $candidaciesQuery = Application::with(['campaign.advertisement', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])
            ->withCount('reviews')
            ->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'));

        if (!empty($this->candidacySearch)) {
            $cs = '%' . trim($this->candidacySearch) . '%';
            $rawId = preg_replace('/[^0-9]/', '', $this->candidacySearch);
            $candidaciesQuery->where(function ($q) use ($cs, $rawId) {
                if (!empty($rawId)) {
                    $q->where('applications.id', (int)$rawId);
                }
                $q->orWhereHas('user', function ($uq) use ($cs) {
                    $uq->where('email', 'like', $cs)
                       ->orWhereHas('basicInfo', function ($bq) use ($cs) {
                           $bq->where('first_name', 'like', $cs)
                              ->orWhere('last_name', 'like', $cs)
                              ->orWhere('cnic_number', 'like', $cs);
                       });
                });
            });
        }

        if ($this->advertisementFilter !== 'ALL') {
            $candidaciesQuery->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        if (!empty($this->candidacyCampaignFilter)) {
            $candidaciesQuery->where('campaign_id', $this->candidacyCampaignFilter);
        }

        if ($this->candidacyAppStatusFilter !== 'ALL') {
            $candidaciesQuery->where('status', $this->candidacyAppStatusFilter);
        }

        if ($this->candidacyStatusFilter === 'NEEDS_DISPATCH') {
            $candidaciesQuery->doesntHave('reviews');
        } elseif ($this->candidacyStatusFilter === 'UNDER_REVIEW') {
            $candidaciesQuery->where('status', 'UNDER_REVIEW');
        } elseif ($this->candidacyStatusFilter === 'COMPLETED') {
            $candidaciesQuery->whereHas('reviews', fn($q) => $q->where('status', 'SUBMITTED'));
        } elseif ($this->candidacyStatusFilter === 'STRONGLY_RECOMMENDED') {
            $candidaciesQuery->whereHas('reviews', fn($q) => $q->where('recommendation', 'STRONGLY_RECOMMENDED'));
        } elseif ($this->candidacyStatusFilter === 'NOT_RECOMMENDED') {
            $candidaciesQuery->whereHas('reviews', fn($q) => $q->whereIn('recommendation', ['NOT_RECOMMENDED', 'DISQUALIFIED']));
        }

        $candidacies = $candidaciesQuery->latest()->get();

        $headers = [
            'Tracking ID', 'Candidate Name', 'Father Name', 'CNIC', 'Advertisement No', 
            'Position Title', 'Department', 'Track Type', 'Application Status', 
            'Referees Assigned', 'Reports Submitted', 'Average Peer Score (/100)', 'Referee Consensus Verdict'
        ];

        $rows = [];
        foreach ($candidacies as $c) {
            $submitted = $c->reviews->where('status', 'SUBMITTED');
            $avgScore = $submitted->count() > 0 ? round($submitted->avg('overall_score'), 2) : 'N/A';
            
            $verdict = 'Awaiting Dispatch';
            if ($c->reviews->count() > 0) {
                if ($submitted->count() === 0) {
                    $verdict = 'Under Peer Review';
                } else {
                    $pos = $submitted->whereIn('recommendation', ['STRONGLY_RECOMMENDED', 'RECOMMENDED'])->count();
                    $verdict = "{$pos}/{$submitted->count()} Recommended (Avg: {$avgScore})";
                }
            }

            $rows[] = [
                $c->tracking_number,
                $c->user->basicInfo ? trim($c->user->basicInfo->first_name . ' ' . $c->user->basicInfo->last_name) : $c->user->email,
                $c->user->basicInfo?->father_name ?? 'N/A',
                $c->user->basicInfo?->cnic_number ?? 'Pending',
                $c->campaign->advertisement?->advertisement_no ?? 'Direct Intake',
                $c->campaign->title,
                $c->campaign->department,
                $c->campaign->track_type,
                $c->status,
                $c->reviews->count(),
                $submitted->count(),
                $avgScore,
                $verdict,
            ];
        }

        return $this->streamCsvDownload('peer-review-dispatch-register-' . date('Y-m-d') . '.csv', $headers, $rows);
    }

    public function exportApplicationPeerReportsCsv($applicationId)
    {
        abort_unless(Auth::user()->can('view-review-reports') || Auth::user()->can('manage-reviewers'), 403);
        $app = Application::with(['campaign.advertisement', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])->findOrFail($applicationId);

        $headers = [
            'Application Ref', 'Candidate Name', 'Position Title', 'Department',
            'Referee Name', 'Referee Rank', 'University / Office', 'Country', 'Contact Email',
            'Evaluation Status', 'Overall Score (/100)', 'Recommendation', 'Assigned Date', 'Due Date', 'Submitted Date',
            'Evaluation Remarks'
        ];

        $rows = [];
        $candidateName = $app->user->basicInfo ? trim($app->user->basicInfo->first_name . ' ' . $app->user->basicInfo->last_name) : $app->user->email;

        foreach ($app->reviews as $r) {
            $prof = $r->reviewer?->reviewerProfile;
            $rows[] = [
                $app->tracking_number,
                $candidateName,
                $app->campaign->title,
                $app->campaign->department,
                $prof?->name ?? 'External Referee',
                $prof?->academic_rank ?? 'Professor',
                $prof?->university ?? 'N/A',
                $prof?->country ?? 'N/A',
                $r->reviewer?->email ?? 'N/A',
                $r->status,
                $r->overall_score !== null ? $r->overall_score : 'Pending',
                $r->recommendation ?? 'Pending',
                $r->assigned_at?->format('Y-m-d') ?? 'N/A',
                $r->due_date?->format('Y-m-d') ?? 'N/A',
                $r->submitted_at?->format('Y-m-d H:i') ?? 'N/A',
                $r->report_text ? str_replace(["\r", "\n"], ' ', $r->report_text) : 'N/A',
            ];
        }

        $filename = 'peer-reports-' . Str::slug($app->tracking_number) . '-' . date('Y-m-d') . '.csv';
        return $this->streamCsvDownload($filename, $headers, $rows);
    }

    public function exportCsv()
    {
        abort_unless(Auth::user()->can('export-reviewer') || Auth::user()->can('view-reviewer') || Auth::user()->can('manage-reviewers'), 403);
        $reviewers = ReviewerProfile::with('user')
            ->whereDoesntHave('user.roles', fn($rq) => $rq->where('name', 'super-admin'))
            ->withCount('reviews')
            ->latest()
            ->get();
        $headers = ['Name', 'Email', 'Academic Rank', 'Subject / Discipline', 'University / Office', 'Country', 'Phone', 'Status', 'Reviews Assigned'];
        $rows = [];
        foreach ($reviewers as $r) {
            $rows[] = [
                $r->name,
                $r->user?->email ?? 'N/A',
                $r->academic_rank,
                $r->subject,
                $r->university,
                $r->country,
                $r->phone,
                $r->is_active ? 'Active / Empanelled' : 'Inactive',
                $r->reviews_count ?? 0,
            ];
        }
        return $this->streamCsvDownload('academic-reviewers-' . date('Y-m-d') . '.csv', $headers, $rows);
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

        $reviewers = $reviewersQuery->latest()->paginate($this->perPage);

        // 2. Query Academic Applications for Review Dispatch (Tab 2)
        $candidaciesQuery = Application::with(['campaign.advertisement', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])
            ->withCount('reviews')
            ->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'));

        if (!empty($this->candidacySearch)) {
            $cs = '%' . trim($this->candidacySearch) . '%';
            $rawId = preg_replace('/[^0-9]/', '', $this->candidacySearch);
            $candidaciesQuery->where(function ($q) use ($cs, $rawId) {
                if (!empty($rawId)) {
                    $q->where('applications.id', (int)$rawId);
                }
                $q->orWhereHas('user', function ($uq) use ($cs) {
                    $uq->where('email', 'like', $cs)
                       ->orWhereHas('basicInfo', function ($bq) use ($cs) {
                           $bq->where('first_name', 'like', $cs)
                              ->orWhere('last_name', 'like', $cs)
                              ->orWhere('cnic_number', 'like', $cs);
                       });
                });
            });
        }

        if ($this->advertisementFilter !== 'ALL') {
            $candidaciesQuery->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$this->advertisementFilter));
        }

        if (!empty($this->candidacyCampaignFilter)) {
            $candidaciesQuery->where('campaign_id', $this->candidacyCampaignFilter);
        }

        if ($this->candidacyAppStatusFilter !== 'ALL') {
            $candidaciesQuery->where('status', $this->candidacyAppStatusFilter);
        }

        if ($this->candidacyStatusFilter === 'NEEDS_DISPATCH') {
            $candidaciesQuery->doesntHave('reviews');
        } elseif ($this->candidacyStatusFilter === 'UNDER_REVIEW') {
            $candidaciesQuery->where('status', 'UNDER_REVIEW');
        } elseif ($this->candidacyStatusFilter === 'COMPLETED') {
            $candidaciesQuery->whereHas('reviews', fn($q) => $q->where('status', 'SUBMITTED'));
        } elseif ($this->candidacyStatusFilter === 'STRONGLY_RECOMMENDED') {
            $candidaciesQuery->whereHas('reviews', fn($q) => $q->where('recommendation', 'STRONGLY_RECOMMENDED'));
        } elseif ($this->candidacyStatusFilter === 'NOT_RECOMMENDED') {
            $candidaciesQuery->whereHas('reviews', fn($q) => $q->whereIn('recommendation', ['NOT_RECOMMENDED', 'DISQUALIFIED']));
        }

        // Dynamic Sorting for candidacies
        if ($this->candidacySortBy === 'candidate_name') {
            $candidaciesQuery->join('users', 'applications.user_id', '=', 'users.id')
                ->leftJoin('basic_infos', 'users.id', '=', 'basic_infos.user_id')
                ->orderBy('basic_infos.first_name', $this->candidacySortDir)
                ->select('applications.*');
        } elseif ($this->candidacySortBy === 'tracking_number') {
            $candidaciesQuery->orderBy('applications.id', $this->candidacySortDir);
        } elseif ($this->candidacySortBy === 'status') {
            $candidaciesQuery->orderBy('applications.status', $this->candidacySortDir);
        } elseif ($this->candidacySortBy === 'reviews_count') {
            $candidaciesQuery->orderBy('reviews_count', $this->candidacySortDir);
        } else {
            $candidaciesQuery->orderBy('applications.created_at', $this->candidacySortDir);
        }

        $academicApplications = $candidaciesQuery->paginate($this->candidacyPerPage, ['*'], 'candidaciesPage');

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

        // 4. Distinct Subjects, Ranks, Advertisements & Campaigns for dropdown filters
        $availableAdvertisements = Advertisement::chronologicalDesc()->get();
        $availableSubjects = ReviewerProfile::distinct()->pluck('subject')->filter()->values();
        $availableRanks = ReviewerProfile::distinct()->pluck('academic_rank')->filter()->values();
        
        $campaignsQuery = Campaign::where('track_type', 'ACADEMIC');
        if ($this->advertisementFilter !== 'ALL') {
            $campaignsQuery->where('advertisement_id', (int)$this->advertisementFilter);
        }
        $availableAcademicCampaigns = $campaignsQuery->orderBy('title')->get();

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
            'availableAdvertisements' => $availableAdvertisements,
            'availableSubjects' => $availableSubjects,
            'availableRanks' => $availableRanks,
            'availableAcademicCampaigns' => $availableAcademicCampaigns,
            'stats' => $stats,
            'countries' => CountryService::all(),
            'priorityCountries' => CountryService::priority(),
        ])->layout('layouts.app');
    }
}
