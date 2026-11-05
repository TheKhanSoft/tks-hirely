<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Livewire\Traits\WithDynamicListing;
use App\Models\SecurityAuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogManagement extends Component
{
    use WithDynamicListing;

    // View state
    public $isDetailsOpen = false;
    public $selectedRecordId;
    public $selectedRecordDetails = null;

    // Filter
    public $actionFilter = '';
    public $sortBy = 'logged_at';
    public $sortDirection = 'desc';

    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function mount()
    {
        abort_unless(Auth::check() && Auth::user()->can('view-audit-log'), 403);
    }

    public function updatingActionFilter()
    {
        $this->resetPage();
    }

    public function updatedActionFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->actionFilter = '';
        $this->sortBy = 'logged_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function viewLogDetails($id)
    {
        abort_unless(Auth::user()->can('view-audit-log'), 403);
        $this->selectedRecordId = $id;
        $this->selectedRecordDetails = SecurityAuditLog::with('user')->findOrFail($id);
        $this->isDetailsOpen = true;
    }

    public function clearAuditLogs()
    {
        abort_unless(Auth::user()->can('delete-audit-log') || Auth::user()->can('purge-audit-logs'), 403);
        SecurityAuditLog::truncate();
        session()->flash('message', 'All security audit logs purged successfully.');
    }

    public function exportCsv()
    {
        abort_unless(Auth::user()->can('export-audit-log') || Auth::user()->can('view-audit-log'), 403);
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

        $logs = $query->orderBy('logged_at', 'desc')->take(1000)->get();

        $rows = [];
        foreach ($logs as $log) {
            $rows[] = [
                $log->id,
                $log->user?->email ?: 'Anonymous / System',
                $log->action_type,
                $log->table_name,
                $log->record_id,
                $log->ip_address,
                $log->logged_at
            ];
        }

        return $this->streamCsvDownload('audit_logs_forensics_' . date('Y_m_d_His') . '.csv', [
            'Log ID', 'User Email', 'Action Type', 'Target Table', 'Record ID', 'IP Address', 'Timestamp Logged'
        ], $rows);
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

        if ($this->sortBy === 'action_type') {
            $query->orderBy('action_type', $this->sortDirection);
        } elseif ($this->sortBy === 'table_name') {
            $query->orderBy('table_name', $this->sortDirection);
        } elseif ($this->sortBy === 'record_id') {
            $query->orderBy('record_id', $this->sortDirection);
        } elseif ($this->sortBy === 'ip_address') {
            $query->orderBy('ip_address', $this->sortDirection);
        } else {
            $query->orderBy('logged_at', $this->sortDirection);
        }

        $logs = $query->paginate($this->perPage);

        return view('livewire.admin.audit-log-management', [
            'logs' => $logs,
        ])->layout('layouts.app', ['title' => 'Security Audit Logs - Hirely']);
    }
}
