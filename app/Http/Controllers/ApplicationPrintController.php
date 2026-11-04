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
            'user.basicInfo.medals',
            'user.basicInfo.trainings',
            'user.basicInfo.patents',
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

        $orgName = SystemSetting::get('org_name', 'Hirely Higher Education & Recruitment Commission');
        $currency = SystemSetting::get('currency_symbol', 'PKR');

        return view('print.application-dossier', [
            'application' => $application,
            'basicInfo' => $application->user?->basicInfo,
            'campaign' => $application->campaign,
            'advertisement' => $application->campaign?->advertisement,
            'billing' => $application->billingLedger,
            'viewMode' => $viewMode,
            'isStaff' => $isStaff,
            'orgName' => $orgName,
            'currency' => $currency,
        ]);
    }
}
