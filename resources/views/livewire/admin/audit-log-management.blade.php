<div>
    <!-- Console Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">Security Audit Logs</h2>
                    <p class="card-subtitle" style="margin: 0;">Immutable compliance logs, forensic change trails, and system transition diffs</p>
                </div>
            </div>
        </div>
        <div>
            @if((auth()->user()->can('delete-audit-log') || auth()->user()->hasAnyRole(['super-admin', 'registrar'])) && count($logs) > 0)
                <button type="button" class="btn btn-danger" wire:click="clearAuditLogs" onclick="confirm('Are you sure you want to permanently purge all security logs? This action cannot be undone.') || event.stopImmediatePropagation()" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    <span>Purge Logs</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="card mb-4" style="padding: 1rem 1.25rem;">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search logs by table name, IP, record ID, or user email...">
            </div>
            <div style="width: 200px;">
                <select class="form-control form-select" wire:model.live="actionFilter">
                    <option value="">All Action Types</option>
                    <option value="CREATED">Created</option>
                    <option value="UPDATED">Updated</option>
                    <option value="DELETED">Deleted</option>
                </select>
            </div>
            <div>
                <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">
                    Showing {{ count($logs) }} log{{ count($logs) === 1 ? '' : 's' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <x-table :headers="['Action User', 'Access IP', 'Action Mode', 'Target Database Table', 'Record reference ID', 'Timestamp Logged', 'Action']">
        @forelse($logs as $log)
            <tr>
                <td><strong>{{ $log->user?->email ?: 'Anonymous / Guest' }}</strong></td>
                <td><code>{{ $log->ip_address }}</code></td>
                <td>
                    <span class="badge" style="background: {{ $log->action_type === 'DELETED' ? 'rgba(239,68,68,0.15)' : ($log->action_type === 'CREATED' ? 'rgba(16,185,129,0.15)' : 'rgba(99,102,241,0.15)') }}; color: {{ $log->action_type === 'DELETED' ? 'var(--danger)' : ($log->action_type === 'CREATED' ? 'var(--success)' : 'var(--primary)') }};">
                        {{ $log->action_type }}
                    </span>
                </td>
                <td><code>{{ $log->table_name }}</code></td>
                <td><code>ID #{{ $log->record_id }}</code></td>
                <td><small style="color: var(--color-text-secondary); font-family: monospace;">{{ $log->logged_at }}</small></td>
                <td>
                    <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="viewLogDetails({{ $log->id }})">Details Diff</button>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center" style="padding: 2.5rem; color: var(--color-text-secondary);">No security audit records logged.</td></tr>
        @endforelse
    </x-table>

    <!-- Audit Details Modal -->
    <x-modal :show="$isDetailsOpen" title="Security Audit State Details Diff" closeAction="$set('isDetailsOpen', false)" style="max-width: 800px;">
        @if($selectedRecordDetails)
            <div style="max-height: 480px; overflow-y: auto;">
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Action Class:</p>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary);">{{ $selectedRecordDetails->action_type }}</span>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Database Table:</p>
                        <code>{{ $selectedRecordDetails->table_name }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Record Reference ID:</p>
                        <code>ID #{{ $selectedRecordDetails->record_id }}</code>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mt-4">
                    <div>
                        <h4 style="color: var(--danger); font-size: 0.95rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--danger);"></span>
                            State Before Change:
                        </h4>
                        <pre style="background: rgba(0,0,0,0.5); padding: 1rem; border-radius: 6px; font-size: 0.8rem; overflow-x: auto; color: #fca5a5; font-family: 'JetBrains Mono', monospace;">{{ json_encode($selectedRecordDetails->state_before, JSON_PRETTY_PRINT) ?: 'No previous state (NEW INSERT)' }}</pre>
                    </div>
                    <div>
                        <h4 style="color: var(--success); font-size: 0.95rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></span>
                            State After Change:
                        </h4>
                        <pre style="background: rgba(0,0,0,0.5); padding: 1rem; border-radius: 6px; font-size: 0.8rem; overflow-x: auto; color: #a7f3d0; font-family: 'JetBrains Mono', monospace;">{{ json_encode($selectedRecordDetails->state_after, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>

                <div class="mt-6 flex justify-end" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" wire:click="$set('isDetailsOpen', false)">Close Diff</button>
                </div>
            </div>
        @endif
    </x-modal>
</div>

