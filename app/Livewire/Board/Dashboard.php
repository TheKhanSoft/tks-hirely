<?php

namespace App\Livewire\Board;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\LiveEvaluation;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $campaignFilter = '';
    public $trackFilter = '';

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('access-board-grading') ||
            Auth::user()->can('evaluate-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'selection-board-member', 'board-member', 'board'])
        ), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCampaignFilter()
    {
        $this->resetPage();
    }

    public function updatingTrackFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Application::with(['user.basicInfo', 'campaign', 'evaluations'])
            ->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])
            ->latest();

        if ($this->campaignFilter) {
            $query->where('campaign_id', $this->campaignFilter);
        }

        if ($this->trackFilter) {
            $query->whereHas('campaign', fn($q) => $q->where('track_type', $this->trackFilter));
        }

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('tracking_number', 'like', $s)
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('email', 'like', $s)
                         ->orWhereHas('basicInfo', function ($bq) use ($s) {
                             $bq->where('first_name', 'like', $s)
                                ->orWhere('last_name', 'like', $s)
                                ->orWhere('cnic_number', 'like', $s);
                         });
                  });
            });
        }

        $candidates = $query->paginate(15);
        $campaigns = Campaign::select('id', 'title', 'job_code', 'department')->orderBy('title')->get();

        $stats = [
            'total_eligible' => Application::whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])->count(),
            'my_graded' => LiveEvaluation::where('evaluator_id', Auth::id())->count(),
            'academic_positions' => Application::whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])
                ->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'))->count(),
        ];

        return view('livewire.board.dashboard', [
            'candidates' => $candidates,
            'campaigns' => $campaigns,
            'stats' => $stats,
        ])->layout('layouts.app', ['title' => 'Selection Board Desk - Hirely']);
    }
}
