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

    /**
     * Parse title prefix (e.g. Prof. Dr., Dr., Prof., Engr.) and remaining clean full name.
     */
    public function getParsedNameAttribute(): array
    {
        $raw = trim($this->name ?? '');
        $prefixes = [
            'Prof. Dr.',
            'Professor Dr.',
            'Prof.',
            'Professor',
            'Assoc. Prof. Dr.',
            'Assoc. Prof.',
            'Assistant Prof.',
            'Asst. Prof.',
            'Dr.',
            'Doctor',
            'Engr.',
            'Engineer',
            'Mr.',
            'Ms.',
            'Mrs.',
        ];

        foreach ($prefixes as $p) {
            if (stripos($raw, $p) === 0) {
                $clean = trim(substr($raw, strlen($p)));
                return [
                    'prefix' => $p,
                    'full_name' => $clean ?: $raw,
                ];
            }
        }

        return [
            'prefix' => '',
            'full_name' => $raw,
        ];
    }
}
