<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Application;
use App\Models\Campaign;
use App\Models\User;
use App\Models\CampaignCommittee;
use App\Models\BillingLedger;
use App\Models\BasicInfo;
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

        // 4. Recruitment Funnel Analytics
        $totalApps = max(1, Application::count());
        $funnel = [
            'total' => Application::count(),
            'fee_settled' => BillingLedger::where('status', 'SETTLED')->count(),
            'scrutiny_cleared' => Application::whereIn('status', ['ELIGIBLE', 'APPROVED', 'UNDER_REVIEW'])->count(),
            'peer_reviewed' => Application::whereHas('reviews', fn($q) => $q->where('status', 'SUBMITTED'))->count(),
            'board_approved' => Application::where('status', 'APPROVED')->orWhereHas('evaluations')->count(),
        ];
        $funnel['fee_settled_pct'] = round(($funnel['fee_settled'] / $totalApps) * 100);
        $funnel['scrutiny_cleared_pct'] = round(($funnel['scrutiny_cleared'] / $totalApps) * 100);
        $funnel['peer_reviewed_pct'] = round(($funnel['peer_reviewed'] / $totalApps) * 100);
        $funnel['board_approved_pct'] = round(($funnel['board_approved'] / $totalApps) * 100);

        // 5. Quota Distribution Analytics
        $quotaStats = BasicInfo::selectRaw('COALESCE(quota, "Open Merit") as quota_name, count(*) as count')
            ->groupBy('quota_name')
            ->orderByDesc('count')
            ->take(6)
            ->get();
        $totalQuotaCount = max(1, $quotaStats->sum('count'));

        // 6. Revenue & 1Link Settlement Analytics
        $settledSum = (float)BillingLedger::where('status', 'SETTLED')->sum('amount');
        $pendingSum = (float)BillingLedger::where('status', 'PENDING')->sum('amount');
        $totalBilling = max(1, $settledSum + $pendingSum);
        $revenueStats = [
            'settled' => $settledSum,
            'pending' => $pendingSum,
            'settlement_rate' => round(($settledSum / $totalBilling) * 100, 1),
            'settled_txns' => BillingLedger::where('status', 'SETTLED')->count(),
            'total_txns' => BillingLedger::count(),
        ];

        // 7. Recent System Activity Feed
        $recentApplications = Application::with(['user.basicInfo', 'campaign.advertisement'])
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        // 8. Determine list of applications to display based on roles & permissions
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
            'funnel' => $funnel,
            'quotaStats' => $quotaStats,
            'totalQuotaCount' => $totalQuotaCount,
            'revenueStats' => $revenueStats,
            'recentApplications' => $recentApplications,
            'pendingScrutiny' => $pendingScrutiny,
            'pendingEvaluations' => $pendingEvaluations,
        ])->layout('layouts.app');
    }
}
