<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">EMAIL SERVICES</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">SMTP &amp; GATEWAY</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Email Delivery, SMTP Protocol &amp; Sender Identities
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure outgoing automated email notifications, appointment offer letters, SMTP host credentials, and test delivery connectivity.
            </p>
        </div>

        @can('manage-settings')
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

    @if(session()->has('message'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Automated Notifications &amp; Candidate Alerts">
        All automated transactional emails—including fee payment confirmations, scrutiny status changes, interview calls, and statutory appointment letters—are routed through this gateway.
    </x-help-hint>

    <form wire:submit.prevent="saveEmailSettings" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- ========================================================================= -->
            <!-- 1. SENDER IDENTITY & HEADERS                                              -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
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
                               wire:model="mail_from_name" 
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
                               wire:model="mail_from_address" 
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
                               wire:model="mail_reply_to" 
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
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
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
                                   wire:model="mail_host" 
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
                                   wire:model="mail_port" 
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
                            <select wire:model="mail_encryption" class="form-control form-select" style="font-size: 0.90rem; padding: 0.65rem 1rem;">
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
                                   wire:model="mail_username" 
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
                                   wire:model="mail_password" 
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
            <div style="background: var(--bg-surface); border: 1.5px dashed var(--border-color); border-radius: 8px; padding: 1.5rem;">
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
                        @if($testStatus['type'] === 'success') background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;
                        @else background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; @endif">
                        <span>{{ $testStatus['type'] === 'success' ? '✅' : '❌' }}</span>
                        <span>{{ $testStatus['message'] }}</span>
                    </div>
                @endif
            </div>

        </div>
    </form>
</div>
