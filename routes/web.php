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
use App\Livewire\Reviewer\EvaluationDesk;
use App\Livewire\Board\LiveGradingPanel;
use App\Livewire\Bank\PaymentSimulator;

// Root redirect logic
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('super-admin') || $user->hasRole('registrar') || $user->hasRole('scrutiny-committee')) {
            return redirect('/admin/dashboard');
        }
        if ($user->hasRole('appellate-committee')) {
            return redirect('/admin/appeals');
        }
        if ($user->hasRole('selection-board-member')) {
            return redirect('/admin/dashboard');
        }
        if ($user->hasRole('academic-reviewer')) {
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

// Protected workflows
Route::middleware('auth')->group(function () {
    // Applicant wizard and dashboard
    Route::get('/applicant/dashboard', ApplicantDashboard::class);
    Route::get('/applicant/wizard', ProfileWizard::class);

    // Administrative dashboard & operations
    Route::get('/admin/dashboard', AdminDashboard::class);
    Route::get('/admin/campaigns', CampaignCrud::class);
    Route::get('/admin/reviewers', ReviewerManagement::class);
    Route::get('/admin/management', EntityManagement::class);
    Route::get('/admin/users', function() { return redirect('/admin/management'); });
    Route::get('/admin/verification/{id}', ApplicationVerification::class);
    Route::get('/admin/appeals', AppellateReview::class);

    // Live board candidate grading panel
    Route::get('/board/evaluate/{id}', LiveGradingPanel::class);

    // Academic external reviewer evaluation desk
    Route::get('/reviewer/dashboard', EvaluationDesk::class);
});
