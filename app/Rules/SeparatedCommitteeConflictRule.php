<?php

namespace App\Rules;

use App\Models\CampaignCommittee;
use Illuminate\Contracts\Validation\Rule;

class SeparatedCommitteeConflictRule implements Rule
{
    protected $campaignId;
    protected $isAppellatePanel;

    public function __construct($campaignId, $isAppellatePanel)
    {
        $this->campaignId = $campaignId;
        $this->isAppellatePanel = $isAppellatePanel;
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        // $value is the target user_id being assigned
        $targetRoleType = $this->isAppellatePanel;

        // Check if the user is already assigned to the opposite panel for this specific job campaign
        $oppositePanelExists = CampaignCommittee::where('campaign_id', $this->campaignId)
            ->where('user_id', $value)
            ->where('is_appellate_panel', !$targetRoleType)
            ->exists();

        return !$oppositePanelExists;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'Conflict of Interest: This member is already assigned to the alternative committee panel for this campaign.';
    }
}
