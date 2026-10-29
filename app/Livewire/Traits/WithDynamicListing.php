<?php

namespace App\Livewire\Traits;

use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait WithDynamicListing
{
    use WithPagination;

    /**
     * Search term for live filtering.
     */
    public $search = '';

    /**
     * Number of items to display per page (default 16 or 12 for neat 4 or 3 card rows).
     */
    public $perPage = 16;

    /**
     * Active listing view mode: 'grid' (cards, default) or 'table' (tabular list).
     */
    public $viewMode = 'grid';

    /**
     * State for the universal entity CSV import modal.
     */
    public $isImportModalOpen = false;
    public $importEntityName = '';

    /**
     * Reset pagination when search query changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when items-per-page selector changes.
     */
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Switch view between responsive card grid and compact table.
     */
    public function setViewMode($mode)
    {
        $this->viewMode = in_array($mode, ['grid', 'table']) ? $mode : 'grid';
    }

    /**
     * Open the universal entity CSV import modal.
     */
    public function openImportModal($entityName = 'Records')
    {
        $this->importEntityName = $entityName;
        $this->isImportModalOpen = true;
    }

    /**
     * Close the import modal.
     */
    public function closeImportModal()
    {
        $this->isImportModalOpen = false;
    }

    /**
     * Stream a standard CSV file export directly to the browser.
     */
    public function streamCsvDownload(string $filename, array $headers, array $rows): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
