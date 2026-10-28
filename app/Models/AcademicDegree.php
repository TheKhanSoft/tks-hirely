<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicDegree extends Model
{
    protected $table = 'academic_degrees';

    protected $fillable = [
        'basic_info_id',
        'degree_level',
        'degree_title',
        'country',
        'institution',
        'year',
        'gpa',
        'scale',
        'major',
        'board',
        'division',
        'proof_path',
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

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
