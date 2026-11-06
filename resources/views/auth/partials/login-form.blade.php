<!-- Sign In Form -->
<form action="/login" method="POST" id="loginForm" class="mb-4">
    @csrf
    <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label" for="emailInput" style="font-weight: 700; font-size: 0.85rem; color: var(--color-text-primary); margin-bottom: 0.4rem; display: block;">Work or Candidate Email</label>
        <div style="position: relative;">
            <input type="email" id="emailInput" class="form-control" name="email" required placeholder="e.g. registrar@university.edu" value="{{ old('email') }}" style="padding-left: 2.5rem; width: 100%;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <rect width="20" height="16" x="2" y="4" rx="2"/>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
        </div>
        @error('email') 
            <span style="color: var(--danger); font-size: 0.82rem; margin-top: 0.35rem; display: block; font-weight: 500;">{{ $message }}</span> 
        @enderror
    </div>

    <div class="form-group" style="margin-bottom: 1.25rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <label class="form-label" for="passwordInput" style="font-weight: 700; font-size: 0.85rem; color: var(--color-text-primary); margin-bottom: 0;">Password</label>
            <span style="font-size: 0.74rem; color: var(--color-text-muted);">Default demo: <code>password</code></span>
        </div>
        <div style="position: relative;">
            <input type="password" id="passwordInput" class="form-control" name="password" required value="password" style="padding-left: 2.5rem; padding-right: 2.5rem; width: 100%;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-text-muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); pointer-events: none;">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <button type="button" id="togglePasswordBtn" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 0.25rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 0.95rem; font-weight: 800; border-radius: var(--radius-sm); box-shadow: 0 4px 14px var(--primary-glow); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
        <span>Sign In to Console</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
        </svg>
    </button>
</form>

<!-- 1-Click Sandbox Fast Switch -->
<div style="border-top: 1px solid var(--border-color); padding-top: 1.15rem; margin-top: 1.25rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem; flex-wrap: wrap; gap: 0.25rem;">
        <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--secondary);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
            <span>1-Click Sandbox Fast Switch</span>
        </div>
        <span style="font-size: 0.7rem; color: var(--color-text-muted); font-weight: 600;">Click role to autofill</span>
    </div>

    <div class="quick-sandbox-grid" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.6rem;">
        <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('registrar@university.edu', 'Registrar')" title="Click to autofill: registrar@university.edu" style="border-left: 3px solid #6366f1;">
            <strong>🏛️ Registrar</strong>
            <span>registrar@university.edu</span>
        </button>
        <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('applicant1@gmail.com', 'Applicant')" title="Click to autofill: applicant1@gmail.com" style="border-left: 3px solid #8b5cf6;">
            <strong>👤 Candidate 1</strong>
            <span>applicant1@gmail.com</span>
        </button>
        <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('scrutiny1@university.edu', 'Scrutiny Officer')" title="Click to autofill: scrutiny1@university.edu" style="border-left: 3px solid #0ea5e9;">
            <strong>🔍 Scrutiny Officer</strong>
            <span>scrutiny1@university.edu</span>
        </button>
        <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('appellate1@university.edu', 'Appellate Panel')" title="Click to autofill: appellate1@university.edu" style="border-left: 3px solid #f59e0b;">
            <strong>⚖️ Appellate Panel</strong>
            <span>appellate1@university.edu</span>
        </button>
        <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('board1@university.edu', 'Selection Board')" title="Click to autofill: board1@university.edu" style="border-left: 3px solid #10b981;">
            <strong>⭐ Selection Board</strong>
            <span>board1@university.edu</span>
        </button>
        <button type="button" class="quick-sandbox-chip" onclick="selectDemoAccount('superadmin@university.edu', 'Super Admin')" title="Click to autofill: superadmin@university.edu" style="border-left: 3px solid #e11d48;">
            <strong>⚡ Super Admin</strong>
            <span>superadmin@university.edu</span>
        </button>
    </div>
</div>

<!-- Helpdesk & Candidate Facilitation Channel -->
@if(!empty($supportEmail) || !empty($supportPhone) || !empty($supportWhatsapp) || !empty($helpdeskLocation))
<div style="margin-top: 1.25rem; padding: 0.85rem 1rem; border-radius: var(--radius-sm); background: var(--bg-surface); border: 1px solid var(--border-color); font-size: 0.8rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.45rem; flex-wrap: wrap; gap: 0.35rem;">
        <div style="font-weight: 800; color: var(--color-text-primary); display: flex; align-items: center; gap: 0.4rem; font-size: 0.82rem;">
            <span>🎧</span>
            <span>Candidate Helpdesk &amp; Inquiries</span>
        </div>
        @if(!empty($supportHours))
            <span style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 600;">{{ $supportHours }}</span>
        @endif
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem 1rem; color: var(--color-text-secondary); margin-top: 0.4rem; font-size: 0.78rem;">
        @if(!empty($supportEmail))
            <a href="mailto:{{ $supportEmail }}" style="color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;">
                <span>📧</span> <span>{{ $supportEmail }}</span>
            </a>
        @endif
        @if(!empty($supportPhone))
            <a href="tel:{{ $supportPhone }}" style="color: var(--color-text-secondary); text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                <span>📞</span> <span>{{ $supportPhone }}</span>
            </a>
        @endif
        @if(!empty($supportWhatsapp))
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supportWhatsapp) }}" target="_blank" style="color: #10b981; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700;">
                <span>💬</span> <span>WhatsApp</span>
            </a>
        @endif
    </div>

    @if(!empty($helpdeskLocation))
        <div style="margin-top: 0.45rem; font-size: 0.72rem; color: var(--color-text-muted); display: flex; align-items: center; gap: 0.35rem; border-top: 1px dotted var(--border-color); padding-top: 0.35rem;">
            <span>📍</span>
            <span>{{ $helpdeskLocation }}</span>
        </div>
    @endif
</div>
@endif

<!-- Footer note -->
<div style="margin-top: 1.25rem; text-align: center;">
    <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 0.25rem;">
        {{ $orgName }} &bull; {{ $footerCopyright }}
    </p>
    @if(!empty($orgWebsite))
        <a href="{{ $orgWebsite }}" target="_blank" style="font-size: 0.72rem; color: var(--primary); text-decoration: none; font-weight: 600;">
            Visit Institutional Portal &rarr;
        </a>
    @endif
</div>

<script>
    function selectDemoAccount(email, roleName) {
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('passwordInput');
        if (emailInput && passwordInput) {
            emailInput.value = email;
            passwordInput.value = 'password';
            
            emailInput.style.borderColor = 'var(--secondary)';
            emailInput.style.boxShadow = '0 0 0 3px var(--secondary-glow)';
            setTimeout(() => {
                emailInput.style.borderColor = '';
                emailInput.style.boxShadow = '';
            }, 600);
        }
    }

    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passInput = document.getElementById('passwordInput');
    if (toggleBtn && passInput) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
        });
    }
</script>
