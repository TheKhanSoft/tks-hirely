<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationReview extends Model
{
    protected $table = 'application_reviews';

    protected $fillable = [
        'application_id',
        'reviewer_id',
        'assigned_by',
        'assigned_at',
        'due_date',
        'confidential_notes',
        'status',
        'recommendation',
        'overall_score',
        'report_text',
        'evaluation_file_path',
        'submitted_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'overall_score' => 'decimal:2',
    ];

    /**
     * Parent application being evaluated.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Reviewer user account.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Registrar who assigned this review.
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
