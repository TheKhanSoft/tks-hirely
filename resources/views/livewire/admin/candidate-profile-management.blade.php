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
                @can('create-basic-info')
                    <button type="button" class="btn btn-primary" wire:click="openCreateBasicInfoForm" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Create Profile</span>
                    </button>
                @endcan
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

    <!-- Executive Metric Cards -->
    @if(!$isFormOpen && !$isDetailsOpen)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--primary);">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(99,102,241,0.12); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Candidate Pool</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--color-text-primary); line-height: 1.2;">{{ $stats['total'] }}</div>
                </div>
            </div>

            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--secondary);">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(6,182,212,0.12); display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Active Applicants</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--secondary); line-height: 1.2;">{{ $stats['with_applications'] }}</div>
                </div>
            </div>

            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #8b5cf6;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(139,92,246,0.12); display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Academic Track</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #8b5cf6; line-height: 1.2;">{{ $stats['academic'] }}</div>
                </div>
            </div>

            <div class="card" style="padding: 1.15rem 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--danger);">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(239,68,68,0.12); display: flex; align-items: center; justify-content: center; color: var(--danger);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--color-text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Locked Dossiers</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--danger); line-height: 1.2;">{{ $stats['locked'] }}</div>
                </div>
            </div>
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
        <div class="card mb-4" style="padding: 1.15rem 1.25rem;">
            <div style="display: flex; gap: 0.85rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 260px; position: relative;">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search by candidate name, CNIC, or email..." style="padding-left: 2.25rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
                <div style="flex: 1; min-width: 170px;">
                    <select class="form-control form-select" wire:model.live="trackFilter">
                        <option value="">All Career Tracks</option>
                        <option value="ACADEMIC">Academic Track</option>
                        <option value="ADMINISTRATIVE">Administrative Track</option>
                        <option value="SUPPORT">Support Staff</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 170px;">
                    <select class="form-control form-select" wire:model.live="lockFilter">
                        <option value="">All Lock States</option>
                        <option value="1">🔒 Locked Dossiers</option>
                        <option value="0">🔓 Editable (Unlocked)</option>
                    </select>
                </div>
                <div>
                    <span style="font-size: 0.85rem; color: var(--color-text-secondary); font-weight: 600; white-space: nowrap;">
                        Showing {{ count($profiles) }} candidate{{ count($profiles) === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Profiles Table -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate Particulars</th>
                            <th>Applied Vacancies</th>
                            <th>Track Classification</th>
                            <th>CNIC Reference</th>
                            <th>Dossier Lock Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profiles as $basicInfo)
                            <tr wire:key="profile-row-{{ $basicInfo->id }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="user-avatar-circle" style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, rgba(99,102,241,0.22), rgba(6,182,212,0.22)); color: var(--primary); font-weight: 800; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(99,102,241,0.2);">
                                            {{ strtoupper(substr($basicInfo->first_name ?: $basicInfo->user->email, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="color: var(--color-text-primary); font-size: 0.94rem;">
                                                {{ $basicInfo->first_name }} {{ $basicInfo->last_name }}
                                            </strong>
                                            <div style="font-size: 0.78rem; color: var(--color-text-muted); font-family: 'JetBrains Mono', monospace; margin-top: 2px;">
                                                {{ $basicInfo->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $apps = $basicInfo->user->applications ?? collect();
                                    @endphp
                                    @if($apps->isNotEmpty())
                                        @foreach($apps->take(2) as $app)
                                            <div style="margin-bottom: 0.3rem;">
                                                <span class="badge" style="background: rgba(6,182,212,0.12); color: var(--secondary); font-size: 0.74rem; border: 1px solid rgba(6,182,212,0.25); font-weight: 600;">
                                                    {{ $app->campaign?->title ?? 'Campaign #'.$app->campaign_id }}
                                                </span>
                                            </div>
                                        @endforeach
                                        @if($apps->count() > 2)
                                            <small style="color: var(--color-text-muted); font-size: 0.74rem;">+{{ $apps->count() - 2 }} more application(s)</small>
                                        @endif
                                    @else
                                        <span style="font-size: 0.78rem; color: var(--color-text-muted); font-style: italic;">No applications submitted yet</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(99,102,241,0.12); color: var(--primary); border: 1px solid rgba(99,102,241,0.25); font-weight: 700; font-size: 0.76rem;">
                                        {{ $basicInfo->track_type }}
                                    </span>
                                </td>
                                <td>
                                    <code style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; color: var(--color-text-primary);">
                                        {{ $basicInfo->cnic_number }}
                                    </code>
                                </td>
                                <td>
                                    @can('lock-candidate-profile')
                                        <button type="button" 
                                                wire:click="toggleLock({{ $basicInfo->id }})"
                                                class="btn" 
                                                style="padding: 0.25rem 0.65rem; font-size: 0.75rem; border-radius: 999px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; {{ $basicInfo->is_locked ? 'background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3);' : 'background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3);' }}"
                                                title="Click to toggle lock state">
                                            {{ $basicInfo->is_locked ? '🔒 Locked' : '🔓 Editable' }}
                                        </button>
                                    @else
                                        <span class="badge" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; border-radius: 999px; font-weight: 700; {{ $basicInfo->is_locked ? 'background: rgba(239,68,68,0.15); color: var(--danger); border: 1px solid rgba(239,68,68,0.3);' : 'background: rgba(16,185,129,0.15); color: var(--success); border: 1px solid rgba(16,185,129,0.3);' }}">
                                            {{ $basicInfo->is_locked ? '🔒 Locked' : '🔓 Editable' }}
                                        </span>
                                    @endcan
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex gap-2 justify-end align-center">
                                        @if(auth()->user()->can('view-candidate-dossier') || auth()->user()->can('view-basic-info'))
                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;" wire:click="viewBasicInfoDetails({{ $basicInfo->id }})">Dossier</button>
                                        @endif
                                        @can('edit-basic-info')
                                            <button type="button" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;" wire:click="openEditBasicInfoForm({{ $basicInfo->id }})">Edit</button>
                                        @endcan
                                        @can('delete-basic-info')
                                            <button type="button" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;" wire:click="deleteBasicInfo({{ $basicInfo->id }})" onclick="confirm('Are you sure you want to delete this profile?') || event.stopImmediatePropagation()">Delete</button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center" style="padding: 3.5rem; color: var(--color-text-muted);">
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(99,102,241,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--color-text-primary); margin: 0 0 0.35rem 0;">No Candidate Profiles Found</h4>
                                    <p style="font-size: 0.82rem; margin: 0;">No applicant records match your filter criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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

