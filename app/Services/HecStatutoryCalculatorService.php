<?php

namespace App\Services;

use App\Models\BasicInfo;
use App\Models\Application;
use App\Models\AcademicDegree;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HecStatutoryCalculatorService
{
    /**
     * Compute comprehensive HEC HJRS journal publication breakdown.
     */
    public static function calculatePublicationsSummary(?BasicInfo $basicInfo): array
    {
        if (!$basicInfo) {
            return self::emptyPublicationSummary();
        }

        $publications = $basicInfo->publications ?? collect();
        $currentYear = (int) date('Y');
        $fiveYearsAgo = $currentYear - 5;

        $total = $publications->count();
        $verifiedCount = $publications->where('is_verified', true)->count();

        $wCount = 0;
        $xCount = 0;
        $yCount = 0;
        $zCount = 0;
        $otherCount = 0;

        $last5YearsCount = 0;
        $firstAuthorCount = 0;
        $corrAuthorCount = 0;
        $firstOrCorrCount = 0;
        $totalImpactFactor = 0.0;

        foreach ($publications as $pub) {
            $cat = strtoupper(trim($pub->hjrs_category ?? ''));
            match ($cat) {
                'W' => $wCount++,
                'X' => $xCount++,
                'Y' => $yCount++,
                'Z' => $zCount++,
                default => $otherCount++,
            };

            $year = (int) ($pub->publication_year ?? 0);
            if ($year >= $fiveYearsAgo && $year <= ($currentYear + 1)) {
                $last5YearsCount++;
            }

            $order = trim((string) ($pub->author_order ?? ''));
            $isFirst = in_array($order, ['1', '1st', 'First', 'first', 'FIRST']);
            $isCorr = (bool) ($pub->is_corresponding_author ?? false);

            if ($isFirst) $firstAuthorCount++;
            if ($isCorr) $corrAuthorCount++;
            if ($isFirst || $isCorr) $firstOrCorrCount++;

            $totalImpactFactor += (float) ($pub->impact_factor ?? 0.0);
        }

        // HEC recognized papers are Category W, X, and Y
        $hecRecognizedCount = $wCount + $xCount + $yCount;

        return [
            'total' => $total,
            'verified' => $verifiedCount,
            'w_category' => $wCount,
            'x_category' => $xCount,
            'y_category' => $yCount,
            'z_category' => $zCount,
            'other_category' => $otherCount,
            'hec_recognized_total' => $hecRecognizedCount,
            'last_5_years' => $last5YearsCount,
            'first_authors' => $firstAuthorCount,
            'corresponding_authors' => $corrAuthorCount,
            'first_or_corr_authors' => $firstOrCorrCount,
            'total_impact_factor' => round($totalImpactFactor, 3),
            'avg_impact_factor' => $total > 0 ? round($totalImpactFactor / $total, 3) : 0.0,
            'summary_string' => "HEC Recognized: {$hecRecognizedCount} (W: {$wCount}, X: {$xCount}, Y: {$yCount}) | Last 5 Years: {$last5YearsCount} | 1st/Corr: {$firstOrCorrCount}",
        ];
    }

    /**
     * Compute comprehensive experience breakdown including post-terminal qualification service.
     */
    public static function calculateExperienceSummary(?BasicInfo $basicInfo, ?int $terminalYear = null): array
    {
        if (!$basicInfo) {
            return self::emptyExperienceSummary();
        }

        $experiences = $basicInfo->experiences ?? collect();

        // Determine terminal degree year if not provided
        if ($terminalYear === null) {
            $terminalDegree = self::findTerminalDegree($basicInfo);
            $terminalYear = $terminalDegree ? (int) $terminalDegree->year : null;
        }

        $totalDays = 0;
        $postTerminalDays = 0;
        $preTerminalDays = 0;
        $teachingDays = 0;
        $researchDays = 0;
        $adminDays = 0;
        $verifiedDays = 0;

        $terminalDate = $terminalYear ? Carbon::createFromDate($terminalYear, 12, 31) : null;

        foreach ($experiences as $exp) {
            if (!$exp->start_date) {
                continue;
            }

            $start = Carbon::parse($exp->start_date);
            $end = $exp->end_date ? Carbon::parse($exp->end_date) : Carbon::now();

            if ($end->lt($start)) {
                continue;
            }

            $days = $start->diffInDays($end);
            $totalDays += $days;

            if ($exp->is_verified) {
                $verifiedDays += $days;
            }

            $type = strtolower(trim($exp->experience_type ?? ''));
            if (str_contains($type, 'teach')) {
                $teachingDays += $days;
            } elseif (str_contains($type, 'research')) {
                $researchDays += $days;
            } else {
                $adminDays += $days;
            }

            // Post-Terminal Calculation
            if ($terminalDate) {
                if ($start->gte($terminalDate)) {
                    // Entire experience started after terminal degree
                    $postTerminalDays += $days;
                } elseif ($end->lte($terminalDate)) {
                    // Entire experience ended before terminal degree
                    $preTerminalDays += $days;
                } else {
                    // Experience spans across terminal degree completion date
                    $preTerminalDays += $start->diffInDays($terminalDate);
                    $postTerminalDays += $terminalDate->diffInDays($end);
                }
            } else {
                $preTerminalDays += $days;
            }
        }

        $totalYears = round($totalDays / 365.25, 1);
        $postTerminalYears = round($postTerminalDays / 365.25, 1);
        $preTerminalYears = round($preTerminalDays / 365.25, 1);
        $teachingYears = round($teachingDays / 365.25, 1);
        $researchYears = round($researchDays / 365.25, 1);

        return [
            'total_days' => $totalDays,
            'total_years' => $totalYears,
            'total_formatted' => self::formatDaysToYearsMonths($totalDays),
            'terminal_year' => $terminalYear,
            'post_terminal_years' => $postTerminalYears,
            'post_terminal_formatted' => self::formatDaysToYearsMonths($postTerminalDays),
            'pre_terminal_years' => $preTerminalYears,
            'pre_terminal_formatted' => self::formatDaysToYearsMonths($preTerminalDays),
            'teaching_years' => $teachingYears,
            'research_years' => $researchYears,
            'verified_years' => round($verifiedDays / 365.25, 1),
            'summary_string' => "Total: {$totalYears} Years (Post-Terminal: {$postTerminalYears} Yrs, Pre-Terminal: {$preTerminalYears} Yrs)",
        ];
    }

    /**
     * Find highest / terminal degree for candidate.
     */
    public static function findTerminalDegree(?BasicInfo $basicInfo): ?AcademicDegree
    {
        if (!$basicInfo) return null;

        $degrees = $basicInfo->degrees ?? collect();
        if ($degrees->isEmpty()) return null;

        // Hierarchy priority: PhD/Terminal > MS/MPhil > Master (16) > Bachelor (16)
        $priorityOrder = [
            'Ph.D. / Terminal Degree' => 10,
            'PhD' => 10,
            'Post Doctorate' => 11,
            'MS / M.Phil (18 Years)' => 8,
            'MPhil' => 8,
            'MS' => 8,
            'Master (16 Years) / MA / MSc' => 6,
            'Masters' => 6,
            'Bachelor (16 Years) / BS / Professional' => 4,
            'Bachelors' => 4,
        ];

        return $degrees->sortByDesc(function ($deg) use ($priorityOrder) {
            $level = trim($deg->degree_level ?? '');
            $score = $priorityOrder[$level] ?? 1;
            return ($score * 10000) + (int) ($deg->year ?? 0);
        })->first();
    }

    /**
     * Evaluate candidate statutory compliance against HEC benchmarks for target campaign rank.
     */
    public static function evaluateCandidateCompliance(?Application $application): array
    {
        if (!$application) {
            return [
                'is_compliant' => false,
                'target_rank' => 'Unknown',
                'checks' => [],
                'deficiencies' => ['No application dossier record provided.'],
                'summary_text' => 'Application data not loaded.',
                'publication_metrics' => self::emptyPublicationSummary(),
                'experience_metrics' => self::emptyExperienceSummary(),
            ];
        }

        $basicInfo = $application->user?->basicInfo;
        $campaign = $application->campaign;
        $title = strtolower($campaign?->title ?? '');

        // Detect statutory academic post rank
        $rank = self::detectTargetRank($title);

        $terminalDegree = self::findTerminalDegree($basicInfo);
        $terminalYear = $terminalDegree ? (int) $terminalDegree->year : null;

        $pubMetrics = self::calculatePublicationsSummary($basicInfo);
        $expMetrics = self::calculateExperienceSummary($basicInfo, $terminalYear);

        $checks = [];
        $deficiencies = [];

        $hasPhd = false;
        $hasMPhil = false;
        if ($basicInfo && $basicInfo->degrees) {
            foreach ($basicInfo->degrees as $deg) {
                $lvl = strtolower($deg->degree_level ?? '');
                $degTitle = strtolower($deg->degree_title ?? '');
                if (str_contains($lvl, 'ph.d') || str_contains($lvl, 'phd') || str_contains($degTitle, 'ph.d') || str_contains($degTitle, 'phd') || str_contains($lvl, 'doctorate')) {
                    $hasPhd = true;
                }
                if (str_contains($lvl, 'm.phil') || str_contains($lvl, 'mphil') || str_contains($lvl, '18 years') || str_contains($degTitle, 'm.phil') || str_contains($degTitle, 'ms ')) {
                    $hasMPhil = true;
                }
            }
        }

        switch ($rank) {
            case 'PROFESSOR':
                // Criteria: PhD + 15 years teaching/research OR 10 years post-PhD + 15 HEC publications (with min 5 in last 5 years)
                // 1. Terminal Degree
                if ($hasPhd) {
                    $checks[] = ['label' => 'Terminal Qualification', 'status' => 'PASS', 'detail' => 'Ph.D. degree held in relevant field'];
                } else {
                    $checks[] = ['label' => 'Terminal Qualification', 'status' => 'FAIL', 'detail' => 'Ph.D. degree in relevant field is mandatory for Professor'];
                    $deficiencies[] = 'Missing mandatory Ph.D. degree requirement for Professor post.';
                }

                // 2. Experience: 15 Years total OR 10 Years post-PhD
                $postPhd = $expMetrics['post_terminal_years'];
                $totalExp = $expMetrics['total_years'];
                if ($postPhd >= 10.0 || $totalExp >= 15.0) {
                    $checks[] = ['label' => 'Teaching/Research Experience', 'status' => 'PASS', 'detail' => "{$postPhd} yrs post-PhD ({$totalExp} yrs total) meets requirement (Min 10 post-PhD or 15 total)"];
                } else {
                    $checks[] = ['label' => 'Teaching/Research Experience', 'status' => 'FAIL', 'detail' => "{$postPhd} yrs post-PhD ({$totalExp} yrs total) is below threshold (Min 10 post-PhD or 15 total)"];
                    $deficiencies[] = "Insufficient experience: Candidate possesses {$postPhd} yrs post-PhD ({$totalExp} yrs total) vs 10 yrs post-PhD / 15 yrs total required.";
                }

                // 3. Publications: Min 15 HEC recognized with min 5 in last 5 years
                $hecPubs = $pubMetrics['hec_recognized_total'];
                $recentPubs = $pubMetrics['last_5_years'];
                if ($hecPubs >= 15 && $recentPubs >= 5) {
                    $checks[] = ['label' => 'HEC Research Publications', 'status' => 'PASS', 'detail' => "{$hecPubs} HEC recognized papers ({$recentPubs} in last 5 years) meets quota (Min 15 total, 5 recent)"];
                } else {
                    $detail = "{$hecPubs}/15 HEC recognized papers, {$recentPubs}/5 in last 5 years";
                    $checks[] = ['label' => 'HEC Research Publications', 'status' => 'FAIL', 'detail' => $detail];
                    $deficiencies[] = "Publication deficit: {$detail} for Professorship.";
                }
                break;

            case 'ASSOCIATE_PROFESSOR':
                // Criteria: PhD + 10 years teaching/research OR 5 years post-PhD + 10 HEC publications (with min 4 in last 5 years)
                if ($hasPhd) {
                    $checks[] = ['label' => 'Terminal Qualification', 'status' => 'PASS', 'detail' => 'Ph.D. degree held in relevant field'];
                } else {
                    $checks[] = ['label' => 'Terminal Qualification', 'status' => 'FAIL', 'detail' => 'Ph.D. degree is mandatory for Associate Professor'];
                    $deficiencies[] = 'Missing mandatory Ph.D. degree requirement for Associate Professor post.';
                }

                $postPhd = $expMetrics['post_terminal_years'];
                $totalExp = $expMetrics['total_years'];
                if ($postPhd >= 5.0 || $totalExp >= 10.0) {
                    $checks[] = ['label' => 'Teaching/Research Experience', 'status' => 'PASS', 'detail' => "{$postPhd} yrs post-PhD ({$totalExp} yrs total) meets requirement (Min 5 post-PhD or 10 total)"];
                } else {
                    $checks[] = ['label' => 'Teaching/Research Experience', 'status' => 'FAIL', 'detail' => "{$postPhd} yrs post-PhD ({$totalExp} yrs total) is below threshold (Min 5 post-PhD or 10 total)"];
                    $deficiencies[] = "Insufficient experience: Candidate possesses {$postPhd} yrs post-PhD ({$totalExp} yrs total) vs 5 yrs post-PhD / 10 yrs total required.";
                }

                $hecPubs = $pubMetrics['hec_recognized_total'];
                $recentPubs = $pubMetrics['last_5_years'];
                if ($hecPubs >= 10 && $recentPubs >= 4) {
                    $checks[] = ['label' => 'HEC Research Publications', 'status' => 'PASS', 'detail' => "{$hecPubs} HEC recognized papers ({$recentPubs} in last 5 years) meets quota (Min 10 total, 4 recent)"];
                } else {
                    $detail = "{$hecPubs}/10 HEC recognized papers, {$recentPubs}/4 in last 5 years";
                    $checks[] = ['label' => 'HEC Research Publications', 'status' => 'FAIL', 'detail' => $detail];
                    $deficiencies[] = "Publication deficit: {$detail} for Associate Professor post.";
                }
                break;

            case 'ASSISTANT_PROFESSOR':
                // Criteria: PhD in relevant field (no experience required) OR Master's (foreign) / MPhil with 4 years experience
                if ($hasPhd) {
                    $checks[] = ['label' => 'Terminal Qualification (PhD)', 'status' => 'PASS', 'detail' => 'Ph.D. held (Eligible without additional experience)'];
                    $checks[] = ['label' => 'HEC Publications', 'status' => 'PASS', 'detail' => "{$pubMetrics['hec_recognized_total']} research publications on record"];
                } elseif ($hasMPhil) {
                    $checks[] = ['label' => 'Terminal Qualification (MPhil/MS)', 'status' => 'PASS', 'detail' => 'MS / M.Phil (18 Years) held'];
                    if ($expMetrics['total_years'] >= 4.0) {
                        $checks[] = ['label' => 'Teaching/Research Experience', 'status' => 'PASS', 'detail' => "{$expMetrics['total_years']} yrs experience meets 4-year requirement for non-PhD"];
                    } else {
                        $checks[] = ['label' => 'Teaching/Research Experience', 'status' => 'FAIL', 'detail' => "{$expMetrics['total_years']} yrs experience is below 4-year requirement for non-PhD"];
                        $deficiencies[] = "Insufficient experience: 4 years teaching/research required for Master's/MPhil candidate ({$expMetrics['total_years']} yrs provided).";
                    }
                } else {
                    $checks[] = ['label' => 'Minimum Qualification', 'status' => 'FAIL', 'detail' => 'Ph.D. or 18-Year MS/MPhil in relevant field required'];
                    $deficiencies[] = 'Candidate does not possess mandatory Ph.D. or 18-Year M.Phil qualification for Assistant Professor post.';
                }
                break;

            case 'LECTURER':
                // Criteria: 18-Year MS/MPhil in relevant subject (no 3rd division in career)
                if ($hasPhd || $hasMPhil) {
                    $checks[] = ['label' => 'Minimum Qualification', 'status' => 'PASS', 'detail' => '18-Year MS/M.Phil or higher terminal degree verified'];
                } else {
                    $checks[] = ['label' => 'Minimum Qualification', 'status' => 'FAIL', 'detail' => 'Minimum 18-Year MS / M.Phil degree required for Lecturer post'];
                    $deficiencies[] = 'Missing minimum 18-Year MS / M.Phil qualification requirement.';
                }
                $checks[] = ['label' => 'Teaching Experience', 'status' => 'PASS', 'detail' => "{$expMetrics['total_years']} yrs experience on record (Not statutory prerequisite for Lecturer)"];
                break;

            default:
                // Administrative / Officer Positions
                $checks[] = ['label' => 'Credentials Audit', 'status' => 'PASS', 'detail' => "{$expMetrics['total_years']} yrs total experience, {$pubMetrics['total']} publications logged"];
                break;
        }

        $isCompliant = empty($deficiencies);

        // Generate ready-to-copy statutory remarks text
        $summaryParts = [];
        $summaryParts[] = "HEC STATUTORY SCRUTINY AUDIT ({$rank}):";
        $summaryParts[] = "• Qualification: " . ($terminalDegree ? "{$terminalDegree->degree_level} - {$terminalDegree->degree_title} ({$terminalDegree->year})" : "No Degree Logged");
        $summaryParts[] = "• Service Records: Total {$expMetrics['total_years']} Yrs | Post-Terminal Service: {$expMetrics['post_terminal_years']} Yrs (Teaching: {$expMetrics['teaching_years']} Yrs)";
        $summaryParts[] = "• Research Papers: {$pubMetrics['hec_recognized_total']} HEC HJRS (W: {$pubMetrics['w_category']}, X: {$pubMetrics['x_category']}, Y: {$pubMetrics['y_category']}) | 5-Year Recent: {$pubMetrics['last_5_years']} | 1st/Corr: {$pubMetrics['first_or_corr_authors']}";
        if ($isCompliant) {
            $summaryParts[] = "• Recommendation: COMPLIANT WITH STATUTORY BENCHMARKS. Cleared for Selection Board.";
        } else {
            $summaryParts[] = "• Recommendation: DEFICIENT. Deficiencies: " . implode('; ', $deficiencies);
        }
        $statutoryRemarks = implode("\n", $summaryParts);

        return [
            'is_compliant' => $isCompliant,
            'target_rank' => $rank,
            'target_title' => $campaign?->title ?? 'Position',
            'terminal_degree' => $terminalDegree,
            'checks' => $checks,
            'deficiencies' => $deficiencies,
            'summary_text' => $statutoryRemarks,
            'publication_metrics' => $pubMetrics,
            'experience_metrics' => $expMetrics,
        ];
    }

    /**
     * Detect academic rank from position title.
     */
    protected static function detectTargetRank(string $title): string
    {
        $t = strtolower($title);
        if (str_contains($t, 'associate professor')) {
            return 'ASSOCIATE_PROFESSOR';
        }
        if (str_contains($t, 'assistant professor')) {
            return 'ASSISTANT_PROFESSOR';
        }
        if (str_contains($t, 'professor') && !str_contains($t, 'assistant') && !str_contains($t, 'associate')) {
            return 'PROFESSOR';
        }
        if (str_contains($t, 'lecturer')) {
            return 'LECTURER';
        }
        return 'OFFICER';
    }

    protected static function formatDaysToYearsMonths(int $totalDays): string
    {
        if ($totalDays <= 0) return '0 Months';
        $years = (int) floor($totalDays / 365.25);
        $remainingDays = $totalDays - ($years * 365.25);
        $months = (int) round($remainingDays / 30.44);

        if ($years > 0 && $months > 0) {
            return "{$years} Yrs, {$months} Mos";
        } elseif ($years > 0) {
            return "{$years} Years";
        } else {
            return max(1, $months) . " Months";
        }
    }

    protected static function emptyPublicationSummary(): array
    {
        return [
            'total' => 0,
            'verified' => 0,
            'w_category' => 0,
            'x_category' => 0,
            'y_category' => 0,
            'z_category' => 0,
            'other_category' => 0,
            'hec_recognized_total' => 0,
            'last_5_years' => 0,
            'first_authors' => 0,
            'corresponding_authors' => 0,
            'first_or_corr_authors' => 0,
            'total_impact_factor' => 0.0,
            'avg_impact_factor' => 0.0,
            'summary_string' => 'No publications on record',
        ];
    }

    protected static function emptyExperienceSummary(): array
    {
        return [
            'total_days' => 0,
            'total_years' => 0.0,
            'total_formatted' => '0 Months',
            'terminal_year' => null,
            'post_terminal_years' => 0.0,
            'post_terminal_formatted' => '0 Months',
            'pre_terminal_years' => 0.0,
            'pre_terminal_formatted' => '0 Months',
            'teaching_years' => 0.0,
            'research_years' => 0.0,
            'verified_years' => 0.0,
            'summary_string' => 'No experience on record',
        ];
    }
}
