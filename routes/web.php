<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\KuickpayWebhookController;
use App\Livewire\Applicant\ProfileWizard;
use App\Livewire\Applicant\Dashboard as ApplicantDashboard;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\CampaignCrud;
use App\Livewire\Admin\ApplicationVerification;
use App\Livewire\Admin\AppellateReview;
use App\Livewire\Admin\EntityManagement;
use App\Livewire\Admin\ReviewerManagement;
use App\Livewire\Admin\ApplicationManagement;
use App\Livewire\Admin\CandidateProfileManagement;
use App\Livewire\Admin\PaymentManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\RolePermissionManagement;
use App\Livewire\Admin\AuditLogManagement;
use App\Livewire\Admin\SystemSettings;
use App\Livewire\Reviewer\EvaluationDesk;
use App\Livewire\Board\LiveGradingPanel;
use App\Livewire\Board\Dashboard as BoardDashboard;
use App\Livewire\Scrutiny\Dashboard as ScrutinyDashboard;
use App\Livewire\Bank\PaymentSimulator;

// Role-Based Root Redirect Logic (Supports both full titles and concise aliases)
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return redirect('/admin/dashboard');
        }
        if ($user->hasRole('registrar')) {
            return redirect('/registrar/dashboard');
        }
        if ($user->hasAnyRole(['scrutiny-committee', 'scrutiny'])) {
            return redirect('/scrutiny/dashboard');
        }
        if ($user->hasAnyRole(['appellate-committee', 'appellate'])) {
            return redirect('/appellate/dashboard');
        }
        if ($user->hasAnyRole(['selection-board-member', 'board-member', 'board'])) {
            return redirect('/board/dashboard');
        }
        if ($user->hasAnyRole(['academic-reviewer', 'reviewer'])) {
            return redirect('/reviewer/dashboard');
        }
        return redirect('/applicant/dashboard');
    }
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Webhook route (CSRF excluded in bootstrap/app.php)
Route::post('/api/webhooks/kuickpay', [KuickpayWebhookController::class, 'handle']);

// Public Sandbox Bank Simulator
Route::get('/bank/simulator', PaymentSimulator::class);

// Protected role-based workflows
Route::middleware('auth')->group(function () {
    // -------------------------------------------------------------
    // Candidate Space
    // -------------------------------------------------------------
    Route::get('/applicant/dashboard', ApplicantDashboard::class);
    Route::get('/applicant/wizard', ProfileWizard::class);

    // -------------------------------------------------------------
    // Scrutiny Committee Workspace
    // -------------------------------------------------------------
    Route::get('/scrutiny/dashboard', ScrutinyDashboard::class);
    Route::get('/scrutiny/queue', ScrutinyDashboard::class);
    Route::get('/scrutiny/verification/{id}', ApplicationVerification::class);

    // -------------------------------------------------------------
    // Selection Board Workspace
    // -------------------------------------------------------------
    Route::get('/board/dashboard', BoardDashboard::class);
    Route::get('/board/evaluate/{id}', LiveGradingPanel::class);

    // -------------------------------------------------------------
    // Appellate Committee Workspace
    // -------------------------------------------------------------
    Route::get('/appellate/dashboard', AppellateReview::class);
    Route::get('/appellate/appeals', AppellateReview::class);

    // -------------------------------------------------------------
    // Academic Peer Review Workspace
    // -------------------------------------------------------------
    Route::get('/reviewer/dashboard', EvaluationDesk::class);

    // -------------------------------------------------------------
    // Registrar Workspace
    // -------------------------------------------------------------
    Route::get('/registrar/dashboard', AdminDashboard::class);
    Route::get('/registrar/campaigns', CampaignCrud::class);
    Route::get('/registrar/applications', ApplicationManagement::class);
    Route::get('/registrar/candidates', CandidateProfileManagement::class);
    Route::get('/registrar/candidate-profiles', function() { return redirect('/registrar/candidates'); });
    Route::get('/registrar/payments', PaymentManagement::class);
    Route::get('/registrar/vouchers', function() { return redirect('/registrar/payments'); });
    Route::get('/registrar/reviewers', ReviewerManagement::class);
    Route::get('/registrar/users', UserManagement::class);
    Route::get('/registrar/roles', RolePermissionManagement::class);
    Route::get('/registrar/permissions', function() { return redirect('/registrar/roles?tab=permissions'); });
    Route::get('/registrar/audit-logs', AuditLogManagement::class);
    Route::get('/registrar/settings', SystemSettings::class);

    // -------------------------------------------------------------
    // Super-Admin & Central Administrative Control (Legacy / Direct)
    // -------------------------------------------------------------
    Route::get('/admin/dashboard', AdminDashboard::class);
    Route::get('/admin/campaigns', CampaignCrud::class);
    Route::get('/admin/applications', ApplicationManagement::class);
    Route::get('/admin/candidates', CandidateProfileManagement::class);
    Route::get('/admin/candidate-profiles', function() { return redirect('/admin/candidates'); });
    Route::get('/admin/payments', PaymentManagement::class);
    Route::get('/admin/vouchers', function() { return redirect('/admin/payments'); });
    Route::get('/admin/reviewers', ReviewerManagement::class);

    // Administrative governance & access control
    Route::get('/admin/users', UserManagement::class);
    Route::get('/admin/roles', RolePermissionManagement::class);
    Route::get('/admin/permissions', function() { return redirect('/admin/roles?tab=permissions'); });
    Route::get('/admin/audit-logs', AuditLogManagement::class);
    Route::get('/admin/settings', SystemSettings::class);

    // Direct / admin compatibility aliases
    Route::get('/admin/verification/{id}', ApplicationVerification::class);
    Route::get('/admin/appeals', AppellateReview::class);

    // Legacy fallback redirect for /admin/management
    Route::get('/admin/management', function () {
        $tab = request('tab');
        return match ($tab) {
            'campaigns' => redirect('/admin/campaigns'),
            'applications' => redirect('/admin/applications'),
            'basic_infos' => redirect('/admin/candidates'),
            'payments' => redirect('/admin/payments'),
            'roles' => redirect('/admin/roles?tab=roles'),
            'permissions' => redirect('/admin/roles?tab=permissions'),
            'logs' => redirect('/admin/audit-logs'),
            default => redirect('/admin/users'),
        };
    });
});
