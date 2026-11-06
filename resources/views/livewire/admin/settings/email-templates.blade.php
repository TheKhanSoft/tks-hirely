<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);" x-data="{
    insertAtCursor(elId, text) {
        const el = document.getElementById(elId);
        if (!el) return;
        const start = el.selectionStart;
        const end = el.selectionEnd;
        const val = el.value;
        el.value = val.substring(0, start) + text + val.substring(end);
        el.selectionStart = el.selectionEnd = start + text.length;
        el.focus();
        el.dispatchEvent(new Event('input'));
    },
    wrapSelection(elId, before, after) {
        const el = document.getElementById(elId);
        if (!el) return;
        const start = el.selectionStart;
        const end = el.selectionEnd;
        const sel = el.value.substring(start, end) || 'text';
        const replacement = before + sel + after;
        el.value = el.value.substring(0, start) + replacement + el.value.substring(end);
        el.selectionStart = start + before.length;
        el.selectionEnd = el.selectionStart + sel.length;
        el.focus();
        el.dispatchEvent(new Event('input'));
    }
}">

    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">COMMUNICATIONS OS</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">TRANSACTIONAL TEMPLATES</span>
                <span class="badge badge-emerald" style="font-size: 0.72rem; font-weight: 700;">TRIGGER AUTOMATION</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Official Email Templates, Automated Triggers &amp; Custom Dispatch Desk
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Manage official institutional templates sent to Candidates, External Reviewers, Selection Board Specialists, and System Security. Toggle triggers on/off or compose custom bespoke emails.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('send-custom-email')
            <button type="button" 
                    wire:click="openCustomEmailModal" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                <span>Compose Custom Email</span>
            </button>
            @endcan
        </div>
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Statutory Notification Engine &amp; Trigger Governance">
        Every recruitment event—from registration credentials and fee vouchers to qualification scrutiny outcomes and interview call letters—has a dedicated, branded email template. You can pause any automated trigger instantly using the trigger switches below without altering template formatting.
    </x-help-hint>

    {{-- LIVE KPI METRICS STRIP --}}
    @php
        $totalCount = $emailTemplates ? $emailTemplates->count() : 0;
        $activeTriggersCount = $emailTemplates ? $emailTemplates->where('is_trigger_enabled', true)->count() : 0;
        $candidateCount = $emailTemplates ? $emailTemplates->where('recipient_group', 'CANDIDATE')->count() : 0;
        $reviewerCount = $emailTemplates ? $emailTemplates->where('recipient_group', 'REVIEWER')->count() : 0;
        $boardCount = $emailTemplates ? $emailTemplates->where('recipient_group', 'BOARD_EXPERT')->count() : 0;
        $securityCount = $emailTemplates ? $emailTemplates->where('recipient_group', 'SECURITY_AUTH')->count() : 0;
    @endphp
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.85rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem;">
            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Total Templates</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: var(--color-text-primary); margin-top: 0.2rem;">{{ $totalCount }}</div>
            <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">All Stakeholder Profiles</div>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem;">
            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Automated Triggers</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: #10b981; margin-top: 0.2rem;">
                {{ $activeTriggersCount }} <span style="font-size: 0.85rem; color: var(--color-text-muted); font-weight: 600;">/ {{ $totalCount }} Active</span>
            </div>
            <div style="font-size: 0.75rem; color: var(--success); margin-top: 0.2rem;">Live Automated Dispatch</div>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem;">
            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Candidate Pipeline</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: #6366f1; margin-top: 0.2rem;">{{ $candidateCount }}</div>
            <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">Intake, Vouchers, Vetting</div>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem;">
            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Academic Referees</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: #0ea5e9; margin-top: 0.2rem;">{{ $reviewerCount }}</div>
            <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">Invitations &amp; Reminders</div>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem;">
            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Selection Board</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: #8b5cf6; margin-top: 0.2rem;">{{ $boardCount }}</div>
            <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">Conventions &amp; Dockets</div>
        </div>

        <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem 1.25rem;">
            <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Security &amp; Auth</div>
            <div style="font-size: 1.5rem; font-weight: 900; color: #e11d48; margin-top: 0.2rem;">{{ $securityCount }}</div>
            <div style="font-size: 0.75rem; color: var(--color-text-secondary); margin-top: 0.2rem;">Registration &amp; Resets</div>
        </div>
    </div>

    <!-- FILTER & SEARCH CONTROL RIBBON -->
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
        <!-- Recipient Filter Tabs -->
        <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
            @php
                $filters = [
                    'ALL' => 'All Templates',
                    'CANDIDATE' => 'Candidates',
                    'REVIEWER' => 'Reviewers',
                    'BOARD_EXPERT' => 'Selection Board',
                    'SECURITY_AUTH' => 'Security & Auth',
                    'GENERAL' => 'Custom & Bespoke',
                ];
            @endphp
            @foreach($filters as $key => $label)
                <button type="button" 
                        wire:click="$set('templateRecipientFilter', '{{ $key }}')"
                        class="btn btn-sm {{ $templateRecipientFilter === $key ? 'btn-primary' : 'btn-secondary' }}"
                        style="padding: 0.35rem 0.85rem; font-size: 0.78rem; font-weight: 700; border-radius: 20px;">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Search Bar -->
        <div style="min-width: 260px; position: relative;">
            <input type="text" 
                   wire:model.live.debounce.300ms="templateSearch" 
                   class="form-control" 
                   placeholder="Search template name, subject, or slug..." 
                   style="padding-left: 2.2rem; font-size: 0.82rem; height: 36px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- EMAIL TEMPLATES GRID                                                      -->
    <!-- ========================================================================= -->
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @forelse($emailTemplates ?? [] as $tmpl)
            @php
                $groupBadgeColor = match($tmpl->recipient_group) {
                    'CANDIDATE' => 'badge-indigo',
                    'REVIEWER' => 'badge-cyan',
                    'BOARD_EXPERT' => 'badge-purple',
                    'SECURITY_AUTH' => 'badge-rose',
                    default => 'badge-emerald',
                };
                $groupBorderColor = match($tmpl->recipient_group) {
                    'CANDIDATE' => '#4f46e5',
                    'REVIEWER' => '#0284c7',
                    'BOARD_EXPERT' => '#7c3aed',
                    'SECURITY_AUTH' => '#e11d48',
                    default => '#059669',
                };
                $groupIcon = match($tmpl->recipient_group) {
                    'CANDIDATE' => '🎓',
                    'REVIEWER' => '🔬',
                    'BOARD_EXPERT' => '🏛️',
                    'SECURITY_AUTH' => '🔐',
                    default => '📢',
                };
            @endphp
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-left: 4px solid {{ $groupBorderColor }}; border-radius: var(--radius-md); padding: 1.25rem 1.5rem; transition: all 0.2s ease; box-shadow: var(--shadow-sm);"
                 class="template-card-hover">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                    <!-- Template Information -->
                    <div style="flex: 1; min-width: 280px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; flex-wrap: wrap;">
                            <span class="badge {{ $groupBadgeColor }}" style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase;">
                                {{ $groupIcon }} {{ str_replace('_', ' ', $tmpl->recipient_group) }}
                            </span>
                            <code style="font-size: 0.74rem; background: rgba(255,255,255,0.06); padding: 0.15rem 0.5rem; border-radius: 4px; color: var(--color-text-secondary); font-family: 'JetBrains Mono', monospace;">
                                {{ $tmpl->slug }}
                            </code>
                            @if($tmpl->trigger_event)
                                <span class="badge badge-slate" style="font-size: 0.66rem;">
                                    ⚡ {{ $tmpl->trigger_event }}
                                </span>
                            @endif
                        </div>

                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">
                            {{ $tmpl->name }}
                        </h3>

                        <div style="font-size: 0.84rem; color: var(--color-text-secondary); margin-bottom: 0.45rem;">
                            <span style="font-weight: 700; color: var(--color-text-muted);">Subject:</span> 
                            <span style="font-family: inherit; color: var(--color-text-primary);">{{ $tmpl->subject }}</span>
                        </div>

                        <p style="margin: 0; font-size: 0.78rem; color: var(--color-text-muted); line-height: 1.45;">
                            {{ $tmpl->description }}
                        </p>
                    </div>

                    <!-- Trigger Toggle Switch & Action Buttons -->
                    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.85rem;">
                        <!-- Trigger Status Toggle -->
                        <div style="display: flex; align-items: center; gap: 0.6rem; background: var(--bg-card); padding: 0.4rem 0.8rem; border-radius: 20px; border: 1px solid var(--border-color);">
                            <span style="font-size: 0.75rem; font-weight: 700; color: {{ $tmpl->is_trigger_enabled ? 'var(--success)' : 'var(--color-text-muted)' }};">
                                {{ $tmpl->is_trigger_enabled ? '● Trigger Active' : '○ Trigger Paused' }}
                            </span>
                            @can('toggle-email-triggers')
                            <button type="button" 
                                    wire:click="toggleTemplateTrigger({{ $tmpl->id }})"
                                    class="btn btn-sm {{ $tmpl->is_trigger_enabled ? 'btn-success' : 'btn-secondary' }}"
                                    style="padding: 0.2rem 0.65rem; font-size: 0.72rem; font-weight: 800; border-radius: 12px;"
                                    title="Click to toggle automated email trigger">
                                {{ $tmpl->is_trigger_enabled ? 'Disable' : 'Enable' }}
                            </button>
                            @endcan
                        </div>

                        <!-- Action Button Group -->
                        <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                            <button type="button" 
                                    wire:click="openPreviewModal({{ $tmpl->id }})" 
                                    class="btn btn-secondary btn-sm" 
                                    style="padding: 0.3rem 0.75rem; font-size: 0.76rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Preview</span>
                            </button>

                            @can('manage-email-templates')
                            <button type="button" 
                                    wire:click="openEditTemplateModal({{ $tmpl->id }})" 
                                    class="btn btn-primary btn-sm" 
                                    style="padding: 0.3rem 0.75rem; font-size: 0.76rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                <span>Edit Template</span>
                            </button>

                            <button type="button" 
                                    wire:click="sendTestTemplateEmail({{ $tmpl->id }})" 
                                    wire:confirm="Send a live test dispatch of this template to your admin email address?"
                                    class="btn btn-secondary btn-sm" 
                                    style="padding: 0.3rem 0.65rem; font-size: 0.76rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;"
                                    title="Send test email to your account">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                                <span>Test Send</span>
                            </button>
                            @endcan

                            @can('send-custom-email')
                            <button type="button" 
                                    wire:click="openCustomEmailModal('{{ $tmpl->slug }}')" 
                                    class="btn btn-secondary btn-sm" 
                                    style="padding: 0.3rem 0.65rem; font-size: 0.76rem; font-weight: 700;"
                                    title="Use this layout to compose a bespoke email">
                                <span>Use Layout</span>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="background: var(--bg-surface); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 3rem; text-align: center;">
                <p style="color: var(--color-text-muted); font-size: 0.95rem; margin: 0 0 1rem 0;">No email templates match the selected filter criteria.</p>
                <button type="button" wire:click="$set('templateRecipientFilter', 'ALL'); $set('templateSearch', '');" class="btn btn-secondary btn-sm">Clear Filters</button>
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- RECENT EMAIL AUDIT LOGS STRIP                                             -->
    <!-- ========================================================================= -->
    @if(!empty($emailLogs) && $emailLogs->isNotEmpty())
        <div style="margin-top: 2.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <span>📜 Recent Communications Audit Log</span>
                </h3>
                <span style="font-size: 0.75rem; color: var(--color-text-muted);">Real-time dispatch tracking</span>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--color-text-muted); font-size: 0.72rem; text-transform: uppercase;">
                            <th style="padding: 0.5rem 0.75rem;">Recipient</th>
                            <th style="padding: 0.5rem 0.75rem;">Subject</th>
                            <th style="padding: 0.5rem 0.75rem;">Template / Slug</th>
                            <th style="padding: 0.5rem 0.75rem;">Type</th>
                            <th style="padding: 0.5rem 0.75rem;">Status</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: right;">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emailLogs as $log)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td style="padding: 0.5rem 0.75rem;">
                                    <strong>{{ $log->recipient_name ?: 'User' }}</strong><br>
                                    <small style="color: var(--color-text-muted);">{{ $log->recipient_email }}</small>
                                </td>
                                <td style="padding: 0.5rem 0.75rem; color: var(--color-text-primary); font-weight: 600;">
                                    {{ Str::limit($log->subject, 40) }}
                                </td>
                                <td style="padding: 0.5rem 0.75rem;">
                                    <code style="font-size: 0.72rem;">{{ $log->template_slug ?: 'manual' }}</code>
                                </td>
                                <td style="padding: 0.5rem 0.75rem;">
                                    <span class="badge badge-slate" style="font-size: 0.65rem;">{{ $log->dispatch_type }}</span>
                                </td>
                                <td style="padding: 0.5rem 0.75rem;">
                                    @if($log->status === 'SENT')
                                        <span class="badge badge-emerald" style="font-size: 0.65rem; font-weight: 800;">✓ SENT</span>
                                    @elseif($log->status === 'TRIGGER_DISABLED_SKIPPED')
                                        <span class="badge badge-amber" style="font-size: 0.65rem; font-weight: 800;">⏸ TRIGGER SKIPPED</span>
                                    @else
                                        <span class="badge badge-rose" style="font-size: 0.65rem; font-weight: 800;" title="{{ $log->error_message }}">✗ FAILED</span>
                                    @endif
                                </td>
                                <td style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.74rem; color: var(--color-text-muted);">
                                    {{ $log->sent_at?->diffForHumans() ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- 1. EDIT EMAIL TEMPLATE MODAL                                              -->
    <!-- ========================================================================= -->
    <x-modal :show="$isTemplateModalOpen" title="Edit Email Template: {{ $template_name }}" closeAction="closeTemplateModal" style="max-width: 820px; width: 95%;">
        @if($isTemplateModalOpen)
            <form wire:submit.prevent="saveTemplate">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Subject Line -->
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            Subject Line *
                        </label>
                        <input type="text" 
                               wire:model="template_subject" 
                               class="form-control" 
                               placeholder="e.g. Application Submitted Successfully: {job_title}"
                               style="font-size: 0.88rem; font-weight: 600;">
                        @error('template_subject') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Preheader Text -->
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            Inbox Preheader Snippet (Preview text shown in email clients)
                        </label>
                        <input type="text" 
                               wire:model="template_preheader" 
                               class="form-control" 
                               placeholder="Brief 1-sentence inbox preview text..."
                               style="font-size: 0.84rem;">
                    </div>

                    <!-- Trigger Toggle Inside Edit -->
                    <div style="background: var(--bg-surface); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-size: 0.84rem; font-weight: 700; color: var(--color-text-primary);">Automated Event Trigger Status</div>
                            <div style="font-size: 0.75rem; color: var(--color-text-secondary);">When enabled, this email is automatically dispatched on the corresponding recruitment milestone.</div>
                        </div>
                        <label class="toggle-switch" style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" wire:model="template_is_trigger_enabled" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                            <span style="font-size: 0.8rem; font-weight: 700; color: {{ $template_is_trigger_enabled ? 'var(--success)' : 'var(--color-text-muted)' }};">
                                {{ $template_is_trigger_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </label>
                    </div>

                    <!-- Dynamic Placeholder Pills -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-primary);">
                                Dynamic Placeholders (Click to insert into email body):
                            </label>
                            <span style="font-size: 0.72rem; color: var(--color-text-muted);">Auto-interpolated on send</span>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; background: var(--bg-card); padding: 0.65rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); max-height: 90px; overflow-y: auto;">
                            @foreach($template_placeholders as $placeholderKey => $label)
                                <button type="button" 
                                        @click="insertAtCursor('template-body-textarea', '{{ $placeholderKey }}')"
                                        class="badge badge-slate" 
                                        style="cursor: pointer; font-size: 0.72rem; padding: 0.2rem 0.5rem; font-family: 'JetBrains Mono', monospace; border: 1px solid rgba(255,255,255,0.1);"
                                        title="{{ $label }}">
                                    + {{ $placeholderKey }}
                                </button>
                            @endforeach
                            <button type="button" @click="insertAtCursor('template-body-textarea', '{org_name}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.72rem;">+ {org_name}</button>
                            <button type="button" @click="insertAtCursor('template-body-textarea', '{login_url}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.72rem;">+ {login_url}</button>
                            <button type="button" @click="insertAtCursor('template-body-textarea', '{support_email}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.72rem;">+ {support_email}</button>
                        </div>
                    </div>

                    <!-- Rich Body Formatting Toolbar -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">
                                Email Template HTML Body *
                            </label>
                            <!-- Rich formatting toolbar -->
                            <div style="display: flex; align-items: center; gap: 0.25rem;">
                                <button type="button" @click="wrapSelection('template-body-textarea', '<strong>', '</strong>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; font-weight: 800;" title="Bold">B</button>
                                <button type="button" @click="wrapSelection('template-body-textarea', '<em>', '</em>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; font-style: italic;" title="Italic">I</button>
                                <button type="button" @click="wrapSelection('template-body-textarea', '<h2 style=\x22margin-top:0; color:#0f172a;\x22>', '</h2>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Heading">H2</button>
                                <button type="button" @click="insertAtCursor('template-body-textarea', '<div class=\x22callout-box\x22>\n    <p>Important announcement text...</p>\n</div>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Callout Box">Box</button>
                                <button type="button" @click="insertAtCursor('template-body-textarea', '<p style=\x22text-align:center; margin:24px 0;\x22><a href=\x22{login_url}\x22 class=\x22btn-action\x22>Action Button &rarr;</a></p>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Action Button">CTA</button>
                            </div>
                        </div>

                        <textarea id="template-body-textarea" 
                                  wire:model="template_body" 
                                  rows="12" 
                                  class="form-control" 
                                  style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; line-height: 1.5;"></textarea>
                        @error('template_body') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Modal Actions Strip -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 0.5rem;">
                        <button type="button" 
                                wire:click="resetTemplateToDefault({{ $selectedTemplateId }})" 
                                wire:confirm="Reset this template to statutory default HTML layout?"
                                class="btn btn-secondary btn-sm" 
                                style="color: #ef4444;">
                            ↺ Restore Statutory Default
                        </button>

                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <button type="button" wire:click="closeTemplateModal" class="btn btn-secondary btn-sm">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 800;">Save Changes</button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </x-modal>

    <!-- ========================================================================= -->
    <!-- 2. LIVE EMAIL PREVIEW MODAL                                               -->
    <!-- ========================================================================= -->
    <x-modal :show="$isPreviewModalOpen" title="Email Inspection: {{ $previewTemplateName }}" closeAction="closePreviewModal" style="max-width: 820px; width: 95%;">
        @if($isPreviewModalOpen)
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                
                <!-- Preview Toolbar & Inspection Mode -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-surface); padding: 0.65rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 0.78rem; font-weight: 700; color: var(--color-text-secondary); text-transform: uppercase;">Rendering Mode:</span>
                        <div style="display: inline-flex; background: var(--bg-card); padding: 2px; border-radius: 6px; border: 1px solid var(--border-color);">
                            <button type="button" 
                                    wire:click="setPreviewMode('sample')"
                                    class="btn btn-sm {{ $previewMode === 'sample' ? 'btn-primary' : 'btn-ghost' }}"
                                    style="padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 700; border-radius: 4px;">
                                ✓ Sample Data
                            </button>
                            <button type="button" 
                                    wire:click="setPreviewMode('raw')"
                                    class="btn btn-sm {{ $previewMode === 'raw' ? 'btn-primary' : 'btn-ghost' }}"
                                    style="padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 700; border-radius: 4px;">
                                { } Placeholders
                            </button>
                        </div>
                    </div>

                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">
                        @if($previewMode === 'sample')
                            <span style="color: #10b981; font-weight: 700;">● Realistic Sample Values Loaded</span> (Candidate, Designation, Tracking, Dates)
                        @else
                            <span style="color: #6366f1; font-weight: 700;">● Raw Template Variables</span>
                        @endif
                    </div>
                </div>

                <!-- Simulated Email Client Envelope Strip -->
                <div style="background: #090d16; border: 1px solid #1e293b; border-radius: 8px 8px 0 0; padding: 0.85rem 1.25rem; font-size: 0.8rem; color: #94a3b8; display: flex; flex-direction: column; gap: 0.35rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-weight: 700; color: #64748b; width: 60px;">From:</span>
                        <span style="color: #cbd5e1;">Statutory Recruitment Directorate &lt;noreply@hirely.app&gt;</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-weight: 700; color: #64748b; width: 60px;">To:</span>
                        <span style="color: #cbd5e1;">Dr. Tariq Mahmood Khan &lt;tariq.mahmood@example.edu.pk&gt;</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; border-top: 1px solid #1e293b; padding-top: 0.35rem; margin-top: 0.15rem;">
                        <span style="font-weight: 700; color: #64748b; width: 60px;">Subject:</span>
                        <span style="color: #ffffff; font-weight: 700;">{{ $previewSubject }}</span>
                    </div>
                </div>

                <!-- Rendered Email Body in Master Branded Layout -->
                <div style="background: #0b1120; border: 1px solid #1e293b; border-top: none; border-radius: 0 0 8px 8px; max-height: 560px; overflow-y: auto; padding: 1.5rem 0.75rem;">
                    {!! $previewHtml !!}
                </div>

                <!-- Preview Actions -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                    @can('manage-email-templates')
                        @if($selectedTemplateId)
                        <button type="button" 
                                wire:click="sendTestTemplateEmail({{ $selectedTemplateId }})" 
                                wire:confirm="Send a live test dispatch of this template to your admin email address?"
                                class="btn btn-secondary btn-sm"
                                style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 700;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                            <span>Send Live Test to My Email</span>
                        </button>
                        @endif
                    @endcan

                    <button type="button" wire:click="closePreviewModal" class="btn btn-primary btn-sm" style="font-weight: 700;">Close Preview</button>
                </div>
            </div>
        @endif
    </x-modal>

    <!-- ========================================================================= -->
    <!-- 3. SEND CUSTOM BESPOKE EMAIL MODAL (WITH RICH TEXTBOX)                    -->
    <!-- ========================================================================= -->
    <x-modal :show="$isCustomEmailModalOpen" title="Compose &amp; Dispatch Bespoke Custom Email" closeAction="closeCustomEmailModal" style="max-width: 860px; width: 95%;">
        @if($isCustomEmailModalOpen)
            <form wire:submit.prevent="sendCustomEmailAction">
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    
                    <!-- Pre-fill from Existing Template -->
                    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: var(--radius-sm);">
                        <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--secondary); text-transform: uppercase; margin-bottom: 0.35rem;">
                            Template Pre-Fill (Optional):
                        </label>
                        <select wire:model.live="custom_selected_template_slug" wire:change="loadTemplateForCustomEmail" class="form-control form-select" style="font-size: 0.85rem;">
                            <option value="">Start from blank bespoke draft...</option>
                            @foreach($emailTemplates ?? [] as $t)
                                <option value="{{ $t->slug }}">Load Template: {{ $t->name }} ({{ $t->recipient_group }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Recipient Selection Controls -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                        <!-- Recipient Target Selection -->
                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                Target Recipient Roster *
                            </label>
                            <select wire:model.live="custom_recipient_type" class="form-control form-select" style="font-size: 0.85rem;">
                                <option value="custom">Direct Email Address (Enter Manually)</option>
                                <option value="candidate">Select from Candidate Applicant Pool</option>
                                <option value="reviewer">Select from Academic Peer Referee Roster</option>
                                <option value="board">Select from Selection Board / Committee Member</option>
                            </select>
                        </div>

                        <!-- User Selector Dropdown (When Not Manual) -->
                        @if($custom_recipient_type === 'candidate')
                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                    Select Candidate:
                                </label>
                                <select wire:model.live="custom_recipient_user_id" class="form-control form-select" style="font-size: 0.85rem;">
                                    <option value="">Choose an applicant...</option>
                                    @foreach($candidatesList as $cand)
                                        <option value="{{ $cand->id }}">
                                            {{ $cand->basicInfo ? ($cand->basicInfo->first_name . ' ' . $cand->basicInfo->last_name) : $cand->email }} ({{ $cand->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($custom_recipient_type === 'reviewer')
                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                    Select Academic Referee:
                                </label>
                                <select wire:model.live="custom_recipient_user_id" class="form-control form-select" style="font-size: 0.85rem;">
                                    <option value="">Choose an expert referee...</option>
                                    @foreach($reviewersList as $rev)
                                        <option value="{{ $rev->user_id }}">
                                            {{ $rev->name }} ({{ $rev->university }}) — {{ $rev->user?->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($custom_recipient_type === 'board')
                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                    Select Committee Member:
                                </label>
                                <select wire:model.live="custom_recipient_user_id" class="form-control form-select" style="font-size: 0.85rem;">
                                    <option value="">Choose board/scrutiny member...</option>
                                    @foreach($boardMembersList as $bm)
                                        <option value="{{ $bm->id }}">{{ $bm->name ?? $bm->email }} ({{ $bm->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Recipient Email & Name Fields -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                Recipient Email Address *
                            </label>
                            <input type="email" 
                                   wire:model="custom_recipient_email" 
                                   class="form-control" 
                                   placeholder="e.g. candidate@university.edu.pk"
                                   style="font-size: 0.85rem;">
                            @error('custom_recipient_email') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                Recipient Salutation Name (Optional)
                            </label>
                            <input type="text" 
                                   wire:model="custom_recipient_name" 
                                   class="form-control" 
                                   placeholder="e.g. Dr. Muhammad Tariq"
                                   style="font-size: 0.85rem;">
                        </div>
                    </div>

                    <!-- Custom Subject -->
                    <div>
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            Email Subject Line *
                        </label>
                        <input type="text" 
                               wire:model="custom_subject" 
                               class="form-control" 
                               placeholder="e.g. Urgent Update: Selection Board Proceedings Schedule"
                               style="font-size: 0.88rem; font-weight: 600;">
                        @error('custom_subject') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Rich Text Editor for Custom Body -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap; gap: 0.4rem;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: var(--color-text-primary);">
                                Email Body Content (Rich Text / HTML Formatted) *
                            </label>

                            <!-- Rich Textbox Formatting Controls -->
                            <div style="display: flex; align-items: center; gap: 0.25rem; flex-wrap: wrap; background: var(--bg-card); padding: 0.25rem 0.4rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                <button type="button" @click="wrapSelection('custom-email-body-textarea', '<strong>', '</strong>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; font-weight: 800;" title="Bold">B</button>
                                <button type="button" @click="wrapSelection('custom-email-body-textarea', '<em>', '</em>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; font-style: italic;" title="Italic">I</button>
                                <button type="button" @click="wrapSelection('custom-email-body-textarea', '<u>', '</u>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem; text-decoration: underline;" title="Underline">U</button>
                                <button type="button" @click="wrapSelection('custom-email-body-textarea', '<h2 style=\x22margin-top:0; color:#0f172a;\x22>', '</h2>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Heading 2">H2</button>
                                <button type="button" @click="wrapSelection('custom-email-body-textarea', '<h3 style=\x22color:#334155;\x22>', '</h3>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Heading 3">H3</button>
                                <button type="button" @click="insertAtCursor('custom-email-body-textarea', '<ul>\n    <li>Bullet point 1</li>\n    <li>Bullet point 2</li>\n</ul>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Bullet List">• List</button>
                                <button type="button" @click="insertAtCursor('custom-email-body-textarea', '<div class=\x22callout-box\x22>\n    <strong>Notice:</strong> Please be advised that...\n</div>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Callout Box">Box</button>
                                <button type="button" @click="insertAtCursor('custom-email-body-textarea', '<p style=\x22text-align:center; margin:24px 0;\x22><a href=\x22{login_url}\x22 class=\x22btn-action\x22>Action Button Text &rarr;</a></p>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Action Button">CTA</button>
                                <button type="button" @click="insertAtCursor('custom-email-body-textarea', '<hr style=\x22border:0; border-top:1px solid #e2e8f0; margin:20px 0;\x22>')" class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.45rem; font-size: 0.72rem;" title="Divider">Line</button>
                            </div>
                        </div>

                        <!-- Variable Insertion Chips -->
                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.45rem; flex-wrap: wrap;">
                            <span style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 700;">Placeholders:</span>
                            <button type="button" @click="insertAtCursor('custom-email-body-textarea', '{recipient_name}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.7rem;">+ {recipient_name}</button>
                            <button type="button" @click="insertAtCursor('custom-email-body-textarea', '{org_name}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.7rem;">+ {org_name}</button>
                            <button type="button" @click="insertAtCursor('custom-email-body-textarea', '{job_title}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.7rem;">+ {job_title}</button>
                            <button type="button" @click="insertAtCursor('custom-email-body-textarea', '{tracking_number}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.7rem;">+ {tracking_number}</button>
                            <button type="button" @click="insertAtCursor('custom-email-body-textarea', '{login_url}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.7rem;">+ {login_url}</button>
                            <button type="button" @click="insertAtCursor('custom-email-body-textarea', '{support_email}')" class="badge badge-slate" style="cursor: pointer; font-size: 0.7rem;">+ {support_email}</button>
                        </div>

                        <!-- Textarea -->
                        <textarea id="custom-email-body-textarea" 
                                  wire:model="custom_body" 
                                  rows="10" 
                                  class="form-control" 
                                  placeholder="Type or paste your message here. HTML tags and placeholders are fully supported..."
                                  style="font-family: 'JetBrains Mono', monospace; font-size: 0.84rem; line-height: 1.55;"></textarea>
                        @error('custom_body') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Dispatch Actions Strip -->
                    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" wire:click="closeCustomEmailModal" class="btn btn-secondary">
                            Cancel
                        </button>

                        <button type="submit" 
                                class="btn btn-primary" 
                                style="font-weight: 800; padding: 0.6rem 1.6rem; display: inline-flex; align-items: center; gap: 0.45rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            <span wire:loading.remove wire:target="sendCustomEmailAction">Dispatch Custom Email</span>
                            <span wire:loading wire:target="sendCustomEmailAction">Sending Email...</span>
                        </button>
                    </div>
                </div>
            </form>
        @endif
    </x-modal>

</div>
