<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SecurityAuditLog;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a target user. Strictly restricted to Super Admin.
     */
    public function start(Request $request, $id)
    {
        // 1. Strict Authorization: Only actual Super Admin or users with impersonate-user permission can initiate impersonation
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->hasRole('super-admin') && !$currentUser->can('impersonate-user'))) {
            abort(403, 'Impersonation is strictly restricted to Super Administrators or authorized personnel.');
        }

        // 2. Locate target user
        $targetUser = User::findOrFail($id);

        // 3. Self-impersonation guard
        if ($targetUser->id === $currentUser->id) {
            return back()->withErrors(['impersonate' => 'You cannot impersonate your own account.']);
        }

        // 4. Super Admin target guard
        if ($targetUser->hasRole('super-admin')) {
            return back()->withErrors(['impersonate' => 'Cannot impersonate another Super Administrator account.']);
        }

        // 5. Save original super admin state in session
        session()->put('impersonated_by', $currentUser->id);
        session()->put('impersonator_name', $currentUser->name ?: $currentUser->email);
        session()->put('impersonator_email', $currentUser->email);

        // 6. Record Security Audit Log
        SecurityAuditLog::create([
            'user_id' => $currentUser->id,
            'ip_address' => $request->ip() ?: '127.0.0.1',
            'action_type' => 'IMPERSONATE_START',
            'table_name' => 'users',
            'record_id' => $targetUser->id,
            'state_before' => [
                'admin_id' => $currentUser->id,
                'admin_email' => $currentUser->email,
                'admin_name' => $currentUser->name,
            ],
            'state_after' => [
                'target_id' => $targetUser->id,
                'target_email' => $targetUser->email,
                'target_name' => $targetUser->name,
                'target_role' => $targetUser->roles->pluck('name')->first() ?? 'applicant',
            ],
            'logged_at' => now(),
        ]);

        // 7. Log in as target user
        Auth::login($targetUser);

        session()->flash('message', "Now viewing workspace as {$targetUser->name} ({$targetUser->email}).");

        return redirect('/');
    }

    /**
     * Leave impersonation and restore original Super Admin session.
     */
    public function leave(Request $request)
    {
        if (!session()->has('impersonated_by')) {
            return redirect('/');
        }

        $adminId = session()->get('impersonated_by');
        $impersonatedUser = Auth::user();
        $admin = User::findOrFail($adminId);

        // Clear impersonation session flags
        session()->forget(['impersonated_by', 'impersonator_name', 'impersonator_email']);

        // Record Security Audit Log for session termination
        SecurityAuditLog::create([
            'user_id' => $admin->id,
            'ip_address' => $request->ip() ?: '127.0.0.1',
            'action_type' => 'IMPERSONATE_STOP',
            'table_name' => 'users',
            'record_id' => $impersonatedUser?->id,
            'state_before' => [
                'impersonated_id' => $impersonatedUser?->id,
                'impersonated_email' => $impersonatedUser?->email,
            ],
            'state_after' => [
                'restored_admin_id' => $admin->id,
                'restored_admin_email' => $admin->email,
            ],
            'logged_at' => now(),
        ]);

        // Log original super admin back in
        Auth::login($admin);

        return redirect('/admin/users')->with('message', 'Impersonation ended. Returned to Super Administrator session.');
    }
}

