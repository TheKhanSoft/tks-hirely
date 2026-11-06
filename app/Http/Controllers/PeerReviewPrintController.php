<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Campaign;
use App\Models\Advertisement;
use App\Models\ReviewerProfile;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeerReviewPrintController extends Controller
{
    /**
     * Display printable official consensus peer review report for an application.
     */
    public function show(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            abort(403, 'Unauthorized access.');
        }

        abort_unless(
            $currentUser->can('view-review-reports') ||
            $currentUser->can('manage-reviewers') ||
            $currentUser->can('view-application') ||
            $currentUser->hasAnyRole(['super-admin', 'registrar', 'selection-board-member', 'scrutiny-committee']),
            403,
            'You do not have permission to view external peer review reports.'
        );

        $application = Application::with([
            'campaign.advertisement',
            'campaign.constitution',
            'user.basicInfo',
            'reviews.reviewer.reviewerProfile',
            'reviews.assigner',
        ])->findOrFail($id);

        $submittedReviews = $application->reviews->where('status', 'SUBMITTED');
        $avgScore = $submittedReviews->count() > 0 ? round((float)$submittedReviews->avg('overall_score'), 2) : 0.0;
        
        $verdict = $application->evaluatePeerReviewVerdict();
        $orgName = SystemSetting::get('org_name', 'Hirely Higher Education & Recruitment Commission');

        return view('print.peer-review-report', [
            'application' => $application,
            'campaign' => $application->campaign,
            'advertisement' => $application->campaign->advertisement,
            'reviews' => $application->reviews,
            'submittedReviews' => $submittedReviews,
            'avgScore' => $avgScore,
            'verdict' => $verdict,
            'orgName' => $orgName,
        ]);
    }

    /**
     * Display printable academic reviewer dispatch ledger across candidacies.
     */
    public function printDispatchRegister(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            abort(403, 'Unauthorized access.');
        }

        abort_unless(
            $currentUser->can('view-review-reports') ||
            $currentUser->can('manage-reviewers') ||
            $currentUser->hasAnyRole(['super-admin', 'registrar']),
            403
        );

        $adId = $request->query('ad');
        $campaignId = $request->query('campaign') ?: $request->query('camp');
        $status = $request->query('status');

        $query = Application::with(['campaign.advertisement', 'user.basicInfo', 'reviews.reviewer.reviewerProfile'])
            ->whereHas('campaign', fn($q) => $q->where('track_type', 'ACADEMIC'));

        if ($adId && $adId !== 'ALL') {
            $query->whereHas('campaign', fn($q) => $q->where('advertisement_id', (int)$adId));
        }

        if ($campaignId && $campaignId !== 'ALL') {
            $query->where('campaign_id', (int)$campaignId);
        }

        if ($status && $status !== 'ALL') {
            if ($status === 'NEEDS_DISPATCH') {
                $query->doesntHave('reviews');
            } elseif ($status === 'UNDER_REVIEW') {
                $query->where('status', 'UNDER_REVIEW');
            } elseif ($status === 'COMPLETED') {
                $query->whereHas('reviews', fn($q) => $q->where('status', 'SUBMITTED'));
            }
        }

        $candidacies = $query->latest()->get();
        $orgName = SystemSetting::get('org_name', 'Hirely Higher Education & Recruitment Commission');
        $ad = $adId && $adId !== 'ALL' ? Advertisement::find($adId) : null;
        $campaign = $campaignId && $campaignId !== 'ALL' ? Campaign::find($campaignId) : null;

        return view('print.peer-dispatch-register', [
            'candidacies' => $candidacies,
            'orgName' => $orgName,
            'advertisement' => $ad,
            'campaign' => $campaign,
        ]);
    }

    /**
     * Display printable official academic reviewer profile & dossiers portfolio.
     */
    public function printReviewerProfile(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            abort(403, 'Unauthorized access.');
        }

        abort_unless(
            $currentUser->can('view-review-reports') ||
            $currentUser->can('view-reviewer') ||
            $currentUser->can('manage-reviewers') ||
            $currentUser->hasAnyRole(['super-admin', 'registrar', 'selection-board-member']),
            403,
            'You do not have permission to view official expert referee dossiers.'
        );

        $reviewer = ReviewerProfile::with([
            'user',
            'creator',
            'reviews.application.campaign.advertisement',
            'reviews.application.user.basicInfo',
            'reviews.assigner',
        ])->findOrFail($id);

        $reviews = $reviewer->reviews ?? collect();
        $submittedReviews = $reviews->where('status', 'SUBMITTED');
        $pendingReviews = $reviews->where('status', '!=', 'SUBMITTED');
        $avgScore = $submittedReviews->count() > 0 ? round((float)$submittedReviews->avg('overall_score'), 2) : 0.0;
        
        $orgName = SystemSetting::get('org_name', 'Hirely Higher Education & Recruitment Commission');

        return view('print.reviewer-profile-dossier', [
            'reviewer' => $reviewer,
            'reviews' => $reviews,
            'submittedReviews' => $submittedReviews,
            'pendingReviews' => $pendingReviews,
            'avgScore' => $avgScore,
            'orgName' => $orgName,
        ]);
    }
}
