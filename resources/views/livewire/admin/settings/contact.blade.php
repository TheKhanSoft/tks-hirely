<div class="card mb-6" style="padding: 1.75rem; border-color: var(--primary); box-shadow: var(--shadow-md);">
    <!-- Card Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                <span class="badge badge-indigo" style="font-size: 0.72rem; font-weight: 800;">COMMUNICATIONS</span>
                <span class="badge badge-slate" style="font-size: 0.72rem;">HELPDESK &amp; ASSISTANCE</span>
            </div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--color-text-primary);">
                Contact Options, Helpdesk Channels &amp; Operating Hours
            </h2>
            <p class="card-subtitle" style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: var(--color-text-secondary);">
                Configure official candidate helpline telephone numbers, WhatsApp support lines, physical facilitation desks, and office operating hours.
            </p>
        </div>

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
    </div>

    @if(session()->has('message'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- System Help Callout --}}
    <x-help-hint type="callout" variant="info" title="Facilitation Channels for Applicants">
        These contact channels are displayed on candidate payment voucher receipts, application print dossiers, and helpdesk dialogue modals.
    </x-help-hint>

    <form wire:submit.prevent="saveContactSettings" style="margin-top: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- ========================================================================= -->
            <!-- 1. DIRECT TELEPHONE & DIGITAL HELPDESK                                    -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
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
                               wire:model="support_email" 
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
                               wire:model="support_phone" 
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
                               wire:model="support_whatsapp" 
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
                               wire:model="registrar_hotline" 
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
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem;">
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
                               wire:model="support_hours" 
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
                               wire:model="org_website" 
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
                    <textarea wire:model="helpdesk_location" 
                              rows="2" 
                              class="form-control" 
                              placeholder="e.g. Recruitment &amp; Facilitation Center, Academic Block B, Ground Floor, Main University Campus" 
                              style="font-size: 0.90rem; padding: 0.65rem 1rem;"></textarea>
                    @error('helpdesk_location') <div style="color: var(--danger); font-size: 0.78rem; font-weight: 700; margin-top: 0.25rem;">{{ $message }}</div> @enderror
                    <small style="color: var(--color-text-secondary); font-size: 0.78rem; display: block; margin-top: 0.25rem;">Detailed on-campus directions for applicants visiting in person.</small>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- 3. LIVE HELPDESK PREVIEW CARD                                             -->
            <!-- ========================================================================= -->
            <div style="background: var(--bg-surface); border: 1.5px dashed var(--border-color); border-radius: 8px; padding: 1.5rem;">
                <h4 style="font-size: 0.85rem; font-weight: 800; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.85rem;">
                    Candidate Portal Helpdesk Preview
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; font-size: 0.85rem;">
                    <div style="background: var(--bg-card); padding: 0.85rem; border-radius: 6px; border: 1px solid var(--border-color);">
                        <strong style="color: var(--primary); display: block; margin-bottom: 0.25rem;">✉️ Email Support</strong>
                        <span>{{ $support_email ?: 'support@hirely.edu.pk' }}</span>
                    </div>
                    <div style="background: var(--bg-card); padding: 0.85rem; border-radius: 6px; border: 1px solid var(--border-color);">
                        <strong style="color: var(--primary); display: block; margin-bottom: 0.25rem;">📞 Telephone Line</strong>
                        <span>{{ $support_phone ?: '+92-51-9250000' }}</span>
                    </div>
                    <div style="background: var(--bg-card); padding: 0.85rem; border-radius: 6px; border: 1px solid var(--border-color);">
                        <strong style="color: var(--primary); display: block; margin-bottom: 0.25rem;">💬 WhatsApp Line</strong>
                        <span>{{ $support_whatsapp ?: '+92-300-1234567' }}</span>
                    </div>
                    <div style="background: var(--bg-card); padding: 0.85rem; border-radius: 6px; border: 1px solid var(--border-color);">
                        <strong style="color: var(--primary); display: block; margin-bottom: 0.25rem;">⏰ Service Hours</strong>
                        <span>{{ $support_hours ?: 'Mon - Fri (08:30 AM - 04:30 PM)' }}</span>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
