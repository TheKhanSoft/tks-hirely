<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RecruitmentProcessFlow extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sector',
        'description',
        'stages',
        'is_default',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'stages' => 'array',
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
        });
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'process_flow_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Default end-to-end dynamic process flow blueprint.
     */
    public static function defaultAcademicPipeline(): array
    {
        return [
            [
                'step' => 1,
                'id' => 'stage_application',
                'name' => 'Application Submission & Fee Settlement',
                'actor_type' => 'APPLICANT',
                'governing_role' => 'Candidate / Applicant',
                'is_enabled' => true,
                'sla_days' => 14,
                'rule_description' => 'Applicant applies during active campaign opening & closing dates. Kuickpay / 1Bill voucher must be settled.',
                'on_pass' => [
                    'target_status' => 'FEE_PAID',
                    'next_step' => 2,
                    'label' => 'Valid Submission & Settled Voucher ➔ Move to Scrutiny Committee',
                ],
                'on_incomplete' => null,
                'on_fail' => [
                    'target_status' => 'CAMPAIGN_CLOSED',
                    'action' => 'Submission Deadline Expired / Registration Blocked',
                ],
            ],
            [
                'step' => 2,
                'id' => 'stage_scrutiny',
                'name' => 'Scrutiny Committee Dossier Vetting',
                'actor_type' => 'COMMITTEE',
                'governing_role' => 'Scrutiny Committee',
                'committee_type' => 'SCRUTINY',
                'is_enabled' => true,
                'sla_days' => 7,
                'rule_description' => 'Applications are scrutinized one by one for basic eligibility, HEC degree equivalence, and mandatory transcripts.',
                'on_pass' => [
                    'target_status' => 'SCRUTINY_PASSED',
                    'next_step' => 3,
                    'label' => 'Credentials Verified ➔ Advance to External Peer Review',
                ],
                'on_incomplete' => [
                    'target_status' => 'INCOMPLETE_DOSSIER',
                    'allow_reupload' => true,
                    'grace_days' => 5,
                    'label' => 'Deficiencies Noted ➔ Candidate Alerted to Upload Missing Documents (5-Day Grace)',
                ],
                'on_fail' => [
                    'target_status' => 'DISQUALIFIED',
                    'allow_appeal' => true,
                    'require_justification' => true,
                    'label' => 'Ineligible Criteria ➔ Disqualified with Formal Justification (Appellate Right Enabled)',
                ],
            ],
            [
                'step' => 3,
                'id' => 'stage_peer_review',
                'name' => 'Academic Subject Experts Peer Review',
                'actor_type' => 'EXPERT_REFEREES',
                'governing_role' => 'External Subject Referees',
                'committee_type' => 'EXPERT_REVIEW',
                'is_enabled' => true,
                'sla_days' => 21,
                'required_experts' => 3,
                'pass_threshold_percent' => 60,
                'rule_description' => 'Dossier dispatched to 3 field-matched external academic referees. Must achieve >= 60% evaluation score or majority positive recommendation.',
                'on_pass' => [
                    'target_status' => 'ELIGIBLE',
                    'next_step' => 4,
                    'label' => 'Peer Review Cleared (>= 60%) ➔ Scheduled for Selection Board Interview',
                ],
                'on_incomplete' => null,
                'on_fail' => [
                    'target_status' => 'REJECTED',
                    'allow_appeal' => true,
                    'label' => 'Below Quality Threshold (< 60%) ➔ Rejected with Referee Evaluation Summary',
                ],
            ],
            [
                'step' => 4,
                'id' => 'stage_selection_board',
                'name' => 'Statutory Selection Board Interview & Viva',
                'actor_type' => 'SELECTION_BOARD',
                'governing_role' => 'Statutory Selection Board',
                'committee_type' => 'SELECTION_BOARD',
                'is_enabled' => true,
                'sla_days' => 7,
                'rule_description' => 'Selection Board conducts live interviews and scores rubric dimensions (Academic, Research, Interview Performance, Aptitude).',
                'on_pass' => [
                    'target_status' => 'APPROVED',
                    'next_step' => 5,
                    'label' => 'Recommended by Board Consensus ➔ Move to Syndicate Final Sanction',
                ],
                'on_incomplete' => null,
                'on_fail' => [
                    'target_status' => 'REJECTED',
                    'label' => 'Not Recommended by Selection Board',
                ],
            ],
            [
                'step' => 5,
                'id' => 'stage_syndicate',
                'name' => 'Syndicate / Governing Body Appointment Sanction',
                'actor_type' => 'EXECUTIVE',
                'governing_role' => 'Syndicate / Executive Council',
                'committee_type' => 'EXECUTIVE',
                'is_enabled' => true,
                'sla_days' => 10,
                'rule_description' => 'Governing Authority reviews recommendations and issues formal appointment letters.',
                'on_pass' => [
                    'target_status' => 'APPROVED',
                    'applicant_status' => 'HIRED',
                    'label' => 'Appointment Sanctioned ➔ Official Offer Letter Issued (Candidate Hired)',
                ],
                'on_incomplete' => null,
                'on_fail' => null,
            ],
            [
                'step' => 6,
                'id' => 'stage_appellate',
                'name' => 'Appellate & Grievance Review Branch',
                'actor_type' => 'COMMITTEE',
                'governing_role' => 'Appellate Grievance Committee',
                'committee_type' => 'APPELLATE',
                'is_enabled' => true,
                'sla_days' => 14,
                'rule_description' => 'Hears appeals against Scrutiny ineligibility or Peer Review rejections. Independent legal and academic arbiter panel.',
                'on_pass' => [
                    'target_status' => 'ELIGIBLE',
                    'next_step' => 4,
                    'label' => 'Grievance Upheld ➔ Reinstated as Eligible to Selection Board Queue',
                ],
                'on_incomplete' => null,
                'on_fail' => [
                    'target_status' => 'DISQUALIFIED',
                    'label' => 'Appeal Dismissed ➔ Final Ineligibility Confirmation',
                ],
            ],
        ];
    }
}
