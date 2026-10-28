<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hirely University Recruitment</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="container auth-wrapper">
        <div class="card auth-card">
            <div class="text-center mb-6">
                <h2 class="card-title" style="font-size: 1.75rem; color: var(--secondary);">Sign In</h2>
                <p class="card-subtitle">Access the Unified Recruitment Platform</p>
            </div>

            <form action="/login" method="POST" class="mb-4">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" required placeholder="e.g. registrar@university.edu" value="{{ old('email') }}">
                    @error('email') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required value="password">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Sign In</button>
            </form>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                <h4 style="font-size: 0.9rem; text-transform: uppercase; color: var(--secondary);" class="mb-4">🔑 Seeded Sandbox Accounts:</h4>
                <div style="font-size: 0.85rem; max-height: 200px; overflow-y: auto;" class="flex-col gap-2">
                    <p style="color: var(--color-text-secondary);">All passwords: <strong>password</strong></p>
                    <div style="padding: 4px; background: rgba(255,255,255,0.02); border-radius: 4px;">
                        <strong>Registrar:</strong> <span style="color: var(--color-text-primary);">registrar@university.edu</span>
                    </div>
                    <div style="padding: 4px; background: rgba(255,255,255,0.02); border-radius: 4px;">
                        <strong>Applicant 1:</strong> <span style="color: var(--color-text-primary);">applicant1@gmail.com</span>
                    </div>
                    <div style="padding: 4px; background: rgba(255,255,255,0.02); border-radius: 4px;">
                        <strong>Scrutiny Officer:</strong> <span style="color: var(--color-text-primary);">scrutiny1@university.edu</span>
                    </div>
                    <div style="padding: 4px; background: rgba(255,255,255,0.02); border-radius: 4px;">
                        <strong>Appellate Panel:</strong> <span style="color: var(--color-text-primary);">appellate1@university.edu</span>
                    </div>
                    <div style="padding: 4px; background: rgba(255,255,255,0.02); border-radius: 4px;">
                        <strong>Board Member 1:</strong> <span style="color: var(--color-text-primary);">board1@university.edu</span>
                    </div>
                    <div style="padding: 4px; background: rgba(255,255,255,0.02); border-radius: 4px;">
                        <strong>Super Admin:</strong> <span style="color: var(--color-text-primary);">superadmin@university.edu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
