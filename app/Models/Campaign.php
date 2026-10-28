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
        'scoring_rules',
    ];

    protected $casts = [
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
        'application_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'scoring_rules' => 'array',
    ];

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
