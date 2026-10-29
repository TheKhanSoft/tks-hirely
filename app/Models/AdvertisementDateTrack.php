<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvertisementDateTrack extends Model
{
    protected $table = 'advertisement_date_tracks';

    protected $fillable = [
        'advertisement_id',
        'previous_valid_to',
        'extended_valid_to',
        'corrigendum_no',
        'reason',
        'extended_at',
        'extended_by',
        'attachment_path',
    ];

    protected $casts = [
        'previous_valid_to' => 'datetime',
        'extended_valid_to' => 'datetime',
        'extended_at' => 'datetime',
    ];

    /**
     * Parent advertisement.
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * User / Registrar / Super Admin who authorized the extension.
     */
    public function extendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'extended_by');
    }
}
