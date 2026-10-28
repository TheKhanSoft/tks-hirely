<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignCommittee extends Model
{
    public $timestamps = false;

    protected $table = 'campaign_committees';

    protected $fillable = [
        'campaign_id',
        'user_id',
        'is_appellate_panel',
        'member_category',
        'role_designation',
        'affiliation',
        'country',
        'assigned_at',
    ];

    protected $casts = [
        'is_appellate_panel' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    /**
     * Get human-readable category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->member_category) {
            'INTERNAL' => 'Internal Organization / Staff',
            'EXTERNAL' => 'External Expert / Other Office',
            'INTERNATIONAL' => 'International / Overseas Specialist',
            'INDEPENDENT' => 'Independent Arbiter / Governance',
            default => 'Committee Member',
        };
    }

    /**
     * Parent job campaign.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Committee member user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
