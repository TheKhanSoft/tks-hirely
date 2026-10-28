<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveEvaluation extends Model
{
    protected $table = 'live_evaluations';

    protected $fillable = [
        'application_id',
        'board_member_id',
        'academic_score',
        'medals_score',
        'publications_score',
        'events_score',
        'project_score',
        'interview_score',
        'is_outlier',
        'outlier_justification',
        'last_modified',
    ];

    protected $casts = [
        'academic_score' => 'decimal:2',
        'medals_score' => 'decimal:2',
        'publications_score' => 'decimal:2',
        'events_score' => 'decimal:2',
        'project_score' => 'decimal:2',
        'interview_score' => 'decimal:2',
        'is_outlier' => 'boolean',
        'last_modified' => 'datetime',
    ];

    /**
     * Application evaluated.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Board member user.
     */
    public function boardMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'board_member_id');
    }
}
