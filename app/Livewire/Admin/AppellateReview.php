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

    // Statutory Decree Modal
    public $isResolutionModalOpen = false;
    public $resolutionType = ''; // 'ELIGIBLE', 'PAID', 'REJECTED'
    public $statutoryOrderNotes = '';

    public function openResolutionModal($type)
    {
        abort_unless(Auth::user()->can('process-appeal'), 403);
        $this->resolutionType = $type;
        $this->statutoryOrderNotes = match($type) {
            'ELIGIBLE' => 'The Appellate Tribunal, having examined the supplementary documentary evidence and equivalence certificates, finds the candidate fully qualified and hereby condones the initial scrutiny deficiency. The candidate is unconditionally restored as ELIGIBLE.',
            'PAID' => 'The Appellate Tribunal hereby remands the dossier back to the Scrutiny Committee for de novo review in light of the clarified credential evidence submitted by the appellant.',
            'REJECTED' => 'The Appellate Tribunal, upon rigorous judicial review of the statutory criteria, affirms the initial scrutiny decision. The grievance is hereby dismissed without merit.',
            default => '',
        };
        $this->isResolutionModalOpen = true;
    }

    public function closeResolutionModal()
    {
        $this->isResolutionModalOpen = false;
        $this->resolutionType = '';
        $this->statutoryOrderNotes = '';
    }

    public function executeAppellateDecree()
    {
        abort_unless(Auth::user()->can('process-appeal'), 403);

        if (!$this->selectedAppeal) {
            return;
        }

        if ($this->resolutionType === 'ELIGIBLE') {
            $this->selectedAppeal->update([
                'status' => 'ELIGIBLE',
                'disqualification_reason' => null,
            ]);
            session()->flash('message', 'Formal Appellate Decree Issued: Candidate restored to ELIGIBLE.');
        } elseif ($this->resolutionType === 'PAID') {
            $this->selectedAppeal->update([
                'status' => 'PAID',
                'disqualification_reason' => null,
            ]);
            session()->flash('message', 'Formal Appellate Remand: Candidate remanded to PAID for re-scrutiny.');
        } elseif ($this->resolutionType === 'REJECTED') {
            $this->selectedAppeal->update([
                'status' => 'REJECTED',
            ]);
            session()->flash('message', 'Formal Appellate Decree: Appeal dismissed and candidacy marked REJECTED.');
        }

        $this->closeResolutionModal();
        $this->selectedAppealId = null;
        $this->loadAppeals();
    }

    public function mount()
    {
        abort_unless(Auth::check() && (
            Auth::user()->can('view-appeal') ||
            Auth::user()->can('process-appeal') ||
            Auth::user()->hasAnyRole(['super-admin', 'appellate-committee', 'appellate', 'registrar'])
        ), 403);
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
        abort_unless(
            Auth::user()->can('view-appeal') ||
            Auth::user()->can('process-appeal') ||
            Auth::user()->hasAnyRole(['super-admin', 'appellate-committee', 'appellate', 'registrar']),
            403
        );

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
        abort_unless(Auth::user()->can('process-appeal'), 403);

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
        abort_unless(Auth::user()->can('process-appeal'), 403);

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
        abort_unless(Auth::user()->can('process-appeal'), 403);

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
