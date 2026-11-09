<?php

namespace App\Livewire\Reviewer;

use App\Models\ApplicationReview;
use App\Models\SecurityAuditLog;
use App\Models\SystemSetting;
use Livewire\Component;
use Livewire\WithFileUploads;

class DirectTokenizedReviewDesk extends Component
{
    use WithFileUploads;

    public string $token = '';
    public ?ApplicationReview $review = null;

    // Form inputs
    public $overall_score = '';
    public $recommendation = '';
    public $report_text = '';
    public $confidential_notes = '';
    public $evaluation_file = null;

    // View state
    public bool $isFeatureDisabled = false;
    public bool $isInvalidToken = false;
    public bool $isSubmitted = false;
    public bool $showSuccessReceipt = false;

    protected $rules = [
        'overall_score' => 'required|numeric|min:0|max:100',
        'recommendation' => 'required|in:STRONGLY_RECOMMENDED,RECOMMENDED,CONDITIONALLY_RECOMMENDED,NOT_RECOMMENDED',
        'report_text' => 'required|string|min:20|max:5000',
        'confidential_notes' => 'nullable|string|max:2000',
        'evaluation_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
    ];

    public function mount(string $token)
    {
        $this->token = $token;

        // 1. Administrative Gate Check
        if (!SystemSetting::isFeatureEnabled('referee_magic_links')) {
            $this->isFeatureDisabled = true;
            return;
        }

        // 2. Look up tokenized review assignment
        $this->review = ApplicationReview::with([
            'application.user.basicInfo.degrees',
            'application.user.basicInfo.experiences',
            'application.user.basicInfo.publications',
            'application.user.basicInfo.researchProjects',
            'application.user.basicInfo.patents',
            'application.campaign.department',
            'reviewer.reviewerProfile',
        ])->where('access_token', $token)->first();

        if (!$this->review || !$this->review->isTokenValid($token)) {
            $this->isInvalidToken = true;
            return;
        }

        // Initialize form fields if already reviewed
        $this->overall_score = $this->review->overall_score ?? '';
        $this->recommendation = $this->review->recommendation ?? '';
        $this->report_text = $this->review->report_text ?? '';
        $this->confidential_notes = $this->review->confidential_notes ?? '';
        $this->isSubmitted = ($this->review->status === 'SUBMITTED');
    }

    public function setQuickScore($score): void
    {
        if ($this->isSubmitted) {
            return;
        }
        $this->overall_score = max(0, min(100, round((float) $score, 1)));
    }

    public function setQuickRecommendation(string $rec): void
    {
        if ($this->isSubmitted) {
            return;
        }
        $this->recommendation = $rec;
    }

    public function submitEvaluation(): void
    {
        if (!SystemSetting::isFeatureEnabled('referee_magic_links')) {
            session()->flash('error', 'Tokenized referee evaluation is currently disabled by university administration.');
            return;
        }

        if (!$this->review || !$this->review->isTokenValid($this->token)) {
            session()->flash('error', 'Security error: The evaluation token is expired or unauthorized.');
            return;
        }

        $this->validate();

        $filePath = $this->review->evaluation_file_path;
        if ($this->evaluation_file) {
            $filePath = $this->evaluation_file->store('reviewer_evaluations', 'public');
        }

        $this->review->update([
            'overall_score' => (float) $this->overall_score,
            'recommendation' => $this->recommendation,
            'report_text' => trim($this->report_text),
            'confidential_notes' => $this->confidential_notes ? trim($this->confidential_notes) : null,
            'evaluation_file_path' => $filePath,
            'status' => 'SUBMITTED',
            'submitted_at' => now(),
        ]);

        // Security Audit Trail
        SecurityAuditLog::record(
            'EXTERNAL_REFEREE_DIRECT_SUBMISSION',
            'ApplicationReview',
            $this->review->id,
            [
                'score' => $this->overall_score,
                'recommendation' => $this->recommendation,
                'token_prefix' => substr($this->token, 0, 8) . '...',
                'candidate_tracking' => $this->review->application?->tracking_number,
                'client_ip' => request()->ip(),
            ],
            null,
            $this->review->reviewer_id
        );

        $this->isSubmitted = true;
        $this->showSuccessReceipt = true;
        session()->flash('message', 'Thank you! Your confidential referee report has been successfully recorded and locked.');
    }

    public function render()
    {
        $orgName = SystemSetting::getOrgName();
        $letterhead = SystemSetting::getLetterheadDetails();

        return view('livewire.reviewer.direct-tokenized-review-desk', [
            'orgName' => $orgName,
            'letterhead' => $letterhead,
        ])->layout('layouts.app', ['title' => 'Confidential Academic Referee Evaluation Desk']);
    }
}
