<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationReview extends Model
{
    protected $table = 'application_reviews';

    protected $fillable = [
        'application_id',
        'reviewer_id',
        'assigned_by',
        'assigned_at',
        'due_date',
        'confidential_notes',
        'status',
        'access_token',
        'token_expires_at',
        'recommendation',
        'overall_score',
        'report_text',
        'evaluation_file_path',
        'submitted_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'date',
        'token_expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'overall_score' => 'decimal:2',
    ];

    /**
     * Ensure a secure cryptographic access token exists for passwordless review.
     */
    public function ensureAccessToken(): string
    {
        if (empty($this->access_token) || ($this->token_expires_at && $this->token_expires_at->isPast())) {
            $days = (int) SystemSetting::get('referee_token_expiry_days', 14);
            $this->access_token = bin2hex(random_bytes(32));
            $this->token_expires_at = now()->addDays($days);
            $this->save();
        }

        return $this->access_token;
    }

    /**
     * Get direct passwordless evaluation link.
     */
    public function getDirectReviewUrl(): string
    {
        $token = $this->ensureAccessToken();
        return url('/referee/direct-review/' . $token);
    }

    /**
     * Check if a given token is valid and unexpired for this review.
     */
    public function isTokenValid(?string $token = null): bool
    {
        $tokenToCheck = $token ?: $this->access_token;

        if (empty($tokenToCheck) || empty($this->access_token)) {
            return false;
        }

        if (!hash_equals($this->access_token, $tokenToCheck)) {
            return false;
        }

        if ($this->token_expires_at && $this->token_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Parent application being evaluated.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Reviewer user account.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Registrar who assigned this review.
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
