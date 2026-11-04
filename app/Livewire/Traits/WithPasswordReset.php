<?php

namespace App\Livewire\Traits;

use App\Models\User;
use App\Services\PasswordResetManager;
use Illuminate\Support\Facades\Auth;

trait WithPasswordReset
{
    // Password Reset Modal State
    public $showPasswordResetModal = false;
    public $resetTargetUserId = null;
    public $resetTargetUser = null;
    public $resetPlainPassword = '';
    public $resetMethod = 'email'; // 'email', 'whatsapp', 'screen'
    public $resetAlwaysSendEmail = true;
    public $resetCustomPassword = '';
    public $resetSuccessResult = null;
    public $resetErrorMessage = null;

    /**
     * Open the administrative password reset modal for a candidate, expert, or user account.
     */
    public function openPasswordResetModal($userId)
    {
        $admin = Auth::user();
        $targetUser = User::with(['roles', 'basicInfo', 'reviewerProfile'])->findOrFail($userId);

        if ($targetUser->hasRole('super-admin') && !$admin->hasRole('super-admin')) {
            $this->resetErrorMessage = 'Security Policy: Only Super Administrators can reset Super Administrator credentials.';
            $this->showPasswordResetModal = true;
            $this->resetTargetUserId = $targetUser->id;
            $this->resetTargetUser = $targetUser;
            return;
        }

        if ($admin->id === $targetUser->id) {
            $this->resetErrorMessage = 'You cannot reset your own password using the administrative override tool. Please use standard profile settings.';
            $this->showPasswordResetModal = true;
            $this->resetTargetUserId = $targetUser->id;
            $this->resetTargetUser = $targetUser;
            return;
        }

        if (!PasswordResetManager::canResetPassword($admin, $targetUser)) {
            abort(403, 'Unauthorized: You do not possess clearance to reset credentials for this account.');
        }

        $this->resetTargetUserId = $targetUser->id;
        $this->resetTargetUser = $targetUser;
        $this->resetPlainPassword = PasswordResetManager::generateTemporaryPassword();
        $this->resetCustomPassword = '';
        $this->resetMethod = 'email';
        $this->resetAlwaysSendEmail = true;
        $this->resetSuccessResult = null;
        $this->resetErrorMessage = null;
        $this->showPasswordResetModal = true;
    }

    /**
     * Close the password reset modal and clear transient states.
     */
    public function closePasswordResetModal()
    {
        $this->showPasswordResetModal = false;
        $this->resetTargetUserId = null;
        $this->resetTargetUser = null;
        $this->resetPlainPassword = '';
        $this->resetCustomPassword = '';
        $this->resetSuccessResult = null;
        $this->resetErrorMessage = null;
    }

    /**
     * Generate a fresh new random temporary password.
     */
    public function regenerateResetPassword()
    {
        $this->resetPlainPassword = PasswordResetManager::generateTemporaryPassword();
        $this->resetCustomPassword = '';
    }

    /**
     * Execute the password reset via the chosen delivery method (Email, WhatsApp, or Screen).
     */
    public function executePasswordReset()
    {
        $admin = Auth::user();
        if (!$this->resetTargetUserId) {
            return;
        }

        $targetUser = User::with(['roles', 'basicInfo', 'reviewerProfile'])->findOrFail($this->resetTargetUserId);

        $passwordToSet = !empty(trim($this->resetCustomPassword)) 
            ? trim($this->resetCustomPassword) 
            : $this->resetPlainPassword;

        if (strlen($passwordToSet) < 6) {
            $this->resetErrorMessage = 'The password must be at least 6 characters in length.';
            return;
        }

        try {
            $result = PasswordResetManager::executeReset(
                $targetUser,
                $admin,
                $passwordToSet,
                $this->resetMethod,
                $this->resetAlwaysSendEmail,
                request()->ip()
            );

            $this->resetSuccessResult = $result;
            $this->resetErrorMessage = null;

            session()->flash('message', "Password successfully reset for {$targetUser->display_name} ({$targetUser->email}).");

        } catch (\Throwable $e) {
            $this->resetErrorMessage = $e->getMessage();
        }
    }
}
