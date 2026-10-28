<div>
    <!-- Console Control Header -->
    <div class="card mb-6" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; position: relative; overflow: hidden;">
        <div style="position: absolute; right: -2rem; top: -2rem; width: 140px; height: 140px; background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%); pointer-events: none;"></div>
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
                <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.35rem;">Candidate Profiles</h2>
                    <p class="card-subtitle" style="margin: 0;">Applicant demographic records, qualification dossiers, and profile lock controls</p>
                </div>
            </div>
        </div>
        <div>
            @if(!$isFormOpen && !$isDetailsOpen)
                @if(auth()->user()->can('create-basic-info') || auth()->user()->hasAnyRole(['super-admin', 'registrar']))
                    <button type="button" class="btn btn-primary" wire:click="openCreateBasicInfoForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Create Profile</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session()->has('message'))
        <div class="alert alert-success mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: var(--success); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger mb-6" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); padding: 0.85rem 1.25rem; border-radius: var(--radius-md);">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Drawer -->
    @if ($isFormOpen)
        <div class="card mb-6" style="border-color: var(--primary-glow); box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 class="card-title" style="margin: 0; font-size: 1.2rem;">
                    {{ $isEditMode ? 'Modify Profile Demographics' : 'Create Candidate Profile Record' }}
                </h3>
                <button type="button" class="btn btn-secondary" wire:click="resetForm" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">Cancel</button>
            </div>
            <form wire:submit.prevent="saveBasicInfo">
                @if(!$isEditMode)
                    <x-select label="Select Applicant Account *" :error="$errors->first('bi_user_id')">
                        <select class="form-control form-select" wire:model="bi_user_id">
                            <option value="">-- Choose Account (Users without profile) --</option>
                            @foreach($availableUsers as $pu)
                                <option value="{{ $pu->id }}">{{ $pu->email }}</option>
                            @endforeach
                        </select>
                    </x-select>
                @endif
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <x-input label="First Name *" :error="$errors->first('bi_first_name')">
                        <input type="text" class="form-control" wire:model="bi_first_name" placeholder="First name">
                    </x-input>
                    <x-input label="Last Name *" :error="$errors->first('bi_last_name')">
                        <input type="text" class="form-control" wire:model="bi_last_name" placeholder="Last name">
                    </x-input>
                </div>
                <div class="grid grid-cols-3 gap-4 mt-4 align-center">
                    <x-input label="CNIC Reference *" :error="$errors->first('bi_cnic_number')">
                        <input type="text" class="form-control" wire:model="bi_cnic_number" placeholder="xxxxx-xxxxxxx-x">
                    </x-input>
                    <x-input label="Date of Birth *" :error="$errors->first('bi_dob')">
                        <input type="date" class="form-control" wire:model="bi_dob">
                    </x-input>
                    <div class="form-group" style="margin-top: 1.75rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" wire:model="bi_is_locked" style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                            <span style="font-weight: 600;">Lock Profile (Voucher generated)</span>
                        </label>
                    </div>
                </div>
                <div class="flex gap-4 mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save Profile</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                </div>
            </form>
        </div>
    @else
        <!-- Filter Bar -->
        <div class="card mb-4" style="padding: 1rem 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by candidate name, CNIC, or email...">
                </div>
                <div style="width: 180px;">
                    <select class="form-control form-select" wire:model.live="trackFilter">
                        <option value="">All Tracks</option>
                        <option value="ACADEMIC">Academic</option>
                        <option value="ADMINISTRATIVE">Administrative</option>
                        <option value="SUPPORT">Support Staff</option>
                    </select>
                </div>
                <div style="width: 180px;">
                    <select class="form-control form-select" wire:model.live="lockFilter">
                        <option value="">All Lock States</option>
                        <option value="1">Locked</option>
                        <option value="0">Editable (Unlocked)</option>
                    </select>
                </div>
                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600;">
                        Showing {{ count($profiles) }} profile{{ count($profiles) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Profiles Table -->
        <x-table :headers="['Candidate Reference', 'Track Type', 'CNIC Number Reference', 'Lock Status', 'Action']">
            @forelse($profiles as $basicInfo)
                <tr>
                    <td>
                        <strong style="color: var(--color-text-primary)">{{ $basicInfo->first_name }} {{ $basicInfo->last_name }}</strong><br>
                        <small style="color: var(--color-text-secondary)">{{ $basicInfo->user->email }}</small>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25);">
                            {{ $basicInfo->track_type }}
                        </span>
                    </td>
                    <td><code>{{ $basicInfo->cnic_number }}</code></td>
                    <td>
                        <button type="button" 
                                wire:click="toggleLock({{ $basicInfo->id }})"
                                class="btn" 
                                style="padding: 0.2rem 0.55rem; font-size: 0.75rem; border-radius: 999px; font-weight: 600; cursor: pointer; {{ $basicInfo->is_locked ? 'background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3);' : 'background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3);' }}"
                                title="Click to toggle lock state">
                            {{ $basicInfo->is_locked ? '🔒 Locked' : '🔓 Editable' }}
                        </button>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="viewBasicInfoDetails({{ $basicInfo->id }})">Full Dossier</button>
                            <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="openEditBasicInfoForm({{ $basicInfo->id }})">Edit</button>
                            <button type="button" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="deleteBasicInfo({{ $basicInfo->id }})" onclick="confirm('Are you sure you want to delete this profile?') || event.stopImmediatePropagation()">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center" style="padding: 2.5rem; color: var(--color-text-secondary);">No candidate profiles found.</td></tr>
            @endforelse
        </x-table>
    @endif

    <!-- Profile Details Modal -->
    <x-modal :show="$isDetailsOpen" title="Complete Candidate Wizard Profile Details" closeAction="$set('isDetailsOpen', false)" style="max-width: 900px; width: 95%;">
        @if($selectedRecordDetails)
            <div style="max-height: 550px; overflow-y: auto; padding-right: 0.5rem;">
                <div class="grid grid-cols-3 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">First & Last Name:</p>
                        <strong>{{ $selectedRecordDetails->first_name }} {{ $selectedRecordDetails->last_name }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">CNIC Reference:</p>
                        <code>{{ $selectedRecordDetails->cnic_number }}</code>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Date of Birth:</p>
                        <strong>{{ $selectedRecordDetails->date_of_birth?->format('Y-m-d') }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4 border-bottom pb-4 mb-4">
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Gender:</p>
                        <strong>{{ $selectedRecordDetails->gender ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Father's Name:</p>
                        <strong>{{ $selectedRecordDetails->father_name ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Contact Phone:</p>
                        <strong>{{ $selectedRecordDetails->phone_number ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <p style="color: var(--color-text-secondary); margin-bottom: 0.2rem;">Domicile Scope:</p>
                        <strong>{{ $selectedRecordDetails->domicile ?? 'N/A' }}</strong>
                    </div>
                </div>

                <!-- Academic History (Ascending sorted) -->
                @if($selectedRecordDetails->degrees && $selectedRecordDetails->degrees->isNotEmpty())
                    <h4 style="color: var(--secondary); font-weight: 700;" class="mt-4 mb-2">Chronological Academic Qualifications</h4>
                    <x-table :headers="['Degree', 'Major Specialization', 'Institution / Board', 'Passing Year', 'Marks Obtd / Scale', 'Division/Grade', 'Transcript Proof']">
                        @foreach($selectedRecordDetails->degrees as $deg)
                            <tr>
                                <td><strong>{{ $deg['degree_title'] }}</strong></td>
                                <td>{{ $deg['major'] }}</td>
                                <td>{{ $deg['institution'] }} ({{ $deg['board'] }})</td>
                                <td>{{ $deg['year'] }}</td>
                                <td>{{ $deg['gpa'] }} / {{ $deg['scale'] }}</td>
                                <td>{{ $deg['division'] }}</td>
                                <td>
                                    @if(isset($deg['proof_path']))
                                        <a href="/storage/{{ $deg['proof_path'] }}" target="_blank" style="color: var(--secondary); text-decoration: underline;">View File</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif

                <!-- Job Experiences -->
                @if($selectedRecordDetails->experiences && $selectedRecordDetails->experiences->isNotEmpty())
                    <h4 style="color: var(--secondary); font-weight: 700;" class="mt-6 mb-2">Employment History Log</h4>
                    <x-table :headers="['Designation', 'Company Name', 'Tenure Dates Range', 'Sector', 'Gross Salary', 'Description', 'Letter File']">
                        @foreach($selectedRecordDetails->experiences as $exp)
                            <tr>
                                <td><strong>{{ $exp->job_title }}</strong></td>
                                <td>{{ $exp->organization }}</td>
                                <td><code>{{ $exp->start_date?->format('Y-m-d') }}</code> to <code>{{ $exp->end_date?->format('Y-m-d') ?: 'Present' }}</code></td>
                                <td>{{ $exp->sector }}</td>
                                <td>PKR {{ number_format($exp->salary ?? 0, 2) }}</td>
                                <td>{{ $exp->description }}</td>
                                <td>
                                    @if(isset($exp['proof_path']))
                                        <a href="/storage/{{ $exp['proof_path'] }}" target="_blank" style="color: var(--secondary); text-decoration: underline;">Open NOC</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                @endif

                <div class="mt-6 flex justify-end" style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" wire:click="$set('isDetailsOpen', false)">Close Dossier</button>
                </div>
            </div>
        @endif
    </x-modal>
</div>

