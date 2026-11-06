<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationPrintController extends Controller
{
    /**
     * Display printable official application dossier (Candidate or Admin View).
     */
    public function show(Request $request, $id)
    {
        $application = Application::with([
            'user.basicInfo.degrees',
            'user.basicInfo.experiences',
            'user.basicInfo.publications',
            'user.basicInfo.researchProjects',
            'user.basicInfo.patents',
            'user.basicInfo.medals',
            'user.basicInfo.trainings',
            'user.basicInfo.events',
            'user.basicInfo.milestones',
            'user.basicInfo.tools',
            'user.basicInfo.portfolioItems',
            'user.basicInfo.referees',
            'campaign.advertisement',
            'campaign.constitution',
            'campaign.processFlow',
            'billingLedger',
            'evaluations.boardMember',
            'reviews.reviewer.reviewerProfile',
        ])->findOrFail($id);

        $currentUser = Auth::user();
        if (!$currentUser) {
            abort(403, 'Unauthorized access to application dossier.');
        }

        $isApplicant = $currentUser->isApplicant();
        $isStaff = $currentUser->hasRole('super-admin') 
            || $currentUser->hasRole('registrar')
            || $currentUser->can('view-application')
            || $currentUser->can('scrutinize-application')
            || $currentUser->can('access-board-grading');

        // Security: Candidate can ONLY print their own application
        if ($isApplicant && $application->user_id !== $currentUser->id) {
            abort(403, 'You are not authorized to view or print this candidate dossier.');
        }

        // Determine view mode: 'candidate' or 'admin'
        // Applicants can never see confidential admin/scrutiny notes or reviewer identities
        $requestedView = $request->query('view', $isStaff ? 'admin' : 'candidate');
        $viewMode = ($isStaff && $requestedView === 'admin') ? 'admin' : 'candidate';

        $orgName = SystemSetting::getOrgName();
        $currency = SystemSetting::get('currency_symbol', 'PKR');

        // Detailed Printing / Downloading User Metadata
        $printedByName = null;
        if ($currentUser->basicInfo) {
            $name = trim(($currentUser->basicInfo->first_name ?? '') . ' ' . ($currentUser->basicInfo->last_name ?? ''));
            if (!empty($name)) {
                $printedByName = $name;
            }
        }
        if (!$printedByName && $currentUser->reviewerProfile && !empty($currentUser->reviewerProfile->name)) {
            $printedByName = $currentUser->reviewerProfile->name;
        }
        if (!$printedByName) {
            $printedByName = $currentUser->name ?? $currentUser->display_name ?? $currentUser->email;
        }

        $printedByUsername = $currentUser->email;

        $rolesCollection = $currentUser->getRoleNames()->map(function ($r) {
            return ucwords(str_replace(['-', '_'], ' ', $r));
        });
        $printedByRole = $rolesCollection->isNotEmpty() 
            ? $rolesCollection->implode(', ') 
            : ($currentUser->isApplicant() ? 'Applicant / Candidate' : 'Authorized User');

        $printedAt = now();

        return view('print.application-dossier', [
            'application' => $application,
            'basicInfo' => $application->user?->basicInfo,
            'campaign' => $application->campaign,
            'advertisement' => $application->campaign?->advertisement,
            'billing' => $application->billingLedger,
            'viewMode' => $viewMode,
            'isStaff' => $isStaff,
            'orgName' => $orgName,
            'orgShortName' => SystemSetting::getOrgShortName(),
            'letterhead' => SystemSetting::getLetterheadDetails(),
            'letterFooter' => SystemSetting::getLetterFooter(),
            'appName' => SystemSetting::getAppName(),
            'appLogo' => SystemSetting::getAppLogo(),
            'currency' => $currency,
            'currentUser' => $currentUser,
            'printedByName' => $printedByName,
            'printedByUsername' => $printedByUsername,
            'printedByRole' => $printedByRole,
            'printedAt' => $printedAt,
        ]);
    }
}
