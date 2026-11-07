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

    /**
     * Standard formatted tracking identifier.
     */
    public function getTrackingNumberAttribute(): string
    {
        if (!empty($this->attributes['tracking_number'])) {
            return $this->attributes['tracking_number'];
        }
        return 'APP-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function setTrackingNumberAttribute($value): void
    {
        $this->attributes['tracking_number'] = $value;
    }

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

    /**
     * Evaluate peer review outcomes against statutory campaign criteria.
     * Checks if minimum required positive recommendations (e.g. 2 out of 3)
     * and score benchmarks are met.
     */
    public function evaluatePeerReviewVerdict(): array
    {
        $campaign = $this->campaign;
        $passingRule = $campaign ? $campaign->getReviewPassingRule() : [
            'total_required' => 3,
            'min_positive' => 2,
            'min_score_percent' => 60,
            'allow_appeal_on_fail' => true,
        ];

        $reviews = $this->reviews()->where('status', 'SUBMITTED')->get();
        $totalSubmitted = $reviews->count();
        $requiredCount = (int)($passingRule['total_required'] ?? 3);

        $positiveRecommendations = ['RECOMMENDED', 'STRONGLY_RECOMMENDED', 'POSITIVE', 'APPROVE'];
        $positiveCount = 0;
        $totalScore = 0;

        foreach ($reviews as $rev) {
            $rec = strtoupper(trim($rev->recommendation ?? ''));
            $score = (float)($rev->overall_score ?? 0);
            $totalScore += $score;

            if (in_array($rec, $positiveRecommendations) || $score >= $passingRule['min_score_percent']) {
                $positiveCount++;
            }
        }

        $avgScore = $totalSubmitted > 0 ? round($totalScore / $totalSubmitted, 1) : 0;
        $minPositiveNeeded = (int)($passingRule['min_positive'] ?? 2);

        $isPassed = ($positiveCount >= $minPositiveNeeded);
        $isComplete = $totalSubmitted >= $requiredCount;

        return [
            'passed' => $isPassed,
            'is_complete' => $isComplete,
            'total_submitted' => $totalSubmitted,
            'required_count' => $requiredCount,
            'positive_count' => $positiveCount,
            'min_positive_needed' => $minPositiveNeeded,
            'average_score' => $avgScore,
            'threshold_score' => $passingRule['min_score_percent'],
            'summary' => "{$positiveCount}/{$totalSubmitted} positive referee recommendations (Avg: {$avgScore}%). Required: at least {$minPositiveNeeded}/{$requiredCount} positive.",
        ];
    }

    /**
     * Get candidate visual application journey milestones (Pizza-Tracker UX).
     */
    public function getJourneyMilestonesAttribute(): array
    {
        $status = strtoupper($this->status ?? 'DRAFT');
        $ledger = $this->billingLedger;
        $consumerId = $ledger?->consumer_id ?? 'NOT-GENERATED';
        $reason = $this->disqualification_reason ?: 'Criteria deficit identified during statutory scrutiny';

        $steps = [];

        // 1. Dossier Submission
        $submitted = ($status !== 'DRAFT');
        $steps[] = [
            'index' => 1,
            'key' => 'submission',
            'title' => 'Dossier Submitted',
            'subtitle' => 'Bio-Data & Credentials Logged',
            'state' => $submitted ? 'completed' : 'active',
            'timestamp' => $this->submission_timestamp?->format('d M Y, h:i A') ?: ($submitted ? 'Submitted' : 'In Draft'),
            'description' => 'Candidate personal bio-data, academic degrees, medals and quota registration logged in recruitment repository.',
            'badge' => $submitted ? 'COMPLETED' : 'IN DRAFT',
            'color' => $submitted ? 'emerald' : 'indigo',
        ];

        // 2. 1Link Fee Voucher Settlement
        $feePaid = in_array($status, ['PAID', 'ELIGIBLE', 'INELIGIBLE', 'APPEALED', 'UNDER_REVIEW', 'APPROVED', 'REJECTED']);
        $feePending = ($status === 'SUBMITTED');
        $steps[] = [
            'index' => 2,
            'key' => 'fee',
            'title' => '1Link Fee Settled',
            'subtitle' => $feePaid ? 'Payment Reconciled' : '1Link Consumer ID Ready',
            'state' => $feePaid ? 'completed' : ($feePending ? 'active' : 'pending'),
            'timestamp' => $feePaid ? ($ledger?->settled_at?->format('d M Y, h:i A') ?: '1Link Verified') : ($feePending ? 'Pending Payment' : 'Voucher Not Generated'),
            'description' => $feePaid
                ? "Fee voucher settled via 1Link Switch (Consumer ID: {$consumerId})."
                : ($feePending ? "Awaiting online payment or bank branch deposit for Consumer ID: {$consumerId}." : 'Fee voucher will be generated upon dossier submission.'),
            'badge' => $feePaid ? 'SETTLED' : ($feePending ? 'ACTION REQUIRED' : 'UPCOMING'),
            'color' => $feePaid ? 'emerald' : ($feePending ? 'amber' : 'slate'),
        ];

        // 3. Statutory Scrutiny Vetting
        $scrutinyDone = in_array($status, ['ELIGIBLE', 'INELIGIBLE', 'APPEALED', 'APPROVED']);
        $scrutinyActive = in_array($status, ['PAID', 'UNDER_REVIEW']);
        $steps[] = [
            'index' => 3,
            'key' => 'scrutiny',
            'title' => 'Scrutiny Vetting',
            'subtitle' => $scrutinyDone ? 'Vetting Concluded' : ($scrutinyActive ? 'Committee Review Active' : 'Queued for Scrutiny'),
            'state' => $scrutinyDone ? 'completed' : ($scrutinyActive ? 'active' : 'pending'),
            'timestamp' => $scrutinyDone ? 'Vetting Concluded' : ($scrutinyActive ? 'In Active Review' : 'Pending Fee Settlement'),
            'description' => 'Statutory scrutiny committee audits academic degrees, divisions, HEC HJRS journal category compliance and experience equivalence.',
            'badge' => $scrutinyDone ? 'SCRUTINIZED' : ($scrutinyActive ? 'IN SCRUTINY' : 'UPCOMING'),
            'color' => $scrutinyDone ? 'emerald' : ($scrutinyActive ? 'indigo' : 'slate'),
        ];

        // 4. Academic Eligibility & Shortlist
        $isEligible = in_array($status, ['ELIGIBLE', 'APPROVED']);
        $isDisqualified = ($status === 'INELIGIBLE');
        $isAppealed = ($status === 'APPEALED');
        $steps[] = [
            'index' => 4,
            'key' => 'eligibility',
            'title' => 'Eligibility & Shortlist',
            'subtitle' => $isEligible ? 'Declared Eligible' : ($isDisqualified ? 'Disqualified (Appealable)' : ($isAppealed ? 'Appeal In Review' : 'Awaiting Verdict')),
            'state' => $isEligible ? 'completed' : ($isDisqualified ? 'alert' : ($isAppealed ? 'active' : 'pending')),
            'timestamp' => $isEligible ? 'Declared Eligible' : ($isDisqualified ? 'Disqualified' : ($isAppealed ? 'Appeal Queued' : 'Pending')),
            'description' => $isEligible
                ? 'Candidate cleared statutory eligibility benchmarks and shortlisted for Selection Board interview.'
                : ($isDisqualified
                    ? "Deficiency flagged: {$reason}. Candidate may lodge a formal grievance appeal."
                    : ($isAppealed ? 'Formal grievance appeal submitted and under committee adjudication.' : 'Provisional eligibility determination based on scrutiny audit.')),
            'badge' => $isEligible ? 'SHORTLISTED' : ($isDisqualified ? 'DEFICIENT / APPEALABLE' : ($isAppealed ? 'APPEAL PENDING' : 'UPCOMING')),
            'color' => $isEligible ? 'emerald' : ($isDisqualified ? 'red' : ($isAppealed ? 'amber' : 'slate')),
        ];

        // 5. Selection Board Interview
        $boardDone = ($status === 'APPROVED');
        $boardActive = ($status === 'ELIGIBLE');
        $steps[] = [
            'index' => 5,
            'key' => 'board',
            'title' => 'Selection Board',
            'subtitle' => $boardDone ? 'Grading Finalized' : ($boardActive ? 'Interview Stage' : 'Pending Clearance'),
            'state' => $boardDone ? 'completed' : ($boardActive ? 'active' : ($status === 'REJECTED' ? 'alert' : 'pending')),
            'timestamp' => $boardDone ? 'Grading Finalized' : ($boardActive ? 'Call Letter Active' : 'Upcoming'),
            'description' => $boardDone
                ? 'Selection Board live evaluation and composite merit scoring successfully concluded.'
                : ($boardActive ? 'Candidate invited for formal interview assessment and technical grading.' : 'Conducted following publication of official scrutiny merit lists.'),
            'badge' => $boardDone ? 'EVALUATED' : ($boardActive ? 'ACTIVE INTERVIEW' : 'UPCOMING'),
            'color' => $boardDone ? 'emerald' : ($boardActive ? 'purple' : 'slate'),
        ];

        // 6. Syndicate Concurrence & Appointment
        $syndicateDone = ($status === 'APPROVED');
        $steps[] = [
            'index' => 6,
            'key' => 'syndicate',
            'title' => 'Syndicate Concurrence',
            'subtitle' => $syndicateDone ? 'Appointment Approved' : 'Statutory Ratification',
            'state' => $syndicateDone ? 'completed' : 'pending',
            'timestamp' => $syndicateDone ? 'Approved by Syndicate' : 'Pending Final Board',
            'description' => $syndicateDone
                ? 'Apex University Syndicate has concurred and confirmed the appointment offer.'
                : 'Final statutory sanction by the University Syndicate and issuance of formal offer letter.',
            'badge' => $syndicateDone ? 'APPOINTED' : 'UPCOMING',
            'color' => $syndicateDone ? 'emerald' : 'slate',
        ];

        return $steps;
    }

    /**
     * Compute current journey progress percentage (0 - 100) and active step index (1 - 6).
     */
    public function getJourneyProgressAttribute(): array
    {
        $status = strtoupper($this->status ?? 'DRAFT');

        return match ($status) {
            'DRAFT' => ['step' => 1, 'percent' => 10, 'label' => 'Application in Draft', 'type' => 'info'],
            'SUBMITTED' => ['step' => 2, 'percent' => 25, 'label' => 'Pending 1Link Fee Payment', 'type' => 'warning'],
            'PAID', 'UNDER_REVIEW' => ['step' => 3, 'percent' => 50, 'label' => 'Under Statutory Scrutiny', 'type' => 'primary'],
            'APPEALED' => ['step' => 4, 'percent' => 60, 'label' => 'Grievance Appeal Under Review', 'type' => 'warning'],
            'INELIGIBLE' => ['step' => 4, 'percent' => 60, 'label' => 'Deficiency Flagged / Disqualified', 'type' => 'danger'],
            'ELIGIBLE' => ['step' => 5, 'percent' => 80, 'label' => 'Shortlisted for Selection Board', 'type' => 'secondary'],
            'APPROVED' => ['step' => 6, 'percent' => 100, 'label' => 'Syndicate Concurrence Granted', 'type' => 'success'],
            'REJECTED' => ['step' => 5, 'percent' => 80, 'label' => 'Selection Concluded', 'type' => 'danger'],
            default => ['step' => 1, 'percent' => 15, 'label' => 'In Process', 'type' => 'info'],
        };
    }
}
