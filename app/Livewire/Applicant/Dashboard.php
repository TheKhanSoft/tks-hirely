<?php

namespace App\Livewire\Applicant;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use WithFileUploads;

    public $appealText;
    public $appealProof;
    public $showAppealForm = false;
    public $selectedApplicationId;

    public function openAppealForm($applicationId)
    {
        $this->selectedApplicationId = $applicationId;
        $this->showAppealForm = true;
    }

    public function submitAppeal()
    {
        $this->validate([
            'appealText' => 'required|string|min:20',
            'appealProof' => 'required|file|max:2048',
        ]);

        $application = Application::findOrFail($this->selectedApplicationId);

        // Ensure user owns this application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        // Handle upload
        $path = $this->appealProof->store('appeals', 'public');

        // Update application state
        $application->update([
            'status' => 'APPEALED',
            'appeal_letter' => $this->appealText,
            'appeal_proof_path' => $path,
        ]);

        session()->flash('message', 'Your formal appeal has been submitted successfully and queued for grievance committee review.');
        
        $this->showAppealForm = false;
        $this->appealText = '';
        $this->appealProof = null;
    }

    public function render()
    {
        $user = Auth::user();
        $basicInfo = $user->basicInfo;
        $applications = $user->applications()->with(['campaign', 'billingLedger'])->latest()->get();

        // Calculate dossier completeness percentage
        $completion = 0;
        if ($basicInfo) {
            if ($basicInfo->is_locked) {
                $completion = 100;
            } else {
                $completion += 30; // Bio-Data filled
                if ($basicInfo->degrees()->count() > 0) $completion += 25;
                if ($basicInfo->experiences()->count() > 0) $completion += 20;
                if ($basicInfo->publications()->count() > 0 || $basicInfo->tools()->count() > 0 || $basicInfo->milestones()->count() > 0) $completion += 15;
                if ($applications->count() > 0) $completion += 10;
                $completion = min(100, $completion);
            }
        }

        return view('livewire.applicant.dashboard', [
            'basicInfo' => $basicInfo,
            'applications' => $applications,
            'completion' => $completion,
            'totalApplications' => $applications->count(),
            'paidCount' => $applications->whereIn('status', ['PAID', 'ELIGIBLE', 'APPROVED'])->count(),
            'pendingCount' => $applications->where('status', 'SUBMITTED')->count(),
            'eligibleCount' => $applications->where('status', 'ELIGIBLE')->count(),
            'ineligibleCount' => $applications->where('status', 'INELIGIBLE')->count(),
        ])->layout('layouts.app');
    }
}
