<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class WorkflowStatus extends Model
{
    protected $fillable = [
        'code',
        'label',
        'scope',
        'badge_color',
        'badge_bg',
        'description',
        'order',
        'is_locked_state',
        'is_terminal',
        'is_system_default',
        'allowed_transitions',
        'requirements',
        'is_active',
    ];

    protected $casts = [
        'allowed_transitions' => 'array',
        'requirements' => 'array',
        'order' => 'integer',
        'is_locked_state' => 'boolean',
        'is_terminal' => 'boolean',
        'is_system_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope query by lifecycle target scope.
     */
    public function scopeForScope(Builder $query, string $scope): Builder
    {
        return $query->where('scope', strtoupper($scope));
    }

    /**
     * Scope query to active statuses only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this status can transition to target status code.
     */
    public function canTransitionTo(string $targetCode): bool
    {
        if ($this->code === $targetCode) {
            return true;
        }

        $allowed = $this->allowed_transitions ?? [];
        return in_array($targetCode, $allowed, true);
    }

    /**
     * Determine badge styling properties.
     */
    public function getBadgeStyle(): array
    {
        $color = $this->badge_color ?: 'primary';
        return match ($color) {
            'success' => ['bg' => 'rgba(16, 185, 129, 0.15)', 'text' => '#10b981', 'border' => 'rgba(16, 185, 129, 0.3)'],
            'warning', 'amber' => ['bg' => 'rgba(245, 158, 11, 0.15)', 'text' => '#f59e0b', 'border' => 'rgba(245, 158, 11, 0.3)'],
            'danger' => ['bg' => 'rgba(239, 68, 68, 0.15)', 'text' => '#ef4444', 'border' => 'rgba(239, 68, 68, 0.3)'],
            'purple' => ['bg' => 'rgba(139, 92, 246, 0.15)', 'text' => '#8b5cf6', 'border' => 'rgba(139, 92, 246, 0.3)'],
            'info', 'cyan' => ['bg' => 'rgba(6, 182, 212, 0.15)', 'text' => '#06b6d4', 'border' => 'rgba(6, 182, 212, 0.3)'],
            'neutral', 'secondary' => ['bg' => 'rgba(148, 163, 184, 0.15)', 'text' => '#94a3b8', 'border' => 'rgba(148, 163, 184, 0.3)'],
            default => ['bg' => 'rgba(99, 102, 241, 0.15)', 'text' => '#6366f1', 'border' => 'rgba(99, 102, 241, 0.3)'],
        };
    }

    /**
     * Validate gate requirements when transitioning an application into this target status.
     */
    public static function validateApplicationTransition(Application $application, string $targetCode, ?string $reason = null): array
    {
        $targetStatus = static::forScope('APPLICATION')->where('code', $targetCode)->first();
        if (!$targetStatus) {
            return ['allowed' => true, 'errors' => []];
        }

        $errors = [];
        $reqs = $targetStatus->requirements ?? [];

        // 1. Fee Payment requirement
        if (!empty($reqs['require_fee_paid'])) {
            $ledger = $application->billingLedger;
            if (!$ledger || $ledger->status !== 'SETTLED') {
                $errors[] = "Transition requires settled billing voucher (challan/1Bill). Application voucher status is " . ($ledger?->status ?? 'NOT_GENERATED') . ".";
            }
        }

        // 2. Justification / deficiency reason requirement
        if (!empty($reqs['require_justification_reason'])) {
            if (empty(trim($reason ?? '')) && empty(trim($application->disqualification_reason ?? ''))) {
                $errors[] = "Transition to {$targetStatus->label} strictly requires a formal justification / deficiency note.";
            }
        }

        // 3. Minimum peer review requirement
        if (!empty($reqs['require_peer_reviews'])) {
            $requiredCount = $application->campaign?->getRequiredPeerReviewersCount() ?? 0;
            if ($requiredCount > 0) {
                $completedReviews = $application->reviews()->where('status', 'COMPLETED')->count();
                if ($completedReviews < $requiredCount) {
                    $errors[] = "Transition requires at least {$requiredCount} completed peer reviews (currently completed: {$completedReviews}).";
                }
            }
        }

        return [
            'allowed' => empty($errors),
            'errors' => $errors,
        ];
    }
}
