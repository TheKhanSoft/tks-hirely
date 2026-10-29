<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BasicInfo extends Model
{
    use Auditable;

    protected $table = 'basic_infos';

    protected $fillable = [
        'user_id',
        'track_type',
        'profile_picture',
        'first_name',
        'last_name',
        'cnic_number',
        'date_of_birth',
        'quota',
        'gender',
        'father_name',
        'phone_number',
        'secondary_phone',
        'secondary_email',
        'same_address',
        'permanent_address',
        'mailing_address',
        'place_of_birth',
        'nationality',
        'religion',
        'marital_status',
        'domicile',
        'is_locked',
        'dynamic_attributes',
    ];

    protected $casts = [
        'dynamic_attributes' => 'array',
        'is_locked' => 'boolean',
        'same_address' => 'boolean',
        'date_of_birth' => 'date',
    ];

    /**
     * User associated with the basic info.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Academic medals ledger.
     */
    public function medals(): HasMany
    {
        return $this->hasMany(AcademicMedal::class, 'basic_info_id');
    }

    /**
     * Academic publications.
     */
    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class, 'basic_info_id');
    }

    /**
     * Professional events.
     */
    public function events(): HasMany
    {
        return $this->hasMany(ProfessionalEvent::class, 'basic_info_id');
    }

    /**
     * Research projects.
     */
    public function researchProjects(): HasMany
    {
        return $this->hasMany(ResearchProject::class, 'basic_info_id');
    }

    /**
     * Academic degrees.
     */
    public function degrees(): HasMany
    {
        return $this->hasMany(AcademicDegree::class, 'basic_info_id')->orderBy('year', 'asc');
    }

    /**
     * Work experiences.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class, 'basic_info_id')->orderBy('start_date', 'asc');
    }

    /**
     * Admin milestones.
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(AdminMilestone::class, 'basic_info_id')->orderBy('year', 'asc');
    }

    /**
     * Support tools.
     */
    public function tools(): HasMany
    {
        return $this->hasMany(SupportTool::class, 'basic_info_id');
    }

    /**
     * Patents.
     */
    public function patents(): HasMany
    {
        return $this->hasMany(Patent::class, 'basic_info_id')->orderBy('date_registered', 'asc');
    }

    /**
     * Portfolio items.
     */
    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class, 'basic_info_id')->orderBy('date_completed', 'asc');
    }

    /**
     * Trainings.
     */
    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class, 'basic_info_id');
    }

    /**
     * Referees.
     */
    public function referees(): HasMany
    {
        return $this->hasMany(Referee::class, 'basic_info_id');
    }

    /**
     * Get candidate profile picture URL.
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if (!empty($this->profile_picture)) {
            if (str_starts_with($this->profile_picture, 'http://') || str_starts_with($this->profile_picture, 'https://') || str_starts_with($this->profile_picture, 'data:image')) {
                return $this->profile_picture;
            }
            return asset('storage/' . $this->profile_picture);
        }

        // Realistic dummy candidate avatar fallback
        $name = urlencode(trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')));
        if (!empty($name)) {
            return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=ffffff&bold=true&rounded=true&size=128";
        }

        return null;
    }
}
