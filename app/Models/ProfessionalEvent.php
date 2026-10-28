<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalEvent extends Model
{
    use Auditable;

    protected $table = 'professional_events';

    protected $fillable = [
        'basic_info_id',
        'event_name',
        'event_type',
        'event_role',
        'paper_title',
        'start_date',
        'end_date',
        'location',
        'organizer',
        'certificate_proof_path',
        'is_verified',
        'verified_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_verified' => 'boolean',
    ];

    public function basicInfo(): BelongsTo
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * User who verified this professional event.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
