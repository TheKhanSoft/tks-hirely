<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    protected $table = 'work_experiences';

    protected $fillable = [
        'basic_info_id',
        'job_title',
        'organization',
        'start_date',
        'end_date',
        'experience_type',
        'sector',
        'salary',
        'proof_path',
        'description',
        'is_verified',
        'verified_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_verified' => 'boolean',
    ];

    /**
     * Get gracefully formatted salary string.
     */
    public function getFormattedSalaryAttribute(): string
    {
        if ($this->salary === null || $this->salary === '') {
            return 'N/A';
        }

        if (is_numeric($this->salary)) {
            return 'PKR ' . number_format((float) $this->salary, 2);
        }

        return (string) $this->salary;
    }

    public function basicInfo(): BelongsTo
    {
        return $this->belongsTo(BasicInfo::class, 'basic_info_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
