<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchProject extends Model
{
    use Auditable;

    protected $table = 'research_projects';

    protected $fillable = [
        'basic_info_id',
        'project_title',
        'funding_agency',
        'grant_amount',
        'start_date',
        'role_in_project',
        'turnitin_similarity_percentage',
        'status',
        'co_pis',
        'award_letter_proof_path',
        'is_verified',
        'verified_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'grant_amount' => 'decimal:2',
        'turnitin_similarity_percentage' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    /**
     * Get gracefully formatted grant amount.
     */
    public function getFormattedGrantAmountAttribute(): string
    {
        if ($this->grant_amount === null || $this->grant_amount === '') {
            return 'N/A';
        }

        if (is_numeric($this->grant_amount)) {
            return 'PKR ' . number_format((float) $this->grant_amount, 2);
        }

        return (string) $this->grant_amount;
    }

    public function basicInfo(): BelongsTo
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    /**
     * User who verified this project.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
