<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referee extends Model
{
    protected $table = 'referees';

    protected $fillable = [
        'basic_info_id',
        'name',
        'email',
        'phone',
        'designation',
        'organization',
        'relation',
        'address',
    ];

    public function basicInfo(): BelongsTo
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }
}
