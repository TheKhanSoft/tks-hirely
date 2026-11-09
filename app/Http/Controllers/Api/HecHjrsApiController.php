<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\HecHjrsJournalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HecHjrsApiController extends Controller
{
    /**
     * REST API Lookup for HEC HJRS Journal Categories & Predatory Detection.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function lookup(Request $request): JsonResponse
    {
        $enabled = SystemSetting::isFeatureEnabled('hjrs_journal_lookup');

        $query = (string) $request->query('q', $request->query('issn', ''));
        $title = (string) $request->query('title', '');

        if (!$enabled) {
            return response()->json([
                'success' => false,
                'enabled' => false,
                'message' => 'HEC HJRS Journal Lookup is currently disabled by institutional settings.',
                'results' => [],
            ], 200);
        }

        if (trim($query) === '' && trim($title) === '') {
            return response()->json([
                'success' => false,
                'enabled' => true,
                'message' => 'Please provide a search query parameter (e.g., ?q=Nature or ?issn=0028-0836).',
                'results' => [],
            ], 422);
        }

        $searchTarget = !empty($query) ? $query : $title;
        $results = HecHjrsJournalService::lookup($searchTarget);
        $predatoryCheck = HecHjrsJournalService::isPredatory($searchTarget, $query);

        return response()->json([
            'success' => true,
            'enabled' => true,
            'query' => $searchTarget,
            'total_matches' => count($results),
            'is_predatory' => $predatoryCheck['is_predatory'],
            'predatory_details' => $predatoryCheck['is_predatory'] ? $predatoryCheck : null,
            'results' => $results,
        ]);
    }
}
