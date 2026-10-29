<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use Auditable;

    protected $fillable = [
        'user_id',
        'campaign_id',
        'status',
        'submission_timestamp',
        'disqualification_reason',
        'appeal_letter',
        'appeal_proof_path',
    ];

    protected $casts = [
        'submission_timestamp' => 'datetime',
    ];

    /**
     * Get dynamic workflow status entity.
     */
    public function getWorkflowStatusAttribute(): ?WorkflowStatus
    {
        return WorkflowStatus::forScope('APPLICATION')->where('code', $this->status)->first();
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $ws = $this->workflow_status;
        if ($ws) {
            return $ws->label;
        }

        return match ($this->status) {
            'DRAFT' => 'Draft',
            'SUBMITTED' => 'Submitted',
            'PAID' => 'Paid / Settled',
            'ELIGIBLE' => 'Eligible',
            'INELIGIBLE' => 'Ineligible / Disqualified',
            'DISQUALIFIED' => 'Disqualified',
            'UNDER_REVIEW' => 'Under External Review',
            'APPROVED' => 'Approved / Selected',
            'REJECTED' => 'Rejected',
            'APPEALED' => 'Appeal Lodged',
            'INCOMPLETE_DOSSIER' => 'Incomplete Dossier',
            default => str_replace('_', ' ', $this->status ?? 'Unknown'),
        };
    }

    /**
     * Get badge visual styling array.
     */
    public function getStatusBadgeStyleAttribute(): array
    {
        $ws = $this->workflow_status;
        if ($ws) {
            return $ws->getBadgeStyle();
        }

        return match ($this->status) {
            'APPROVED', 'ELIGIBLE', 'PAID' => ['bg' => 'rgba(16, 185, 129, 0.15)', 'text' => '#10b981', 'border' => 'rgba(16, 185, 129, 0.3)'],
            'UNDER_REVIEW' => ['bg' => 'rgba(139, 92, 246, 0.15)', 'text' => '#8b5cf6', 'border' => 'rgba(139, 92, 246, 0.3)'],
            'INCOMPLETE_DOSSIER', 'APPEALED' => ['bg' => 'rgba(245, 158, 11, 0.15)', 'text' => '#f59e0b', 'border' => 'rgba(245, 158, 11, 0.3)'],
            'REJECTED', 'INELIGIBLE', 'DISQUALIFIED' => ['bg' => 'rgba(239, 68, 68, 0.15)', 'text' => '#ef4444', 'border' => 'rgba(239, 68, 68, 0.3)'],
            'SUBMITTED' => ['bg' => 'rgba(99, 102, 241, 0.15)', 'text' => '#6366f1', 'border' => 'rgba(99, 102, 241, 0.3)'],
            default => ['bg' => 'rgba(148, 163, 184, 0.15)', 'text' => '#94a3b8', 'border' => 'rgba(148, 163, 184, 0.3)'],
        };
    }

    /**
     * Get list of allowed next status transitions based on dynamic lifecycle rules.
     */
    public function getAllowedNextTransitions(): array
    {
        $ws = $this->workflow_status;
        if ($ws && !empty($ws->allowed_transitions)) {
            return WorkflowStatus::forScope('APPLICATION')
                ->whereIn('code', $ws->allowed_transitions)
                ->where('is_active', true)
                ->orderBy('order')
                ->get()
                ->toArray();
        }

        // Fallback default transitions if not configured
        return WorkflowStatus::forScope('APPLICATION')
            ->where('code', '!=', $this->status)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    /**
     * Applicant user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Job campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Associated billing voucher record.
     */
    public function billingLedger(): HasOne
    {
        return $this->hasOne(BillingLedger::class);
    }

    /**
     * Committee evaluations for this application.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(LiveEvaluation::class);
    }

    /**
     * External academic peer reviews for this application.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ApplicationReview::class);
    }
}
