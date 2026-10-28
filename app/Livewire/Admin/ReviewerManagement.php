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

    // Filter & Search Properties
    public $search = '';
    public $subjectFilter = '';

    // Application Review Dispatch Modal Properties
    public $showDispatchModal = false;
    public $targetApplicationId;
    public $targetApplication;
    public $selectedReviewerIds = [];
    public $reviewDueDate;
    public $reviewNotes;
    public $filterByFieldOnly = true;

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
        abort_unless(Auth::check() && (Auth::user()->hasAnyRole(['super-admin', 'registrar']) || Auth::user()->can('manage-reviewers')), 403);
        $this->reviewDueDate = now()->addDays(14)->format('Y-m-d');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'email', 'subject', 'university', 'phone', 'specialization']);
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

        session()->flash('message', "Academic Reviewer '{$profile->name}' empanelled successfully! Credentials and evaluation guidance dispatched to {$user->email}. (Temporary Password: {$plainPassword})");

        $this->showCreateModal = false;
    }

    public function toggleReviewerStatus($profileId)
    {
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
        $this->targetApplicationId = $applicationId;
        $this->targetApplication = Application::with(['campaign', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])->findOrFail($applicationId);
        $this->selectedReviewerIds = [];
        $this->reviewNotes = "Please evaluate the candidate's research publications, impact, and suitability for the position of {$this->targetApplication->campaign->title} under statutory university appointment criteria.";
        $this->reviewDueDate = now()->addDays(14)->format('Y-m-d');
        $this->filterByFieldOnly = true;
        $this->resetErrorBag();
        $this->showDispatchModal = true;
    }

    public function closeDispatchModal()
    {
        $this->showDispatchModal = false;
        $this->targetApplicationId = null;
        $this->targetApplication = null;
        $this->selectedReviewerIds = [];
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
        // 1. Query Reviewer Profiles
        $reviewersQuery = ReviewerProfile::with(['user', 'creator'])
            ->withCount('reviews');

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $reviewersQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('university', 'like', $s)
                  ->orWhere('subject', 'like', $s)
                  ->orWhere('specialization', 'like', $s);
            });
        }

        if (!empty($this->subjectFilter)) {
            $reviewersQuery->where('subject', $this->subjectFilter);
        }

        $reviewers = $reviewersQuery->latest()->paginate(10);

        // 2. Query Academic Applications for Review Dispatch
        $academicApplications = Application::with(['campaign', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])
            ->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'))
            ->latest()
            ->get();

        // 3. For the dispatch modal: retrieve matching reviewers
        $fieldMatchedReviewers = collect();
        if ($this->targetApplication) {
            $dept = trim($this->targetApplication->campaign->department);
            
            $query = ReviewerProfile::with(['user', 'reviews'])->where('is_active', true);
            
            if ($this->filterByFieldOnly) {
                $query->where(function ($q) use ($dept) {
                    $q->where('subject', 'LIKE', '%' . $dept . '%')
                      ->orWhere('specialization', 'LIKE', '%' . $dept . '%');
                });
            }
            
            $fieldMatchedReviewers = $query->get();
        }

        // 4. Distinct Subjects for filter dropdown
        $availableSubjects = ReviewerProfile::distinct()->pluck('subject')->filter()->values();

        return view('livewire.admin.reviewer-management', [
            'reviewers' => $reviewers,
            'academicApplications' => $academicApplications,
            'fieldMatchedReviewers' => $fieldMatchedReviewers,
            'availableSubjects' => $availableSubjects,
        ])->layout('layouts.app');
    }
}
