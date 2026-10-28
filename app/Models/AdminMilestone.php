<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMilestone extends Model
{
    protected $table = 'admin_milestones';

    protected $fillable = [
        'basic_info_id',
        'title',
        'budget',
        'year',
        'description',
        'proof_path',
        'is_verified',
        'verified_by',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
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
