<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-rose" style="font-size: 0.72rem; font-weight: 800; background: rgba(225, 29, 72, 0.12); color: #e11d48; border: 1px solid rgba(225, 29, 72, 0.25);">ACCESS CONTROL</span>
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 700;">AUTHENTICATION POLICIES</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">STATUTORY AUDIT</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Security Governance, Two-Factor Authentication & Access Policies
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure 2FA enforcement, idle session timeouts, brute force account lockout thresholds, password complexity standards, and candidate intake gating.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @if(Auth::user()->can('manage-settings') || Auth::user()->can('manage-security-settings'))
            <button type="button" 
                    wire:click="resetSecurityToDefault" 
                    wire:confirm="Reset security, session, and intake policies to standard institutional defaults?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore security policies to defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset Security Defaults</span>
            </button>

            <button type="button" 
                    wire:click="saveSecuritySettings" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span wire:loading.remove wire:target="saveSecuritySettings">Save Security Policies</span>
                <span wire:loading wire:target="saveSecuritySettings">Saving...</span>
            </button>
            @endif
        </div>
    </div>

    {{-- Statutory Callout --}}
    <x-help-hint type="callout" variant="statutory" statute="Statutory University Cyber & Governance Protocols" title="Authentication & Session Hardening">
        These controls enforce zero-trust session management, protect academic peer-review integrity, prevent automated brute force attacks, and regulate public candidate enrollment intake.
    </x-help-hint>

    {{-- LIVE SECURITY POSTURE STRIP --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>🛡️ Active Institutional Security Posture</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span class="badge {{ $candidate_registration_status === 'open' ? 'badge-emerald' : 'badge-rose' }}" style="font-size: 0.7rem; font-weight: 800;">
                    Registration: {{ strtoupper($candidate_registration_status) }}
                </span>
                <span class="badge badge-indigo" style="font-size: 0.7rem; font-weight: 800;">
                    Timeout: {{ $auth_session_timeout_minutes }} Mins
                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">2FA Policy</div>
                <div style="font-size: 0.92rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ match($auth_two_factor_policy) {
                        'disabled' => 'Disabled (Standard Password)',
                        'optional' => 'Optional (User Choice)',
                        'enforced_staff' => 'Mandatory for Staff & Reviewers',
                        'enforced_all' => 'Mandatory for All Users',
                        default => 'Optional'
                    } }}
                </div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Brute Force Lockout</div>
                <div style="font-size: 0.92rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ $auth_max_login_attempts }} Attempts • {{ $auth_lockout_duration_minutes }}m Lock
                </div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Job Board Access</div>
                <div style="font-size: 0.92rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ $job_board_visibility === 'public' ? 'Public (All Visitors)' : 'Members Only (Auth Required)' }}
                </div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                <div style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 700; text-transform: uppercase;">Upload Constraints</div>
                <div style="font-size: 0.92rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    Max {{ $max_upload_size_mb }}MB • {{ strtoupper(str_replace(' ', '', $allowed_file_types)) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Authentication & Multi-Factor Security -->
    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>🔐</span> Authentication &amp; Multi-Factor Session Hardening
        </h3>
        <p style="font-size: 0.83rem; color: var(--color-text-secondary); margin: 0 0 1.25rem 0;">
            Govern session longevity, multi-factor login challenges, and rate limiting thresholds.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <div id="setting-security-2fa">
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Two-Factor Authentication (2FA) Enforcement Policy
                </label>
                <select wire:model="auth_two_factor_policy" class="form-control" style="font-weight: 600;">
                    <option value="disabled">Disabled (Single Password Verification)</option>
                    <option value="optional">Optional (Enabled by User Preference)</option>
                    <option value="enforced_staff">Enforced for Staff, Reviewers &amp; Committee Members</option>
                    <option value="enforced_all">Enforced Globally for All Candidate &amp; Staff Accounts</option>
                </select>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Requires OTP / Authenticator code verification upon login.
                </div>
            </div>

            <div id="setting-security-session-timeout">
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Idle Session Inactivity Timeout (Minutes)
                </label>
                <input type="number" wire:model="auth_session_timeout_minutes" min="5" max="1440" class="form-control" style="font-weight: 600;" placeholder="60">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Automatically terminates idle administrative and reviewer sessions (Default: 60 mins).
                </div>
            </div>

            <div id="setting-security-failed-attempts">
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Max Consecutive Failed Login Attempts
                </label>
                <input type="number" wire:model="auth_max_login_attempts" min="1" max="20" class="form-control" style="font-weight: 600;" placeholder="5">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Suspends authentication attempts to protect against password guessing attacks (Default: 5).
                </div>
            </div>

            <div id="setting-security-lockout-duration">
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Account Lockout Suspension Duration (Minutes)
                </label>
                <input type="number" wire:model="auth_lockout_duration_minutes" min="1" max="1440" class="form-control" style="font-weight: 600;" placeholder="15">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Cooldown period before locked accounts can re-attempt authentication (Default: 15 mins).
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Candidate Intake & Career Board Access -->
    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>👥</span> Candidate Intake Status &amp; Career Portal Access
        </h3>
        <p style="font-size: 0.83rem; color: var(--color-text-secondary); margin: 0 0 1.25rem 0;">
            Control whether prospective candidates can register new accounts or browse active advertisements.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Candidate Online Registration Status
                </label>
                <select wire:model="candidate_registration_status" class="form-control" style="font-weight: 700;">
                    <option value="open">🟢 Open (Accepting New Candidate Registrations)</option>
                    <option value="closed">🔴 Closed (Registration Disabled / Intake Closed)</option>
                </select>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Set to closed during examination cycles, semester breaks, or between advertisement batches.
                </div>
            </div>

            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Public Job Vacancy Board Visibility
                </label>
                <select wire:model="job_board_visibility" class="form-control" style="font-weight: 600;">
                    <option value="public">🌐 Public (All Visitors &amp; Search Engines Can View Jobs)</option>
                    <option value="members_only">🔒 Restricted (Sign-in Required to Browse Advertisements)</option>
                </select>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Determines guest access to the recruitment advertisement landing page.
                </div>
            </div>

            <div style="grid-column: 1 / -1;">
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Registration Closed Candidate Guidance Notice
                </label>
                <textarea wire:model="candidate_registration_closed_message" rows="2" class="form-control" style="font-size: 0.85rem;" placeholder="Notice displayed when registration is closed..."></textarea>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Banner presented on the registration page when intake status is toggled to closed.
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Password Complexity & Identity Validation -->
    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>🛡️</span> Password Policy &amp; CNIC Format Standards
        </h3>
        <p style="font-size: 0.83rem; color: var(--color-text-secondary); margin: 0 0 1.25rem 0;">
            Mandate statutory password entropy and national identity verification formatting.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Minimum Password Length
                </label>
                <input type="number" wire:model="password_min_length" min="6" max="64" class="form-control" style="font-weight: 600;" placeholder="8">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Statutory recommended baseline: 8 characters.
                </div>
            </div>

            <div style="background: var(--bg-surface); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">Require Uppercase Letter</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-muted);">At least one A-Z character</div>
                </div>
                <input type="checkbox" wire:model="password_require_uppercase" style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
            </div>

            <div style="background: var(--bg-surface); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">Require Numerical Digit</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-muted);">At least one 0-9 number</div>
                </div>
                <input type="checkbox" wire:model="password_require_numeric" style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
            </div>

            <div style="background: var(--bg-surface); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">Require Special Symbol</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-muted);">At least one !@#$%^&* symbol</div>
                </div>
                <input type="checkbox" wire:model="password_require_special" style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
            </div>

            <div style="background: var(--bg-surface); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">Force Reset on Initial Login</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-muted);">For new reviewers &amp; committee members</div>
                </div>
                <input type="checkbox" wire:model="force_first_login_password_change" style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
            </div>

            <div style="background: var(--bg-surface); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary);">Pakistani CNIC Mask (13 Digits)</div>
                    <div style="font-size: 0.72rem; color: var(--color-text-muted);">Enforce XXXXX-XXXXXXX-X format</div>
                </div>
                <input type="checkbox" wire:model="enforce_strict_cnic_format" style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
            </div>
        </div>
    </div>

    <!-- Section 4: Document Upload Limits & Safe Extensions -->
    <div>
        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--color-text-primary); margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>📁</span> Candidate Upload File Safety &amp; Size Quotas
        </h3>
        <p style="font-size: 0.83rem; color: var(--color-text-secondary); margin: 0 0 1.25rem 0;">
            Define safe document formats and upload payload constraints to protect server storage.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Max Single Document Size Limit (MB)
                </label>
                <input type="number" wire:model="max_upload_size_mb" min="1" max="50" class="form-control" style="font-weight: 600;" placeholder="5">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Maximum single file upload quota per degree transcript or certificate (Recommended: 5 MB).
                </div>
            </div>

            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">
                    Permitted MIME File Formats
                </label>
                <input type="text" wire:model="allowed_file_types" class="form-control" style="font-weight: 600;" placeholder="pdf, jpg, jpeg, png, docx">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                    Comma-separated list of permitted extensions (e.g., pdf, jpg, jpeg, png, docx).
                </div>
            </div>
        </div>
    </div>
</div>
