<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Application;
use App\Models\AcademicMedal;
use App\Models\Publication;
use App\Models\ProfessionalEvent;
use App\Models\ResearchProject;
use Illuminate\Support\Facades\Auth;

class ApplicationVerification extends Component
{
    public $applicationId;
    public $application;
    
    // Checklist verification states
    public $verified_biodata = false;
    public $verified_degrees = false;
    public $verified_medals = false;
    public $verified_publications = false;
    public $verified_projects = false;
    public $verified_events = false;

    // Disqualification inputs
    public $disqualification_reason = '';
    public $showDisqualificationModal = false;

    // Right-pane document viewer state
    public $viewingDocName = 'No Document Selected';
    public $viewingDocPath = '';
    public $viewingDocType = 'Default';

    public function mount($id)
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('scrutinize-application') ||
            Auth::user()->can('view-application') ||
            Auth::user()->hasAnyRole(['super-admin', 'registrar', 'scrutiny-committee', 'scrutiny'])
        ), 403);

        $this->applicationId = $id;
        $this->application = Application::with([
            'user.basicInfo.degrees',
            'user.basicInfo.medals',
            'user.basicInfo.publications',
            'user.basicInfo.events',
            'user.basicInfo.researchProjects',
            'campaign.advertisement',
            'campaign.constitution',
            'campaign.processFlow'
        ])->findOrFail($id);
    }

    public function autoFillAcademicDisqualification()
    {
        abort_unless(Auth::user()->can('scrutinize-application') || Auth::user()->can('change-application-status'), 403);

        $evaluation = \App\Services\AcademicEligibilityEvaluator::evaluateCandidate(
            $this->application->user?->basicInfo,
            $this->application->campaign
        );
        if (!empty($evaluation['violations'])) {
            $this->disqualification_reason = "Statutory Academic Eligibility Violations:\n• " . implode("\n• ", $evaluation['violations']);
            $this->showDisqualificationModal = true;
        }
    }

    public function viewDocument($name, $path, $type = 'Default')
    {
        abort_unless(
            Auth::user()->can('verify-documents') ||
            Auth::user()->can('scrutinize-application') ||
            Auth::user()->can('view-application'),
            403
        );

        $this->viewingDocName = $name;
        $this->viewingDocPath = $path;
        $this->viewingDocType = $type;
    }

    // Toggle individual verification state in DB for audit trail
    public function toggleVerifyMedal($medalId)
    {
        abort_unless(
            Auth::user()->can('verify-medal') ||
            Auth::user()->can('verify-documents') ||
            Auth::user()->can('scrutinize-application'),
            403
        );

        $medal = AcademicMedal::findOrFail($medalId);
        $medal->update([
            'is_verified' => !$medal->is_verified,
            'verified_by' => auth()->id(),
        ]);
        $this->application->load('user.basicInfo.medals');
    }

    public function toggleVerifyPublication($pubId)
    {
        abort_unless(
            Auth::user()->can('verify-publication') ||
            Auth::user()->can('verify-documents') ||
            Auth::user()->can('scrutinize-application'),
            403
        );

        $pub = Publication::findOrFail($pubId);
        $pub->update([
            'is_verified' => !$pub->is_verified,
            'verified_by' => auth()->id(),
        ]);
        $this->application->load('user.basicInfo.publications');
    }

    public function toggleVerifyProject($projId)
    {
        abort_unless(
            Auth::user()->can('verify-project') ||
            Auth::user()->can('verify-documents') ||
            Auth::user()->can('scrutinize-application'),
            403
        );

        $proj = ResearchProject::findOrFail($projId);
        $proj->update([
            'is_verified' => !$proj->is_verified,
            'verified_by' => auth()->id(),
        ]);
        $this->application->load('user.basicInfo.researchProjects');
    }

    public function toggleVerifyEvent($eventId)
    {
        abort_unless(
            Auth::user()->can('verify-event') ||
            Auth::user()->can('verify-documents') ||
            Auth::user()->can('scrutinize-application'),
            403
        );

        $event = ProfessionalEvent::findOrFail($eventId);
        $event->update([
            'is_verified' => !$event->is_verified,
            'verified_by' => auth()->id(),
        ]);
        $this->application->load('user.basicInfo.events');
    }

    // Main eligibility transitions
    public function markEligible()
    {
        abort_unless(Auth::user()->can('scrutinize-application') || Auth::user()->can('change-application-status'), 403);

        $this->application->update([
            'status' => 'ELIGIBLE',
            'disqualification_reason' => null
        ]);

        session()->flash('message', 'Candidate marked as ELIGIBLE successfully.');
        $redirectUrl = auth()->user()->hasRole('scrutiny-committee') ? '/scrutiny/dashboard' : '/admin/dashboard';
        return redirect()->to($redirectUrl);
    }

    public function openDisqualificationForm()
    {
        abort_unless(Auth::user()->can('scrutinize-application') || Auth::user()->can('change-application-status'), 403);
        $this->showDisqualificationModal = true;
    }

    public function markIneligible()
    {
        abort_unless(Auth::user()->can('scrutinize-application') || Auth::user()->can('change-application-status'), 403);

        $this->validate([
            'disqualification_reason' => 'required|string|min:10',
        ], [
            'disqualification_reason.required' => 'You must provide a structural code or administrative reference justification for disqualification.'
        ]);

        $this->application->update([
            'status' => 'INELIGIBLE',
            'disqualification_reason' => $this->disqualification_reason
        ]);

        session()->flash('message', 'Candidate disqualified and marked as INELIGIBLE. 7-day appellate window has opened.');
        $this->showDisqualificationModal = false;
        $redirectUrl = auth()->user()->hasRole('scrutiny-committee') ? '/scrutiny/dashboard' : '/admin/dashboard';
        return redirect()->to($redirectUrl);
    }

    public function render()
    {
        $academicEvaluation = \App\Services\AcademicEligibilityEvaluator::evaluateCandidate(
            $this->application->user?->basicInfo,
            $this->application->campaign
        );

        return view('livewire.admin.application-verification', [
            'academicEvaluation' => $academicEvaluation,
        ])->layout('layouts.app');
    }
}
