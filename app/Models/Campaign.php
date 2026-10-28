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
        return (bool)($this->scoring_rules['require_transcript_proof'] ?? true);
    }

    /**
     * Get statutory committee constitution requirements for this campaign.
     */
    public function getCommitteeConstitution(): array
    {
        $rules = $this->scoring_rules ?? [];
        if (!empty($rules['committee_constitution'])) {
            return $rules['committee_constitution'];
        }

        // Academic track: e.g. Assistant Professor, Associate Professor, Lecturer
        if ($this->track_type === 'ACADEMIC') {
            return [
                'required_total' => 5,
                'required_internal' => 2,
                'required_external' => 2,
                'required_international' => 1,
                'required_independent' => 0,
                'title' => 'Statutory Academic Selection Committee',
                'description' => 'Statutory Quorum: 2 Internal Leaders/Faculty, 2 External Subject Experts (Other Institutions/Offices), 1 International/Overseas Specialist.',
                'enforce_strict_quota' => true,
            ];
        }

        // Corporate / Administrative track:
        return [
            'required_total' => 4,
            'required_internal' => 2,
            'required_external' => 1,
            'required_international' => 0,
            'required_independent' => 1,
            'title' => 'Executive & Operational Selection Board',
            'description' => 'Operational Quorum: 2 Internal Officers/Directors, 1 External Industry/Office Delegate, 1 Independent/Governance Member.',
            'enforce_strict_quota' => true,
        ];
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

        return [
            'constitution' => $constitution,
            'total_assigned' => $totalAssigned,
            'required_total' => $requiredTotal,
            'is_complete' => $isComplete,
            'is_over_allocated' => $totalAssigned > $requiredTotal,
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
