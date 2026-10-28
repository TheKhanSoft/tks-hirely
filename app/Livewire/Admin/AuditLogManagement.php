<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SecurityAuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogManagement extends Component
{
    // View state
    public $isDetailsOpen = false;
    public $selectedRecordId;
    public $selectedRecordDetails = null;

    // Filters & Search
    public $search = '';
    public $actionFilter = '';

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
    }

    public function viewLogDetails($id)
    {
        abort_unless(Auth::user()->can('view-audit-log') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        $this->selectedRecordId = $id;
        $this->selectedRecordDetails = SecurityAuditLog::with('user')->findOrFail($id);
        $this->isDetailsOpen = true;
    }

    public function clearAuditLogs()
    {
        abort_unless(Auth::user()->can('delete-audit-log') || Auth::user()->hasAnyRole(['super-admin', 'registrar']), 403);
        SecurityAuditLog::truncate();
        session()->flash('message', 'All security audit logs purged successfully.');
    }

    public function render()
    {
        $query = SecurityAuditLog::with('user');

        if (!empty($this->actionFilter)) {
            $query->where('action_type', $this->actionFilter);
        }

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('table_name', 'like', $searchTerm)
                  ->orWhere('ip_address', 'like', $searchTerm)
                  ->orWhere('record_id', 'like', $searchTerm)
                  ->orWhereHas('user', function ($uq) use ($searchTerm) {
                      $uq->where('email', 'like', $searchTerm);
                  });
            });
        }

        $logs = $query->orderBy('logged_at', 'desc')->take(200)->get();

        return view('livewire.admin.audit-log-management', [
            'logs' => $logs,
        ])->layout('layouts.app', ['title' => 'Security Audit Logs - Hirely']);
    }
}
