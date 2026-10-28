<div>
    <x-card title="👤 Applicant Recruitment Wizard" subtitle="Provide your biodata, educational history, professional experience, milestones, and generate your processing voucher.">
        <!-- Wizard Progress Steps with Labels Underneath -->
        @php
            $stepLabels = [
                1 => 'Bio-Data',
                2 => 'Academics',
                3 => 'Experience',
                4 => 'Research & Tools',
                5 => 'Portfolio & Patents',
                6 => 'Events & Training',
                7 => 'References',
                8 => 'Payment Voucher'
            ];
        @endphp
        <div class="wizard-steps" style="margin-top: 1.5rem;">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="wizard-step-container {{ $currentStep == $i ? 'active' : ($currentStep > $i ? 'completed' : '') }}">
                    <div class="wizard-step {{ $currentStep == $i ? 'active' : ($currentStep > $i ? 'completed' : '') }}">
                        @if($currentStep > $i) ✓ @else {{ $i }} @endif
                    </div>
                    <span class="wizard-step-label">{{ $stepLabels[$i] }}</span>
                </div>
            @endfor
        </div>
    </x-card>

    @if($basicInfo && $basicInfo->is_locked && $currentStep < 8)
        <x-alert type="warning" message="⚠️ Your basicInfo is currently locked because your application has been submitted and processing voucher generated. Please proceed to Step 8 to access your voucher." />
    @endif

    <div style="margin-top: 2rem;">
        <!-- ========================================== -->
        <!-- STEP 1: BIO-DATA / PERSONAL INFO -->
        <!-- ========================================== -->
        @if ($currentStep === 1)
            <x-card title="📋 Step 1: Personal Bio-Data Details">
                <div class="flex-col gap-6">
                    <x-select label="Select Recruitment Campaign *" :error="$errors->first('campaign_id')">
                        <select class="form-control form-select" wire:model.live="campaign_id" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            <option value="">-- Select Active Position --</option>
                            @foreach ($campaigns as $camp)
                                <option value="{{ $camp->id }}">{{ $camp->title }} (Job Code: {{ $camp->job_code }} | Track: {{ $camp->track_type }})</option>
                            @endforeach
                        </select>
                    </x-select>

                    @if($campaign_id)
                        <div style="padding: 1rem; background: rgba(6, 182, 212, 0.08); border-radius: 8px; border: 1px solid var(--secondary-glow);" class="mb-4">
                            <strong style="color: var(--secondary)">RECRUITMENT TRACK DETECTED:</strong> 
                            <x-badge type="appealed" style="margin-left: 0.5rem; font-weight: 800;">{{ $this->trackType }}</x-badge>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="First Name *" :error="$errors->first('first_name')">
                            <input type="text" class="form-control" wire:model="first_name" {{ $basicInfo?->is_locked ? 'disabled' : '' }} placeholder="e.g. Ali">
                        </x-input>
                        <x-input label="Last Name (Optional)" :error="$errors->first('last_name')">
                            <input type="text" class="form-control" wire:model="last_name" {{ $basicInfo?->is_locked ? 'disabled' : '' }} placeholder="e.g. Khan">
                        </x-input>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Father's / Husband's Full Name *" :error="$errors->first('father_name')">
                            <input type="text" class="form-control" wire:model="father_name" {{ $basicInfo?->is_locked ? 'disabled' : '' }} placeholder="Full Name">
                        </x-input>
                        <x-select label="Gender *" :error="$errors->first('gender')">
                            <select class="form-control form-select" wire:model="gender" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </x-select>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="CNIC Number * (xxxxx-xxxxxxx-x)" :error="$errors->first('cnic_number')">
                            <input type="text" class="form-control" wire:model.live="cnic_number" placeholder="37405-1234567-1" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                        <x-input label="Date of Birth *" :error="$errors->first('date_of_birth')">
                            <input type="date" class="form-control" wire:model="date_of_birth" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                    </div>

                    <div class="grid grid-cols-3 gap-6">
                        <x-input label="Mobile / Contact Number *" :error="$errors->first('phone_number')">
                            <input type="text" class="form-control" wire:model="phone_number" placeholder="e.g. 0300-1234567" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                        <x-input label="Secondary Phone/Mobile (Optional)" :error="$errors->first('secondary_phone')">
                            <input type="text" class="form-control" wire:model="secondary_phone" placeholder="e.g. 0300-7654321" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                        <x-input label="Secondary Email (Optional)" :error="$errors->first('secondary_email')">
                            <input type="email" class="form-control" wire:model="secondary_email" placeholder="e.g. secondary@domain.com" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                    </div>

                    <div class="grid grid-cols-3 gap-6">
                        <x-input label="Place of Birth (City/District) *" :error="$errors->first('place_of_birth')">
                            <input type="text" class="form-control" wire:model="place_of_birth" placeholder="e.g. Rawalpindi" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                        <x-input label="Nationality *" :error="$errors->first('nationality')">
                            <input type="text" class="form-control" wire:model="nationality" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                        <x-input label="Religion *" :error="$errors->first('religion')">
                            <input type="text" class="form-control" wire:model="religion" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                    </div>

                    <div class="grid grid-cols-3 gap-6">
                        <x-select label="Marital Status *" :error="$errors->first('marital_status')">
                            <select class="form-control form-select" wire:model="marital_status" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </x-select>
                        <x-input label="Domicile (City/Province) *" :error="$errors->first('domicile')">
                            <input type="text" class="form-control" wire:model="domicile" placeholder="e.g. Punjab (Rawalpindi)" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                        </x-input>
                        <x-select label="Quota Selection *" :error="$errors->first('quota')">
                            <select class="form-control form-select" wire:model.live="quota" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                <option value="General">General / Open Merit</option>
                                <option value="Minority">Minority Quota</option>
                                <option value="Disabled">Disabled Persons Quota</option>
                                <option value="Female">Female Quota</option>
                            </select>
                        </x-select>
                    </div>

                    @if($quota === 'Disabled')
                        <div style="padding: 1.5rem; border: 1px dashed var(--border-color); border-radius: var(--radius-md); background: rgba(255,255,255,0.01);">
                            <x-input label="Upload Disability Certificate * (PDF/Image max 2MB)" :error="$errors->first('disability_cert')">
                                <input type="file" class="form-control" wire:model="disability_cert" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            @if($basicInfo && isset($basicInfo->dynamic_attributes['disability_cert_path']))
                                <div class="mt-4"><a href="/storage/{{ $basicInfo->dynamic_attributes['disability_cert_path'] }}" target="_blank" style="color: var(--secondary); font-weight: 600;">📄 View Uploaded Certificate</a></div>
                            @endif
                        </div>
                    @endif

                    <div style="margin-top: 1rem; padding: 0.5rem 0;">
                        <label class="flex align-center gap-2" style="cursor: pointer; user-select: none; font-size: 0.95rem; font-weight: 600; color: var(--color-text-primary);">
                            <input type="checkbox" wire:model.live="same_address" {{ $basicInfo?->is_locked ? 'disabled' : '' }} style="width: 1.2rem; height: 1.2rem; accent-color: var(--secondary); cursor: pointer;">
                            <span>Mailing Address is same as Permanent Address</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <x-textarea label="Permanent Home Address *" :error="$errors->first('permanent_address')">
                            <textarea rows="3" class="form-control" wire:model="permanent_address" {{ $basicInfo?->is_locked ? 'disabled' : '' }} placeholder="Street, Sector, City, Country..."></textarea>
                        </x-textarea>
                        <x-textarea label="Mailing Address (For correspondence) *" :error="$errors->first('mailing_address')">
                            <textarea rows="3" class="form-control" wire:model="mailing_address" {{ $basicInfo?->is_locked || $same_address ? 'disabled' : '' }} placeholder="PO Box, Street, City..."></textarea>
                        </x-textarea>
                    </div>
                </div>
            </x-card>

        <!-- ========================================== -->
        <!-- STEP 2: ACADEMICS -->
        <!-- ========================================== -->
        @elseif ($currentStep === 2)
            <x-card title="🎓 Step 2: Academic Qualifications & Achievements">
                <div class="flex-col gap-6">
                    <!-- Degree Input Form -->
                    <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);" class="mb-4">
                        <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">➕ Add Educational Record</h4>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <x-select label="Degree Level / Tier *" :error="$errors->first('new_degree_level')">
                                <select class="form-control form-select" wire:model.live="new_degree_level" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                    @foreach($this->nextDegreeLevelOptions as $val => $lbl)
                                        <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                    @if(empty($this->nextDegreeLevelOptions))
                                        <option value="">-- All Levels Added --</option>
                                    @endif
                                </select>
                            </x-select>
                            <x-input label="Degree / Certificate Title *" :error="$errors->first('new_degree_title')">
                                <input type="text" class="form-control" wire:model="new_degree_title" placeholder="e.g. Matric / FSc / BS CS" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Major Specialization *" :error="$errors->first('new_degree_major')">
                                <input type="text" class="form-control" wire:model="new_degree_major" placeholder="e.g. Science / Software Engineering" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <x-select label="Country of Degree *" :error="$errors->first('new_degree_country')">
                                <select class="form-control form-select" wire:model="new_degree_country" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Germany">Germany</option>
                                    <option value="China">China</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="UAE">United Arab Emirates</option>
                                    <option value="Other">Other</option>
                                </select>
                            </x-select>
                            <x-input label="Board / University *" :error="$errors->first('new_degree_board')">
                                <input type="text" class="form-control" wire:model="new_degree_board" placeholder="e.g. FBISE / NUST" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Institution Name *" :error="$errors->first('new_degree_inst')">
                                <input type="text" class="form-control" wire:model="new_degree_inst" placeholder="School/College name" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                        </div>

                        <div class="grid grid-cols-4 gap-4 mt-4">
                            <x-input label="Passing Year *" :error="$errors->first('new_degree_year')">
                                <input type="number" class="form-control" wire:model="new_degree_year" placeholder="e.g. 2018" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Marks Obtd. / GPA *" :error="$errors->first('new_degree_gpa')">
                                <input type="text" class="form-control" wire:model="new_degree_gpa" placeholder="e.g. 3.8" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Total Marks / Scale *" :error="$errors->first('new_degree_scale')">
                                <input type="text" class="form-control" wire:model="new_degree_scale" placeholder="e.g. 4.0 / 1100" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-select label="Division / Grade *" :error="$errors->first('new_degree_division')">
                                <select class="form-control form-select" wire:model="new_degree_division" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                    <option value="">-- Choose Division --</option>
                                    <option value="First Division">First Division</option>
                                    <option value="Second Division">Second Division</option>
                                    <option value="Third Division">Third Division</option>
                                    <option value="A+ Grade">A+ Grade</option>
                                    <option value="A Grade">A Grade</option>
                                    <option value="B Grade">B Grade</option>
                                </select>
                            </x-select>
                        </div>

                        <div class="grid grid-cols-1 gap-4 mt-4">
                            <x-input label="Transcript Proof * (PDF/Image max 2MB)" :error="$errors->first('new_degree_proof')">
                                <input type="file" class="form-control" wire:model="new_degree_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                        </div>

                        <div class="mt-4">
                            <x-button variant="secondary" wire:click="addDegree" :disabled="$basicInfo?->is_locked || empty($this->nextDegreeLevelOptions)">➕ Add Qualification</x-button>
                        </div>
                    </div>

                    <!-- Degrees Sorted Listing Grid -->
                    <h4 style="color: var(--color-text-primary); font-weight: 700;">Qualifications Registry (Chronological Order)</h4>
                    <x-table :headers="['Level / Tier', 'Degree Title', 'Country', 'Major / Board', 'Institution', 'Year', 'Marks / Scale', 'Grade', 'Proof Doc', 'Action']">
                        @forelse($degrees as $idx => $deg)
                            <tr>
                                <td><x-badge type="appealed">{{ str_replace('_', ' ', $deg['degree_level'] ?? 'N/A') }}</x-badge></td>
                                <td><strong style="color: var(--secondary)">{{ $deg['degree_title'] }}</strong></td>
                                <td>{{ $deg['country'] ?? 'Pakistan' }}</td>
                                <td>{{ $deg['major'] }}<br><small style="color: var(--color-text-secondary)">{{ $deg['board'] }}</small></td>
                                <td>{{ $deg['institution'] }}</td>
                                <td>{{ $deg['year'] }}</td>
                                <td><strong>{{ $deg['gpa'] }}</strong> / {{ $deg['scale'] }}</td>
                                <td><x-badge type="appealed">{{ $deg['division'] }}</x-badge></td>
                                <td>
                                    @if(isset($deg['proof_path']))
                                        <a href="/storage/{{ $deg['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View File</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeDegree({{ $deg['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">No educational qualifications added.</td></tr>
                        @endforelse
                    </x-table>

                    @if ($this->trackType !== 'SUPPORT')
                        <!-- Medals Subform -->
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);" class="mt-6">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">🥇 Academic Medals (Gold/Silver/Bronze)</h4>
                            <div class="grid grid-cols-4 gap-4 align-center">
                                <x-input label="Degree Reference" :error="$errors->first('new_medal_degree')">
                                    <input type="text" class="form-control" wire:model="new_medal_degree" placeholder="e.g. BS Computer Science" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-select label="Medal Type" :error="$errors->first('new_medal_type')">
                                    <select class="form-control form-select" wire:model="new_medal_type" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="GOLD">Gold Medal</option>
                                        <option value="SILVER">Silver Medal</option>
                                        <option value="BRONZE">Bronze Medal</option>
                                    </select>
                                </x-select>
                                <x-input label="Award Year" :error="$errors->first('new_medal_year')">
                                    <input type="number" class="form-control" wire:model="new_medal_year" placeholder="e.g. 2022" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Transcription Proof (PDF/Image)" :error="$errors->first('new_medal_proof')">
                                    <input type="file" class="form-control" wire:model="new_medal_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addMedal" :disabled="$basicInfo?->is_locked">🥇 Record Medal</x-button>
                            </div>
                        </div>

                        <!-- Medals Table -->
                        <x-table :headers="['Degree Scope', 'Medal Class', 'Year', 'Proof Document', 'Verification Gate', 'Action']" class="mt-4">
                            @forelse($medals as $m)
                                <tr>
                                    <td>{{ $m['degree_title'] }}</td>
                                    <td>
                                        <strong style="color: {{ $m['medal_type'] === 'GOLD' ? '#fbbf24' : ($m['medal_type'] === 'SILVER' ? '#9ca3af' : '#b45309') }}">
                                            🏆 {{ $m['medal_type'] }}
                                        </strong>
                                    </td>
                                    <td>{{ $m['year_awarded'] }}</td>
                                    <td><a href="/storage/{{ $m['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 Open Proof</a></td>
                                    <td>
                                        <x-badge :type="$basicInfo->is_locked ? 'paid' : 'draft'">Pending Scrutiny</x-badge>
                                    </td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeMedal({{ $m['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No medals recorded.</td></tr>
                            @endforelse
                        </x-table>
                    @endif
                </div>
            </x-card>

        <!-- ========================================== -->
        <!-- STEP 3: EXPERIENCE -->
        <!-- ========================================== -->
        @elseif ($currentStep === 3)
            <x-card title="💼 Step 3: Professional Employment History">
                <div class="flex-col gap-6">
                    <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">➕ Record Employment Period</h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="Designation / Position" :error="$errors->first('new_exp_title')">
                                <input type="text" class="form-control" wire:model="new_exp_title" placeholder="e.g. Assistant Manager / Lecturer" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Organization Name" :error="$errors->first('new_exp_company')">
                                <input type="text" class="form-control" wire:model="new_exp_company" placeholder="e.g. University of Punjab" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <x-select label="Organization Sector" :error="$errors->first('new_exp_sector')">
                                <select class="form-control form-select" wire:model="new_exp_sector" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                    <option value="Private">Private Sector</option>
                                    <option value="Government">Government Sector</option>
                                    <option value="Semi-Government">Semi-Government Sector</option>
                                </select>
                            </x-select>
                            <x-input label="Gross Monthly Salary (PKR)" :error="$errors->first('new_exp_salary')">
                                <input type="number" class="form-control" wire:model="new_exp_salary" placeholder="e.g. 120000" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Experience Proof / NOC (PDF max 2MB)" :error="$errors->first('new_exp_proof')">
                                <input type="file" class="form-control" wire:model="new_exp_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mt-4 align-center">
                            <x-input label="Start Date" :error="$errors->first('new_exp_start')">
                                <input type="date" class="form-control" wire:model="new_exp_start" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="End Date (Leave blank if current)" :error="$errors->first('new_exp_end')">
                                <input type="date" class="form-control" wire:model="new_exp_end" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            
                            @if ($this->trackType === 'ACADEMIC')
                                <x-select label="Experience Type" :error="$errors->first('new_exp_type')">
                                    <select class="form-control form-select" wire:model="new_exp_type" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="Pre-PhD">Pre-PhD Academic Experience</option>
                                        <option value="Post-PhD">Post-PhD Academic Experience</option>
                                    </select>
                                </x-select>
                            @else
                                <x-input label="Role Category">
                                    <input type="text" class="form-control" wire:model="new_exp_type" placeholder="e.g. Administrative / Technical" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            @endif
                        </div>

                        <div class="mt-4">
                            <x-textarea label="Core Job Responsibilities / Description" :error="$errors->first('new_exp_description')">
                                <textarea rows="3" class="form-control" wire:model="new_exp_description" placeholder="Briefly summarize your daily operational scope..." {{ $basicInfo?->is_locked ? 'disabled' : '' }}></textarea>
                            </x-textarea>
                        </div>

                        <div class="mt-4">
                            <x-button variant="secondary" wire:click="addExperience" :disabled="$basicInfo?->is_locked">💼 Log Experience Record</x-button>
                        </div>
                    </div>

                    <!-- Experience Registry Grid -->
                    <h4 style="color: var(--color-text-primary); font-weight: 700;">Job Experience Log (Sorted Chronologically)</h4>
                    <x-table :headers="['Job Position', 'Organization / Sector', 'Tenure / Duration', 'Monthly Salary', 'Proof NOC', 'Description', 'Action']">
                        @forelse($experience as $idx => $exp)
                            <tr>
                                <td>
                                    <strong style="color: var(--secondary)">{{ $exp['title'] }}</strong><br>
                                    <x-badge type="appealed" style="font-size: 0.75rem;">{{ $exp['type'] }}</x-badge>
                                </td>
                                <td>
                                    <strong>{{ $exp['company'] }}</strong><br>
                                    <small style="color: var(--color-text-secondary)">Sector: {{ $exp['sector'] }}</small>
                                </td>
                                <td>
                                    <code>{{ $exp['start_date'] }}</code> to 
                                    <code>{{ $exp['end_date'] ?: 'Present' }}</code>
                                </td>
                                <td>PKR {{ number_format($exp['salary'], 2) }}</td>
                                <td>
                                    @if(isset($exp['proof_path']))
                                        <a href="/storage/{{ $exp['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View Letter</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td><small style="max-width: 200px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $exp['description'] }}</small></td>
                                <td>
                                    <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeExperience({{ $exp['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No experience records mapped.</td></tr>
                        @endforelse
                    </x-table>
                </div>
            </x-card>

        <!-- ========================================== -->
        <!-- STEP 4: RESEARCH / MILESTONES / TOOLS -->
        <!-- ========================================== -->
        @elseif ($currentStep === 4)
            <x-card title="📄 Step 4: Track Specific Milestones & Core Capabilities">
                <!-- Academic Publications -->
                @if ($this->trackType === 'ACADEMIC')
                    <div class="flex-col gap-6">
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">📚 Record Peer-Reviewed Publication</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <x-input label="Research Paper Title" :error="$errors->first('new_pub_title')">
                                    <input type="text" class="form-control" wire:model="new_pub_title" placeholder="Full paper title" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Authors List (Comma Separated)" :error="$errors->first('new_pub_authors')">
                                    <input type="text" class="form-control" wire:model="new_pub_authors" placeholder="e.g. A. Khan, B. Smith" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mt-4">
                                <x-input label="Journal Name" :error="$errors->first('new_pub_journal')">
                                    <input type="text" class="form-control" wire:model="new_pub_journal" placeholder="e.g. IEEE Transactions" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="ISSN Print (Format: xxxx-xxxx)" :error="$errors->first('new_pub_issn')">
                                    <input type="text" class="form-control" wire:model="new_pub_issn" placeholder="1234-5678" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Digital Object Identifier (DOI)" :error="$errors->first('new_pub_doi')">
                                    <input type="text" class="form-control" wire:model="new_pub_doi" placeholder="e.g. 10.1109/..." {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-4 gap-4 mt-4 align-center">
                                <x-select label="HJRS Category" :error="$errors->first('new_pub_category')">
                                    <select class="form-control form-select" wire:model="new_pub_category" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="W">W Category</option>
                                        <option value="X">X Category</option>
                                        <option value="Y">Y Category</option>
                                        <option value="Z">Z Category</option>
                                    </select>
                                </x-select>
                                <x-input label="Impact Factor Score" :error="$errors->first('new_pub_impact')">
                                    <input type="number" step="0.001" class="form-control" wire:model="new_pub_impact" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Author Order Number" :error="$errors->first('new_pub_author_order')">
                                    <input type="number" class="form-control" wire:model="new_pub_author_order" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Publication Year" :error="$errors->first('new_pub_year')">
                                    <input type="number" class="form-control" wire:model="new_pub_year" placeholder="e.g. 2024" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4 align-center">
                                <x-input label="Web URL Address" :error="$errors->first('new_pub_url')">
                                    <input type="url" class="form-control" wire:model="new_pub_url" placeholder="https://..." {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="First Page Proof PDF (max 2MB)" :error="$errors->first('new_pub_proof')">
                                    <input type="file" class="form-control" wire:model="new_pub_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="mt-4 flex gap-4 align-center">
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                    <input type="checkbox" wire:model="new_pub_is_corresponding" style="width: 1.15rem; height: 1.15rem;" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                    <span style="font-weight: 600;">Set as Corresponding Author</span>
                                </label>
                            </div>

                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addPublication" :disabled="$basicInfo?->is_locked">📚 Register Publication</x-button>
                            </div>
                        </div>

                        <!-- Publications Table -->
                        <x-table :headers="['Research Title', 'Journal & ISSN', 'Category / Impact', 'Authorship Scope', 'Year', 'Proof Document', 'Status', 'Action']">
                            @forelse($publications as $pub)
                                <tr>
                                    <td>
                                        <strong style="color: var(--secondary)">{{ $pub['title'] }}</strong><br>
                                        <small style="color: var(--color-text-secondary)">DOI: {{ $pub['doi'] }}</small>
                                    </td>
                                    <td>{{ $pub['journal_name'] }}<br><code>{{ $pub['issn_print'] }}</code></td>
                                    <td>
                                        Category: <x-badge type="appealed">{{ $pub['hjrs_category'] }}</x-badge><br>
                                        Impact: <strong>{{ number_format($pub['impact_factor'], 3) }}</strong>
                                    </td>
                                    <td>
                                        Order: <strong>{{ $pub['author_order'] }}</strong><br>
                                        Corresponding: <strong>{{ $pub['is_corresponding_author'] ? '✓ Yes' : '✗ No' }}</strong>
                                    </td>
                                    <td>{{ $pub['publication_year'] }}</td>
                                    <td><a href="/storage/{{ $pub['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View PDF</a></td>
                                    <td><x-badge type="draft">Pending Scrutiny</x-badge></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removePublication({{ $pub['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">No research papers registered.</td></tr>
                            @endforelse
                        </x-table>
                    </div>

                <!-- Admin Milestones -->
                @elseif ($this->trackType === 'ADMINISTRATIVE')
                    <div class="flex-col gap-6">
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">⚙️ Record Governance Milestone / Budget Managed</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <x-input label="Milestone Title" :error="$errors->first('new_ms_title')">
                                    <input type="text" class="form-control" wire:model="new_ms_title" placeholder="e.g. ERP Deployment Control" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Budget Managed (Millions PKR)" :error="$errors->first('new_ms_budget')">
                                    <input type="number" class="form-control" wire:model="new_ms_budget" placeholder="e.g. 15.5" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Execution Year" :error="$errors->first('new_ms_year')">
                                    <input type="number" class="form-control" wire:model="new_ms_year" placeholder="e.g. 2023" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4 align-center">
                                <x-input label="Narrative / Impact Statement" :error="$errors->first('new_ms_description')">
                                    <input type="text" class="form-control" wire:model="new_ms_description" placeholder="Briefly describe operational scale..." {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Supporting Verification File (PDF)" :error="$errors->first('new_ms_proof')">
                                    <input type="file" class="form-control" wire:model="new_ms_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addMilestone" :disabled="$basicInfo?->is_locked">⚙️ Register Milestone</x-button>
                            </div>
                        </div>

                        <x-table :headers="['Milestone Name', 'Budget Managed', 'Year', 'Statement', 'Proof Doc', 'Action']">
                            @forelse($milestones as $idx => $ms)
                                <tr>
                                    <td><strong style="color: var(--secondary)">{{ $ms['title'] }}</strong></td>
                                    <td>PKR {{ number_format($ms['budget'], 1) }} Million</td>
                                    <td>{{ $ms['year'] }}</td>
                                    <td>{{ $ms['description'] }}</td>
                                    <td><a href="/storage/{{ $ms['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View Proof</a></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeMilestone({{ $ms['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No governance milestones logged.</td></tr>
                            @endforelse
                        </x-table>
                    </div>

                <!-- Support Tools -->
                @elseif ($this->trackType === 'SUPPORT')
                    <div class="flex-col gap-6">
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">🔧 Register Technical Tool / Vocational Skill</h4>
                            <div class="grid grid-cols-4 gap-4 align-center">
                                <x-input label="Tool Name" :error="$errors->first('new_tool_name')">
                                    <input type="text" class="form-control" wire:model="new_tool_name" placeholder="e.g. Electrical Safety Grid" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-select label="Proficiency Level" :error="$errors->first('new_tool_proficiency')">
                                    <select class="form-control form-select" wire:model="new_tool_proficiency" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="Beginner">Beginner</option>
                                        <option value="Intermediate">Intermediate</option>
                                        <option value="Expert">Expert / Master</option>
                                    </select>
                                </x-select>
                                <x-input label="Years of Active Handling" :error="$errors->first('new_tool_experience')">
                                    <input type="number" class="form-control" wire:model="new_tool_experience" placeholder="e.g. 5" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Fitness Proof / Certification" :error="$errors->first('new_tool_fitness')">
                                    <input type="file" class="form-control" wire:model="new_tool_fitness" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addTool" :disabled="$basicInfo?->is_locked">🔧 Save Skill Details</x-button>
                            </div>
                        </div>

                        <x-table :headers="['Vocational Skill / Tool', 'Proficiency Class', 'Handling Span', 'Certification Document', 'Action']">
                            @forelse($tools as $idx => $t)
                                <tr>
                                    <td><strong style="color: var(--secondary)">{{ $t['name'] }}</strong></td>
                                    <td><x-badge type="appealed">{{ $t['proficiency'] }}</x-badge></td>
                                    <td>{{ $t['experience'] }} Years</td>
                                    <td><a href="/storage/{{ $t['fitness_cert'] }}" target="_blank" style="color: var(--secondary)">📄 View File</a></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeTool({{ $t['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No tool capabilities logged yet.</td></tr>
                            @endforelse
                        </x-table>
                    </div>
                @endif
            </x-card>

        <!-- ========================================== -->
        <!-- STEP 5: PORTFOLIO -->
        <!-- ========================================== -->
        @elseif ($currentStep === 5)
            <x-card title="📋 Step 5: Professional Portfolio / Projects Registry">
                <!-- Academic Projects & Patents -->
                @if ($this->trackType === 'ACADEMIC')
                    <div class="flex-col gap-6">
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">🔬 Log Research Grant / Project</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <x-input label="Research Project Title" :error="$errors->first('new_proj_title')">
                                    <input type="text" class="form-control" wire:model="new_proj_title" placeholder="Project name" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Co-PIs Details (If any)" :error="$errors->first('new_proj_co_pis')">
                                    <input type="text" class="form-control" wire:model="new_proj_co_pis" placeholder="Names of co-investigators" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mt-4">
                                <x-input label="Funding Agency Name" :error="$errors->first('new_proj_funding')">
                                    <input type="text" class="form-control" wire:model="new_proj_funding" placeholder="e.g. HEC, PSF" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Grant Sponsor / Body" :error="$errors->first('new_proj_sponsor')">
                                    <input type="text" class="form-control" wire:model="new_proj_sponsor" placeholder="Sponsoring office" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Total Grant Value (PKR)" :error="$errors->first('new_proj_amount')">
                                    <input type="number" class="form-control" wire:model="new_proj_amount" placeholder="e.g. 500000" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-4 gap-4 mt-4 align-center">
                                <x-input label="Project Initiation Date" :error="$errors->first('new_proj_start')">
                                    <input type="date" class="form-control" wire:model="new_proj_start" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-select label="Role inside Project" :error="$errors->first('new_proj_role')">
                                    <select class="form-control form-select" wire:model="new_proj_role" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="PI">Principal Investigator (PI)</option>
                                        <option value="Co-PI">Co-Principal Investigator</option>
                                        <option value="Researcher">Key Researcher</option>
                                    </select>
                                </x-select>
                                <x-select label="Project Status" :error="$errors->first('new_proj_status')">
                                    <select class="form-control form-select" wire:model="new_proj_status" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="ONGOING">Ongoing</option>
                                        <option value="COMPLETED">Completed</option>
                                    </select>
                                </x-select>
                                <x-input label="Turnitin Similarity Index (%)" :error="$errors->first('new_proj_turnitin')">
                                    <input type="number" step="0.01" class="form-control" wire:model="new_proj_turnitin" placeholder="e.g. 12.5" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <x-input label="Award Letter Document (PDF max 2MB)" :error="$errors->first('new_proj_proof')">
                                    <input type="file" class="form-control" wire:model="new_proj_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addProject" :disabled="$basicInfo?->is_locked">🔬 Log Research Project</x-button>
                            </div>
                        </div>

                        <!-- Projects Table -->
                        <x-table :headers="['Project Scope', 'Funding & Value', 'Initiated', 'Role / Status', 'Similarity Index', 'Award Letter', 'Action']">
                            @forelse($projects as $p)
                                <tr>
                                    <td>
                                        <strong style="color: var(--secondary)">{{ $p['project_title'] }}</strong><br>
                                        <small style="color: var(--color-text-secondary)">Co-PIs: {{ $p['co_pis'] ?: 'None' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $p['funding_agency'] }}</strong><br>
                                        PKR {{ number_format($p['grant_amount'], 2) }}
                                    </td>
                                    <td>{{ $p['start_date'] }}</td>
                                    <td>
                                        Role: <strong>{{ $p['role_in_project'] }}</strong><br>
                                        Status: <x-badge :type="$p['status'] === 'COMPLETED' ? 'eligible' : 'pending'">{{ $p['status'] }}</x-badge>
                                    </td>
                                    <td>
                                        <strong style="color: {{ $p['turnitin_similarity_percentage'] >= 19.00 ? 'var(--danger)' : 'var(--success)' }}">
                                            {{ $p['turnitin_similarity_percentage'] }}%
                                        </strong>
                                    </td>
                                    <td><a href="/storage/{{ $p['award_letter_proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View File</a></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeProject({{ $p['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">No projects registered.</td></tr>
                            @endforelse
                        </x-table>

                        <!-- Patents Section -->
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);" class="mt-6">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">💡 Register Patent / IP Granted</h4>
                            <div class="grid grid-cols-3 gap-4 align-center">
                                <x-input label="Patent Title" :error="$errors->first('new_pat_title')">
                                    <input type="text" class="form-control" wire:model="new_pat_title" placeholder="Intellectual property name" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Registry Reference ID" :error="$errors->first('new_pat_registry_id')">
                                    <input type="text" class="form-control" wire:model="new_pat_registry_id" placeholder="Registry ID" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Grant Date" :error="$errors->first('new_pat_date')">
                                    <input type="date" class="form-control" wire:model="new_pat_date" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4 align-center">
                                <x-input label="Country of Registry" :error="$errors->first('new_pat_country')">
                                    <input type="text" class="form-control" wire:model="new_pat_country" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Patent Certificate (PDF)" :error="$errors->first('new_pat_proof')">
                                    <input type="file" class="form-control" wire:model="new_pat_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="mt-4 flex gap-4 align-center">
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                    <input type="checkbox" wire:model="new_pat_licensed" style="width: 1.15rem; height: 1.15rem;" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                    <span style="font-weight: 600;">Check if Technology Licensed to industry</span>
                                </label>
                            </div>
                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addPatent" :disabled="$basicInfo?->is_locked">💡 Record Patent</x-button>
                            </div>
                        </div>

                        <!-- Patents List -->
                        <x-table :headers="['Patent Title', 'Registry Reference', 'Country / Date', 'Licensed Status', 'Certificate Document', 'Action']">
                            @forelse($patents as $idx => $pat)
                                <tr>
                                    <td><strong style="color: var(--secondary)">{{ $pat['title'] }}</strong></td>
                                    <td><code>{{ $pat['registry_id'] }}</code></td>
                                    <td>{{ $pat['country'] }} ({{ $pat['date'] }})</td>
                                    <td>
                                        <x-badge :type="$pat['licensed'] ? 'eligible' : 'draft'">
                                            {{ $pat['licensed'] ? 'Licensed' : 'Unlicensed' }}
                                        </x-badge>
                                    </td>
                                    <td><a href="/storage/{{ $pat['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View Certificate</a></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removePatent({{ $pat['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No intellectual property registered.</td></tr>
                            @endforelse
                        </x-table>
                    </div>

                <!-- Admin & Support Portfolio items -->
                @else
                    <div class="flex-col gap-6">
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">💼 Log Portfolio Item / Key Project</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <x-input label="Item Title" :error="$errors->first('new_port_title')">
                                    <input type="text" class="form-control" wire:model="new_port_title" placeholder="e.g. Campus Network Security Audit" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Completion Date" :error="$errors->first('new_port_date')">
                                    <input type="date" class="form-control" wire:model="new_port_date" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Item URL Link (If any)" :error="$errors->first('new_port_url')">
                                    <input type="url" class="form-control" wire:model="new_port_url" placeholder="https://..." {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4 align-center">
                                <x-input label="Operational Impact Details" :error="$errors->first('new_port_description')">
                                    <input type="text" class="form-control" wire:model="new_port_description" placeholder="Summarize deliverables..." {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Upload Supporting Verification Letter (PDF)" :error="$errors->first('new_port_proof')">
                                    <input type="file" class="form-control" wire:model="new_port_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addPortfolioItem" :disabled="$basicInfo?->is_locked">💼 Record Portfolio Item</x-button>
                            </div>
                        </div>

                        <x-table :headers="['Portfolio Work Scope', 'Completion Date', 'Impact Statement', 'URL Address', 'Verification Doc', 'Action']">
                            @forelse($portfolio_items as $idx => $item)
                                <tr>
                                    <td><strong style="color: var(--secondary)">{{ $item['title'] }}</strong></td>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['description'] }}</td>
                                    <td>
                                        @if($item['url'])
                                            <a href="{{ $item['url'] }}" target="_blank" style="color: var(--secondary)">Visit Link</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td><a href="/storage/{{ $item['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View File</a></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removePortfolioItem({{ $item['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No portfolio items logged.</td></tr>
                            @endforelse
                        </x-table>
                    </div>
                @endif
            </x-card>

        <!-- ========================================== -->
        <!-- STEP 6: EVENTS & TRAINING -->
        <!-- ========================================== -->
        @elseif ($currentStep === 6)
            <x-card title="📋 Step 6: Professional Events, Seminars, & Safety Training">
                <!-- Academic / Admin Events -->
                @if ($this->trackType !== 'SUPPORT')
                    <div class="flex-col gap-6">
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">🎤 Record Event / Seminar / Symposium Details</h4>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <x-input label="Event Name" :error="$errors->first('new_event_name')">
                                    <input type="text" class="form-control" wire:model="new_event_name" placeholder="e.g. Higher Education Summit" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Presented Paper Title (If applicable)" :error="$errors->first('new_event_paper')">
                                    <input type="text" class="form-control" wire:model="new_event_paper" placeholder="Paper title" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mt-4">
                                <x-select label="Event Type" :error="$errors->first('new_event_type')">
                                    <select class="form-control form-select" wire:model="new_event_type" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="SEMINAR">Seminar</option>
                                        <option value="CONFERENCE">Conference</option>
                                        <option value="WORKSHOP">Workshop</option>
                                        <option value="SYMPOSIUM">Symposium</option>
                                    </select>
                                </x-select>
                                <x-select label="Role inside Event" :error="$errors->first('new_event_role')">
                                    <select class="form-control form-select" wire:model="new_event_role" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                        <option value="LEADER_ORGANIZER">Leader / Organizer</option>
                                        <option value="ASSOCIATE_ORGANIZER">Associate Organizer</option>
                                        <option value="PRESENTER_INTERNATIONAL">Presenter (Intl.)</option>
                                        <option value="PRESENTER_NATIONAL">Presenter (Natl.)</option>
                                        <option value="ATTENDEE">Attendee</option>
                                    </select>
                                </x-select>
                                <x-input label="Event Location" :error="$errors->first('new_event_location')">
                                    <input type="text" class="form-control" wire:model="new_event_location" placeholder="City, Country" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mt-4 align-center">
                                <x-input label="Event Start Date" :error="$errors->first('new_event_start')">
                                    <input type="date" class="form-control" wire:model="new_event_start" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Event End Date" :error="$errors->first('new_event_end')">
                                    <input type="date" class="form-control" wire:model="new_event_end" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Organizer Name" :error="$errors->first('new_event_organizer')">
                                    <input type="text" class="form-control" wire:model="new_event_organizer" placeholder="Organizer body" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <x-input label="Certificate of Event (PDF max 2MB)" :error="$errors->first('new_event_proof')">
                                    <input type="file" class="form-control" wire:model="new_event_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>

                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addEvent" :disabled="$basicInfo?->is_locked">🎤 Record Event</x-button>
                            </div>
                        </div>

                        <!-- Events List -->
                        <x-table :headers="['Event Name', 'Type / Organizer', 'Role / Location', 'Initiated', 'Certificate File', 'Action']">
                            @forelse($events as $e)
                                <tr>
                                    <td>
                                        <strong style="color: var(--secondary)">{{ $e['event_name'] }}</strong><br>
                                        <small style="color: var(--color-text-secondary)">Paper: {{ $e['paper_title'] ?: 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <x-badge type="appealed">{{ $e['event_type'] }}</x-badge><br>
                                        <small style="color: var(--color-text-secondary)">Organizer: {{ $e['organizer'] }}</small>
                                    </td>
                                    <td>
                                        Role: <strong>{{ str_replace('_', ' ', $e['event_role']) }}</strong><br>
                                        Location: <strong>{{ $e['location'] }}</strong>
                                    </td>
                                    <td>{{ $e['start_date'] }} to {{ $e['end_date'] }}</td>
                                    <td><a href="/storage/{{ $e['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View Certificate</a></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeEvent({{ $e['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">No professional events logged.</td></tr>
                            @endforelse
                        </x-table>
                    </div>

                <!-- Support Safety Training & Certifications -->
                @else
                    <div class="flex-col gap-6">
                        <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                            <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">🚨 Log Safety Training / Vocational License</h4>
                            <div class="grid grid-cols-3 gap-4 align-center">
                                <x-input label="Training Title" :error="$errors->first('new_training_title')">
                                    <input type="text" class="form-control" wire:model="new_training_title" placeholder="e.g. High Voltage Grid Control" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Issuing Body / Office" :error="$errors->first('new_training_body')">
                                    <input type="text" class="form-control" wire:model="new_training_body" placeholder="e.g. Civil Defense" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                                <x-input label="Expiry Date (If applicable)" :error="$errors->first('new_training_expiry')">
                                    <input type="date" class="form-control" wire:model="new_training_expiry" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="grid grid-cols-1 gap-4 mt-4">
                                <x-input label="Upload Certificate (PDF)" :error="$errors->first('new_training_proof')">
                                    <input type="file" class="form-control" wire:model="new_training_proof" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                                </x-input>
                            </div>
                            <div class="mt-4">
                                <x-button variant="secondary" wire:click="addTraining" :disabled="$basicInfo?->is_locked">🚨 Register Training</x-button>
                            </div>
                        </div>

                        <x-table :headers="['Training / License Title', 'Issuing Body', 'Validity Expiry', 'Proof File', 'Action']">
                            @forelse($trainings as $idx => $t)
                                <tr>
                                    <td><strong style="color: var(--secondary)">{{ $t['title'] }}</strong></td>
                                    <td>{{ $t['body'] }}</td>
                                    <td><code>{{ $t['expiry'] ?: 'Lifetime' }}</code></td>
                                    <td><a href="/storage/{{ $t['proof_path'] }}" target="_blank" style="color: var(--secondary)">📄 View Doc</a></td>
                                    <td>
                                        <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeTraining({{ $t['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No safety trainings registered.</td></tr>
                            @endforelse
                        </x-table>
                    </div>
                @endif
            </x-card>

        <!-- ========================================== -->
        <!-- STEP 7: REFEREES -->
        <!-- ========================================== -->
        @elseif ($currentStep === 7)
            <x-card title="👥 Step 7: Professional & Academic Referees">
                <div class="flex-col gap-6">
                    <div style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <h4 style="color: var(--secondary); font-weight: 700; margin-bottom: 1rem;">➕ Record referee contact details</h4>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <x-input label="Referee Full Name" :error="$errors->first('new_ref_name')">
                                <input type="text" class="form-control" wire:model="new_ref_name" placeholder="e.g. Dr. Jamil Ahmad" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Designation / Title" :error="$errors->first('new_ref_designation')">
                                <input type="text" class="form-control" wire:model="new_ref_designation" placeholder="e.g. Professor / Director" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Institution / Organization" :error="$errors->first('new_ref_org')">
                                <input type="text" class="form-control" wire:model="new_ref_org" placeholder="e.g. NUST / Air University" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <x-input label="Email Address" :error="$errors->first('new_ref_email')">
                                <input type="email" class="form-control" wire:model="new_ref_email" placeholder="referee@email.com" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Contact Number / Mobile" :error="$errors->first('new_ref_phone')">
                                <input type="text" class="form-control" wire:model="new_ref_phone" placeholder="0300-1234567" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                            <x-input label="Relationship to Candidate" :error="$errors->first('new_ref_relation')">
                                <input type="text" class="form-control" wire:model="new_ref_relation" placeholder="e.g. PhD Supervisor / Former Manager" {{ $basicInfo?->is_locked ? 'disabled' : '' }}>
                            </x-input>
                        </div>

                        <div class="mt-4">
                            <x-textarea label="Official Address" :error="$errors->first('new_ref_address')">
                                <textarea rows="2" class="form-control" wire:model="new_ref_address" placeholder="Dept, Building, University..." {{ $basicInfo?->is_locked ? 'disabled' : '' }}></textarea>
                            </x-textarea>
                        </div>

                        <div class="mt-4">
                            <x-button variant="secondary" wire:click="addReferee" :disabled="$basicInfo?->is_locked">👥 Record Referee</x-button>
                        </div>
                    </div>

                    <!-- Referees List Table -->
                    <x-table :headers="['Referee Name / Title', 'Organization Name', 'Contact Info', 'Relationship Scope', 'Official Address', 'Action']">
                        @forelse($referees as $idx => $ref)
                            <tr>
                                <td>
                                    <strong style="color: var(--secondary)">{{ $ref['name'] }}</strong><br>
                                    <small>{{ $ref['designation'] }}</small>
                                </td>
                                <td><strong>{{ $ref['org'] }}</strong></td>
                                <td>
                                    Email: <code>{{ $ref['email'] }}</code><br>
                                    Phone: <code>{{ $ref['phone'] }}</code>
                                </td>
                                <td><x-badge type="appealed">{{ $ref['relation'] }}</x-badge></td>
                                <td><small style="max-width: 200px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $ref['address'] }}</small></td>
                                <td>
                                    <x-button variant="danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" wire:click="removeReferee({{ $ref['id'] }})" :disabled="$basicInfo?->is_locked">Remove</x-button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No references recorded. Min 2 references recommended.</td></tr>
                        @endforelse
                    </x-table>
                </div>
            </x-card>

        <!-- ========================================== -->
        <!-- STEP 8: PAYMENT AND LOCK -->
        <!-- ========================================== -->
        @elseif ($currentStep === 8)
            <x-card title="💳 Step 8: Processing Challan & Submission Lock">
                <div class="flex-col gap-6 align-center text-center">
                    @if (!$challanGenerated && !$basicInfo?->is_locked)
                        <h4 style="color: var(--secondary); font-weight: 800;">Submit Application and Generate Processing Challan</h4>
                        <p style="color: var(--color-text-secondary); max-width: 600px;">
                            By clicking the button below, you will lock your basicInfo details from further modifications. The system will automatically construct your 12-digit consumer billing reference for checkout.
                        </p>
                        <x-button variant="primary" style="padding: 0.75rem 2rem; font-size: 1.1rem;" wire:click="generateChallan">
                            🔒 Lock basicInfo & Generate Challan
                        </x-button>
                    @else
                        @php
                            $userApp = Auth::user()->applications()->first();
                            $billing = $userApp?->billingLedger;
                        @endphp
                        
                        <div class="challan-box" style="width: 100%; max-width: 600px;">
                            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;" class="flex justify-between align-center">
                                <strong style="color: var(--color-text-primary); font-size: 1.25rem;">HIRELY PROCESSING VOUCHER</strong>
                                <x-badge :type="$billing?->status === 'SETTLED' ? 'paid' : 'pending'">
                                    {{ $billing?->status === 'SETTLED' ? '✓ Voucher PAID' : '⚡ PENDING PAYMENT' }}
                                </x-badge>
                            </div>

                            <p style="color: var(--color-text-secondary); text-align: left; font-size: 0.9rem;" class="mb-4">
                                Use the 12-digit consumer reference ID listed below to pay your processing fee via the bank simulator switch, mobile banking, or OTG teller.
                            </p>

                            <div style="background: rgba(0,0,0,0.4); padding: 1.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                <small style="text-transform: uppercase; color: var(--color-text-secondary); letter-spacing: 1px; font-weight: 700;">Consumer ID Reference</small>
                                <h2 class="challan-id">{{ $billing?->consumer_id ?? $consumer_id }}</h2>
                                <div class="flex justify-between mt-4">
                                    <span style="color: var(--color-text-secondary)">Total Processing Fee:</span>
                                    <strong style="color: var(--success); font-size: 1.15rem;">PKR {{ number_format($billing?->amount ?? $application_fee, 2) }}</strong>
                                </div>
                            </div>

                            @if($billing?->status === 'SETTLED')
                                <div style="margin-top: 1.5rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); border-radius: var(--radius-sm); padding: 1rem;">
                                    <p style="color: #6ee7b7; font-weight: 700; margin: 0;">
                                        ✓ Payment Reconciled. Transaction ID: <code>{{ $billing->transaction_id }}</code>. Your file has been pushed to the scrutiny committee verification queue.
                                    </p>
                                </div>
                            @else
                                <div style="margin-top: 1.5rem; background: rgba(245, 158, 11, 0.1); border: 1px dashed var(--warning); border-radius: var(--radius-sm); padding: 1rem;">
                                    <p style="color: #fcd34d; font-weight: 600; margin: 0; font-size: 0.9rem;">
                                        📟 Note: Use the 💳 Bank Switch Sim link in the sidebar to complete payment.
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </x-card>
        @endif

        <!-- Wizard Navigation Controls -->
        <div class="flex justify-between mt-6" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            @if ($currentStep > 1)
                <x-button variant="secondary" wire:click="prevStep">⬅ Back Step</x-button>
            @else
                <div></div>
            @endif

            @if ($currentStep < $totalSteps)
                <x-button variant="primary" wire:click="nextStep" :disabled="!$campaign_id">Continue Next ➡</x-button>
            @endif
        </div>
    </div>
</div>
