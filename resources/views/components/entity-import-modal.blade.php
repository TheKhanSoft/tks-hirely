@props([
    'isOpen' => false,
    'entityName' => 'Records',
    'sampleColumns' => [],
])

@if($isOpen)
    <div class="modal-backdrop" style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 99999; padding: 1.25rem;">
        <div class="card card-ambient-mesh" style="width: 100%; max-width: 540px; padding: 1.75rem; border: 1px solid var(--border-color); box-shadow: var(--shadow-xl); position: relative; max-height: 90vh; overflow-y: auto;">
            
            <!-- Modal Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.85rem;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" x2="12" y1="3" y2="15"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--color-text-primary);">
                            Import {{ $entityName }}
                        </h3>
                        <p style="margin: 0.15rem 0 0; font-size: 0.78rem; color: var(--color-text-secondary);">
                            Batch upload via standard CSV or Excel file format
                        </p>
                    </div>
                </div>

                <button type="button" 
                        wire:click="closeImportModal" 
                        style="background: transparent; border: none; color: var(--color-text-muted); cursor: pointer; padding: 0.25rem; border-radius: 6px;"
                        aria-label="Close">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Upload Area -->
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="border: 2px dashed rgba(99, 102, 241, 0.3); border-radius: var(--radius-md); padding: 2rem 1rem; text-align: center; background: rgba(99, 102, 241, 0.03);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color: var(--primary); margin-bottom: 0.75rem;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="18" x2="12" y2="12"></line>
                        <line x1="9" y1="15" x2="15" y2="15"></line>
                    </svg>
                    <div style="font-weight: 700; font-size: 0.92rem; color: var(--color-text-primary); margin-bottom: 0.25rem;">
                        Select CSV file to upload
                    </div>
                    <p style="font-size: 0.78rem; color: var(--color-text-muted); margin: 0 0 1rem 0;">
                        Maximum file size: 10MB (.csv, .xlsx)
                    </p>
                    <input type="file" 
                           wire:model="importFile" 
                           accept=".csv,.txt" 
                           class="form-control" 
                           style="max-width: 320px; margin: 0 auto; font-size: 0.8rem;">
                </div>

                @if(!empty($sampleColumns))
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem 1rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Expected CSV Headers:</span>
                        <div style="margin-top: 0.35rem; display: flex; flex-wrap: wrap; gap: 0.35rem;">
                            @foreach($sampleColumns as $col)
                                <code style="font-size: 0.74rem; background: rgba(99, 102, 241, 0.12); color: var(--primary); padding: 0.15rem 0.45rem; border-radius: 4px; font-weight: 700;">
                                    {{ $col }}
                                </code>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button type="button" 
                        class="btn btn-secondary" 
                        wire:click="closeImportModal" 
                        style="padding: 0.45rem 1rem; font-size: 0.82rem; font-weight: 700;">
                    Cancel
                </button>
                <button type="button" 
                        class="btn btn-primary" 
                        wire:click="closeImportModal" 
                        style="padding: 0.45rem 1.25rem; font-size: 0.82rem; font-weight: 700;">
                    Start Processing
                </button>
            </div>
        </div>
    </div>
@endif
