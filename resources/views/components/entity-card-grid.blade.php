@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'items' => null,
    'search' => null,
    'searchPlaceholder' => 'Search records by name, email, identifier...',
    'perPage' => 16,
    'perPageOptions' => [12, 16, 24, 32, 48, 64],
    'viewMode' => 'grid',
    'canExport' => true,
    'canImport' => true,
    'exportAction' => 'exportCsv',
    'importAction' => 'openImportModal',
    'entityName' => 'Records',
    'gridClass' => '',
])

<div>
    <!-- Unified Listing Control Ribbon -->
    <div class="entity-controls-bar">
        <!-- Top Toolbar: Search, Primary Action, Export, Import, View Toggle, Per Page -->
        <div class="entity-controls-top">
            <!-- Search Bar with Live Debounce -->
            <div style="flex: 1; min-width: 260px; max-width: 520px; position: relative;">
                <input type="text" 
                       class="form-control" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="{{ $searchPlaceholder }}" 
                       style="padding-left: 2.35rem; font-size: 0.88rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                @if(!empty($search))
                    <button type="button" 
                            wire:click="$set('search', '')" 
                            style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 0;"
                            data-tooltip="Clear search">
                        ✕
                    </button>
                @endif
            </div>

            <!-- Utility Controls: Per Page, View Switcher, Export, Import, Custom Actions -->
            <div class="entity-controls-actions">
                <!-- Items Per Page (Default 12 or 16) -->
                <div style="display: flex; align-items: center; gap: 0.45rem;">
                    <span style="font-size: 0.76rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Per Page:</span>
                    <select class="form-control form-select" 
                            wire:model.live="perPage" 
                            style="width: 78px; padding: 0.35rem 0.65rem; font-size: 0.82rem; font-weight: 700;">
                        @foreach($perPageOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- View Switcher (Grid / Table) -->
                @if(isset($table))
                    <div class="view-mode-toggle">
                        <button type="button" 
                                class="view-mode-btn {{ $viewMode === 'grid' ? 'active' : '' }}" 
                                wire:click="setViewMode('grid')" 
                                data-tooltip="Card Grid View (3-5 per row)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <rect width="7" height="7" x="3" y="3" rx="1.5"/>
                                <rect width="7" height="7" x="14" y="3" rx="1.5"/>
                                <rect width="7" height="7" x="14" y="14" rx="1.5"/>
                                <rect width="7" height="7" x="3" y="14" rx="1.5"/>
                            </svg>
                        </button>
                        <button type="button" 
                                class="view-mode-btn {{ $viewMode === 'table' ? 'active' : '' }}" 
                                wire:click="setViewMode('table')" 
                                data-tooltip="Table View">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <line x1="3" x2="21" y1="6" y2="6"/>
                                <line x1="3" x2="21" y1="12" y2="12"/>
                                <line x1="3" x2="21" y1="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Export CSV Option -->
                @if($canExport)
                    <button type="button" 
                            class="btn btn-secondary" 
                            wire:click="{{ $exportAction }}" 
                            style="padding: 0.45rem 0.85rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem;"
                            data-tooltip="Export {{ $entityName }} as CSV">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" x2="12" y1="15" y2="3"/>
                        </svg>
                        <span>Export</span>
                    </button>
                @endif

                <!-- Import Option -->
                @if($canImport)
                    <button type="button" 
                            class="btn btn-secondary" 
                            wire:click="{{ $importAction }}('{{ $entityName }}')" 
                            style="padding: 0.45rem 0.85rem; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem;"
                            data-tooltip="Import {{ $entityName }} Data">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" x2="12" y1="3" y2="15"/>
                        </svg>
                        <span>Import</span>
                    </button>
                @endif

                <!-- Custom Header Action Slot (e.g. + Create Button) -->
                @if(isset($headerActions))
                    {{ $headerActions }}
                @endif
            </div>
        </div>

        <!-- Custom Filters Slot (Pills, Role Selects, Status Filters, etc.) -->
        @if(isset($filters))
            <div style="border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 0.85rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                {{ $filters }}
            </div>
        @endif
    </div>

    <!-- Active Content Area: Card Grid vs Table -->
    @php
        $count = $items ? ($items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : count($items)) : 0;
    @endphp

    @if($count > 0)
        @if($viewMode === 'table' && isset($table))
            <div class="table-container" style="margin-bottom: 1.25rem;">
                {{ $table }}
            </div>
        @else
            <!-- Responsive Card Grid: 3 to 5 Cards per row -->
            <div class="entity-cards-grid {{ $gridClass }} mb-6">
                {{ $cards ?? $slot }}
            </div>
        @endif

        <!-- Pagination & Record Counter Footer -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-top: 1.25rem; padding: 0.5rem 0.25rem;">
            <div style="font-size: 0.82rem; color: var(--color-text-muted); font-weight: 700;">
                @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Showing <strong style="color: var(--primary);">{{ $items->firstItem() ?? 0 }}</strong> to <strong style="color: var(--primary);">{{ $items->lastItem() ?? 0 }}</strong> of <strong style="color: var(--color-text-primary);">{{ $items->total() }}</strong> {{ strtolower($entityName) }}
                @else
                    Showing <strong style="color: var(--primary);">{{ $count }}</strong> {{ strtolower($entityName) }}
                @endif
            </div>

            @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator && $items->hasPages())
                <div>
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Sleek Empty State Illustration -->
        <div class="card card-ambient-mesh" style="text-align: center; padding: 3.5rem 1.5rem; margin-bottom: 1.5rem;">
            <div style="width: 60px; height: 60px; border-radius: 16px; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.25); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; color: var(--primary);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0 0 0.45rem; color: var(--color-text-primary);">
                No {{ $entityName }} Found
            </h3>
            <p style="font-size: 0.85rem; color: var(--color-text-secondary); max-width: 440px; margin: 0 auto 1.25rem;">
                No matching records found for the applied search criteria or filters. Try adjusting your search query or reset the filters.
            </p>
            <div>
                <button type="button" 
                        class="btn btn-secondary" 
                        wire:click="$set('search', '')" 
                        style="padding: 0.45rem 1.15rem; font-size: 0.82rem; font-weight: 700;">
                    Reset Search & Filters
                </button>
            </div>
        </div>
    @endif
</div>

