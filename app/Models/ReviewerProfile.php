<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewerProfile extends Model
{
    protected $table = 'reviewer_profiles';

    protected $fillable = [
        'user_id',
        'name',
        'subject',
        'academic_rank',
        'university',
        'country',
        'phone',
        'specialization',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * User authentication account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registrar or Administrator who created this reviewer record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Assigned academic reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ApplicationReview::class, 'reviewer_id', 'user_id');
    }
}
