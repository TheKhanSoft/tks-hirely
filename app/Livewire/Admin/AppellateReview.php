<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class AppellateReview extends Component
{
    public $appeals;
    public $selectedAppealId;
    public $selectedAppeal;

    // Split screen document viewer
    public $viewingDocName = 'No Document Selected';
    public $viewingDocPath = '';

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'appellate-committee']), 403);
        $this->loadAppeals();
    }

    public function loadAppeals()
    {
        $this->appeals = Application::with('user.basicInfo', 'campaign')
            ->where('status', 'APPEALED')
            ->get();
            
        if ($this->selectedAppealId) {
            $this->selectAppeal($this->selectedAppealId);
        } elseif ($this->appeals->isNotEmpty()) {
            $this->selectAppeal($this->appeals->first()->id);
        } else {
            $this->selectedAppeal = null;
        }
    }

    public function selectAppeal($id)
    {
        $this->selectedAppealId = $id;
        $this->selectedAppeal = Application::with('user.basicInfo', 'campaign')->findOrFail($id);
        
        if ($this->selectedAppeal->appeal_proof_path) {
            $this->viewingDocName = 'Appeal Grievance Proof';
            $this->viewingDocPath = $this->selectedAppeal->appeal_proof_path;
        } else {
            $this->viewingDocName = 'No Document Selected';
            $this->viewingDocPath = '';
        }
    }

    public function approveAppealToPaid()
    {
        if (!$this->selectedAppeal) {
            return;
        }

        // Shifting status back to PAID, clearing disqualification reason
        $this->selectedAppeal->update([
            'status' => 'PAID',
            'disqualification_reason' => null,
        ]);

        session()->flash('message', 'Appeal approved. Candidate status reset to PAID and returned to Scrutiny Pipeline.');
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function approveAppealToEligible()
    {
        if (!$this->selectedAppeal) {
            return;
        }

        // Shifting status directly to ELIGIBLE
        $this->selectedAppeal->update([
            'status' => 'ELIGIBLE',
            'disqualification_reason' => null,
        ]);

        session()->flash('message', 'Appeal approved. Candidate status upgraded directly to ELIGIBLE.');
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function rejectAppeal()
    {
        if (!$this->selectedAppeal) {
            return;
        }

        // Shifting status to REJECTED (appeal finalized and dismissed)
        $this->selectedAppeal->update([
            'status' => 'REJECTED',
        ]);

        session()->flash('message', 'Appeal rejected. Candidate status permanently updated to REJECTED.');
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function render()
    {
        return view('livewire.admin.appellate-review')
            ->layout('layouts.app');
    }
}
