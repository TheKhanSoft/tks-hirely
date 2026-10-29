<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CommitteeConstitution extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'organization_sector',
        'description',
        'routing_flow',
        'eligibility_criteria',
        'required_total',
        'required_internal',
        'required_external',
        'required_international',
        'required_independent',
        'required_peer_reviewers',
        'enforce_strict_quota',
        'roles_breakdown',
        'is_default',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'routing_flow' => 'array',
        'eligibility_criteria' => 'array',
        'roles_breakdown' => 'array',
        'required_total' => 'integer',
        'required_internal' => 'integer',
        'required_external' => 'integer',
        'required_international' => 'integer',
        'required_independent' => 'integer',
        'required_peer_reviewers' => 'integer',
        'enforce_strict_quota' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name) . '-' . rand(1000, 9999);
            }
            // Auto recalculate quotas from roles breakdown if available
            if (!empty($model->roles_breakdown) && is_array($model->roles_breakdown)) {
                $model->required_total = (int)collect($model->roles_breakdown)->sum('count');
                $model->required_internal = (int)collect($model->roles_breakdown)->where('category', 'INTERNAL')->sum('count');
                $model->required_external = (int)collect($model->roles_breakdown)->where('category', 'EXTERNAL')->sum('count');
                $model->required_international = (int)collect($model->roles_breakdown)->where('category', 'INTERNATIONAL')->sum('count');
                $model->required_independent = (int)collect($model->roles_breakdown)->where('category', 'INDEPENDENT')->sum('count');
            }
        });
    }

    /**
     * Campaigns utilizing this statutory board constitution.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'constitution_id');
    }

    /**
     * User who authored this constitution.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Standard routing workflow stages fallback.
     */
    public function getEffectiveRoutingFlow(): array
    {
        if (!empty($this->routing_flow) && is_array($this->routing_flow)) {
            return $this->routing_flow;
        }

        // Sector-aware baseline pipeline flow
        return match ($this->organization_sector) {
            'CORPORATE' => [
                ['step' => 1, 'stage' => 'HR_PRE_SCREEN', 'name' => 'HR Screening & Resume Shortlist', 'cadre' => 'OFFICER', 'sla_days' => 3, 'mandatory' => true],
                ['step' => 2, 'stage' => 'TECH_ASSESSMENT', 'name' => 'Technical Competency Evaluation', 'cadre' => 'EXPERT', 'sla_days' => 5, 'mandatory' => true],
                ['step' => 3, 'stage' => 'EXECUTIVE_PANEL', 'name' => 'Executive Committee Interview', 'cadre' => 'EXECUTIVE', 'sla_days' => 7, 'mandatory' => true],
                ['step' => 4, 'stage' => 'OFFER_APPROVAL', 'name' => 'Managing Director / Board Offer Approval', 'cadre' => 'EXECUTIVE', 'sla_days' => 4, 'mandatory' => true],
            ],
            'HEALTHCARE' => [
                ['step' => 1, 'stage' => 'LICENSURE_CHECK', 'name' => 'Medical Council Credential & License Audit', 'cadre' => 'OFFICER', 'sla_days' => 5, 'mandatory' => true],
                ['step' => 2, 'stage' => 'CLINICAL_PEER_REVIEW', 'name' => 'Clinical Specialty Peer Review', 'cadre' => 'EXPERT', 'sla_days' => 10, 'mandatory' => true],
                ['step' => 3, 'stage' => 'HOSPITAL_BOARD', 'name' => 'Medical Advisory Board Interview', 'cadre' => 'FACULTY', 'sla_days' => 7, 'mandatory' => true],
                ['step' => 4, 'stage' => 'GOVERNING_SANCTION', 'name' => 'Hospital Trust / Governing Body Sanction', 'cadre' => 'EXECUTIVE', 'sla_days' => 5, 'mandatory' => true],
            ],
            'PUBLIC_SECTOR' => [
                ['step' => 1, 'stage' => 'SCRUTINY_AUDIT', 'name' => 'Civil Service Eligibility & Domicile Scrutiny', 'cadre' => 'OFFICER', 'sla_days' => 7, 'mandatory' => true],
                ['step' => 2, 'stage' => 'WRITTEN_EVALUATION', 'name' => 'Competitive Assessment / Subject Vetting', 'cadre' => 'EXPERT', 'sla_days' => 14, 'mandatory' => true],
                ['step' => 3, 'stage' => 'COMMISSION_INTERVIEW', 'name' => 'Statutory Public Service Board Interview', 'cadre' => 'OFFICER', 'sla_days' => 10, 'mandatory' => true],
                ['step' => 4, 'stage' => 'APPOINTMENT_ORDER', 'name' => 'Competent Authority Formal Notification', 'cadre' => 'EXECUTIVE', 'sla_days' => 7, 'mandatory' => true],
            ],
            default => [
                ['step' => 1, 'stage' => 'PRE_SCRUTINY', 'name' => 'Administrative & Fee Scrutiny', 'cadre' => 'OFFICER', 'sla_days' => 5, 'mandatory' => true],
                ['step' => 2, 'stage' => 'PEER_REVIEW', 'name' => 'External Academic Peer Review', 'cadre' => 'EXPERT', 'sla_days' => 14, 'mandatory' => $this->required_peer_reviewers > 0],
                ['step' => 3, 'stage' => 'SELECTION_BOARD', 'name' => 'Statutory Selection Board Interview', 'cadre' => 'FACULTY', 'sla_days' => 7, 'mandatory' => true],
                ['step' => 4, 'stage' => 'SYNDICATE_SANCTION', 'name' => 'Syndicate / Governing Body Approval', 'cadre' => 'EXECUTIVE', 'sla_days' => 10, 'mandatory' => true],
            ]
        };
    }

    /**
     * Standard eligibility criteria fallback.
     */
    public function getEffectiveEligibilityCriteria(): array
    {
        if (!empty($this->eligibility_criteria) && is_array($this->eligibility_criteria)) {
            return $this->eligibility_criteria;
        }

        return [
            'min_degree_level' => match ($this->organization_sector) {
                'ACADEMIC' => 'Ph.D. / Terminal Degree',
                'HEALTHCARE' => 'FCPS / MBBS / Specialist Fellowship',
                'CORPORATE' => 'Master\'s / Bachelor\'s in Relevant Discipline',
                'PUBLIC_SECTOR' => 'Bachelor\'s / Master\'s from Recognized Institution',
                default => 'Relevant Accredited Qualification'
            },
            'min_experience_years' => 5,
            'min_publications' => match ($this->organization_sector) {
                'ACADEMIC' => 4,
                default => 0
            },
            'requires_fee_settlement' => true,
            'requires_allied_discipline' => true,
            'min_evaluation_percentile' => 60,
        ];
    }

    /**
     * Sector badge visual formatting.
     */
    public function getSectorBadgeAttribute(): array
    {
        return match ($this->organization_sector) {
            'ACADEMIC' => ['label' => 'Higher Education / Academic', 'color' => '#6366f1', 'bg' => 'rgba(99, 102, 241, 0.12)', 'icon' => '🎓'],
            'CORPORATE' => ['label' => 'Corporate / Enterprise', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.12)', 'icon' => '🏢'],
            'HEALTHCARE' => ['label' => 'Healthcare & Clinical', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.12)', 'icon' => '🩺'],
            'PUBLIC_SECTOR' => ['label' => 'Public Sector & Civil Service', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.12)', 'icon' => '🏛️'],
            'JUDICIAL' => ['label' => 'Judicial & Governance', 'color' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.12)', 'icon' => '⚖️'],
            'DEFENSE' => ['label' => 'Security & Strategic', 'color' => '#ef4444', 'bg' => 'rgba(239, 68, 68, 0.12)', 'icon' => '🛡️'],
            default => ['label' => 'Cross-Sector / General', 'color' => '#06b6d4', 'bg' => 'rgba(6, 182, 212, 0.12)', 'icon' => '🌐'],
        };
    }
}
