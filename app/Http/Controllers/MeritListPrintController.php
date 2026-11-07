<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\SystemSetting;
use App\Services\MeritListService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeritListPrintController extends Controller
{
    /**
     * Display printable official statutory merit list gazette.
     */
    public function show(Request $request, $campaignId, MeritListService $meritService)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            abort(403, 'Unauthorized access to recruitment merit list.');
        }

        $campaign = Campaign::with(['advertisement', 'constitution', 'processFlow'])->findOrFail($campaignId);

        // Security check
        $canView = $currentUser->hasRole('super-admin')
            || $currentUser->hasRole('registrar')
            || $currentUser->hasRole('selection-board-member')
            || $currentUser->can('view-board-roster')
            || $currentUser->can('view-merit-list')
            || $currentUser->can('view-application')
            || $currentUser->can('scrutinize-application');

        if (!$canView) {
            // Check if user is an applicant who submitted for this campaign
            $hasApplied = $currentUser->applications()->where('campaign_id', $campaign->id)->exists();
            if (!$hasApplied) {
                abort(403, 'You are not authorized to view this campaign merit list.');
            }
        }

        $stage = $request->query('stage', 'consolidated');
        $quota = $request->query('quota', 'ALL');
        $status = $request->query('status', 'ALL');
        $fee = $request->query('fee', 'ALL');
        $gender = $request->query('gender', 'ALL');
        $minScore = $request->query('minScore', '');
        $recommendation = $request->query('recommendation', 'ALL');
        $search = $request->query('search', '');
        $sortBy = $request->query('sortBy', 'rank');
        $sortDir = $request->query('sortDir', 'asc');

        $data = $meritService->getStageData($campaign, $stage, [
            'quota' => $quota,
            'status' => $status,
            'fee' => $fee,
            'gender' => $gender,
            'minScore' => $minScore,
            'recommendation' => $recommendation,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);

        $orgName = SystemSetting::get('org_name', 'Hirely Higher Education & Recruitment Commission');

        return view('print.merit-list-gazette', [
            'campaign' => $campaign,
            'advertisement' => $campaign->advertisement,
            'stage' => $stage,
            'stageTitle' => $data['stage_title'],
            'applications' => $data['applications'],
            'metrics' => $data['metrics'],
            'orgName' => $orgName,
            'quota' => $quota,
        ]);
    }
}
