<?php

namespace App\Livewire\Reviewer;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ApplicationReview;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class EvaluationDesk extends Component
{
    use WithFileUploads;

    public $activeTab = 'pending'; // 'pending' or 'completed'

    // Active Evaluation Modal Properties
    public $showEvalModal = false;
    public $activeReviewId;
    public $activeReview;
    public $candidateApplication;

    // Evaluation Form Inputs
    public $overall_score;
    public $recommendation;
    public $report_text;
    public $evaluation_file;

    protected $rules = [
        'overall_score' => 'required|numeric|min:0|max:100',
        'recommendation' => 'required|in:STRONGLY_RECOMMENDED,RECOMMENDED,CONDITIONALLY_RECOMMENDED,NOT_RECOMMENDED',
        'report_text' => 'required|string|min:30',
        'evaluation_file' => 'nullable|file|max:5120',
    ];

    public function mount()
    {
        abort_unless(Auth::check() && (Auth::user()->hasRole('academic-reviewer') || Auth::user()->can('evaluate-academic-dossier')), 403);
    }

    public function openEvalModal($reviewId)
    {
        $review = ApplicationReview::with([
            'application.campaign',
            'application.user.basicInfo.degrees',
            'application.user.basicInfo.publications',
            'application.user.basicInfo.researchProjects',
            'application.user.basicInfo.milestones',
            'application.user.basicInfo.patents',
        ])->where('reviewer_id', Auth::id())->findOrFail($reviewId);

        $this->activeReviewId = $reviewId;
        $this->activeReview = $review;
        $this->candidateApplication = $review->application;

        $this->overall_score = $review->overall_score;
        $this->recommendation = $review->recommendation;
        $this->report_text = $review->report_text;
        $this->resetErrorBag();

        $this->showEvalModal = true;
    }

    public function closeEvalModal()
    {
        $this->showEvalModal = false;
        $this->activeReviewId = null;
        $this->activeReview = null;
        $this->candidateApplication = null;
        $this->resetErrorBag();
    }

    public function submitEvaluation()
    {
        abort_unless(Auth::user()->can('evaluate-academic-dossier') || Auth::user()->hasAnyRole(['academic-reviewer', 'reviewer', 'super-admin']), 403);

        $this->validate();

        $review = ApplicationReview::where('reviewer_id', Auth::id())->findOrFail($this->activeReviewId);

        $filePath = $review->evaluation_file_path;
        if ($this->evaluation_file) {
            $filePath = $this->evaluation_file->store('reviewer_reports', 'public');
        }

        $review->update([
            'overall_score' => $this->overall_score,
            'recommendation' => $this->recommendation,
            'report_text' => $this->report_text,
            'evaluation_file_path' => $filePath,
            'status' => 'SUBMITTED',
            'submitted_at' => now(),
        ]);

        session()->flash('message', 'Confidential academic peer review report submitted successfully to the Registrar Secretariat.');

        $this->showEvalModal = false;
    }

    public function render()
    {
        $user = Auth::user();
        $reviewerProfile = $user->reviewerProfile;

        $reviewsQuery = ApplicationReview::with(['application.campaign', 'application.user.basicInfo'])
            ->where('reviewer_id', $user->id);

        if ($this->activeTab === 'pending') {
            $reviewsQuery->where('status', 'PENDING');
        } else {
            $reviewsQuery->where('status', 'SUBMITTED');
        }

        $reviews = $reviewsQuery->latest('assigned_at')->get();

        $pendingCount = ApplicationReview::where('reviewer_id', $user->id)->where('status', 'PENDING')->count();
        $completedCount = ApplicationReview::where('reviewer_id', $user->id)->where('status', 'SUBMITTED')->count();

        return view('livewire.reviewer.evaluation-desk', [
            'reviewerProfile' => $reviewerProfile,
            'reviews' => $reviews,
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
        ])->layout('layouts.app');
    }
}
