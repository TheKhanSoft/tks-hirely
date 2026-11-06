<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-slate" style="font-size: 0.72rem; font-weight: 800;">SYSTEM RUNTIME</span>
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 700;">CACHE MANAGEMENT</span>
                <span class="badge badge-cyan" style="font-size: 0.72rem;">DIAGNOSTICS</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                System Health, Runtime Diagnostics &amp; Maintenance Mode
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Inspect server runtime metrics, PHP/database specifications, manage framework caches, and activate candidate maintenance mode.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @if(Auth::user()->can('manage-settings') || Auth::user()->can('manage-system-maintenance'))
            <button type="button" 
                    wire:click="resetMaintenanceToDefault" 
                    wire:confirm="Reset system maintenance configurations to standard baseline (maintenance off)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore maintenance settings to defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset Maintenance Defaults</span>
            </button>

            <button type="button" 
                    wire:click="saveMaintenanceSettings" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span wire:loading.remove wire:target="saveMaintenanceSettings">Save Maintenance Mode</span>
                <span wire:loading wire:target="saveMaintenanceSettings">Saving...</span>
            </button>
            @endif
        </div>
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Infrastructure Runtime &amp; Cache Governance">
        Use this operations console to monitor operational environment health, clear stale application caches after deploying code updates, and manage emergency service maintenance windows.
    </x-help-hint>

    {{-- Action Feedback Alert --}}
    @if(session()->has('maintenance_action_status'))
        @php $status = session('maintenance_action_status'); @endphp
        <div class="alert alert-{{ $status['type'] }} mb-4" style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md); margin-top: 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-weight: 700; font-size: 0.88rem;">{{ $status['message'] }}</span>
        </div>
    @endif

    {{-- LIVE SYSTEM RUNTIME DIAGNOSTICS CARD --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>🖥️ Server Runtime &amp; Environment Diagnostics</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="badge badge-indigo" style="font-size: 0.7rem; font-weight: 800;">
                    PHP {{ $this->serverStats['php_version'] }}
                </span>
                <span class="badge badge-slate" style="font-size: 0.7rem; font-weight: 800;">
                    Laravel {{ $this->serverStats['laravel_version'] }}
                </span>
                <span class="badge {{ $this->serverStats['environment'] === 'production' ? 'badge-emerald' : 'badge-amber' }}" style="font-size: 0.7rem; font-weight: 800;">
                    {{ strtoupper($this->serverStats['environment']) }}
                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.7rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Database Driver</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ strtoupper($this->serverStats['db_driver']) }}
                </div>
                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Primary relational storage</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.7rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Cache / Session</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ strtoupper($this->serverStats['cache_driver']) }} • {{ strtoupper($this->serverStats['session_driver']) }}
                </div>
                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Active cache drivers</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.7rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Memory / Timeout</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ $this->serverStats['memory_limit'] }} • {{ $this->serverStats['max_execution_time'] }}
                </div>
                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.15rem;">PHP runtime thresholds</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.7rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Upload Max Size</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ $this->serverStats['upload_max_filesize'] }} (Post: {{ $this->serverStats['post_max_size'] }})
                </div>
                <div style="font-size: 0.72rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Server inbound payload</div>
            </div>
        </div>
    </div>

    {{-- LIVE DATABASE RECORD TALLIES --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
        <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
            <span>📊 Live Relational Records Volume</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 1rem; text-align: center;">
                <div style="font-size: 1.35rem; font-weight: 900; color: var(--primary);">{{ number_format($this->tableCounts['users']) }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 700;">Users &amp; Staff</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 1rem; text-align: center;">
                <div style="font-size: 1.35rem; font-weight: 900; color: #0ea5e9;">{{ number_format($this->tableCounts['applications']) }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 700;">Applications</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 1rem; text-align: center;">
                <div style="font-size: 1.35rem; font-weight: 900; color: #8b5cf6;">{{ number_format($this->tableCounts['campaigns']) }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 700;">Job Campaigns</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 1rem; text-align: center;">
                <div style="font-size: 1.35rem; font-weight: 900; color: #10b981;">{{ number_format($this->tableCounts['advertisements']) }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 700;">Advertisements</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 1rem; text-align: center;">
                <div style="font-size: 1.35rem; font-weight: 900; color: #f59e0b;">{{ number_format($this->tableCounts['payments']) }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 700;">Fee Vouchers</div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.75rem 1rem; text-align: center;">
                <div style="font-size: 1.35rem; font-weight: 900; color: #ec4899;">{{ number_format($this->tableCounts['audit_logs']) }}</div>
                <div style="font-size: 0.75rem; color: var(--color-text-secondary); font-weight: 700;">Audit Trails</div>
            </div>
        </div>
    </div>

    <!-- Section 1: Maintenance Mode Configuration -->
    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>🚧</span> Statutory Maintenance Mode &amp; Public Window Control
        </h3>
        <p style="font-size: 0.83rem; color: var(--color-text-secondary); margin: 0 0 1.25rem 0;">
            When maintenance mode is activated, public candidates see a branded notice while authorized institutional administrators retain normal session access.
        </p>

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div style="background: var(--bg-surface); padding: 1rem 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                        <span>Activate Candidate Maintenance Mode</span>
                        @if($system_maintenance_mode)
                            <span class="badge badge-rose" style="font-size: 0.68rem; font-weight: 800;">MAINTENANCE ACTIVE</span>
                        @else
                            <span class="badge badge-emerald" style="font-size: 0.68rem; font-weight: 800;">NORMAL OPERATIONS</span>
                        @endif
                    </div>
                    <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                        Temporarily locks out candidate application submissions during scheduled database migrations or server maintenance.
                    </div>
                </div>

                <input type="checkbox" wire:model="system_maintenance_mode" style="width: 22px; height: 22px; accent-color: var(--danger); cursor: pointer;">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                <div>
                    <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                        Maintenance Notice Heading
                    </label>
                    <input type="text" wire:model="maintenance_notice_heading" class="form-control" style="font-weight: 700;" placeholder="Scheduled Statutory System Maintenance">
                </div>

                <div style="grid-column: 1 / -1;">
                    <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                        Maintenance Notice Body &amp; Instructions
                    </label>
                    <textarea wire:model="maintenance_notice_message" rows="2" class="form-control" style="font-size: 0.85rem;" placeholder="Notice text presented to candidates during downtime..."></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Framework Cache & Optimization Management -->
    <div>
        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>⚡</span> Framework Cache Flusher &amp; Performance Optimization
        </h3>
        <p style="font-size: 0.83rem; color: var(--color-text-secondary); margin: 0 0 1.25rem 0;">
            Instantly purge compiled templates, route registers, and application cache keys after applying administrative updates.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;">
            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
                <div>
                    <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">Application Cache</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Flushes settings and dynamic session caches</div>
                </div>
                <button type="button" wire:click="clearApplicationCache" wire:loading.attr="disabled" class="btn btn-secondary btn-sm" style="font-weight: 700; font-size: 0.78rem;">
                    Flush App Cache
                </button>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
                <div>
                    <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">Blade View Cache</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Purges compiled Blade HTML templates</div>
                </div>
                <button type="button" wire:click="clearViewCache" wire:loading.attr="disabled" class="btn btn-secondary btn-sm" style="font-weight: 700; font-size: 0.78rem;">
                    Clear View Cache
                </button>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
                <div>
                    <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">Route Cache</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Re-indexes route controller bindings</div>
                </div>
                <button type="button" wire:click="clearRouteCache" wire:loading.attr="disabled" class="btn btn-secondary btn-sm" style="font-weight: 700; font-size: 0.78rem;">
                    Reset Route Cache
                </button>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
                <div>
                    <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary);">Configuration Cache</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Re-reads .env environment variables</div>
                </div>
                <button type="button" wire:click="clearConfigCache" wire:loading.attr="disabled" class="btn btn-secondary btn-sm" style="font-weight: 700; font-size: 0.78rem;">
                    Clear Config Cache
                </button>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem; border-radius: var(--radius-sm); border: 1.5px solid var(--primary); display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
                <div>
                    <div style="font-weight: 800; font-size: 0.88rem; color: var(--primary);">Master System Optimize</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.15rem;">Executes optimize:clear across all subsystems</div>
                </div>
                <button type="button" wire:click="optimizeSystem" wire:loading.attr="disabled" class="btn btn-primary btn-sm" style="font-weight: 800; font-size: 0.78rem;">
                    Full Optimize Clear
                </button>
            </div>
        </div>
    </div>
</div>
