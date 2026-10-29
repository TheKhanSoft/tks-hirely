<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\WorkflowStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkflowStatusManagement extends Component
{
    // Active scope tab: 'APPLICATION', 'APPLICANT', 'SYSTEM'
    public $activeScope = 'APPLICATION';

    // Search filter
    public $search = '';

    // Modal state
    public $showModal = false;
    public $isEdit = false;
    public $selectedStatusId = null;

    // Form fields
    public $code = '';
    public $label = '';
    public $scope = 'APPLICATION';
    public $badge_color = 'primary';
    public $description = '';
    public $order = 1;
    public $is_locked_state = false;
    public $is_terminal = false;
    public $is_system_default = false;
    public $allowed_transitions = [];

    // Gate Requirements toggles
    public $req_fee_paid = false;
    public $req_docs_verified = false;
    public $req_peer_reviews = false;
    public $req_justification = false;
    public $req_lock_profile = false;

    public function mount()
    {
        abort_unless(Auth::user()->can('manage-statuses') || Auth::user()->can('manage-settings') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
    }

    public function setScope($scope)
    {
        $this->activeScope = strtoupper($scope);
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->scope = $this->activeScope;
        $this->order = (WorkflowStatus::forScope($this->activeScope)->max('order') ?? 0) + 1;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $status = WorkflowStatus::findOrFail($id);
        $this->isEdit = true;
        $this->selectedStatusId = $status->id;
        $this->code = $status->code;
        $this->label = $status->label;
        $this->scope = $status->scope;
        $this->badge_color = $status->badge_color;
        $this->description = $status->description;
        $this->order = $status->order;
        $this->is_locked_state = $status->is_locked_state;
        $this->is_terminal = $status->is_terminal;
        $this->is_system_default = $status->is_system_default;
        $this->allowed_transitions = $status->allowed_transitions ?? [];

        $reqs = $status->requirements ?? [];
        $this->req_fee_paid = !empty($reqs['require_fee_paid']);
        $this->req_docs_verified = !empty($reqs['require_documents_verified']);
        $this->req_peer_reviews = !empty($reqs['require_peer_reviews']);
        $this->req_justification = !empty($reqs['require_justification_reason']);
        $this->req_lock_profile = !empty($reqs['lock_applicant_profile']);

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'code',
            'label',
            'description',
            'allowed_transitions',
            'selectedStatusId',
            'req_fee_paid',
            'req_docs_verified',
            'req_peer_reviews',
            'req_justification',
            'req_lock_profile',
            'is_locked_state',
            'is_terminal',
            'is_system_default',
        ]);
        $this->badge_color = 'primary';
        $this->order = 1;
        $this->showModal = false;
        $this->isEdit = false;
    }

    public function toggleAllowedTransition($targetCode)
    {
        if (in_array($targetCode, $this->allowed_transitions)) {
            $this->allowed_transitions = array_values(array_diff($this->allowed_transitions, [$targetCode]));
        } else {
            $this->allowed_transitions[] = $targetCode;
        }
    }

    public function saveStatus()
    {
        abort_unless(Auth::user()->can('manage-statuses') || Auth::user()->can('manage-settings') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);

        $codeRule = 'required|string|max:50|regex:/^[A-Z0-9_]+$/';
        if ($this->isEdit) {
            $codeRule .= '|unique:workflow_statuses,code,' . $this->selectedStatusId;
        } else {
            $codeRule .= '|unique:workflow_statuses,code';
        }

        $this->validate([
            'code' => $codeRule,
            'label' => 'required|string|min:2|max:100',
            'scope' => 'required|in:APPLICATION,APPLICANT,SYSTEM',
            'badge_color' => 'required|string|in:primary,success,warning,danger,purple,info,neutral,amber',
            'order' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string|max:500',
        ], [
            'code.regex' => 'Status Code must consist of UPPERCASE letters, numbers, and underscores (e.g. INCOMPLETE_DOSSIER).',
        ]);

        $requirements = [
            'require_fee_paid' => (bool)$this->req_fee_paid,
            'require_documents_verified' => (bool)$this->req_docs_verified,
            'require_peer_reviews' => (bool)$this->req_peer_reviews,
            'require_justification_reason' => (bool)$this->req_justification,
            'lock_applicant_profile' => (bool)$this->req_lock_profile,
        ];

        // Clean empty flags
        $requirements = array_filter($requirements);

        $data = [
            'code' => strtoupper(trim($this->code)),
            'label' => trim($this->label),
            'scope' => $this->scope,
            'badge_color' => $this->badge_color,
            'description' => $this->description,
            'order' => (int)$this->order,
            'is_locked_state' => (bool)$this->is_locked_state,
            'is_terminal' => (bool)$this->is_terminal,
            'is_system_default' => (bool)$this->is_system_default,
            'allowed_transitions' => $this->allowed_transitions,
            'requirements' => $requirements,
            'is_active' => true,
        ];

        if ($this->is_system_default) {
            // Unset other defaults in same scope
            WorkflowStatus::forScope($this->scope)->where('id', '!=', $this->selectedStatusId)->update(['is_system_default' => false]);
        }

        if ($this->isEdit && $this->selectedStatusId) {
            $status = WorkflowStatus::findOrFail($this->selectedStatusId);
            $status->update($data);
            session()->flash('message', "Workflow Status '{$status->label}' updated successfully.");
        } else {
            $status = WorkflowStatus::create($data);
            session()->flash('message', "New Workflow Status '{$status->label}' [{$status->code}] added to {$status->scope} lifecycle.");
        }

        $this->closeModal();
    }

    public function toggleStatusActive($id)
    {
        abort_unless(Auth::user()->can('manage-statuses') || Auth::user()->can('manage-settings') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $status = WorkflowStatus::findOrFail($id);
        $status->is_active = !$status->is_active;
        $status->save();

        session()->flash('message', "Status '{$status->label}' is now " . ($status->is_active ? 'Active' : 'Inactive') . ".");
    }

    public function deleteStatus($id)
    {
        abort_unless(Auth::user()->can('manage-statuses') || Auth::user()->can('manage-settings') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $status = WorkflowStatus::findOrFail($id);
        $label = $status->label;
        $status->delete();
        session()->flash('message', "Status '{$label}' removed from lifecycle.");
    }

    public function render()
    {
        $query = WorkflowStatus::forScope($this->activeScope)->orderBy('order')->orderBy('code');

        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', $term)
                  ->orWhere('label', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        $statuses = $query->get();

        // Scope sibling statuses for transition matrix checkboxes
        $allScopeStatuses = WorkflowStatus::forScope($this->activeScope)->where('is_active', true)->orderBy('order')->get();

        // Metrics for active scope
        $stats = [
            'total' => WorkflowStatus::forScope($this->activeScope)->count(),
            'active' => WorkflowStatus::forScope($this->activeScope)->where('is_active', true)->count(),
            'terminal' => WorkflowStatus::forScope($this->activeScope)->where('is_terminal', true)->count(),
            'locked' => WorkflowStatus::forScope($this->activeScope)->where('is_locked_state', true)->count(),
        ];

        return view('livewire.admin.workflow-status-management', [
            'statuses' => $statuses,
            'allScopeStatuses' => $allScopeStatuses,
            'stats' => $stats,
        ])->layout('layouts.app', ['title' => 'Dynamic Status & Lifecycle Engine - Hirely']);
    }
}
