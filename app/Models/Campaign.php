<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use Auditable;

    protected $fillable = [
        'job_code',
        'title',
        'track_type',
        'department',
        'opening_date',
        'closing_date',
        'application_fee',
        'is_active',
        'require_transcript_proof',
        'scoring_rules',
        'constitution_id',
    ];

    protected $casts = [
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
        'application_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'require_transcript_proof' => 'boolean',
        'scoring_rules' => 'array',
    ];

    /**
     * Determine if academic degrees require transcript proof.
     */
    public function requiresTranscriptProof(): bool
    {
        if (isset($this->require_transcript_proof)) {
            return (bool)$this->require_transcript_proof;
        }
        if (isset($this->scoring_rules['require_transcript_proof'])) {
            return (bool)$this->scoring_rules['require_transcript_proof'];
        }
        return SystemSetting::isDocumentRequired('transcript');
    }

    /**
     * Get the statutory required number of external peer reviewers for this position.
     * E.g. For Assistant Professor: exactly 3 experts.
     */
    public function getRequiredPeerReviewersCount(): int
    {
        $rules = $this->scoring_rules ?? [];
        if (isset($rules['required_peer_reviewers'])) {
            return (int)$rules['required_peer_reviewers'];
        }

        $preset = SystemSetting::getConstitutionPresetForPost($this->title, $this->track_type);
        return (int)($preset['required_peer_reviewers'] ?? 3);
    }

    /**
     * Statutory board constitution assigned to this campaign.
     */
    public function constitution(): BelongsTo
    {
        return $this->belongsTo(CommitteeConstitution::class, 'constitution_id');
    }

    /**
     * Get statutory committee constitution requirements for this campaign.
     */
    public function getCommitteeConstitution(): array
    {
        $rules = $this->scoring_rules ?? [];
        if (!empty($rules['committee_constitution'])) {
            $constitution = $rules['committee_constitution'];
            if (empty($constitution['rules']) && !empty($constitution['roles_breakdown'])) {
                $constitution['rules'] = $constitution['roles_breakdown'];
            }
            return $constitution;
        }

        if ($this->constitution_id) {
            $c = $this->constitution;
            if ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->name,
                    'description' => $c->description,
                    'organization_sector' => $c->organization_sector,
                    'required_total' => $c->required_total,
                    'required_internal' => $c->required_internal,
                    'required_external' => $c->required_external,
                    'required_international' => $c->required_international,
                    'required_independent' => $c->required_independent,
                    'required_peer_reviewers' => $c->required_peer_reviewers,
                    'enforce_strict_quota' => $c->enforce_strict_quota,
                    'roles_breakdown' => $c->roles_breakdown ?? [],
                    'rules' => $c->roles_breakdown ?? [],
                    'routing_flow' => $c->getEffectiveRoutingFlow(),
                    'eligibility_criteria' => $c->getEffectiveEligibilityCriteria(),
                ];
            }
        }

        // Dynamically resolve preset constitution based on post title & track from SystemSetting
        $preset = SystemSetting::getConstitutionPresetForPost($this->title, $this->track_type);
        $preset['enforce_strict_quota'] = SystemSetting::get('enforce_strict_committee_quorum', true);
        if (empty($preset['rules']) && !empty($preset['roles_breakdown'])) {
            $preset['rules'] = $preset['roles_breakdown'];
        }

        return $preset;
    }

    /**
     * Get live committee quota and allocation metrics.
     */
    public function getCommitteeMetrics(): array
    {
        $constitution = $this->getCommitteeConstitution();
        $assigned = $this->committees()->get();

        $internalCount = $assigned->where('member_category', 'INTERNAL')->count();
        $externalCount = $assigned->where('member_category', 'EXTERNAL')->count();
        $internationalCount = $assigned->where('member_category', 'INTERNATIONAL')->count();
        $independentCount = $assigned->where('member_category', 'INDEPENDENT')->count();
        $totalAssigned = $assigned->count();

        $requiredTotal = (int)($constitution['required_total'] ?? 5);
        $requiredInternal = (int)($constitution['required_internal'] ?? 2);
        $requiredExternal = (int)($constitution['required_external'] ?? 2);
        $requiredInternational = (int)($constitution['required_international'] ?? 1);
        $requiredIndependent = (int)($constitution['required_independent'] ?? 0);

        $isComplete = ($totalAssigned === $requiredTotal) &&
            ($internalCount === $requiredInternal) &&
            ($externalCount === $requiredExternal) &&
            ($internationalCount === $requiredInternational) &&
            ($independentCount === $requiredIndependent);

        $rulesList = $constitution['rules'] ?? $constitution['roles_breakdown'] ?? [];

        return [
            'constitution' => $constitution,
            'total_assigned' => $totalAssigned,
            'required_total' => $requiredTotal,
            'is_complete' => $isComplete,
            'is_over_allocated' => $totalAssigned > $requiredTotal,
            'rules' => $rulesList,
            'categories' => [
                'INTERNAL' => [
                    'label' => 'Internal Organization / Staff',
                    'assigned' => $internalCount,
                    'required' => $requiredInternal,
                    'satisfied' => $internalCount === $requiredInternal,
                    'available' => max(0, $requiredInternal - $internalCount),
                ],
                'EXTERNAL' => [
                    'label' => 'External Experts (Other Institutions / Offices)',
                    'assigned' => $externalCount,
                    'required' => $requiredExternal,
                    'satisfied' => $externalCount === $requiredExternal,
                    'available' => max(0, $requiredExternal - $externalCount),
                ],
                'INTERNATIONAL' => [
                    'label' => 'International / Overseas Specialists',
                    'assigned' => $internationalCount,
                    'required' => $requiredInternational,
                    'satisfied' => $internationalCount === $requiredInternational,
                    'available' => max(0, $requiredInternational - $internationalCount),
                ],
                'INDEPENDENT' => [
                    'label' => 'Independent Arbiters / Governance',
                    'assigned' => $independentCount,
                    'required' => $requiredIndependent,
                    'satisfied' => $independentCount === $requiredIndependent,
                    'available' => max(0, $requiredIndependent - $independentCount),
                ],
            ]
        ];
    }

    /**
     * Committees assigned to this campaign.
     */
    public function committees(): HasMany
    {
        return $this->hasMany(CampaignCommittee::class);
    }

    /**
     * Applications submitted to this campaign.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
