<?php

namespace App\Services;

use App\Models\Publication;
use App\Models\SystemSetting;
use Illuminate\Support\Str;

class HecHjrsJournalService
{
    /**
     * Recognized HEC Higher Education Journal Recognition System (HJRS) Directory.
     * Includes Category W (Top tier/Impact Factor), Category X (Mid tier), and Category Y (Standard).
     */
    protected static array $hjrsIndex = [
        // Category W (Highest Tier - Web of Science JCR / Top Quartiles)
        [
            'issn' => '0028-0836',
            'e_issn' => '1476-4687',
            'title' => 'Nature',
            'category' => 'W',
            'discipline' => 'Multidisciplinary Sciences',
            'publisher' => 'Nature Portfolio / Springer',
            'impact_factor' => '64.8',
            'percentile' => '99th',
            'is_predatory' => false,
        ],
        [
            'issn' => '0036-8075',
            'e_issn' => '1095-9203',
            'title' => 'Science',
            'category' => 'W',
            'discipline' => 'Multidisciplinary Sciences',
            'publisher' => 'AAAS',
            'impact_factor' => '56.9',
            'percentile' => '99th',
            'is_predatory' => false,
        ],
        [
            'issn' => '0140-6736',
            'e_issn' => '1474-547X',
            'title' => 'The Lancet',
            'category' => 'W',
            'discipline' => 'General Medicine',
            'publisher' => 'Elsevier',
            'impact_factor' => '168.9',
            'percentile' => '99th',
            'is_predatory' => false,
        ],
        [
            'issn' => '0018-9219',
            'e_issn' => '1558-2256',
            'title' => 'Proceedings of the IEEE',
            'category' => 'W',
            'discipline' => 'Electrical Engineering & Computer Science',
            'publisher' => 'IEEE',
            'impact_factor' => '20.6',
            'percentile' => '98th',
            'is_predatory' => false,
        ],
        [
            'issn' => '0360-0300',
            'e_issn' => '1557-7341',
            'title' => 'ACM Computing Surveys',
            'category' => 'W',
            'discipline' => 'Computer Science & Software',
            'publisher' => 'Association for Computing Machinery',
            'impact_factor' => '16.6',
            'percentile' => '97th',
            'is_predatory' => false,
        ],
        [
            'issn' => '1932-6203',
            'e_issn' => '1932-6203',
            'title' => 'PLOS ONE',
            'category' => 'W',
            'discipline' => 'Multidisciplinary Sciences',
            'publisher' => 'Public Library of Science',
            'impact_factor' => '3.7',
            'percentile' => '85th',
            'is_predatory' => false,
        ],
        [
            'issn' => '2073-4409',
            'e_issn' => '2073-4409',
            'title' => 'Cells',
            'category' => 'W',
            'discipline' => 'Cell Biology',
            'publisher' => 'MDPI',
            'impact_factor' => '6.0',
            'percentile' => '80th',
            'is_predatory' => false,
        ],

        // Category X (Mid Tier - HEC Recognized Peer Reviewed International & National)
        [
            'issn' => '0556-3321',
            'e_issn' => '2070-3368',
            'title' => 'Pakistan Journal of Botany',
            'category' => 'X',
            'discipline' => 'Plant Sciences & Agricultural Biology',
            'publisher' => 'Pakistan Botanical Society',
            'impact_factor' => '1.1',
            'percentile' => '65th',
            'is_predatory' => false,
        ],
        [
            'issn' => '0253-5106',
            'e_issn' => '2073-4077',
            'title' => 'Journal of the Chemical Society of Pakistan',
            'category' => 'X',
            'discipline' => 'Chemical Sciences',
            'publisher' => 'Chemical Society of Pakistan',
            'impact_factor' => '0.8',
            'percentile' => '55th',
            'is_predatory' => false,
        ],
        [
            'issn' => '1018-7081',
            'e_issn' => '2309-8694',
            'title' => 'The Journal of Animal & Plant Sciences',
            'category' => 'X',
            'discipline' => 'Veterinary & Animal Sciences',
            'publisher' => 'Pakistan Agricultural Scientists Forum',
            'impact_factor' => '0.7',
            'percentile' => '52nd',
            'is_predatory' => false,
        ],
        [
            'issn' => '1813-0968',
            'e_issn' => '2410-1397',
            'title' => 'International Journal of Agriculture & Biology',
            'category' => 'X',
            'discipline' => 'Agricultural Sciences',
            'publisher' => 'Friends Science Publishers',
            'impact_factor' => '0.8',
            'percentile' => '50th',
            'is_predatory' => false,
        ],
        [
            'issn' => '1560-8530',
            'e_issn' => '2074-9007',
            'title' => 'Mehran University Research Journal of Engineering & Technology',
            'category' => 'X',
            'discipline' => 'Engineering & Technology',
            'publisher' => 'Mehran UET Jamshoro',
            'impact_factor' => '0.6',
            'percentile' => '48th',
            'is_predatory' => false,
        ],

        // Category Y (Standard HEC Recognized Peer Reviewed Journals)
        [
            'issn' => '1012-7682',
            'e_issn' => '2415-007X',
            'title' => 'Pakistan Journal of Social Sciences',
            'category' => 'Y',
            'discipline' => 'Social Sciences',
            'publisher' => 'Bahauddin Zakariya University Multan',
            'impact_factor' => null,
            'percentile' => '35th',
            'is_predatory' => false,
        ],
        [
            'issn' => '1012-7046',
            'e_issn' => '2309-7841',
            'title' => 'Pakistan Journal of History and Culture',
            'category' => 'Y',
            'discipline' => 'Arts, Humanities & History',
            'publisher' => 'National Institute of Historical and Cultural Research (QAU)',
            'impact_factor' => null,
            'percentile' => '30th',
            'is_predatory' => false,
        ],
        [
            'issn' => '0254-9204',
            'e_issn' => '2521-4284',
            'title' => 'Pakistan Journal of Applied Economics',
            'category' => 'Y',
            'discipline' => 'Economics & Development Studies',
            'publisher' => 'Applied Economics Research Centre (AERC)',
            'impact_factor' => null,
            'percentile' => '32nd',
            'is_predatory' => false,
        ],
        [
            'issn' => '2070-254X',
            'e_issn' => '2616-9657',
            'title' => 'Journal of Business and Tourism',
            'category' => 'Y',
            'discipline' => 'Management & Tourism Sciences',
            'publisher' => 'Abdul Wali Khan University Mardan',
            'impact_factor' => null,
            'percentile' => '28th',
            'is_predatory' => false,
        ],
        [
            'issn' => '2222-1735',
            'e_issn' => '2307-8588',
            'title' => 'VFAST Transactions on Software Engineering',
            'category' => 'Y',
            'discipline' => 'Computer Science & Software',
            'publisher' => 'VFAST Research',
            'impact_factor' => null,
            'percentile' => '25th',
            'is_predatory' => false,
        ],
    ];

    /**
     * Blacklist of Known Predatory, Hijacked, Cloned, or Unaccredited Journals.
     */
    protected static array $predatoryBlacklist = [
        [
            'issn' => '1993-8233',
            'title' => 'African Journal of Business Management',
            'risk_level' => 'HIGH',
            'reason' => 'Identified as vanity predatory publisher on Beall\'s List; charges non-peer-reviewed APCs.',
        ],
        [
            'issn' => '2277-8179',
            'title' => 'International Journal of Scientific Research',
            'risk_level' => 'CRITICAL',
            'reason' => 'Known predatory paper mill with fictitious editorial board and fabricated peer review.',
        ],
        [
            'issn' => '1561-882X',
            'title' => 'Wulfenia Journal',
            'risk_level' => 'CRITICAL',
            'reason' => 'Known hijacked cloned journal; fraudulent website soliciting payments using legitimate journal name.',
        ],
        [
            'issn' => '0378-1844',
            'title' => 'Interciencia Journal',
            'risk_level' => 'CRITICAL',
            'reason' => 'Domain hijacking alert; counterfeit web portal collecting fraudulent publication fees.',
        ],
        [
            'issn' => '0039-7660',
            'title' => 'Sylwan',
            'risk_level' => 'HIGH',
            'reason' => 'Extensively cloned Polish forestry journal domain; fake English-language submissions.',
        ],
        [
            'issn' => '2039-2117',
            'title' => 'Mediterranean Journal of Social Sciences',
            'risk_level' => 'HIGH',
            'reason' => 'Delisted by HEC and Scopus due to irregular editorial and bulk commercial publishing practices.',
        ],
        [
            'issn' => '1450-216X',
            'title' => 'European Journal of Scientific Research',
            'risk_level' => 'HIGH',
            'reason' => 'Blacklisted by HEC for failing statutory verification and absence of blind peer scrutiny.',
        ],
    ];

    /**
     * Search and lookup journal credentials by ISSN, e-ISSN, or title query.
     */
    public static function lookup(string $query): array
    {
        $q = trim(strtolower($query));
        if ($q === '') {
            return [];
        }

        // Clean ISSN format (remove hyphens or spaces for normalized comparison)
        $cleanQ = preg_replace('/[^0-9a-zx]/i', '', $q);

        $results = [];

        // 1. Check HJRS Index
        foreach (self::$hjrsIndex as $item) {
            $issnClean = preg_replace('/[^0-9a-zx]/i', '', strtolower($item['issn'] ?? ''));
            $eIssnClean = preg_replace('/[^0-9a-zx]/i', '', strtolower($item['e_issn'] ?? ''));
            $titleClean = strtolower($item['title'] ?? '');

            if (
                (!empty($cleanQ) && ($issnClean === $cleanQ || $eIssnClean === $cleanQ)) ||
                str_contains($titleClean, $q) ||
                (!empty($item['publisher']) && str_contains(strtolower($item['publisher']), $q))
            ) {
                $results[] = array_merge($item, [
                    'source' => 'HEC_HJRS_VERIFIED',
                    'is_blacklisted' => false,
                ]);
            }
        }

        // 2. Check Predatory Blacklist
        foreach (self::$predatoryBlacklist as $bad) {
            $issnClean = preg_replace('/[^0-9a-zx]/i', '', strtolower($bad['issn'] ?? ''));
            $titleClean = strtolower($bad['title'] ?? '');

            if (
                (!empty($cleanQ) && $issnClean === $cleanQ) ||
                str_contains($titleClean, $q)
            ) {
                $results[] = [
                    'issn' => $bad['issn'],
                    'e_issn' => null,
                    'title' => $bad['title'],
                    'category' => 'PREDATORY_BLACKLISTED',
                    'discipline' => 'Unknown / Commercial Cloned',
                    'publisher' => 'Predatory / Unaccredited Entity',
                    'impact_factor' => '0.0',
                    'percentile' => 'None',
                    'is_predatory' => true,
                    'risk_level' => $bad['risk_level'],
                    'warning_reason' => $bad['reason'],
                    'source' => 'PREDATORY_REGISTRY',
                    'is_blacklisted' => true,
                ];
            }
        }

        return $results;
    }

    /**
     * Check if a journal name or ISSN matches known predatory, cloned, or blacklisted journals.
     */
    public static function isPredatory(string $title, ?string $issn = null): array
    {
        $cleanTitle = strtolower(trim($title));
        $cleanIssn = $issn ? preg_replace('/[^0-9a-zx]/i', '', strtolower(trim($issn))) : '';

        foreach (self::$predatoryBlacklist as $item) {
            $itemIssn = preg_replace('/[^0-9a-zx]/i', '', strtolower($item['issn'] ?? ''));
            $itemTitle = strtolower($item['title']);

            if (
                (!empty($cleanIssn) && $cleanIssn === $itemIssn) ||
                (!empty($cleanTitle) && (str_contains($cleanTitle, $itemTitle) || str_contains($itemTitle, $cleanTitle)))
            ) {
                return [
                    'is_predatory' => true,
                    'risk_level' => $item['risk_level'],
                    'journal' => $item['title'],
                    'issn' => $item['issn'],
                    'reason' => $item['reason'],
                ];
            }
        }

        return [
            'is_predatory' => false,
            'risk_level' => 'SAFE',
            'journal' => $title,
            'issn' => $issn,
            'reason' => null,
        ];
    }

    /**
     * Verify a Publication model or data array against HEC HJRS and Predatory Filters.
     */
    public static function verifyPublication($publication): array
    {
        if (!SystemSetting::isFeatureEnabled('hjrs_journal_lookup')) {
            return [
                'enabled' => false,
                'category' => $publication->category ?? 'UNVERIFIED',
                'category_label' => 'HJRS Lookup Disabled',
                'is_predatory' => false,
                'badge_color' => 'slate',
                'warning' => null,
                'status' => 'DISABLED',
            ];
        }

        $journalName = is_array($publication) ? ($publication['journal_name'] ?? $publication['journal'] ?? '') : ($publication->journal_name ?? '');
        $issn = is_array($publication) ? ($publication['issn_print'] ?? $publication['issn'] ?? '') : ($publication->issn_print ?? $publication->issn ?? '');
        $categoryClaimed = is_array($publication) ? ($publication['category'] ?? '') : ($publication->category ?? '');

        // 1. Check for Predatory Blacklist Match
        $predatoryCheck = self::isPredatory($journalName, $issn);
        if ($predatoryCheck['is_predatory']) {
            return [
                'enabled' => true,
                'category' => 'PREDATORY',
                'category_label' => 'Predatory / Blacklisted',
                'is_predatory' => true,
                'risk_level' => $predatoryCheck['risk_level'],
                'warning' => $predatoryCheck['reason'],
                'badge_color' => 'danger',
                'badge_text' => '⚠️ PREDATORY / BLACKLISTED',
                'status' => 'FLAGGED',
            ];
        }

        // 2. Lookup in HJRS Registry
        $matches = self::lookup($issn ?: $journalName);
        $hjrsMatch = collect($matches)->firstWhere('is_blacklisted', false);

        if ($hjrsMatch) {
            $cat = strtoupper($hjrsMatch['category']);
            $badgeColor = match ($cat) {
                'W' => 'success',
                'X' => 'primary',
                'Y' => 'warning',
                default => 'indigo',
            };

            return [
                'enabled' => true,
                'category' => $cat,
                'category_label' => "HEC HJRS Category {$cat}",
                'is_predatory' => false,
                'impact_factor' => $hjrsMatch['impact_factor'] ?? null,
                'percentile' => $hjrsMatch['percentile'] ?? null,
                'discipline' => $hjrsMatch['discipline'] ?? null,
                'publisher' => $hjrsMatch['publisher'] ?? null,
                'badge_color' => $badgeColor,
                'badge_text' => "HJRS CATEGORY {$cat}",
                'status' => 'VERIFIED',
                'warning' => null,
            ];
        }

        // 3. Fallback: Unindexed or Peer-Reviewed National
        $cat = !empty($categoryClaimed) && in_array(strtoupper($categoryClaimed), ['W', 'X', 'Y'])
            ? strtoupper($categoryClaimed)
            : 'UNINDEXED';

        return [
            'enabled' => true,
            'category' => $cat,
            'category_label' => $cat === 'UNINDEXED' ? 'Not in HJRS Master Index' : "Self-Reported Category {$cat}",
            'is_predatory' => false,
            'badge_color' => $cat === 'UNINDEXED' ? 'slate' : 'amber',
            'badge_text' => $cat === 'UNINDEXED' ? 'UNINDEXED / MANUAL VETTING' : "CLAIMED CAT {$cat}",
            'status' => 'UNINDEXED',
            'warning' => $cat === 'UNINDEXED' ? 'Journal not found in standard HEC HJRS index. Scrutiny committee manual verification required.' : null,
        ];
    }
}
