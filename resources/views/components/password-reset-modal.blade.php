@php
    $showPasswordResetModal = $showPasswordResetModal ?? ($this->showPasswordResetModal ?? false);
    $resetTargetUser = $resetTargetUser ?? ($this->resetTargetUser ?? null);
    $resetErrorMessage = $resetErrorMessage ?? ($this->resetErrorMessage ?? null);
    $resetSuccessResult = $resetSuccessResult ?? ($this->resetSuccessResult ?? null);
    $resetMethod = $resetMethod ?? ($this->resetMethod ?? 'email');
@endphp

@if($showPasswordResetModal && $resetTargetUser)
    <div style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.82); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;" wire:key="admin-password-reset-modal">
        <div class="card" style="width: 100%; max-width: 620px; border-color: var(--primary); box-shadow: 0 20px 45px -10px rgba(0,0,0,0.7), 0 0 30px rgba(99,102,241,0.2); background: var(--bg-card-solid); border-radius: var(--radius-lg); overflow: hidden;">
            
            <!-- Modal Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding: 1.25rem 1.5rem; background: var(--bg-tertiary);">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="card-title" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary);">
                            Reset User Credentials & Password
                        </h3>
                        <p class="card-subtitle" style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--color-text-muted);">
                            Issue new credentials via Email, WhatsApp dispatch, or Screen handover.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" wire:click="closePasswordResetModal" style="padding: 0.35rem 0.65rem;">
                    ✕
                </button>
            </div>

            <!-- Target Account Overview Strip -->
            <div style="padding: 1rem 1.5rem; background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 1rem;">
                        {{ strtoupper(substr($resetTargetUser->display_name ?: $resetTargetUser->email, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: var(--color-text-primary);">
                            {{ $resetTargetUser->display_name }}
                        </div>
                        <div style="font-size: 0.82rem; color: var(--color-text-muted); display: flex; align-items: center; gap: 0.35rem;">
                            <span>{{ $resetTargetUser->email }}</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                    <span class="badge" style="background: rgba(99,102,241,0.15); color: var(--primary); font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">
                        {{ str_replace('-', ' ', $resetTargetUser->roles->first()?->name ?? 'User') }}
                    </span>

                    @php
                        $userPhone = $resetTargetUser->contact_phone;
                    @endphp
                    @if($userPhone)
                        <span class="badge badge-emerald" style="font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <span>📱 {{ $userPhone }}</span>
                        </span>
                    @else
                        <span class="badge badge-amber" style="font-size: 0.72rem; font-weight: 700;">
                            No Phone On Profile
                        </span>
                    @endif
                </div>
            </div>

            <!-- Error Banner -->
            @if($resetErrorMessage)
                <div style="margin: 1rem 1.5rem 0; padding: 0.75rem 1rem; border-radius: 8px; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <span>⚠️</span>
                    <span>{{ $resetErrorMessage }}</span>
                </div>
            @endif

            <!-- Modal Body: If Reset Executed Successfully -->
            @if($resetSuccessResult)
                <div style="padding: 1.5rem;">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <div style="width: 52px; height: 52px; border-radius: 50%; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.4); color: var(--success); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <h4 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary);">
                            Password Successfully Reset & Saved!
                        </h4>
                        <p style="margin: 0.35rem 0 0; font-size: 0.85rem; color: var(--color-text-muted);">
                            The new password has been hashed and activated in the database.
                        </p>
                    </div>

                    <!-- Password Display Box -->
                    <div style="background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem;">
                        <div style="font-size: 0.78rem; text-transform: uppercase; font-weight: 700; color: var(--color-text-muted); margin-bottom: 0.45rem;">
                            Temporary Generated Password
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; background: var(--bg-card); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);"
                             x-data="{ copied: false }">
                            <span style="font-family: 'JetBrains Mono', 'Courier New', monospace; font-size: 1.2rem; font-weight: 800; color: var(--primary); letter-spacing: 1px;">
                                {{ $resetSuccessResult['temporary_password'] }}
                            </span>
                            <button type="button" 
                                    class="btn btn-secondary btn-sm"
                                    @click="navigator.clipboard.writeText('{{ $resetSuccessResult['temporary_password'] }}'); copied = true; setTimeout(() => copied = false, 2500);"
                                    style="padding: 0.35rem 0.75rem; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <span x-show="!copied">📋 Copy Password</span>
                                <span x-show="copied" style="color: var(--success); font-weight: 800;">✓ Copied!</span>
                            </button>
                        </div>
                    </div>

                    <!-- Dispatch Delivery Feedback -->
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.15rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.85rem;">
                            <!-- Email Status -->
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="color: var(--color-text-muted);">Audit Notification Email:</span>
                                @if($resetSuccessResult['email_dispatched'])
                                    <span style="color: var(--success); font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        ✓ Sent to {{ $resetSuccessResult['target_email'] }}
                                    </span>
                                @else
                                    <span style="color: var(--warning); font-weight: 600;">
                                        ⚠️ Queued / Offline (SMTP not connected)
                                    </span>
                                @endif
                            </div>

                            <!-- WhatsApp Dispatch Option if available -->
                            @if(!empty($resetSuccessResult['whatsapp']))
                                <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem; margin-top: 0.25rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                        <span style="color: var(--color-text-muted);">Verified WhatsApp:</span>
                                        <span style="font-weight: 700; color: var(--color-text-primary);">
                                            {{ $resetSuccessResult['whatsapp']['raw_phone'] }}
                                        </span>
                                    </div>

                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;" x-data="{ textCopied: false }">
                                        <a href="{{ $resetSuccessResult['whatsapp']['url'] }}" 
                                           target="_blank" 
                                           class="btn"
                                           style="flex: 1; padding: 0.55rem 1rem; font-size: 0.85rem; font-weight: 700; background: #25D366; color: #ffffff; border-color: #25D366; display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem; text-decoration: none; border-radius: 8px;">
                                            <span>💬 Open in WhatsApp</span>
                                        </a>

                                        <button type="button" 
                                                class="btn btn-secondary"
                                                @click="navigator.clipboard.writeText({{ json_encode($resetSuccessResult['whatsapp']['message']) }}); textCopied = true; setTimeout(() => textCopied = false, 2500);"
                                                style="padding: 0.55rem 1rem; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                                            <span x-show="!textCopied">Copy Message</span>
                                            <span x-show="textCopied" style="color: var(--success);">✓ Copied!</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="button" class="btn btn-primary" wire:click="closePasswordResetModal" style="padding: 0.55rem 1.5rem; font-weight: 700;">
                            Done & Close
                        </button>
                    </div>
                </div>

            <!-- Modal Body: Configure & Execute Password Reset -->
            @else
                <div style="padding: 1.5rem;">
                    <!-- Channel / Method Selection -->
                    <div style="margin-bottom: 1.35rem;">
                        <label style="display: block; font-size: 0.82rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-secondary); margin-bottom: 0.5rem;">
                            1. Select Dispatch / Handover Method *
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                            <!-- Option 1: Email -->
                            <label style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 0.95rem 0.65rem; border-radius: 10px; cursor: pointer; border: 2px solid {{ $resetMethod === 'email' ? 'var(--primary)' : 'var(--border-color)' }}; background: {{ $resetMethod === 'email' ? 'rgba(99,102,241,0.08)' : 'var(--bg-tertiary)' }}; transition: all 0.2s ease;">
                                <input type="radio" wire:model.live="resetMethod" value="email" style="display: none;">
                                <div style="font-size: 1.35rem; margin-bottom: 0.3rem;">✉️</div>
                                <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Send via Email</div>
                                <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 0.2rem;">Direct credentials inbox delivery</div>
                            </label>

                            <!-- Option 2: WhatsApp -->
                            <label style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 0.95rem 0.65rem; border-radius: 10px; cursor: pointer; border: 2px solid {{ $resetMethod === 'whatsapp' ? '#25D366' : 'var(--border-color)' }}; background: {{ $resetMethod === 'whatsapp' ? 'rgba(37,211,102,0.08)' : 'var(--bg-tertiary)' }}; transition: all 0.2s ease; {{ !$userPhone ? 'opacity: 0.55; cursor: not-allowed;' : '' }}">
                                <input type="radio" wire:model.live="resetMethod" value="whatsapp" style="display: none;" {{ !$userPhone ? 'disabled' : '' }}>
                                <div style="font-size: 1.35rem; margin-bottom: 0.3rem;">💬</div>
                                <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Send via WhatsApp</div>
                                <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 0.2rem;">
                                    {{ $userPhone ? 'Verified number dispatch' : 'No phone registered' }}
                                </div>
                            </label>

                            <!-- Option 3: Screen Handover -->
                            <label style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 0.95rem 0.65rem; border-radius: 10px; cursor: pointer; border: 2px solid {{ $resetMethod === 'screen' ? 'var(--secondary)' : 'var(--border-color)' }}; background: {{ $resetMethod === 'screen' ? 'rgba(6,182,212,0.08)' : 'var(--bg-tertiary)' }}; transition: all 0.2s ease;">
                                <input type="radio" wire:model.live="resetMethod" value="screen" style="display: none;">
                                <div style="font-size: 1.35rem; margin-bottom: 0.3rem;">👁️</div>
                                <div style="font-weight: 800; font-size: 0.85rem; color: var(--color-text-primary);">Show on Screen</div>
                                <div style="font-size: 0.72rem; color: var(--color-text-muted); margin-top: 0.2rem;">Immediate reveal & copy</div>
                            </label>
                        </div>
                    </div>

                    <!-- Password Configuration -->
                    <div style="margin-bottom: 1.35rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                            <label style="font-size: 0.82rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-secondary); margin: 0;">
                                2. New Temporary Password *
                            </label>
                            <button type="button" 
                                    class="btn btn-secondary btn-sm" 
                                    wire:click="regenerateResetPassword"
                                    style="padding: 0.2rem 0.55rem; font-size: 0.74rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <span>↻ Re-generate</span>
                            </button>
                        </div>

                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="text" 
                                   class="form-control" 
                                   wire:model="resetPlainPassword" 
                                   style="font-family: 'JetBrains Mono', 'Courier New', monospace; font-size: 1.05rem; font-weight: 800; color: var(--primary); letter-spacing: 0.5px;"
                                   placeholder="e.g. Hirely#123456">
                        </div>
                        <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.35rem;">
                            You may keep the auto-generated password or type a custom alphanumeric string (min 6 characters).
                        </div>
                    </div>

                    <!-- Security Alert Policy Checkbox -->
                    <div style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.2); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: flex-start; gap: 0.65rem; cursor: pointer; margin: 0;">
                            <input type="checkbox" wire:model="resetAlwaysSendEmail" style="width: 1.15rem; height: 1.15rem; margin-top: 0.15rem; accent-color: var(--primary);">
                            <div>
                                <span style="font-weight: 700; font-size: 0.84rem; color: var(--color-text-primary);">
                                    Send security audit notification email to {{ $resetTargetUser->email }}
                                </span>
                                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.15rem;">
                                    Informs the account holder that an administrative password reset was executed by the Registrar's Office.
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                        <button type="button" class="btn btn-secondary" wire:click="closePasswordResetModal" style="padding: 0.55rem 1.25rem; font-weight: 700;">
                            Cancel
                        </button>
                        <button type="button" 
                                class="btn btn-primary" 
                                wire:click="executePasswordReset"
                                wire:loading.attr="disabled"
                                style="padding: 0.55rem 1.65rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 14px rgba(99,102,241,0.4);">
                            <span wire:loading.remove wire:target="executePasswordReset">Confirm & Reset Password</span>
                            <span wire:loading wire:target="executePasswordReset">Processing Reset...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
