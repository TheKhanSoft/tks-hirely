<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">EMAIL SERVICES</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">SMTP &amp; GATEWAY</span>
                <span class="badge badge-cyan" style="font-size: 0.72rem; font-weight: 700;">TRANSACTIONAL DISPATCH</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Email Delivery, SMTP Protocol &amp; Sender Identities
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure outgoing automated email notifications, appointment offer letters, SMTP host credentials, and test delivery connectivity.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetEmailToDefault" 
                    wire:confirm="Reset email settings to system defaults (log driver, Hirely sender)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore email settings to standard defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset SMTP Defaults</span>
            </button>

            <button type="button" 
                    wire:click="saveEmailSettings" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span wire:loading.remove wire:target="saveEmailSettings">Save Email Settings</span>
                <span wire:loading wire:target="saveEmailSettings">Saving...</span>
            </button>
            @endcan
        </div>
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Automated Notifications &amp; Candidate Alerts">
        All automated transactional emails—including fee payment confirmations, scrutiny status changes, interview calls, and statutory appointment letters—are routed through this gateway.
    </x-help-hint>

    {{-- LIVE GATEWAY STATUS STRIP --}}
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>📡 Active SMTP Dispatch Gateway Summary</span>
            </div>
            <span class="badge {{ $mail_mailer === 'smtp' ? 'badge-emerald' : 'badge-slate' }}" style="font-size: 0.7rem; font-weight: 800;">
                Protocol: {{ strtoupper($mail_mailer ?? 'SMTP') }}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
            <div style="background: var(--bg-card); padding: 0.75rem 0.95rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Host &amp; Port</div>
                <div style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.15rem;">
                    {{ $mail_host ?: '127.0.0.1' }}:{{ $mail_port ?: '587' }}
                </div>
            </div>

            <div style="background: var(--bg-card); padding: 0.75rem 0.95rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Security Encryption</div>
                <div style="font-size: 0.88rem; font-weight: 800; color: var(--secondary); margin-top: 0.15rem;">
                    {{ strtoupper($mail_encryption ?: 'TLS') }}
                </div>
            </div>

            <div style="background: var(--bg-card); padding: 0.75rem 0.95rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Sender Display</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary); margin-top: 0.15rem; word-break: break-all;">
                    {{ $mail_from_name ?: 'Hirely Desk' }}
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveEmailSettings" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- ========================================================================= -->
            <!-- 1. SENDER IDENTITY & HEADERS                                              -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>👤 Outgoing Sender Identity &amp; Header Labels</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <!-- From Name -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Sender "From" Display Name</span>
                            <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text" 
                               wire:model.live="mail_from_name" 
                               class="form-control" 
                               placeholder="e.g. Hirely Recruitment Desk" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('mail_from_name') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Name shown in candidate inboxes as the sender.</small>
                    </div>

                    <!-- From Address -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Sender "From" Email Address</span>
                            <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="email" 
                               wire:model.live="mail_from_address" 
                               class="form-control" 
                               placeholder="e.g. noreply@hirely.edu.pk" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('mail_from_address') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Authorized sending address configured with your SPF / DKIM.</small>
                    </div>

                    <!-- Reply-To -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Reply-To Email Address</span>
                        </label>
                        <input type="email" 
                               wire:model.live="mail_reply_to" 
                               class="form-control" 
                               placeholder="e.g. support@hirely.edu.pk" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('mail_reply_to') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Address receiving direct candidate email replies.</small>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. SMTP PROTOCOL & GATEWAY CREDENTIALS                                    -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>⚙️ Mail Protocol &amp; Server Connection</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <!-- Driver / Protocol -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Mail Driver / Protocol</span>
                            <span style="color: var(--danger);">*</span>
                        </label>
                        <select wire:model.live="mail_mailer" class="form-control form-select" style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                            <option value="smtp">⚡ SMTP Server (Gmail, Office 365, Mailgun, SES, CPanel)</option>
                            <option value="sendmail">🖥️ Sendmail (Local Server Binary)</option>
                            <option value="mailgun">🚀 Mailgun API Gateway</option>
                            <option value="ses">☁️ Amazon Simple Email Service (SES)</option>
                            <option value="log">📝 Log Driver (Development / Test Simulation)</option>
                        </select>
                        @error('mail_mailer') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                    </div>

                    @if($mail_mailer === 'smtp')
                        <!-- SMTP Host -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>SMTP Host Server</span>
                                <span style="color: var(--danger);">*</span>
                            </label>
                            <input type="text" 
                                   wire:model.live="mail_host" 
                                   class="form-control" 
                                   placeholder="e.g. smtp.gmail.com" 
                                   style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                            @error('mail_host') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        </div>

                        <!-- SMTP Port -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>SMTP Port</span>
                                <span style="color: var(--danger);">*</span>
                            </label>
                            <input type="number" 
                                   wire:model.live="mail_port" 
                                   class="form-control" 
                                   placeholder="587" 
                                   style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                            @error('mail_port') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        </div>

                        <!-- SMTP Encryption -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>Security Encryption</span>
                            </label>
                            <select wire:model.live="mail_encryption" class="form-control form-select" style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                                <option value="tls">TLS (Standard - Port 587)</option>
                                <option value="ssl">SSL (Implicit - Port 465)</option>
                                <option value="none">None (Plaintext - Port 25 / Internal)</option>
                            </select>
                        </div>

                        <!-- SMTP Username -->
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>SMTP Username / API Key</span>
                            </label>
                            <input type="text" 
                                   wire:model.live="mail_username" 
                                   class="form-control" 
                                   placeholder="e.g. postmaster@yourdomain.com" 
                                   style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                            @error('mail_username') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        </div>

                        <!-- SMTP Password -->
                        <div>
                            <label style="display: flex; align-items: center; justify-content: space-between; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                                <span>SMTP Password / Secret</span>
                                <button type="button" wire:click="togglePasswordVisibility" style="background: none; border: none; font-size: 0.75rem; color: var(--primary); cursor: pointer; text-decoration: underline;">
                                    {{ $showPassword ? 'Hide Password' : 'Show Password' }}
                                </button>
                            </label>
                            <input type="{{ $showPassword ? 'text' : 'password' }}" 
                                   wire:model.live="mail_password" 
                                   class="form-control" 
                                   placeholder="••••••••••••" 
                                   style="font-size: 0.90rem; padding: 0.65rem 1rem;">
                            @error('mail_password') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. LIVE EMAIL GATEWAY DIAGNOSTICS & TEST TOOL                             -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-surface); border: 1.5px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--color-text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <span>🧪 Live Email Delivery Verification Test</span>
                        </h4>
                        <p style="font-size: 0.82rem; color: var(--color-text-secondary); margin: 0.25rem 0 0 0;">
                            Send a real-time verification email to ensure your SMTP server connects, authenticates, and dispatches without errors.
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; max-width: 550px; flex-wrap: wrap;">
                    <input type="email" 
                           wire:model="test_email_recipient" 
                           class="form-control" 
                           placeholder="Enter test recipient email (e.g. your email)" 
                           style="flex: 1; min-width: 260px; font-size: 0.90rem;">
                    
                    @can('manage-settings')
                    <button type="button" 
                            wire:click="sendTestEmail" 
                            class="btn btn-secondary" 
                            style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.25rem; font-weight: 700;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        <span wire:loading.remove wire:target="sendTestEmail">Send Test Email</span>
                        <span wire:loading wire:target="sendTestEmail">Dispatching...</span>
                    </button>
                    @endcan
                </div>
                @error('test_email_recipient') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.35rem;">{{ $message }}</div> @enderror

                @if(session()->has('test_email_status'))
                    @php $testStatus = session('test_email_status'); @endphp
                    <div style="margin-top: 1rem; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;
                        @if($testStatus['type'] === 'success') background: rgba(16, 185, 129, 0.12); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3);
                        @else background: rgba(239, 68, 68, 0.12); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3); @endif">
                        <span>{{ $testStatus['type'] === 'success' ? '✅' : '❌' }}</span>
                        <span>{{ $testStatus['message'] }}</span>
                    </div>
                @endif
            </div>

        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetEmailToDefault" 
                    wire:confirm="Reset email settings to system defaults (log driver, Hirely sender)?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                <span>Reset SMTP Defaults</span>
            </button>

            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveEmailSettings">Save Email Settings</span>
                <span wire:loading wire:target="saveEmailSettings">Saving Email Settings...</span>
            </button>
            @endcan
        </div>
    </form>

    {{-- DEDICATED SMS & TRANSACTIONAL ALERT GATEWAY SECTION --}}
    <div style="margin-top: 2.5rem; border-top: 2px dashed var(--border-color); padding-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                    <span class="badge badge-emerald" style="font-size: 0.72rem; font-weight: 800;">SMS SERVICES</span>
                    <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 700;">TRANSACTIONAL ALERTS</span>
                    <span class="badge badge-slate" style="font-size: 0.72rem;">TELECOM API</span>
                </div>
                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--color-text-primary);">
                    📱 SMS Notification Gateway &amp; Candidate Mobile Alerts
                </h3>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.83rem; color: var(--color-text-secondary);">
                    Broadcast real-time SMS notifications to applicants upon voucher verification, scrutiny deficiency notices, and selection board interview call letters.
                </p>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                @if(Auth::user()->can('manage-settings') || Auth::user()->can('manage-sms-settings'))
                <button type="button" 
                        wire:click="resetSmsToDefault" 
                        wire:confirm="Reset SMS gateway to defaults (disabled, AWKUM-HR mask)?"
                        class="btn btn-secondary btn-sm" 
                        style="font-size: 0.82rem; font-weight: 700;">
                    Reset SMS Defaults
                </button>
                <button type="button" 
                        wire:click="saveSmsSettings" 
                        class="btn btn-primary btn-sm" 
                        style="font-size: 0.82rem; font-weight: 800; padding: 0.5rem 1.25rem;">
                    Save SMS Settings
                </button>
                @endif
            </div>
        </div>

        <div style="background: var(--bg-surface); padding: 1rem 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <span>Enable Automated Transactional SMS Dispatch</span>
                    @if($sms_enabled)
                        <span class="badge badge-emerald" style="font-size: 0.68rem; font-weight: 800;">ACTIVE</span>
                    @else
                        <span class="badge badge-slate" style="font-size: 0.68rem; font-weight: 800;">DISABLED</span>
                    @endif
                </div>
                <div style="font-size: 0.8rem; color: var(--color-text-secondary); margin-top: 0.2rem;">
                    Enables candidate text messages for critical statutory notifications (Pakistan &amp; International).
                </div>
            </div>

            <input type="checkbox" wire:model="sms_enabled" style="width: 22px; height: 22px; accent-color: var(--primary); cursor: pointer;">
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">SMS Gateway Provider</label>
                <select wire:model="sms_provider" class="form-control" style="font-weight: 600;">
                    <option value="twilio">Twilio Cloud Communications</option>
                    <option value="zong">Zong Corporate Bulk SMS Gateway</option>
                    <option value="telenor">Telenor Corporate Messaging</option>
                    <option value="generic_webhook">Generic HTTP Webhook / REST API</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">Alphanumeric Sender ID / Mask</label>
                <input type="text" wire:model="sms_sender_id" class="form-control" style="font-weight: 700;" placeholder="AWKUM-HR">
                <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 0.25rem;">Max 11 chars PTA-registered brand mask</div>
            </div>

            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">API Key / Authentication Secret</label>
                <input type="password" wire:model="sms_api_key" class="form-control" placeholder="••••••••••••••••">
            </div>

            <div>
                <label class="form-label" style="font-weight: 700; font-size: 0.84rem;">Gateway Endpoint URL</label>
                <input type="text" wire:model="sms_api_url" class="form-control" placeholder="https://api.gateway.com/v1/sms/send">
            </div>
        </div>

        <!-- Automated SMS Event Triggers -->
        <div style="margin-bottom: 1.5rem;">
            <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary); margin-bottom: 0.75rem;">
                Statutory Event SMS Triggers:
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 0.75rem;">
                <label style="background: var(--bg-surface); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                    <span style="font-size: 0.82rem; font-weight: 700;">Application Finalized</span>
                    <input type="checkbox" wire:model="sms_trigger_app_submitted" style="accent-color: var(--primary);">
                </label>

                <label style="background: var(--bg-surface); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                    <span style="font-size: 0.82rem; font-weight: 700;">Challan / Fee Paid</span>
                    <input type="checkbox" wire:model="sms_trigger_payment_verified" style="accent-color: var(--primary);">
                </label>

                <label style="background: var(--bg-surface); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                    <span style="font-size: 0.82rem; font-weight: 700;">Scrutiny Deficiency Notice</span>
                    <input type="checkbox" wire:model="sms_trigger_scrutiny_deficiency" style="accent-color: var(--primary);">
                </label>

                <label style="background: var(--bg-surface); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                    <span style="font-size: 0.82rem; font-weight: 700;">Interview Call Shortlist</span>
                    <input type="checkbox" wire:model="sms_trigger_interview_call" style="accent-color: var(--primary);">
                </label>
            </div>
        </div>

        <!-- Live SMS Test Delivery -->
        <div style="background: var(--bg-card); border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.25rem;">
            <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                🧪 Live SMS Dispatch Verification
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                <input type="text" wire:model="test_sms_recipient" class="form-control" style="max-width: 300px; font-weight: 600;" placeholder="+923001234567">
                <button type="button" wire:click="sendTestSms" class="btn btn-secondary btn-sm" style="font-weight: 800;">
                    Dispatch Test SMS
                </button>
            </div>
            @if(session()->has('test_sms_status'))
                @php $smsStatus = session('test_sms_status'); @endphp
                <div style="margin-top: 0.75rem; font-size: 0.82rem; font-weight: 700; color: var(--success);">
                    ✅ {{ $smsStatus['message'] }}
                </div>
            @endif
        </div>
    </div>
</div>
