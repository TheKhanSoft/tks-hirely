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
