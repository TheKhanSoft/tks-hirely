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
        'assigned_at',
    ];

    protected $casts = [
        'is_appellate_panel' => 'boolean',
        'assigned_at' => 'datetime',
    ];

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
