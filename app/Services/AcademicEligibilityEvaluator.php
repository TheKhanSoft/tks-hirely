<?php

namespace App\Services;

use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\AcademicDegree;
use App\Models\SystemSetting;
use Illuminate\Support\Collection;

class AcademicEligibilityEvaluator
{
    /**
     * Degree level hierarchy ranking (lowest to highest).
     */
    public const DEGREE_RANK = [
        'Matric / SSC / O-Level' => 1,
        'Matric' => 1,
        'SSC' => 1,
        'O-Level' => 1,
        'Intermediate / HSSC / A-Level' => 2,
        'Intermediate' => 2,
        'HSSC' => 2,
        'A-Level' => 2,
        'Bachelor (14 Years)' => 3,
        'Bachelor (16 Years) / BS / Professional' => 4,
        'Bachelors' => 4,
        'Master (16 Years) / MA / MSc' => 5,
        'Masters' => 5,
        'MS / M.Phil (18 Years)' => 6,
        'MPhil' => 6,
        'MS' => 6,
        'Ph.D. / Terminal Degree' => 7,
        'PhD' => 7,
        'Post Doctorate' => 8,
    ];

    /**
     * Default global academic eligibility criteria.
     */
    public static function getGlobalCriteria(): array
    {
        $setting = SystemSetting::get('academic_eligibility_criteria', null);
        if ($setting && is_array($setting)) {
            return $setting;
        }

        return [
            'disallow_third_division' => false,
            'max_allowed_third_divisions' => null, // e.g. 0 = none, 1 = exactly one allowed
            'disallow_third_in_terminal' => true,
            'disallow_two_second_divisions' => false, // 2 second divisions not allowed
            'max_allowed_second_divisions' => null, // e.g. 1 = max 1 allowed
            'disallow_second_in_terminal' => false,
            'min_marks_percentage' => null, // e.g. 60.0
            'min_terminal_percentage' => null, // e.g. 60.0
            'min_terminal_cgpa' => null, // e.g. 3.0
            'min_cgpa' => null,
            'scope' => 'GLOBAL',
            'rule_notes' => 'Standard statutory qualifications and division policy',
        ];
    }

    /**
     * Save global academic eligibility criteria in SystemSetting.
     */
    public static function setGlobalCriteria(array $criteria): void
    {
        SystemSetting::set('academic_eligibility_criteria', $criteria, group: 'academic_criteria', type: 'json', label: 'Global Academic Division & Marks Benchmarks');
    }

    /**
     * Normalize division string to standardized enum: 'FIRST', 'SECOND', 'THIRD', or 'OTHER'.
     */
    public static function normalizeDivision(?string $rawDivision, ?float $gpa = null, ?float $scale = null): string
    {
        $raw = strtolower(trim($rawDivision ?? ''));

        if (str_contains($raw, 'first') || str_contains($raw, '1st') || str_contains($raw, 'a+') || str_contains($raw, 'a grade')) {
            return 'FIRST';
        }

        if (str_contains($raw, 'second') || str_contains($raw, '2nd') || str_contains($raw, 'b grade')) {
            return 'SECOND';
        }

        if (str_contains($raw, 'third') || str_contains($raw, '3rd') || str_contains($raw, 'c grade')) {
            return 'THIRD';
        }

        // If division is missing or ambiguous, compute from percentage or CGPA if available
        if ($gpa !== null && $scale !== null && $scale > 0) {
            if ($scale <= 5.0) {
                // GPA scale (e.g. 4.0 or 5.0)
                $ratio = $gpa / $scale;
                if ($ratio >= 0.75 || $gpa >= 3.0) return 'FIRST';
                if ($ratio >= 0.50 || $gpa >= 2.0) return 'SECOND';
                return 'THIRD';
            } else {
                // Marks scale (e.g. out of 850, 1100, 100)
                $percentage = ($gpa / $scale) * 100;
                if ($percentage >= 60.0) return 'FIRST';
                if ($percentage >= 45.0) return 'SECOND';
                return 'THIRD';
            }
        }

        return 'FIRST'; // Default baseline if not determined
    }

    /**
     * Calculate score percentage for a degree.
     */
    public static function calculatePercentage(AcademicDegree $degree): ?float
    {
        $gpa = (float)($degree->gpa ?? 0);
        $scale = (float)($degree->scale ?? 0);

        if ($scale <= 0 || $gpa <= 0) {
            return null;
        }

        if ($scale <= 5.0) {
            // Convert GPA ratio to approximate percentage: (GPA / Scale) * 100
            return round(($gpa / $scale) * 100, 2);
        }

        // Marks (e.g. 890 / 1100)
        return round(($gpa / $scale) * 100, 2);
    }

    /**
     * Identify the terminal (highest level) degree from a candidate's qualification collection.
     */
    public static function findTerminalDegree(Collection $degrees): ?AcademicDegree
    {
        if ($degrees->isEmpty()) {
            return null;
        }

        return $degrees->sortByDesc(function ($deg) {
            $lvl = $deg->degree_level ?? '';
            return self::DEGREE_RANK[$lvl] ?? 0;
        })->first();
    }

    /**
     * Comprehensive evaluation of candidate's academic qualifications against division & marks criteria.
     *
     * @param BasicInfo|mixed $basicInfo
     * @param Campaign|null $campaign
     * @param array|null $customCriteria
     * @return array
     */
    public static function evaluateCandidate($basicInfo, ?Campaign $campaign = null, ?array $customCriteria = null): array
    {
        if (!$basicInfo) {
            return [
                'is_eligible' => false,
                'violations' => ['Candidate has not registered academic basic profile.'],
                'has_violations' => true,
                'total_degrees' => 0,
                'first_division_count' => 0,
                'second_division_count' => 0,
                'third_division_count' => 0,
                'terminal_degree' => null,
                'criteria_applied' => [],
                'badge' => [
                    'label' => 'INCOMPLETE',
                    'color' => '#ef4444',
                    'text' => 'No Academic Records Found',
                ],
            ];
        }

        // Load degrees
        $degrees = $basicInfo->degrees ?? collect();

        // Resolve applicable criteria
        if ($customCriteria !== null) {
            $criteria = $customCriteria;
            $criteriaSource = 'CUSTOM';
        } elseif ($campaign !== null) {
            $criteria = $campaign->getEffectiveAcademicCriteria();
            $criteriaSource = 'CAMPAIGN';
        } else {
            $criteria = self::getGlobalCriteria();
            $criteriaSource = 'GLOBAL';
        }

        // Division counts
        $firstCount = 0;
        $secondCount = 0;
        $thirdCount = 0;
        $degreeDetails = [];

        foreach ($degrees as $deg) {
            $normDiv = self::normalizeDivision($deg->division, (float)$deg->gpa, (float)$deg->scale);
            $pct = self::calculatePercentage($deg);

            if ($normDiv === 'FIRST') $firstCount++;
            elseif ($normDiv === 'SECOND') $secondCount++;
            elseif ($normDiv === 'THIRD') $thirdCount++;

            $degreeDetails[] = [
                'id' => $deg->id,
                'level' => $deg->degree_level,
                'title' => $deg->degree_title,
                'division_raw' => $deg->division,
                'division_normalized' => $normDiv,
                'gpa' => (float)$deg->gpa,
                'scale' => $deg->scale,
                'percentage' => $pct,
                'year' => $deg->year,
            ];
        }

        $terminal = self::findTerminalDegree($degrees);
        $terminalNormDiv = $terminal ? self::normalizeDivision($terminal->division, (float)$terminal->gpa, (float)$terminal->scale) : null;
        $terminalPct = $terminal ? self::calculatePercentage($terminal) : null;
        $terminalGpa = $terminal ? (float)$terminal->gpa : null;

        $violations = [];

        // -------------------------------------------------------------
        // RULE 1: Third Division Restrictions
        // -------------------------------------------------------------
        $disallowThird = !empty($criteria['disallow_third_division']);
        $maxThird = isset($criteria['max_allowed_third_divisions']) && $criteria['max_allowed_third_divisions'] !== '' && $criteria['max_allowed_third_divisions'] !== null
            ? (int)$criteria['max_allowed_third_divisions']
            : null;

        if ($disallowThird || $maxThird === 0) {
            if ($thirdCount > 0) {
                $violations[] = "3rd Division is not permitted in academic career. Candidate has {$thirdCount} third division(s).";
            }
        } elseif ($maxThird !== null && $thirdCount > $maxThird) {
            $violations[] = "Maximum of {$maxThird} third division(s) permitted. Candidate has {$thirdCount} third divisions.";
        }

        // Terminal degree cannot be 3rd division
        if (!empty($criteria['disallow_third_in_terminal']) && $terminalNormDiv === 'THIRD') {
            $termTitle = $terminal->degree_title ?? $terminal->degree_level;
            $violations[] = "3rd Division is strictly prohibited in terminal/highest degree ('{$termTitle}').";
        }

        // -------------------------------------------------------------
        // RULE 2: Second Division Restrictions
        // -------------------------------------------------------------
        $disallowTwoSecond = !empty($criteria['disallow_two_second_divisions']);
        $maxSecond = isset($criteria['max_allowed_second_divisions']) && $criteria['max_allowed_second_divisions'] !== '' && $criteria['max_allowed_second_divisions'] !== null
            ? (int)$criteria['max_allowed_second_divisions']
            : null;

        if ($disallowTwoSecond || $maxSecond === 1) {
            if ($secondCount >= 2) {
                $violations[] = "Two or more 2nd divisions are not allowed. Candidate has {$secondCount} second divisions (maximum 1 allowed).";
            }
        } elseif ($maxSecond !== null && $secondCount > $maxSecond) {
            $violations[] = "Maximum of {$maxSecond} second division(s) permitted. Candidate has {$secondCount} second divisions.";
        }

        // Terminal degree cannot be 2nd division
        if (!empty($criteria['disallow_second_in_terminal']) && $terminalNormDiv === 'SECOND') {
            $termTitle = $terminal->degree_title ?? $terminal->degree_level;
            $violations[] = "2nd Division is not permitted in terminal/highest degree ('{$termTitle}'). Minimum 1st Division or equivalent CGPA required.";
        }

        // -------------------------------------------------------------
        // RULE 3: Percentage / Marks Restrictions ("xx out of nn marks not allowed")
        // -------------------------------------------------------------
        if (!empty($criteria['min_marks_percentage'])) {
            $minPct = (float)$criteria['min_marks_percentage'];
            foreach ($degreeDetails as $degInfo) {
                if ($degInfo['percentage'] !== null && $degInfo['percentage'] < $minPct) {
                    $violations[] = "Minimum {$minPct}% marks required in all qualifications. Degree '{$degInfo['title']}' scored {$degInfo['percentage']}% ({$degInfo['gpa']}/{$degInfo['scale']}).";
                }
            }
        }

        if (!empty($criteria['min_terminal_percentage']) && $terminalPct !== null) {
            $minTermPct = (float)$criteria['min_terminal_percentage'];
            if ($terminalPct < $minTermPct) {
                $termTitle = $terminal->degree_title ?? $terminal->degree_level;
                $violations[] = "Terminal degree ('{$termTitle}') requires minimum {$minTermPct}% marks. Candidate scored {$terminalPct}%.";
            }
        }

        // -------------------------------------------------------------
        // RULE 4: Minimum Terminal CGPA
        // -------------------------------------------------------------
        if (!empty($criteria['min_terminal_cgpa']) && $terminalGpa !== null) {
            $minGpa = (float)$criteria['min_terminal_cgpa'];
            $termScale = (float)($terminal->scale ?? 4.0);
            if ($termScale <= 5.0 && $terminalGpa < $minGpa) {
                $termTitle = $terminal->degree_title ?? $terminal->degree_level;
                $violations[] = "Terminal degree ('{$termTitle}') requires minimum CGPA of {$minGpa}. Candidate has {$terminalGpa} / {$termScale}.";
            }
        }

        $isEligible = count($violations) === 0;

        return [
            'is_eligible' => $isEligible,
            'violations' => $violations,
            'has_violations' => count($violations) > 0,
            'total_degrees' => $degrees->count(),
            'first_division_count' => $firstCount,
            'second_division_count' => $secondCount,
            'third_division_count' => $thirdCount,
            'terminal_degree' => $terminal ? [
                'level' => $terminal->degree_level,
                'title' => $terminal->degree_title,
                'division_raw' => $terminal->division,
                'division_normalized' => $terminalNormDiv,
                'gpa' => (float)$terminal->gpa,
                'scale' => $terminal->scale,
                'percentage' => $terminalPct,
            ] : null,
            'degree_details' => $degreeDetails,
            'criteria_source' => $criteriaSource,
            'criteria_applied' => $criteria,
            'badge' => $isEligible ? [
                'label' => 'ACADEMICALLY ELIGIBLE',
                'color' => '#10b981',
                'bg' => 'rgba(16, 185, 129, 0.15)',
                'border' => 'rgba(16, 185, 129, 0.35)',
                'text' => 'Meets all statutory division & marks criteria.',
            ] : [
                'label' => 'CRITERIA VIOLATION',
                'color' => '#ef4444',
                'bg' => 'rgba(239, 68, 68, 0.15)',
                'border' => 'rgba(239, 68, 68, 0.35)',
                'text' => implode(' | ', $violations),
            ],
        ];
    }
}
