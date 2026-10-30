<?php

namespace App\Livewire\Board;

use Livewire\Component;
use App\Models\Application;
use App\Models\LiveEvaluation;
use Illuminate\Support\Facades\Auth;

class LiveGradingPanel extends Component
{
    public $applicationId;
    public $application;

    // Scores
    public $academic_score = 40.00; // Base baseline academic grade
    public $medals_score = 0.00;
    public $publications_score = 0.00;
    public $events_score = 0.00;
    public $project_score = 0.00;
    public $interview_score = 30.00; // Default out of 50

    // Outlier status
    public $isOutlier = false;
    public $outlier_justification = '';
    public $otherAverage = 35.00; // Simulated other board members average if none exists

    public function mount($id)
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('access-board-grading') ||
            Auth::user()->can('evaluate-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'selection-board-member', 'board-member', 'board'])
        ), 403);

        $this->applicationId = $id;
        $this->application = Application::with([
            'user.basicInfo.medals',
            'user.basicInfo.publications',
            'user.basicInfo.events',
            'user.basicInfo.researchProjects',
            'campaign'
        ])->findOrFail($id);

        $this->calculateObjectiveScores();
        $this->calculatePanelAverage();
    }

    protected function calculateObjectiveScores()
    {
        $basicInfo = $this->application->user->basicInfo;
        if (!$basicInfo) return;

        // 1. Medals Score: Gold = 5, Silver = 3, Bronze = 2 (summed for verified medals)
        $this->medals_score = floatval($basicInfo->medals->where('is_verified', true)->sum(function($m) {
            if ($m->medal_type === 'GOLD') return 5;
            if ($m->medal_type === 'SILVER') return 3;
            if ($m->medal_type === 'BRONZE') return 2;
            return 0;
        }));

        // 2. Publications Score: Category weights (W = 1.0, X = 0.75, Y = 0.5, Z = 0.15) for verified papers
        $this->publications_score = floatval($basicInfo->publications->where('is_verified', true)->sum(function($p) {
            if ($p->hjrs_category === 'W') return 1.0;
            if ($p->hjrs_category === 'X') return 0.75;
            if ($p->hjrs_category === 'Y') return 0.5;
            if ($p->hjrs_category === 'Z') return 0.15;
            return 0;
        }));

        // 3. Projects Score: 1.0 Mark per Million PKR PI funding, max 13
        $projectSum = floatval($basicInfo->researchProjects->where('is_verified', true)->sum(function($pr) {
            // PI role matches exactly, turnitin similarity strictly below 19%
            if (strtoupper($pr->role_in_project) === 'PI' && $pr->turnitin_similarity_percentage < 19.00) {
                return $pr->grant_amount; // amount in millions
            }
            return 0;
        }));
        $this->project_score = min($projectSum, 13.00);

        // 4. Events Score: Organizer leader = 3, associate = 1.5, presenter int = 1, presenter nat = 0.5, attendee = 0.1
        $this->events_score = floatval($basicInfo->events->where('is_verified', true)->sum(function($e) {
            if ($e->event_role === 'LEADER_ORGANIZER') return 3.0;
            if ($e->event_role === 'ASSOCIATE_ORGANIZER') return 1.5;
            if ($e->event_role === 'PRESENTER_INTERNATIONAL') return 1.0;
            if ($e->event_role === 'PRESENTER_NATIONAL') return 0.5;
            if ($e->event_role === 'ATTENDEE') return 0.1;
            return 0;
        }));
    }

    public function calculatePanelAverage()
    {
        // Query other evaluations for this application from the database
        $otherEvaluations = LiveEvaluation::where('application_id', $this->applicationId)
            ->where('board_member_id', '!=', Auth::id())
            ->get();

        if ($otherEvaluations->isNotEmpty()) {
            $this->otherAverage = floatval($otherEvaluations->avg('interview_score'));
        } else {
            // Default simulated baseline for other board members (35.00 out of 50)
            $this->otherAverage = 35.00;
        }

        $this->checkOutlier();
    }

    public function updatedInterviewScore()
    {
        $this->checkOutlier();
    }

    protected function checkOutlier()
    {
        $currentVal = floatval($this->interview_score);
        $meanVal = floatval($this->otherAverage);

        if ($meanVal > 0) {
            $deviation = abs($currentVal - $meanVal) / $meanVal;
            $this->isOutlier = ($deviation > 0.25);
        } else {
            $this->isOutlier = false;
        }
    }

    public function saveEvaluation()
    {
        abort_unless(Auth::user()->can('submit-board-score') || Auth::user()->can('evaluate-application'), 403);

        $this->validate([
            'academic_score' => 'required|numeric|min:0|max:100',
            'interview_score' => 'required|numeric|min:0|max:50',
            'outlier_justification' => $this->isOutlier ? 'required|string|min:10' : 'nullable|string',
        ], [
            'outlier_justification.required' => 'As an outlier score (deviating > 25% from panel mean of ' . $this->otherAverage . '), you must provide a written justification.'
        ]);

        LiveEvaluation::updateOrCreate(
            [
                'application_id' => $this->applicationId,
                'board_member_id' => Auth::id(),
            ],
            [
                'academic_score' => $this->academic_score,
                'medals_score' => $this->medals_score,
                'publications_score' => $this->publications_score,
                'events_score' => $this->events_score,
                'project_score' => $this->project_score,
                'interview_score' => $this->interview_score,
                'is_outlier' => $this->isOutlier,
                'outlier_justification' => $this->isOutlier ? $this->outlier_justification : null,
            ]
        );

        session()->flash('message', 'Candidate evaluation marks submitted successfully.');
        $redirectUrl = auth()->user()->hasRole('selection-board-member') ? '/board/dashboard' : '/admin/dashboard';
        return redirect()->to($redirectUrl);
    }

    public function render()
    {
        return view('livewire.board.live-grading-panel')
            ->layout('layouts.app');
    }
}
