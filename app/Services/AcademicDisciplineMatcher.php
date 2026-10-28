<?php

namespace App\Services;

use App\Models\ReviewerProfile;
use Illuminate\Database\Eloquent\Builder;

class AcademicDisciplineMatcher
{
    /**
     * Academic Domain Taxonomy & Allied Discipline Clusters.
     * Maps central academic domains to synonyms, subfields, and related disciplines.
     */
    protected static array $domainClusters = [
        'computing' => [
            'name' => 'Computing, AI, Informatics & Software',
            'keywords' => [
                'computer', 'computing', 'software', 'information technology', 
                'artificial intelligence', 'data science', 'cyber', 'cybersecurity', 
                'cryptography', 'network', 'networks', 'robotics', 'informatics', 
                'machine learning', 'deep learning', 'algorithms', 'embedded', 'hardware',
                'vlsi', 'computer engineering', 'telecommunication'
            ],
            'aliases' => [
                'Computer Science',
                'Computer Science & Robotics',
                'Information Technology & Networks',
                'Computer Engineering',
                'Artificial Intelligence & Systems',
                'Cybersecurity & Cryptography',
                'Software Engineering',
                'Data Science & Analytics',
                'Information Systems',
                'Computational Science',
            ],
        ],
        'electrical_engineering' => [
            'name' => 'Electrical, Electronics & Telecom',
            'keywords' => [
                'electrical', 'electronic', 'electronics', 'telecom', 'telecommunication', 
                'power systems', 'mechatronics', 'control systems', 'signal processing', 'microelectronics'
            ],
            'aliases' => [
                'Electrical Engineering',
                'Electronic Engineering',
                'Telecommunication Engineering',
                'Power Systems Engineering',
                'Mechatronics & Control',
            ],
        ],
        'mechanical_civil' => [
            'name' => 'Mechanical, Civil & Aerospace Engineering',
            'keywords' => [
                'mechanical', 'civil', 'aerospace', 'structural', 'thermal', 'fluid', 
                'materials engineering', 'manufacturing', 'transportation', 'environmental engineering'
            ],
            'aliases' => [
                'Mechanical Engineering',
                'Civil Engineering',
                'Aerospace Engineering',
                'Materials & Manufacturing',
            ],
        ],
        'business_management' => [
            'name' => 'Business, Management & Economics',
            'keywords' => [
                'business', 'management', 'finance', 'accounting', 'marketing', 
                'human resource', 'economics', 'commerce', 'banking', 'supply chain',
                'entrepreneurship', 'public administration'
            ],
            'aliases' => [
                'Management Sciences',
                'Business Administration',
                'Finance & Banking',
                'Economics',
                'Accounting & Finance',
                'Marketing & Strategy',
                'Public Administration',
            ],
        ],
        'biological_sciences' => [
            'name' => 'Life & Biological Sciences',
            'keywords' => [
                'biology', 'biotechnology', 'bioinformatics', 'microbiology', 
                'biochemistry', 'genetics', 'molecular', 'botany', 'zoology', 
                'immunology', 'pharmacy', 'pharmacology'
            ],
            'aliases' => [
                'Biotechnology',
                'Bioinformatics',
                'Microbiology',
                'Biochemistry',
                'Molecular Biology',
                'Pharmacy',
            ],
        ],
        'chemical_sciences' => [
            'name' => 'Chemical & Physical Sciences',
            'keywords' => [
                'chemistry', 'chemical', 'physics', 'applied physics', 'materials science', 
                'nanotechnology', 'polymer'
            ],
            'aliases' => [
                'Chemistry',
                'Chemical Engineering',
                'Physics',
                'Applied Physics',
                'Materials Science',
            ],
        ],
        'mathematics_statistics' => [
            'name' => 'Mathematics & Statistics',
            'keywords' => [
                'mathematics', 'math', 'statistics', 'applied mathematics', 
                'actuarial', 'quantitative'
            ],
            'aliases' => [
                'Mathematics',
                'Applied Mathematics',
                'Statistics',
                'Data Analytics',
            ],
        ],
        'humanities_social' => [
            'name' => 'Humanities & Social Sciences',
            'keywords' => [
                'english', 'literature', 'linguistics', 'sociology', 'psychology', 
                'international relations', 'political science', 'history', 'law', 
                'education', 'media', 'journalism', 'islamic studies', 'philosophy'
            ],
            'aliases' => [
                'English & Literature',
                'Linguistics',
                'Psychology',
                'Sociology',
                'Political Science',
                'Law & Legal Studies',
                'Media Studies',
            ],
        ],
    ];

    /**
     * Clean and normalize department strings by removing administrative prefixes.
     * E.g. "Department of Computer Science" -> "Computer Science"
     */
    public static function normalizeDepartment(string $department): string
    {
        $cleaned = trim($department);
        // Remove prefixes like "Department of", "Faculty of", "Centre for", "School of", "Institute of", "Division of"
        $cleaned = preg_replace('/^(Department|Faculty|School|Division|Institute|Center|Centre|Dept\.?)\s+(of|for)?\s+/i', '', $cleaned);
        // Remove suffixes like "Department", "Division"
        $cleaned = preg_replace('/\s+(Department|Division|Faculty|School)$/i', '', $cleaned);
        return trim($cleaned);
    }

    /**
     * Get aliases from matched domain clusters.
     *
     * @return string[]
     */
    public static function getClusterAliases(string $department, string $campaignTitle = ''): array
    {
        $normalizedDept = self::normalizeDepartment($department);
        $fullText = strtolower($normalizedDept . ' ' . $campaignTitle);
        $aliases = [];

        foreach (self::$domainClusters as $cluster) {
            $clusterMatched = false;
            foreach ($cluster['keywords'] as $ck) {
                if (preg_match('/\b' . preg_quote($ck, '/') . '\b/i', $fullText)) {
                    $clusterMatched = true;
                    break;
                }
            }

            if ($clusterMatched && !empty($cluster['aliases'])) {
                $aliases = array_merge($aliases, $cluster['aliases']);
            }
        }

        return array_values(array_unique($aliases));
    }

    /**
     * Extract significant keywords from department and campaign title.
     *
     * @return string[]
     */
    public static function extractKeywords(string $department, string $campaignTitle = ''): array
    {
        $normalizedDept = self::normalizeDepartment($department);
        $fullText = strtolower($normalizedDept . ' ' . $campaignTitle);

        // Stopwords to discard
        $stopwords = [
            'and', 'the', 'for', 'with', 'professor', 'associate', 'assistant', 
            'lecturer', 'chair', 'postdoctoral', 'fellow', 'head', 'dean', 
            'officer', 'services', 'campus', 'unit', 'center', 'centre', 'dept',
            'department', 'faculty', 'school', 'institute', 'division'
        ];

        // Tokenize words (length >= 4 to avoid broad SQL LIKE substring traps)
        $rawTokens = preg_split('/[\s,\&\/\-]+/', $fullText);
        $tokens = [];
        foreach ($rawTokens as $t) {
            $t = trim($t);
            if (strlen($t) >= 4 && !in_array($t, $stopwords)) {
                $tokens[] = $t;
            }
        }

        // Determine if any predefined domain cluster matches
        $matchedClusterKeywords = [];
        foreach (self::$domainClusters as $cluster) {
            $clusterMatched = false;
            foreach ($cluster['keywords'] as $ck) {
                if (preg_match('/\b' . preg_quote($ck, '/') . '\b/i', $fullText)) {
                    $clusterMatched = true;
                    break;
                }
            }

            if ($clusterMatched) {
                $matchedClusterKeywords = array_merge($matchedClusterKeywords, $cluster['keywords']);
            }
        }

        return array_values(array_unique(array_merge($tokens, $matchedClusterKeywords)));
    }

    /**
     * Check if a reviewer matches the department/campaign keywords.
     */
    public static function matchesReviewer(ReviewerProfile $reviewer, string $department, string $campaignTitle = ''): bool
    {
        $keywords = self::extractKeywords($department, $campaignTitle);
        $reviewerText = strtolower($reviewer->subject . ' ' . ($reviewer->specialization ?? ''));

        foreach ($keywords as $kw) {
            if (strlen($kw) >= 4 && str_contains($reviewerText, $kw)) {
                return true;
            }
        }

        // Fallback: check normalized department substring
        $cleanDept = strtolower(self::normalizeDepartment($department));
        if (!empty($cleanDept) && (str_contains($reviewerText, $cleanDept) || str_contains($cleanDept, strtolower($reviewer->subject)))) {
            return true;
        }

        return false;
    }

    /**
     * Build an optimized query to fetch matching reviewers for a given candidacy.
     */
    public static function queryForCandidacy(
        string $department,
        string $campaignTitle = '',
        string $search = '',
        string $disciplineFilter = '',
        bool $filterByFieldOnly = true
    ): Builder {
        $query = ReviewerProfile::with(['user', 'reviews'])
            ->where('is_active', true)
            ->whereDoesntHave('user.roles', fn($rq) => $rq->where('name', 'super-admin'));

        // 1. Direct Discipline filter if explicitly selected
        if (!empty($disciplineFilter)) {
            $query->where('subject', $disciplineFilter);
        }

        // 2. Text Search if entered by user
        if (!empty($search)) {
            $s = '%' . trim($search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('university', 'like', $s)
                  ->orWhere('subject', 'like', $s)
                  ->orWhere('specialization', 'like', $s)
                  ->orWhere('country', 'like', $s);
            });
        }

        // 3. Smart Domain & Keyword Matching if filterByFieldOnly is enabled
        if ($filterByFieldOnly) {
            $cleanDept = self::normalizeDepartment($department);
            $aliases = self::getClusterAliases($department, $campaignTitle);
            $keywords = self::extractKeywords($department, $campaignTitle);

            $query->where(function ($q) use ($cleanDept, $aliases, $keywords) {
                // Check if reviewer subject matches clean department
                if (!empty($cleanDept)) {
                    $q->where('subject', 'like', '%' . $cleanDept . '%')
                      ->orWhere('specialization', 'like', '%' . $cleanDept . '%');
                }

                // Check cluster aliases (e.g. Computer Science & Robotics, Cybersecurity & Cryptography, etc.)
                if (!empty($aliases)) {
                    $q->orWhereIn('subject', $aliases);
                    foreach ($aliases as $alias) {
                        $q->orWhere('subject', 'like', '%' . $alias . '%');
                    }
                }

                // Check all domain & cluster keywords (>= 4 chars)
                foreach ($keywords as $kw) {
                    $kw = trim($kw);
                    if (strlen($kw) >= 4) {
                        $pattern = '%' . $kw . '%';
                        $q->orWhere('subject', 'like', $pattern)
                          ->orWhere('specialization', 'like', $pattern);
                    }
                }
            });
        }

        return $query->orderBy('name');
    }

    /**
     * Get all predefined domain clusters for reference.
     */
    public static function getDomainClusters(): array
    {
        return self::$domainClusters;
    }
}
