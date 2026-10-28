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

        session()->flash('message', 'Your appeal has been submitted successfully for committee review.');
        
        $this->showAppealForm = false;
        $this->appealText = '';
        $this->appealProof = null;
    }

    public function render()
    {
        $user = Auth::user();
        return view('livewire.applicant.dashboard', [
            'basicInfo' => $user->basicInfo,
            'applications' => $user->applications()->with('campaign', 'billingLedger')->get()
        ])->layout('layouts.app');
    }
}
