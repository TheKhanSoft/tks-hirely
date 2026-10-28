<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use Auditable;

    protected $fillable = [
        'job_code',
        'title',
        'track_type',
        'department',
        'opening_date',
        'closing_date',
        'application_fee',
        'is_active',
        'require_transcript_proof',
        'scoring_rules',
    ];

    protected $casts = [
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
        'application_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'require_transcript_proof' => 'boolean',
        'scoring_rules' => 'array',
    ];

    /**
     * Determine if academic degrees require transcript proof.
     */
    public function requiresTranscriptProof(): bool
    {
        if (isset($this->require_transcript_proof)) {
            return (bool)$this->require_transcript_proof;
        }
        return (bool)($this->scoring_rules['require_transcript_proof'] ?? true);
    }

    /**
     * Committees assigned to this campaign.
     */
    public function committees(): HasMany
    {
        return $this->hasMany(CampaignCommittee::class);
    }

    /**
     * Applications submitted to this campaign.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
