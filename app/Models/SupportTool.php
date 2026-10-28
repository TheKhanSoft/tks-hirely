<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTool extends Model
{
    protected $table = 'support_tools';

    protected $fillable = [
        'basic_info_id',
        'tool_name',
        'proficiency_level',
        'experience_years',
        'fitness_proof_path',
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
