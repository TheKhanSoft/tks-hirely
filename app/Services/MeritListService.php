<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Campaign;
use App\Models\AcademicDegree;
use App\Models\Publication;
use App\Models\WorkExperience;
use Illuminate\Support\Collection;

class MeritListService
{
    /**
     * Compute multi-stage merit records for a given campaign and stage.
     *
     * @param Campaign $campaign
     * @param string $stage intake|scrutiny|peer_review|selection_board|consolidated
     * @param array $filters ['search' => '', 'quota' => '', 'status' => '']
     * @return array ['applications' => Collection, 'metrics' => array, 'stage_title' => string]
     */
    public function getStageData(Campaign $campaign, string $stage = 'consolidated', array $filters = []): array
    {
        $query = Application::with([
            'user.basicInfo.degrees',
            'user.basicInfo.experiences',
            'user.basicInfo.publications',
            'user.basicInfo.medals',
            'billingLedger',
            'evaluations.boardMember',
            'reviews.reviewer.reviewerProfile',
        ])->where('campaign_id', $campaign->id);

        // Stage-specific query scoping
        if ($stage === 'scrutiny') {
            // All applications that have submitted or been scrutinized
            $query->whereIn('status', ['SUBMITTED', 'PAID', 'ELIGIBLE', 'INELIGIBLE', 'DISQUALIFIED', 'INCOMPLETE_DOSSIER', 'UNDER_REVIEW', 'APPROVED', 'REJECTED', 'APPEALED']);
        } elseif ($stage === 'peer_review') {
            // Only candidates who cleared scrutiny or are under review / approved
            $query->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED', 'REJECTED']);
        } elseif ($stage === 'selection_board') {
            // Only candidates shortlisted for interview
            $query->whereIn('status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED', 'REJECTED']);
        }

        // Apply filters
        $search = trim($filters['search'] ?? '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('email', 'like', "%{$search}%");
                })->orWhereHas('user.basicInfo', function ($bq) use ($search) {
                    $bq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('cnic_number', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        $quota = trim($filters['quota'] ?? '');
        if (!empty($quota) && $quota !== 'ALL') {
            $query->whereHas('user.basicInfo', function ($bq) use ($quota) {
                $bq->where('quota', $quota);
            });
        }

        $status = trim($filters['status'] ?? '');
        if (!empty($status) && $status !== 'ALL') {
            $query->where('status', $status);
        }

        $gender = trim($filters['gender'] ?? '');
        if (!empty($gender) && $gender !== 'ALL') {
            $query->whereHas('user.basicInfo', function ($bq) use ($gender) {
                $bq->where('gender', $gender);
            });
        }

        $fee = trim($filters['fee'] ?? '');
        if (!empty($fee) && $fee !== 'ALL') {
            if ($fee === 'PAID') {
                $query->where(function ($q) {
                    $q->whereHas('billingLedger', function ($lq) {
                        $lq->where('status', 'SETTLED');
                    })->orWhereIn('status', ['PAID', 'ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']);
                });
            } elseif ($fee === 'UNPAID') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('billingLedger', function ($lq) {
                        $lq->where('status', 'SETTLED');
                    })->whereNotIn('status', ['PAID', 'ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']);
                });
            }
        }

        $apps = $query->get();

        // Calculate stage scores & metadata for each application
        $scoredApps = $apps->map(function ($app) use ($campaign) {
            return $this->calculateApplicationMetrics($app, $campaign);
        });

        // Score cutoff filter
        $minScore = (float)($filters['minScore'] ?? 0);
        if ($minScore > 0) {
            $scoredApps = $scoredApps->filter(function ($item) use ($minScore) {
                return ($item['aggregate_score'] ?? 0) >= $minScore;
            })->values();
        }

        // Stage-specific sorting
        $sortedApps = match ($stage) {
            'intake' => $scoredApps->sortByDesc(fn ($item) => $item['app']->submission_timestamp ?? $item['app']->created_at)->values(),
            'scrutiny' => $scoredApps->sort(function ($a, $b) {
                // Eligible first, then highest academic score
                $statusOrder = ['ELIGIBLE' => 1, 'UNDER_REVIEW' => 2, 'APPROVED' => 3, 'INCOMPLETE_DOSSIER' => 4, 'INELIGIBLE' => 5, 'DISQUALIFIED' => 6];
                $orderA = $statusOrder[$a['app']->status] ?? 7;
                $orderB = $statusOrder[$b['app']->status] ?? 7;
                if ($orderA !== $orderB) {
                    return $orderA <=> $orderB;
                }
                return $b['academic_score'] <=> $a['academic_score'];
            })->values(),
            'peer_review' => $scoredApps->sortByDesc('peer_score_avg')->values(),
            'selection_board' => $scoredApps->sortByDesc('interview_score')->values(),
            default => $scoredApps->sort(function ($a, $b) {
                // Disqualified always at the bottom
                $isDisqA = in_array($a['app']->status, ['INELIGIBLE', 'DISQUALIFIED']);
                $isDisqB = in_array($b['app']->status, ['INELIGIBLE', 'DISQUALIFIED']);
                if ($isDisqA !== $isDisqB) {
                    return $isDisqA ? 1 : -1;
                }
                return $b['aggregate_score'] <=> $a['aggregate_score'];
            })->values(),
        };

        // Assign computed ranks (excluding disqualified from top numerical ranks in final)
        $rank = 1;
        $finalList = $sortedApps->map(function ($item) use (&$rank, $stage) {
            $isDisq = in_array($item['app']->status, ['INELIGIBLE', 'DISQUALIFIED']);
            if ($isDisq && in_array($stage, ['consolidated', 'selection_board', 'peer_review'])) {
                $item['rank'] = '-';
            } else {
                $item['rank'] = $rank++;
            }

            // Assign statutory recommendation label
            if ($stage === 'consolidated') {
                if ($isDisq) {
                    $item['recommendation'] = 'DISQUALIFIED / INELIGIBLE';
                    $item['recommendation_badge'] = 'badge-rose';
                } elseif ($item['rank'] === 1) {
                    $item['recommendation'] = 'SELECTED FOR APPOINTMENT (RANK 1)';
                    $item['recommendation_badge'] = 'badge-emerald';
                } elseif ($item['rank'] === 2) {
                    $item['recommendation'] = 'WAITLISTED / STANDBY 1 (RANK 2)';
                    $item['recommendation_badge'] = 'badge-indigo';
                } elseif ($item['rank'] === 3) {
                    $item['recommendation'] = 'WAITLISTED / STANDBY 2 (RANK 3)';
                    $item['recommendation_badge'] = 'badge-cyan';
                } else {
                    $item['recommendation'] = 'NOT RECOMMENDED (MERIT BENCHMARK)';
                    $item['recommendation_badge'] = 'badge-slate';
                }
            }

            return $item;
        });

        // Recommendation filter (Step 5 Consolidated)
        $recommendation = trim($filters['recommendation'] ?? '');
        if (!empty($recommendation) && $recommendation !== 'ALL') {
            $finalList = $finalList->filter(function ($item) use ($recommendation) {
                if ($recommendation === 'SELECTED') {
                    return str_contains($item['recommendation'] ?? '', 'SELECTED');
                } elseif ($recommendation === 'WAITLISTED') {
                    return str_contains($item['recommendation'] ?? '', 'WAITLISTED');
                } elseif ($recommendation === 'NOT_RECOMMENDED') {
                    return str_contains($item['recommendation'] ?? '', 'NOT RECOMMENDED');
                } elseif ($recommendation === 'DISQUALIFIED') {
                    return str_contains($item['recommendation'] ?? '', 'DISQUALIFIED');
                }
                return true;
            })->values();
        }

        // Custom multi-column sorting if specified
        $sortBy = $filters['sortBy'] ?? 'rank';
        $sortDir = strtolower($filters['sortDir'] ?? 'asc');

        if (!empty($sortBy) && $sortBy !== 'rank') {
            $finalList = $finalList->sort(function ($a, $b) use ($sortBy, $sortDir) {
                $valA = match($sortBy) {
                    'name' => $a['candidate_name'] ?? '',
                    'tracking_number' => $a['tracking_number'] ?? '',
                    'cnic' => $a['cnic'] ?? '',
                    'academic_score' => $a['academic_score'] ?? 0,
                    'research_score', 'research_exp_score' => $a['research_exp_score'] ?? 0,
                    'peer_score', 'peer_score_avg' => $a['peer_score_avg'] ?? 0,
                    'interview_score' => $a['interview_score'] ?? 0,
                    'aggregate_score', 'total_score' => $a['aggregate_score'] ?? 0,
                    'status' => $a['status'] ?? '',
                    'created_at' => $a['app']->created_at?->timestamp ?? 0,
                    default => $a[$sortBy] ?? '',
                };
                $valB = match($sortBy) {
                    'name' => $b['candidate_name'] ?? '',
                    'tracking_number' => $b['tracking_number'] ?? '',
                    'cnic' => $b['cnic'] ?? '',
                    'academic_score' => $b['academic_score'] ?? 0,
                    'research_score', 'research_exp_score' => $b['research_exp_score'] ?? 0,
                    'peer_score', 'peer_score_avg' => $b['peer_score_avg'] ?? 0,
                    'interview_score' => $b['interview_score'] ?? 0,
                    'aggregate_score', 'total_score' => $b['aggregate_score'] ?? 0,
                    'status' => $b['status'] ?? '',
                    'created_at' => $b['app']->created_at?->timestamp ?? 0,
                    default => $b[$sortBy] ?? '',
                };

                if ($valA === $valB) return 0;
                $res = (is_numeric($valA) && is_numeric($valB))
                    ? ($valA <=> $valB)
                    : strcasecmp((string)$valA, (string)$valB);

                return $sortDir === 'desc' ? -$res : $res;
            })->values();
        } elseif ($sortBy === 'rank' && $sortDir === 'desc') {
            $finalList = $finalList->reverse()->values();
        }

        // Compute summary metrics
        $metrics = $this->computeSummaryMetrics($finalList, $stage);

        $stageTitles = [
            'intake' => 'Application Intake & Fee Settlement Roster (Step 1)',
            'scrutiny' => 'Scrutiny Committee Shortlist & Eligibility Merit List (Step 2)',
            'peer_review' => 'Academic Peer Review & Referee Evaluation Merit List (Step 3)',
            'selection_board' => 'Statutory Selection Board Interview Merit List (Step 4)',
            'consolidated' => 'Final Consolidated Cumulative Merit List & Selection Order (Step 5)',
        ];

        return [
            'applications' => $finalList,
            'metrics' => $metrics,
            'stage_title' => $stageTitles[$stage] ?? 'Recruitment Merit Roster',
        ];
    }

    /**
     * Compute component scores for a single application.
     */
    public function calculateApplicationMetrics(Application $app, ?Campaign $campaign = null): array
    {
        $campaign = $campaign ?? $app->campaign;
        $basicInfo = $app->user?->basicInfo;
        $degrees = $basicInfo?->degrees ?? collect();
        $experiences = $basicInfo?->experiences ?? collect();
        $publications = $basicInfo?->publications ?? collect();
        $medals = $basicInfo?->medals ?? collect();
        $evaluations = $app->evaluations ?? collect();
        $reviews = $app->reviews ?? collect();

        // 1. Academic Credentials Score (Max 40 Marks)
        // If board member gave academic scores, use average; otherwise compute deterministically from degrees
        $academicScore = 0.0;
        if ($evaluations->isNotEmpty() && $evaluations->avg('academic_score') > 0) {
            $academicScore = round((float)$evaluations->avg('academic_score'), 2);
        } else {
            // Objective academic score from degrees
            $degreePoints = 0.0;
            $hasPhD = false;
            $hasMS = false;
            foreach ($degrees as $deg) {
                $level = strtoupper(trim($deg->degree_level ?? ''));
                $div = strtoupper(trim($deg->division ?? ''));
                $isFirst = str_contains($div, '1') || str_contains($div, 'FIRST');
                $isSecond = str_contains($div, '2') || str_contains($div, 'SECOND');

                if (str_contains($level, 'MATRIC') || str_contains($level, 'SSC')) {
                    $degreePoints += $isFirst ? 5.0 : ($isSecond ? 3.5 : 2.5);
                } elseif (str_contains($level, 'INTER') || str_contains($level, 'HSSC')) {
                    $degreePoints += $isFirst ? 7.0 : ($isSecond ? 5.0 : 3.5);
                } elseif (str_contains($level, 'BACHELOR')) {
                    $degreePoints += $isFirst ? 10.0 : ($isSecond ? 7.5 : 5.0);
                } elseif (str_contains($level, 'MASTER') || str_contains($level, 'MPHIL') || str_contains($level, 'MS')) {
                    $hasMS = true;
                    $degreePoints += $isFirst ? 8.0 : ($isSecond ? 6.0 : 4.0);
                } elseif (str_contains($level, 'PHD') || str_contains($level, 'DOCTOR')) {
                    $hasPhD = true;
                    $degreePoints += 10.0;
                }
            }
            if ($degrees->isNotEmpty() && $degreePoints < 25.0) {
                $degreePoints = 30.0; // Baseline floor for verified graduates
            }
            $academicScore = min(40.0, round($degreePoints, 2));
        }

        // 2. Research & Experience Score (Max 15 Marks)
        $researchExpScore = 0.0;
        if ($evaluations->isNotEmpty() && ($evaluations->avg('publications_score') > 0 || $evaluations->avg('medals_score') > 0)) {
            $researchExpScore = round((float)($evaluations->avg('publications_score') + $evaluations->avg('medals_score')), 2);
        } else {
            $pubScore = 0.0;
            foreach ($publications as $pub) {
                $cat = strtoupper(trim($pub->hjrs_category ?? ''));
                if ($cat === 'W') $pubScore += 3.0;
                elseif ($cat === 'X') $pubScore += 2.0;
                else $pubScore += 1.0;
            }
            $expScore = min(5.0, (float)$experiences->count() * 1.0);
            $medalScore = min(3.0, (float)$medals->count() * 1.5);
            $researchExpScore = min(15.0, round($pubScore + $expScore + $medalScore, 2));
        }

        // 3. External Peer Review Score (Max 15 Marks / Percentage)
        $submittedReviews = $reviews->where('status', 'SUBMITTED');
        $peerScoreAvg = 0.0;
        $peerVerdict = 'N/A';
        $peerVerdictBadge = 'badge-slate';
        $requiresPeer = $campaign ? ($campaign->getRequiredPeerReviewersCount() > 0) : false;

        if ($submittedReviews->isNotEmpty()) {
            $peerScoreAvg = round((float)$submittedReviews->avg('overall_score'), 2);
            $passingRule = $campaign ? $campaign->getReviewPassingRule() : ['min_score_percent' => 60.0];
            $minScore = (float)($passingRule['min_score_percent'] ?? 60.0);
            if ($peerScoreAvg >= $minScore) {
                $peerVerdict = 'CLEARED (' . $peerScoreAvg . '%)';
                $peerVerdictBadge = 'badge-emerald';
            } else {
                $peerVerdict = 'BELOW BENCHMARK (' . $peerScoreAvg . '%)';
                $peerVerdictBadge = 'badge-rose';
            }
        } elseif ($requiresPeer) {
            $peerVerdict = 'PENDING REVIEWS (' . $reviews->count() . ' assigned)';
            $peerVerdictBadge = 'badge-amber';
        }

        // Scale peer review score to 15 marks
        $peerReviewScaledScore = $requiresPeer ? round(($peerScoreAvg / 100.0) * 15.0, 2) : 0.0;

        // 4. Selection Board Interview & Viva-Voce Score (Max 30 Marks)
        $interviewScore = 0.0;
        $boardStatus = 'AWAITING INTERVIEW';
        $boardStatusBadge = 'badge-slate';

        if ($evaluations->isNotEmpty() && $evaluations->avg('interview_score') > 0) {
            $rawInterview = (float)$evaluations->avg('interview_score'); // usually out of 40
            $interviewScore = round(($rawInterview / 40.0) * 30.0, 2);
            $boardStatus = $interviewScore >= 18.0 ? 'RECOMMENDED' : 'NOT RECOMMENDED';
            $boardStatusBadge = $interviewScore >= 18.0 ? 'badge-emerald' : 'badge-rose';
        }

        // 5. Cumulative Final Aggregate Score (Max 100 Marks)
        // If peer review not required, normalize academic & interview slightly
        $aggregateScore = 0.0;
        if ($requiresPeer) {
            $aggregateScore = round($academicScore + $researchExpScore + $peerReviewScaledScore + $interviewScore, 2);
        } else {
            // Re-weight without peer review: Academic (45) + Research/Exp (20) + Interview (35)
            $scaledAcad = round(($academicScore / 40.0) * 45.0, 2);
            $scaledRes = round(($researchExpScore / 15.0) * 20.0, 2);
            $scaledInt = round(($interviewScore / 30.0) * 35.0, 2);
            $aggregateScore = round($scaledAcad + $scaledRes + $scaledInt, 2);
        }

        // Candidate Full Name & Identifiers
        $fullName = $basicInfo ? trim(($basicInfo->first_name ?? '') . ' ' . ($basicInfo->last_name ?? '')) : $app->user?->email;
        if (empty($fullName)) {
            $fullName = $app->user?->email ?? "Applicant #{$app->id}";
        }

        // Fee settlement status
        $isPaid = $app->billingLedger?->status === 'SETTLED' || in_array($app->status, ['PAID', 'ELIGIBLE', 'UNDER_REVIEW', 'APPROVED']);

        return [
            'app' => $app,
            'candidate_name' => $fullName,
            'father_name' => $basicInfo?->father_name ?? '—',
            'cnic' => $basicInfo?->cnic_number ?? 'N/A',
            'quota' => $basicInfo?->quota ?? 'Open Merit',
            'domicile' => $basicInfo?->domicile ?? 'Punjab',
            'gender' => $basicInfo?->gender ?? '—',
            'tracking_number' => $app->tracking_number,
            'highest_degree' => $degrees->last()?->degree_title ?? ($degrees->last()?->degree_level ?? '—'),
            'terminal_division' => $degrees->last()?->division ?? '1st Division',
            'terminal_gpa' => $degrees->last()?->gpa ?? '—',
            'is_paid' => $isPaid,
            'challan_id' => $app->billingLedger?->consumer_id ?? ('1000' . str_pad($app->id, 8, '0', STR_PAD_LEFT)),
            'academic_score' => $academicScore,
            'research_exp_score' => $researchExpScore,
            'peer_score_avg' => $peerScoreAvg,
            'peer_score_scaled' => $peerReviewScaledScore,
            'peer_verdict' => $peerVerdict,
            'peer_verdict_badge' => $peerVerdictBadge,
            'interview_score' => $interviewScore,
            'board_status' => $boardStatus,
            'board_status_badge' => $boardStatusBadge,
            'aggregate_score' => min(100.0, $aggregateScore),
            'disqualification_reason' => $app->disqualification_reason,
            'status' => $app->status,
            'status_label' => $app->status_label,
            'status_badge' => $app->status_badge_style,
        ];
    }

    /**
     * Compute summary KPIs for the active stage.
     */
    protected function computeSummaryMetrics(Collection $scoredApps, string $stage): array
    {
        $total = $scoredApps->count();
        $eligibleCount = $scoredApps->whereIn('app.status', ['ELIGIBLE', 'UNDER_REVIEW', 'APPROVED'])->count();
        $ineligibleCount = $scoredApps->whereIn('app.status', ['INELIGIBLE', 'DISQUALIFIED', 'REJECTED'])->count();
        $deficientCount = $scoredApps->where('app.status', 'INCOMPLETE_DOSSIER')->count();
        $feePaidCount = $scoredApps->where('is_paid', true)->count();
        $avgAggregate = $total > 0 ? round($scoredApps->avg('aggregate_score'), 1) : 0.0;
        $topScore = $total > 0 ? round($scoredApps->max('aggregate_score'), 1) : 0.0;

        return [
            'total_applications' => $total,
            'eligible_count' => $eligibleCount,
            'ineligible_count' => $ineligibleCount,
            'deficient_count' => $deficientCount,
            'fee_paid_count' => $feePaidCount,
            'average_aggregate' => $avgAggregate,
            'top_score' => $topScore,
        ];
    }
}
