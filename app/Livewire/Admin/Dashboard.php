<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\User;
use App\Models\CampaignCommittee;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-campaign') ||
            Auth::user()->can('view-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'scrutiny-committee', 'selection-board-member', 'admin', 'board', 'scrutiny'])
        ), 403);
    }

    public function render()
    {
        $user = Auth::user();
        
        // 1. Calculate General metrics
        $metrics = [
            'total' => Application::count(),
            'submitted' => Application::where('status', 'SUBMITTED')->count(),
            'paid' => Application::where('status', 'PAID')->count(),
            'eligible' => Application::where('status', 'ELIGIBLE')->count(),
            'ineligible' => Application::where('status', 'INELIGIBLE')->count(),
            'appealed' => Application::where('status', 'APPEALED')->count(),
            'evaluated' => Application::whereHas('evaluations')->count(),
        ];

        // 2. Track breakdown metrics
        $tracks = [
            'ACADEMIC' => Application::whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'))->count(),
            'ADMINISTRATIVE' => Application::whereHas('campaign', fn($q) => $q->where('track_type', 'ADMINISTRATIVE'))->count(),
            'SUPPORT' => Application::whereHas('campaign', fn($q) => $q->where('track_type', 'SUPPORT'))->count(),
        ];

        // 3. Active campaigns monitor
        $campaigns = Campaign::withCount('applications')->get();

        // 4. Determine list of applications to display based on roles & permissions
        $pendingScrutiny = [];
        $pendingEvaluations = [];
        
        if ($user->can('scrutinize-application') || $user->hasAnyRole(['super-admin', 'registrar', 'scrutiny-committee', 'scrutiny'])) {
            // Applications with PAID status are ready for Scrutiny
            $pendingScrutiny = Application::with('user.basicInfo', 'campaign')
                ->where('status', 'PAID')
                ->get();
        }

        if ($user->can('evaluate-application') || $user->can('access-board-grading') || $user->hasAnyRole(['super-admin', 'selection-board-member', 'board-member', 'board'])) {
            // ELIGIBLE or UNDER_REVIEW applications are ready for Live Selection Board Evaluation
            $pendingEvaluations = Application::with('user.basicInfo', 'campaign')
                ->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])
                ->get();
        }

        return view('livewire.admin.dashboard', [
            'metrics' => $metrics,
            'tracks' => $tracks,
            'campaigns' => $campaigns,
            'pendingScrutiny' => $pendingScrutiny,
            'pendingEvaluations' => $pendingEvaluations,
        ])->layout('layouts.app');
    }
}
