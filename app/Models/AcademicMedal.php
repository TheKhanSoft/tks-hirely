<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicMedal extends Model
{
    use Auditable;

    protected $table = 'academic_medals';

    protected $fillable = [
        'basic_info_id',
        'degree_title',
        'medal_type',
        'year_awarded',
        'transcription_proof_path',
        'is_verified',
        'verified_by',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function basicInfo(): BelongsTo
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * User who verified this medal.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
