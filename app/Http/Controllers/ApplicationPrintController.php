<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\BasicInfo;
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

    /**
     * Display printable official candidate dossier (from BasicInfo ID or User ID).
     */
    public function printCandidateProfile(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser) {
            abort(403, 'Unauthorized access to candidate dossier.');
        }

        // Try finding BasicInfo by ID or by user_id
        $basicInfo = BasicInfo::with([
            'user.applications.campaign.advertisement',
            'degrees',
            'experiences',
            'publications',
            'researchProjects',
            'patents',
            'medals',
            'trainings',
            'events',
            'milestones',
            'tools',
            'portfolioItems',
            'referees',
        ])->where('id', $id)->orWhere('user_id', $id)->firstOrFail();

        $targetUser = $basicInfo->user;

        $isApplicant = $currentUser->isApplicant();
        $isStaff = $currentUser->hasRole('super-admin') 
            || $currentUser->hasRole('registrar')
            || $currentUser->can('view-candidate-dossier')
            || $currentUser->can('view-basic-info')
            || $currentUser->can('view-application')
            || $currentUser->can('scrutinize-application')
            || $currentUser->can('access-board-grading');

        // Security: Candidate can ONLY print their own dossier
        if ($isApplicant && $targetUser && $targetUser->id !== $currentUser->id) {
            abort(403, 'You are not authorized to view or print this candidate dossier.');
        }

        // Check if an application_id was passed specifically, or pick the latest application
        $specificAppId = $request->query('application_id');
        $application = null;
        if ($specificAppId && $targetUser) {
            $application = $targetUser->applications()->where('id', $specificAppId)->first();
        }
        if (!$application && $targetUser) {
            $application = $targetUser->applications()->latest()->first();
        }

        // If no application exists yet, construct a virtual application object for comprehensive profile printing
        if (!$application) {
            $application = new Application([
                'tracking_number' => 'CAND-' . str_pad($basicInfo->id, 6, '0', STR_PAD_LEFT),
                'status' => $basicInfo->is_locked ? 'VERIFIED_PROFILE' : 'DRAFT_PROFILE',
                'submission_timestamp' => $basicInfo->updated_at ?? now(),
                'created_at' => $basicInfo->created_at ?? now(),
            ]);
            $application->id = 0;
            $application->user_id = $basicInfo->user_id;
            $application->setRelation('user', $targetUser);
        }

        $requestedView = $request->query('view', $isStaff ? 'admin' : 'candidate');
        $viewMode = ($isStaff && $requestedView === 'admin') ? 'admin' : 'candidate';

        $orgName = SystemSetting::getOrgName();
        $currency = SystemSetting::get('currency_symbol', 'PKR');

        // Metadata for print audit
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
            : ($currentUser->isApplicant() ? 'Applicant / Candidate' : 'Authorized Staff');

        $printedAt = now();

        return view('print.application-dossier', [
            'application' => $application,
            'basicInfo' => $basicInfo,
            'campaign' => $application->campaign ?? null,
            'advertisement' => $application->campaign?->advertisement ?? null,
            'billing' => $application->billingLedger ?? null,
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
