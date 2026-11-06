<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">COMMUNICATIONS</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">HELPDESK &amp; ASSISTANCE</span>
                <span class="badge badge-emerald" style="font-size: 0.72rem; font-weight: 700;">PUBLIC CHANNELS</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Contact Options, Helpdesk Channels &amp; Operating Hours
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure official candidate helpline telephone numbers, WhatsApp support lines, physical facilitation desks, and office operating hours.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetContactToDefault" 
                    wire:confirm="Reset all contact channels and helpdesk information to defaults?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 0.95rem; font-weight: 700; font-size: 0.82rem;"
                    title="Restore contact channels to standard defaults">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                <span>Reset Contact Channels</span>
            </button>

            <button type="button" 
                    wire:click="saveContactSettings" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; font-weight: 800; font-size: 0.88rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span wire:loading.remove wire:target="saveContactSettings">Save Contact Settings</span>
                <span wire:loading wire:target="saveContactSettings">Saving...</span>
            </button>
            @endcan
        </div>
    </div>

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Facilitation Channels for Applicants">
        These contact channels are displayed on candidate payment voucher receipts, application print dossiers, and helpdesk dialogue modals.
    </x-help-hint>

    {{-- LIVE HELPDESK PREVIEW CARD --}}
    <div style="background: var(--bg-surface); border: 1.5px dashed var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin-top: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--secondary); letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.4rem;">
                <span>📱 Live Candidate Facilitation Card Preview</span>
            </div>
            <span class="badge badge-emerald" style="font-size: 0.7rem; font-weight: 800;">Active On Vouchers &amp; Login Page</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.75rem;">
            <div style="background: var(--bg-card); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">✉️ Email Helpdesk</div>
                <div style="font-size: 0.88rem; font-weight: 800; color: var(--primary); margin-top: 0.2rem; word-break: break-all;">
                    {{ $support_email ?: 'support@hirely.edu.pk' }}
                </div>
            </div>

            <div style="background: var(--bg-card); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">📞 Landline PBX</div>
                <div style="font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ $support_phone ?: '+92-51-9250000' }}
                </div>
            </div>

            <div style="background: var(--bg-card); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">💬 WhatsApp Helpline</div>
                <div style="font-size: 0.88rem; font-weight: 800; color: var(--success); margin-top: 0.2rem;">
                    {{ $support_whatsapp ?: '+92-300-1234567' }}
                </div>
            </div>

            <div style="background: var(--bg-card); padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">⏰ Operating Hours</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: var(--color-text-primary); margin-top: 0.2rem;">
                    {{ $support_hours ?: 'Mon - Fri (08:30 AM - 04:30 PM)' }}
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveContactSettings" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- ========================================================================= -->
            <!-- 1. DIRECT TELEPHONE & DIGITAL HELPDESK                                    -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📞 Direct Helplines &amp; Electronic Support</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <!-- Support Email -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Support &amp; Inquiries Email</span>
                            <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="email" 
                               wire:model.live="support_email" 
                               class="form-control" 
                               placeholder="e.g. support@hirely.edu.pk" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('support_email') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Official inbox for applicant questions and fee reconciliation.</small>
                    </div>

                    <!-- Support Phone -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Helpdesk Landline / PBX Number</span>
                        </label>
                        <input type="text" 
                               wire:model.live="support_phone" 
                               class="form-control" 
                               placeholder="e.g. +92-51-9250000 Ext 204" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('support_phone') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Primary phone number printed on bank deposit vouchers.</small>
                    </div>

                    <!-- WhatsApp Helpline -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>WhatsApp Candidate Helpline</span>
                        </label>
                        <input type="text" 
                               wire:model.live="support_whatsapp" 
                               class="form-control" 
                               placeholder="e.g. +92-300-1234567" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('support_whatsapp') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Instant messaging number for candidate assistance.</small>
                    </div>

                    <!-- Registrar Hotline -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Registrar Secretariat Hotline</span>
                        </label>
                        <input type="text" 
                               wire:model.live="registrar_hotline" 
                               class="form-control" 
                               placeholder="e.g. +92-51-9250001" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('registrar_hotline') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Direct contact for urgent administrative coordination.</small>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. PHYSICAL LOCATION & OPERATING TIMING                                   -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📍 Facilitation Desk, Campus Address &amp; Operating Hours</span>
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <!-- Operating Hours -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Support Service Hours</span>
                        </label>
                        <input type="text" 
                               wire:model.live="support_hours" 
                               class="form-control" 
                               placeholder="e.g. Monday to Friday: 08:30 AM – 04:30 PM PKT" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('support_hours') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Timings when officers and telephone switchboards are active.</small>
                    </div>

                    <!-- Official Website -->
                    <div>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                            <span>Official Institution Website URL</span>
                        </label>
                        <input type="url" 
                               wire:model.live="org_website" 
                               class="form-control" 
                               placeholder="e.g. https://hirely.edu.pk" 
                               style="font-size: 0.92rem; padding: 0.65rem 1rem;">
                        @error('org_website') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                        <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Public web address linked in candidate footers and dossiers.</small>
                    </div>
                </div>

                <!-- Physical Facilitation Desk Location -->
                <div style="margin-top: 1.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.88rem; font-weight: 800; color: var(--color-text-primary); margin-bottom: 0.35rem;">
                        <span>Physical Helpdesk &amp; Facilitation Center Location</span>
                    </label>
                    <textarea wire:model.live="helpdesk_location" 
                              rows="2" 
                              class="form-control" 
                              placeholder="e.g. Recruitment &amp; Facilitation Center, Academic Block B, Ground Floor, Main University Campus" 
                              style="font-size: 0.90rem; padding: 0.65rem 1rem;"></textarea>
                    @error('helpdesk_location') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                    <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Detailed on-campus directions for applicants visiting in person.</small>
                </div>
            </div>

        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            @can('manage-settings')
            <button type="button" 
                    wire:click="resetContactToDefault" 
                    wire:confirm="Reset all contact channels and helpdesk information to defaults?"
                    class="btn btn-secondary btn-sm" 
                    style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
                <span>Reset Contact Channels</span>
            </button>

            <button type="submit" 
                    class="btn btn-primary" 
                    style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.75rem; font-weight: 800; font-size: 0.92rem; box-shadow: 0 4px 14px var(--primary-glow);">
                <span wire:loading.remove wire:target="saveContactSettings">Save Contact Settings</span>
                <span wire:loading wire:target="saveContactSettings">Saving Contact Settings...</span>
            </button>
            @endcan
        </div>
    </form>
</div>
