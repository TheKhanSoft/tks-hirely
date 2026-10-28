<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patent extends Model
{
    protected $table = 'patents';

    protected $fillable = [
        'basic_info_id',
        'title',
        'registry_id',
        'country',
        'date_registered',
        'is_licensed',
        'proof_path',
        'is_verified',
        'verified_by',
    ];

    protected $casts = [
        'date_registered' => 'date',
        'is_licensed' => 'boolean',
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
