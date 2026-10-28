<?php

namespace App\Livewire\Applicant;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Campaign;
use App\Models\BasicInfo;
use App\Models\Application;
use App\Models\AcademicMedal;
use App\Models\Publication;
use App\Models\ProfessionalEvent;
use App\Models\ResearchProject;
use App\Models\BillingLedger;
use App\Models\AcademicDegree;
use App\Models\WorkExperience;
use App\Models\AdminMilestone;
use App\Models\SupportTool;
use App\Models\Patent;
use App\Models\PortfolioItem;
use App\Models\Training;
use App\Models\Referee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfileWizard extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $totalSteps = 8;
    
    // Step 1: Bio-Data
    public $campaign_id;
    public $first_name;
    public $last_name;
    public $cnic_number;
    public $date_of_birth;
    public $quota = 'General';
    public $disability_cert;
    public $disability_cert_path;
    
    // Additional Bio-Data fields
    public $gender = 'Male';
    public $father_name;
    public $phone_number;
    public $secondary_phone;
    public $secondary_email;
    public $same_address = false;
    public $permanent_address;
    public $mailing_address;
    public $place_of_birth;
    public $nationality = 'Pakistani';
    public $religion = 'Islam';
    public $marital_status = 'Single';
    public $domicile;
    
    // Step 2: Academics
    public $degrees = [];
    public $medals = [];
    public $new_degree_level;
    public $new_degree_country = 'Pakistan';
    public $new_degree_title;
    public $new_degree_inst;
    public $new_degree_year;
    public $new_degree_gpa;
    public $new_degree_major;
    public $new_degree_board;
    public $new_degree_division;
    public $new_degree_scale = '4.0';
    public $new_degree_proof;
    
    public $new_medal_degree;
    public $new_medal_type = 'GOLD';
    public $new_medal_year;
    public $new_medal_proof;
    
    // Step 3: History / Experience
    public $experience = [];
    public $new_exp_title;
    public $new_exp_company;
    public $new_exp_start;
    public $new_exp_end;
    public $new_exp_type; // e.g. Pre-PhD, Post-PhD, Chronological Management, Vocational
    public $new_exp_sector = 'Private';
    public $new_exp_salary;
    public $new_exp_proof;
    public $new_exp_description;
    
    // Step 4: Research / Milestones / Tools
    // Academic Publications
    public $publications = [];
    public $new_pub_title;
    public $new_pub_journal;
    public $new_pub_issn;
    public $new_pub_category = 'W';
    public $new_pub_impact = 0.00;
    public $new_pub_author_order = 1;
    public $new_pub_is_corresponding = false;
    public $new_pub_authors;
    public $new_pub_doi;
    public $new_pub_volume;
    public $new_pub_url;
    public $new_pub_proof;
    public $new_pub_year;

    // Admin Milestones
    public $milestones = [];
    public $new_ms_title;
    public $new_ms_budget;
    public $new_ms_year;
    public $new_ms_description;
    public $new_ms_proof;

    // Support Tools
    public $tools = [];
    public $new_tool_name;
    public $new_tool_fitness; // Fitness certificate path/status
    public $new_tool_proficiency = 'Intermediate';
    public $new_tool_experience;
    
    // Step 5: Portfolio
    // Academic projects & patents
    public $projects = [];
    public $new_proj_title;
    public $new_proj_funding;
    public $new_proj_amount;
    public $new_proj_start;
    public $new_proj_role = 'PI';
    public $new_proj_turnitin = 0.00;
    public $new_proj_status = 'Completed';
    public $new_proj_co_pis;
    public $new_proj_sponsor;
    public $new_proj_proof;

    public $patents = [];
    public $new_pat_title;
    public $new_pat_licensed = false;
    public $new_pat_registry_id;
    public $new_pat_date;
    public $new_pat_country = 'Pakistan';
    public $new_pat_proof;

    // Admin & Support Portfolio
    public $portfolio_items = [];
    public $new_port_title;
    public $new_port_description;
    public $new_port_url;
    public $new_port_date;
    public $new_port_proof;
    
    // Step 6: Events & Training
    // Seminars / Conferences
    public $events = [];
    public $new_event_name;
    public $new_event_type = 'SEMINAR';
    public $new_event_role = 'ATTENDEE';
    public $new_event_paper;
    public $new_event_start;
    public $new_event_end;
    public $new_event_proof;
    public $new_event_location;
    public $new_event_organizer;

    // Support Training
    public $trainings = [];
    public $new_training_title;
    public $new_training_body;
    public $new_training_expiry;
    public $new_training_proof;
    
    // Step 7: Referees
    public $referees = [];
    public $new_ref_name;
    public $new_ref_email;
    public $new_ref_phone;
    public $new_ref_designation;
    public $new_ref_org;
    public $new_ref_relation;
    public $new_ref_address;
    
    // Step 8: Payment
    public $challanGenerated = false;
    public $consumer_id;
    public $application_fee;
    
    public function getRules()
    {
        if ($this->currentStep === 1) {
            return [
                'campaign_id' => 'required|exists:campaigns,id',
                'first_name' => 'required|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'cnic_number' => ['required', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
                'date_of_birth' => 'required|date|before:today',
                'quota' => 'required|string',
                'gender' => 'required|in:Male,Female,Other',
                'father_name' => 'required|string|max:150',
                'phone_number' => ['required', 'regex:/^((\+92|0092|92)?[\s\-]?)?(0?3[0-9]{2}[\s\-]?[0-9]{7}|0[1-9][0-9]{1,2}[\s\-]?[0-9]{6,8}|\+[1-9][0-9]{7,14})$/'],
                'secondary_phone' => ['nullable', 'regex:/^((\+92|0092|92)?[\s\-]?)?(0?3[0-9]{2}[\s\-]?[0-9]{7}|0[1-9][0-9]{1,2}[\s\-]?[0-9]{6,8}|\+[1-9][0-9]{7,14})$/'],
                'secondary_email' => 'nullable|email|max:100',
                'same_address' => 'boolean',
                'permanent_address' => 'required|string|max:500',
                'mailing_address' => 'required|string|max:500',
                'place_of_birth' => 'required|string|max:100',
                'nationality' => 'required|string|max:100',
                'religion' => 'required|string|max:100',
                'marital_status' => 'required|string|max:50',
                'domicile' => 'required|string|max:100',
            ];
        }
        return [];
    }

    protected $messages = [
        'cnic_number.regex' => 'The CNIC number format must be 12345-1234567-1.',
        'phone_number.required' => 'Mobile / Contact number is required.',
        'phone_number.regex' => 'Please enter a valid mobile number (e.g. 0300-1234567 or +923001234567).',
        'secondary_phone.regex' => 'Please enter a valid secondary phone/mobile number (e.g. 0300-7654321 or +923007654321).',
    ];

    public function mount()
    {
        $this->campaign_id = Auth::user()->applications()->first()?->campaign_id;
        
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->first_name = $basicInfo->first_name;
            $this->last_name = $basicInfo->last_name;
            $this->cnic_number = $basicInfo->cnic_number;
            $this->date_of_birth = $basicInfo->date_of_birth?->format('Y-m-d');
            
            $this->quota = $basicInfo->quota ?? 'General';
            $this->gender = $basicInfo->gender ?? 'Male';
            $this->father_name = $basicInfo->father_name ?? '';
            $this->phone_number = $basicInfo->phone_number ?? '';
            $this->secondary_phone = $basicInfo->secondary_phone ?? '';
            $this->secondary_email = $basicInfo->secondary_email ?? '';
            $this->same_address = (bool)($basicInfo->same_address ?? false);
            $this->permanent_address = $basicInfo->permanent_address ?? '';
            $this->mailing_address = $basicInfo->mailing_address ?? '';
            $this->place_of_birth = $basicInfo->place_of_birth ?? '';
            $this->nationality = $basicInfo->nationality ?? 'Pakistani';
            $this->religion = $basicInfo->religion ?? 'Islam';
            $this->marital_status = $basicInfo->marital_status ?? 'Single';
            $this->domicile = $basicInfo->domicile ?? '';
            
            // Reload databases & load normalized tables
            $this->refreshDegrees();
            $this->refreshExperiences();
            $this->refreshMilestones();
            $this->refreshTools();
            $this->refreshPatents();
            $this->refreshPortfolioItems();
            $this->refreshTrainings();
            $this->refreshReferees();
            
            $this->reloadDatabaseRelations($basicInfo);
        }

        $options = $this->getNextDegreeLevelOptionsProperty();
        $this->new_degree_level = !empty($options) ? array_key_first($options) : null;
    }

    protected function reloadDatabaseRelations($basicInfo)
    {
        $this->medals = $basicInfo->medals->map(fn($m) => [
            'id' => $m->id,
            'degree_title' => $m->degree_title,
            'medal_type' => $m->medal_type,
            'year_awarded' => $m->year_awarded,
            'proof_path' => $m->transcription_proof_path,
        ])->toArray();

        $this->publications = $basicInfo->publications->map(fn($p) => [
            'id' => $p->id,
            'title' => $p->title,
            'journal_name' => $p->journal_name,
            'issn_print' => $p->issn_print,
            'hjrs_category' => $p->hjrs_category,
            'impact_factor' => $p->impact_factor,
            'author_order' => $p->author_order,
            'is_corresponding_author' => $p->is_corresponding_author,
            'authors' => $p->authors,
            'doi' => $p->doi,
            'volume_issue' => $p->volume_issue,
            'url' => $p->url,
            'proof_path' => $p->proof_path,
            'publication_year' => $p->publication_year,
        ])->toArray();
        usort($this->publications, fn($a, $b) => ($a['publication_year'] ?? 0) <=> ($b['publication_year'] ?? 0));

        $this->projects = $basicInfo->researchProjects->map(fn($r) => [
            'id' => $r->id,
            'project_title' => $r->project_title,
            'funding_agency' => $r->funding_agency,
            'grant_amount' => $r->grant_amount,
            'start_date' => $r->start_date?->format('Y-m-d'),
            'role_in_project' => $r->role_in_project,
            'turnitin_similarity_percentage' => $r->turnitin_similarity_percentage,
            'status' => $r->status,
            'co_pis' => $r->co_pis,
            'award_letter_proof_path' => $r->award_letter_proof_path,
        ])->toArray();
        usort($this->projects, fn($a, $b) => strcmp($a['start_date'] ?? '', $b['start_date'] ?? ''));

        $this->events = $basicInfo->events->map(fn($e) => [
            'id' => $e->id,
            'event_name' => $e->event_name,
            'event_type' => $e->event_type,
            'event_role' => $e->event_role,
            'paper_title' => $e->paper_title,
            'start_date' => $e->start_date?->format('Y-m-d'),
            'end_date' => $e->end_date?->format('Y-m-d'),
            'proof_path' => $e->certificate_proof_path,
            'location' => $e->location,
            'organizer' => $e->organizer,
        ])->toArray();
        usort($this->events, fn($a, $b) => strcmp($a['start_date'] ?? '', $b['start_date'] ?? ''));
    }

    public function getSelectedCampaignModelProperty()
    {
        return Campaign::find($this->campaign_id);
    }

    public function getTrackTypeProperty()
    {
        return $this->selectedCampaignModel?->track_type ?? 'ACADEMIC';
    }

    // Step navigation
    public function nextStep()
    {
        if ($this->currentStep === 1) {
            $this->cnic_number = $this->formatCnic($this->cnic_number);
            if ($this->same_address) {
                $this->mailing_address = $this->permanent_address;
            }
        }

        $rules = $this->getRules();
        if (!empty($rules)) {
            $this->validate($rules);
        }

        if ($this->currentStep === 1) {
            $this->saveProfileStep1();
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        $step = intval($step);
        if ($step < 1 || $step > $this->totalSteps) {
            return;
        }

        // Validate and save step 1 if navigating forward from step 1
        if ($this->currentStep === 1 && $step > 1) {
            $this->cnic_number = $this->formatCnic($this->cnic_number);
            if ($this->same_address) {
                $this->mailing_address = $this->permanent_address;
            }
            $rules = $this->getRules();
            if (!empty($rules)) {
                $this->validate($rules);
            }
            $this->saveProfileStep1();
        }

        $this->currentStep = $step;
    }

    public function getCountriesProperty()
    {
        return [
            'Pakistan',
            'United States',
            'United Kingdom',
            'Canada',
            'Australia',
            'Saudi Arabia',
            'United Arab Emirates',
            'China',
            'Germany',
            'France',
            'Turkey',
            'Malaysia',
            'Qatar',
            'Oman',
            'Kuwait',
            'Bahrain',
            'Singapore',
            'Japan',
            'South Korea',
            'India',
            'Bangladesh',
            'Sri Lanka',
            'Nepal',
            'Afghanistan',
            'Iran',
            'Egypt',
            'South Africa',
            'Nigeria',
            'Kenya',
            'Italy',
            'Spain',
            'Netherlands',
            'Switzerland',
            'Sweden',
            'Norway',
            'Denmark',
            'Finland',
            'Ireland',
            'New Zealand',
            'Brazil',
            'Other Country',
        ];
    }

    public function getStatesProperty()
    {
        $country = strtolower(trim($this->nationality ?? ''));

        if (in_array($country, ['pakistan', 'pakistani', ''])) {
            return [
                'Punjab',
                'Sindh',
                'Khyber Pakhtunkhwa',
                'Balochistan',
                'Islamabad Capital Territory',
                'Gilgit-Baltistan',
                'Azad Jammu and Kashmir',
            ];
        }

        if (in_array($country, ['united states', 'usa', 'us', 'american'])) {
            return [
                'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado',
                'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho',
                'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
                'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota',
                'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada',
                'New Hampshire', 'New Jersey', 'New Mexico', 'New York',
                'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon',
                'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota',
                'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington',
                'West Virginia', 'Wisconsin', 'Wyoming'
            ];
        }

        if (in_array($country, ['canada', 'canadian'])) {
            return [
                'Alberta', 'British Columbia', 'Manitoba', 'New Brunswick',
                'Newfoundland and Labrador', 'Northwest Territories', 'Nova Scotia',
                'Nunavut', 'Ontario', 'Prince Edward Island', 'Quebec', 'Saskatchewan', 'Yukon'
            ];
        }

        if (in_array($country, ['united kingdom', 'uk', 'british', 'great britain'])) {
            return ['England', 'Scotland', 'Wales', 'Northern Ireland'];
        }

        if (in_array($country, ['australia', 'australian'])) {
            return [
                'Australian Capital Territory', 'New South Wales', 'Northern Territory',
                'Queensland', 'South Australia', 'Tasmania', 'Victoria', 'Western Australia'
            ];
        }

        if (in_array($country, ['united arab emirates', 'uae', 'emirati'])) {
            return ['Abu Dhabi', 'Dubai', 'Sharjah', 'Ajman', 'Umm Al Quwain', 'Ras Al Khaimah', 'Fujairah'];
        }

        if (in_array($country, ['saudi arabia', 'saudi'])) {
            return [
                'Riyadh', 'Makkah', 'Eastern Province', 'Madinah', 'Al-Qassim',
                'Asir', 'Tabuk', 'Hail', 'Northern Borders', 'Jazan', 'Najran', 'Al-Bahah', 'Al-Jawf'
            ];
        }

        if (in_array($country, ['china', 'chinese'])) {
            return ['Beijing', 'Shanghai', 'Guangdong', 'Zhejiang', 'Jiangsu', 'Sichuan', 'Hubei', 'Shandong'];
        }

        if (in_array($country, ['germany', 'german'])) {
            return ['Bavaria', 'Berlin', 'Baden-Württemberg', 'North Rhine-Westphalia', 'Hesse', 'Saxony', 'Hamburg'];
        }

        if (in_array($country, ['turkey', 'turkish', 'türkiye'])) {
            return ['Istanbul', 'Ankara', 'Izmir', 'Bursa', 'Antalya', 'Adana', 'Konya'];
        }

        if (in_array($country, ['india', 'indian'])) {
            return ['Maharashtra', 'Delhi', 'Karnataka', 'Tamil Nadu', 'Uttar Pradesh', 'Gujarat', 'West Bengal', 'Punjab'];
        }

        return [];
    }

    public function updatedNationality($value)
    {
        $states = $this->states;
        if (!empty($states)) {
            if (!in_array($this->domicile, $states)) {
                $this->domicile = $states[0] ?? '';
            }
        }
    }

    // Save basicInfo Step 1
    protected function saveProfileStep1()
    {
        $basicInfo = BasicInfo::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'track_type' => $this->trackType,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name ?: null,
                'cnic_number' => $this->cnic_number,
                'date_of_birth' => $this->date_of_birth,
                'quota' => $this->quota,
                'gender' => $this->gender,
                'father_name' => $this->father_name,
                'phone_number' => $this->phone_number,
                'secondary_phone' => $this->secondary_phone,
                'secondary_email' => $this->secondary_email,
                'same_address' => $this->same_address,
                'permanent_address' => $this->permanent_address,
                'mailing_address' => $this->mailing_address,
                'place_of_birth' => $this->place_of_birth,
                'nationality' => $this->nationality,
                'religion' => $this->religion,
                'marital_status' => $this->marital_status,
                'domicile' => $this->domicile,
            ]
        );

        if ($this->disability_cert) {
            $path = $this->disability_cert->store('certificates', 'public');
            $basicInfo->update([
                'dynamic_attributes' => array_merge(
                    $basicInfo->dynamic_attributes,
                    ['disability_cert_path' => $path]
                )
            ]);
        }
    }

    // Real-time CNIC formatting and Address synchronization
    public function updatedCnicNumber($value)
    {
        $this->cnic_number = $this->formatCnic($value);
    }

    public function updatedSameAddress($value)
    {
        if ($value) {
            $this->mailing_address = $this->permanent_address;
        }
    }

    public function updatedPermanentAddress($value)
    {
        if ($this->same_address) {
            $this->mailing_address = $value;
        }
    }

    protected function formatCnic($value)
    {
        if (!$value) return $value;
        $digits = preg_replace('/[^0-9]/', '', $value);
        $formatted = '';
        if (strlen($digits) > 0) {
            $formatted .= substr($digits, 0, min(5, strlen($digits)));
        }
        if (strlen($digits) > 5) {
            $formatted .= '-' . substr($digits, 5, min(7, strlen($digits) - 5));
        }
        if (strlen($digits) > 12) {
            $formatted .= '-' . substr($digits, 12, min(1, strlen($digits) - 12));
        }
        return $formatted;
    }

    // Dynamic sequential degree progression options
    public function getNextDegreeLevelOptionsProperty()
    {
        $existingTypes = collect($this->degrees)->pluck('degree_level')->toArray();

        // 1. If SSC is missing
        if (!in_array('SSC', $existingTypes)) {
            return ['SSC' => 'Secondary School Certificate (Matric / O-Level / Equivalent)'];
        }

        // 2. If HSSC is missing
        if (!in_array('HSSC', $existingTypes)) {
            return ['HSSC' => 'Higher Secondary School Certificate (FA / FSc / A-Level / Equivalent)'];
        }

        // 3. If neither 14_YEARS nor 16_YEARS is present
        if (!in_array('14_YEARS', $existingTypes) && !in_array('16_YEARS', $existingTypes)) {
            return [
                '14_YEARS' => 'Bachelor\'s (14-Year: BA / BSc / Associate Degree / Equivalent)',
                '16_YEARS' => 'Bachelor\'s / Master\'s (16-Year: BS / BE / BSc Hons / Equivalent)'
            ];
        }

        // 4. If 14_YEARS is present but 16_YEARS_MA is missing (and 16_YEARS is also missing)
        if (in_array('14_YEARS', $existingTypes) && !in_array('16_YEARS_MA', $existingTypes) && !in_array('16_YEARS', $existingTypes)) {
            return ['16_YEARS_MA' => 'Master\'s (16-Year: MA / MSc / Equivalent)'];
        }

        // 5. If 16-year is complete (either via 16_YEARS or 14_YEARS + 16_YEARS_MA)
        if (!in_array('18_YEARS', $existingTypes)) {
            return [
                '18_YEARS' => 'Master\'s / MPhil (18-Year: MS / MPhil / ME / Equivalent)',
                'PHD' => 'Doctoral (PhD / Equivalent)'
            ];
        }

        // 6. If 18_YEARS is present but PHD is missing
        if (!in_array('PHD', $existingTypes)) {
            return ['PHD' => 'Doctoral (PhD / Equivalent)'];
        }

        // 7. If PHD is present but POST_DOC is missing
        if (!in_array('POST_DOC', $existingTypes)) {
            return ['POST_DOC' => 'Postdoctoral Fellowship (Post-Doc)'];
        }

        return [];
    }

    // Academic Degrees
    public function addDegree()
    {
        if (empty($this->getNextDegreeLevelOptionsProperty())) {
            $this->addError('new_degree_level', 'All supported degrees have already been added.');
            return;
        }

        $isProofRequired = $this->selectedCampaignModel?->requiresTranscriptProof() ?? true;

        $this->validate([
            'new_degree_level' => 'required|string|in:' . implode(',', array_keys($this->getNextDegreeLevelOptionsProperty())),
            'new_degree_country' => 'required|string|max:100',
            'new_degree_title' => 'required|string',
            'new_degree_inst' => 'required|string',
            'new_degree_year' => 'required|integer',
            'new_degree_gpa' => 'required|numeric',
            'new_degree_major' => 'required|string',
            'new_degree_board' => 'required|string',
            'new_degree_division' => 'required|string',
            'new_degree_scale' => 'required|string',
            'new_degree_proof' => $isProofRequired ? 'required|file|max:2048' : 'nullable|file|max:2048',
        ]);

        $path = $this->new_degree_proof ? $this->new_degree_proof->store('degrees', 'public') : null;
        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->degrees()->create([
            'degree_level' => $this->new_degree_level,
            'degree_title' => $this->new_degree_title,
            'country' => $this->new_degree_country,
            'institution' => $this->new_degree_inst,
            'year' => (int)$this->new_degree_year,
            'gpa' => $this->new_degree_gpa,
            'major' => $this->new_degree_major,
            'board' => $this->new_degree_board,
            'division' => $this->new_degree_division,
            'scale' => $this->new_degree_scale,
            'proof_path' => $path,
        ]);

        $this->refreshDegrees();

        // Reset inputs
        $this->new_degree_title = '';
        $this->new_degree_inst = '';
        $this->new_degree_year = '';
        $this->new_degree_gpa = '';
        $this->new_degree_major = '';
        $this->new_degree_board = '';
        $this->new_degree_division = '';
        $this->new_degree_scale = '4.0';
        $this->new_degree_proof = null;
    }

    public function removeDegree($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->degrees()->where('id', $id)->delete();
            $this->refreshDegrees();
        }
    }

    // Medals
    public function addMedal()
    {
        $this->validate([
            'new_medal_degree' => 'required|string',
            'new_medal_type' => 'required|in:GOLD,SILVER,BRONZE',
            'new_medal_year' => 'required|integer',
            'new_medal_proof' => 'required|file|max:2048',
        ]);

        $path = $this->new_medal_proof->store('medals', 'public');
        $basicInfo = Auth::user()->basicInfo;

        AcademicMedal::create([
            'basic_info_id' => $basicInfo->id,
            'degree_title' => $this->new_medal_degree,
            'medal_type' => $this->new_medal_type,
            'year_awarded' => $this->new_medal_year,
            'transcription_proof_path' => $path,
        ]);

        $this->reloadDatabaseRelations($basicInfo);

        $this->new_medal_degree = '';
        $this->new_medal_year = '';
        $this->new_medal_proof = null;
    }

    public function removeMedal($id)
    {
        AcademicMedal::destroy($id);
        $this->reloadDatabaseRelations(Auth::user()->basicInfo);
    }

    // Experience
    public function addExperience()
    {
        $this->validate([
            'new_exp_title' => 'required|string',
            'new_exp_company' => 'required|string',
            'new_exp_start' => 'required|date',
            'new_exp_end' => 'nullable|date|after_or_equal:new_exp_start',
            'new_exp_sector' => 'required|in:Government,Semi-Government,Private',
            'new_exp_salary' => 'required|numeric',
            'new_exp_proof' => 'required|file|max:2048',
            'new_exp_description' => 'required|string|max:1000',
        ]);

        $path = $this->new_exp_proof->store('experience', 'public');
        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->experiences()->create([
            'job_title' => $this->new_exp_title,
            'organization' => $this->new_exp_company,
            'start_date' => $this->new_exp_start,
            'end_date' => $this->new_exp_end ?: null,
            'experience_type' => $this->new_exp_type ?? 'Default',
            'sector' => $this->new_exp_sector,
            'salary' => $this->new_exp_salary,
            'proof_path' => $path,
            'description' => $this->new_exp_description,
        ]);

        $this->refreshExperiences();

        $this->new_exp_title = '';
        $this->new_exp_company = '';
        $this->new_exp_start = '';
        $this->new_exp_end = '';
        $this->new_exp_salary = '';
        $this->new_exp_proof = null;
        $this->new_exp_description = '';
    }

    public function removeExperience($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->experiences()->where('id', $id)->delete();
            $this->refreshExperiences();
        }
    }

    // Publications (Academic Step 4)
    public function addPublication()
    {
        $this->validate([
            'new_pub_title' => 'required|string',
            'new_pub_journal' => 'required|string',
            'new_pub_issn' => 'required|string|max:9',
            'new_pub_category' => 'required|in:W,X,Y,Z',
            'new_pub_impact' => 'required|numeric',
            'new_pub_author_order' => 'required|integer',
            'new_pub_authors' => 'required|string',
            'new_pub_doi' => 'required|string',
            'new_pub_volume' => 'required|string',
            'new_pub_url' => 'required|url',
            'new_pub_proof' => 'required|file|max:2048',
            'new_pub_year' => 'required|integer',
        ]);

        $path = $this->new_pub_proof->store('publications', 'public');
        $basicInfo = Auth::user()->basicInfo;

        Publication::create([
            'basic_info_id' => $basicInfo->id,
            'title' => $this->new_pub_title,
            'journal_name' => $this->new_pub_journal,
            'issn_print' => $this->new_pub_issn,
            'hjrs_category' => $this->new_pub_category,
            'impact_factor' => $this->new_pub_impact,
            'author_order' => $this->new_pub_author_order,
            'is_corresponding_author' => $this->new_pub_is_corresponding,
            'authors' => $this->new_pub_authors,
            'doi' => $this->new_pub_doi,
            'volume_issue' => $this->new_pub_volume,
            'url' => $this->new_pub_url,
            'proof_path' => $path,
            'publication_year' => (int)$this->new_pub_year,
        ]);

        $this->reloadDatabaseRelations($basicInfo);

        $this->new_pub_title = '';
        $this->new_pub_journal = '';
        $this->new_pub_issn = '';
        $this->new_pub_impact = 0.00;
        $this->new_pub_author_order = 1;
        $this->new_pub_is_corresponding = false;
        $this->new_pub_authors = '';
        $this->new_pub_doi = '';
        $this->new_pub_volume = '';
        $this->new_pub_url = '';
        $this->new_pub_proof = null;
        $this->new_pub_year = '';
    }

    public function removePublication($id)
    {
        Publication::destroy($id);
        $this->reloadDatabaseRelations(Auth::user()->basicInfo);
    }

    // Admin Milestones (Step 4)
    public function addMilestone()
    {
        $this->validate([
            'new_ms_title' => 'required|string',
            'new_ms_budget' => 'required|numeric',
            'new_ms_year' => 'required|integer',
            'new_ms_description' => 'required|string',
            'new_ms_proof' => 'required|file|max:2048',
        ]);

        $path = $this->new_ms_proof->store('milestones', 'public');
        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->milestones()->create([
            'title' => $this->new_ms_title,
            'budget' => $this->new_ms_budget,
            'year' => (int)$this->new_ms_year,
            'description' => $this->new_ms_description,
            'proof_path' => $path,
        ]);

        $this->refreshMilestones();
        
        $this->new_ms_title = '';
        $this->new_ms_budget = '';
        $this->new_ms_year = '';
        $this->new_ms_description = '';
        $this->new_ms_proof = null;
    }

    public function removeMilestone($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->milestones()->where('id', $id)->delete();
            $this->refreshMilestones();
        }
    }

    // Support Tools (Step 4)
    public function addTool()
    {
        $this->validate([
            'new_tool_name' => 'required|string',
            'new_tool_fitness' => 'required|file|max:2048',
            'new_tool_proficiency' => 'required|in:Beginner,Intermediate,Expert',
            'new_tool_experience' => 'required|integer',
        ]);

        $path = $this->new_tool_fitness->store('certificates', 'public');
        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->tools()->create([
            'tool_name' => $this->new_tool_name,
            'proficiency_level' => $this->new_tool_proficiency,
            'experience_years' => $this->new_tool_experience,
            'fitness_proof_path' => $path,
        ]);

        $this->refreshTools();

        $this->new_tool_name = '';
        $this->new_tool_fitness = null;
        $this->new_tool_proficiency = 'Intermediate';
        $this->new_tool_experience = '';
    }

    public function removeTool($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->tools()->where('id', $id)->delete();
            $this->refreshTools();
        }
    }

    // Academic Projects (Step 5)
    public function addProject()
    {
        $this->validate([
            'new_proj_title' => 'required|string',
            'new_proj_funding' => 'required|string',
            'new_proj_amount' => 'required|numeric',
            'new_proj_start' => 'required|date',
            'new_proj_role' => 'required|string',
            'new_proj_turnitin' => 'required|numeric|max:100',
            'new_proj_status' => 'required|in:ONGOING,COMPLETED',
            'new_proj_co_pis' => 'nullable|string',
            'new_proj_sponsor' => 'required|string',
            'new_proj_proof' => 'required|file|max:2048',
        ]);

        // Constraint check: Turnitin similarity index limit < 19%
        if ($this->new_proj_turnitin >= 19.00) {
            $this->addError('new_proj_turnitin', 'Turnitin Similarity Index must be strictly below 19%.');
            return;
        }

        $path = $this->new_proj_proof->store('projects', 'public');
        $basicInfo = Auth::user()->basicInfo;

        ResearchProject::create([
            'basic_info_id' => $basicInfo->id,
            'project_title' => $this->new_proj_title,
            'funding_agency' => $this->new_proj_funding,
            'grant_amount' => $this->new_proj_amount,
            'start_date' => $this->new_proj_start,
            'role_in_project' => $this->new_proj_role,
            'turnitin_similarity_percentage' => $this->new_proj_turnitin,
            'status' => $this->new_proj_status,
            'co_pis' => $this->new_proj_co_pis,
            'award_letter_proof_path' => $path,
        ]);

        $this->reloadDatabaseRelations($basicInfo);

        $this->new_proj_title = '';
        $this->new_proj_funding = '';
        $this->new_proj_amount = '';
        $this->new_proj_start = '';
        $this->new_proj_role = 'PI';
        $this->new_proj_turnitin = 0.00;
        $this->new_proj_status = 'Completed';
        $this->new_proj_co_pis = '';
        $this->new_proj_sponsor = '';
        $this->new_proj_proof = null;
    }

    public function removeProject($id)
    {
        ResearchProject::destroy($id);
        $this->reloadDatabaseRelations(Auth::user()->basicInfo);
    }

    // Academic Patents (Step 5)
    public function addPatent()
    {
        $this->validate([
            'new_pat_title' => 'required|string',
            'new_pat_registry_id' => 'required|string',
            'new_pat_date' => 'required|date',
            'new_pat_country' => 'required|string',
            'new_pat_proof' => 'required|file|max:2048',
        ]);

        $path = $this->new_pat_proof->store('patents', 'public');
        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->patents()->create([
            'title' => $this->new_pat_title,
            'registry_id' => $this->new_pat_registry_id,
            'is_licensed' => (bool)$this->new_pat_licensed,
            'date_registered' => $this->new_pat_date,
            'country' => $this->new_pat_country,
            'proof_path' => $path,
        ]);

        $this->refreshPatents();

        $this->new_pat_title = '';
        $this->new_pat_registry_id = '';
        $this->new_pat_licensed = false;
        $this->new_pat_date = '';
        $this->new_pat_country = 'Pakistan';
        $this->new_pat_proof = null;
    }

    public function removePatent($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->patents()->where('id', $id)->delete();
            $this->refreshPatents();
        }
    }

    // Admin & Support Portfolio (Step 5)
    public function addPortfolioItem()
    {
        $this->validate([
            'new_port_title' => 'required|string',
            'new_port_description' => 'required|string',
            'new_port_url' => 'nullable|url',
            'new_port_date' => 'required|date',
            'new_port_proof' => 'required|file|max:2048',
        ]);

        $path = $this->new_port_proof->store('portfolio', 'public');
        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->portfolioItems()->create([
            'title' => $this->new_port_title,
            'description' => $this->new_port_description,
            'url' => $this->new_port_url ?: null,
            'date_completed' => $this->new_port_date,
            'proof_path' => $path,
        ]);

        $this->refreshPortfolioItems();

        $this->new_port_title = '';
        $this->new_port_description = '';
        $this->new_port_url = '';
        $this->new_port_date = '';
        $this->new_port_proof = null;
    }

    public function removePortfolioItem($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->portfolioItems()->where('id', $id)->delete();
            $this->refreshPortfolioItems();
        }
    }

    // Professional Events (Step 6)
    public function addEvent()
    {
        $this->validate([
            'new_event_name' => 'required|string',
            'new_event_type' => 'required|in:SEMINAR,CONFERENCE,WORKSHOP,SYMPOSIUM',
            'new_event_role' => 'required|in:LEADER_ORGANIZER,ASSOCIATE_ORGANIZER,PRESENTER_INTERNATIONAL,PRESENTER_NATIONAL,ATTENDEE',
            'new_event_start' => 'required|date',
            'new_event_end' => 'required|date|after_or_equal:new_event_start',
            'new_event_location' => 'required|string',
            'new_event_organizer' => 'required|string',
            'new_event_proof' => 'required|file|max:2048',
        ]);

        $path = $this->new_event_proof->store('events', 'public');
        $basicInfo = Auth::user()->basicInfo;

        ProfessionalEvent::create([
            'basic_info_id' => $basicInfo->id,
            'event_name' => $this->new_event_name,
            'event_type' => $this->new_event_type,
            'event_role' => $this->new_event_role,
            'paper_title' => $this->new_event_paper,
            'start_date' => $this->new_event_start,
            'end_date' => $this->new_event_end,
            'location' => $this->new_event_location,
            'organizer' => $this->new_event_organizer,
            'certificate_proof_path' => $path,
        ]);

        $this->reloadDatabaseRelations($basicInfo);

        $this->new_event_name = '';
        $this->new_event_paper = '';
        $this->new_event_start = '';
        $this->new_event_end = '';
        $this->new_event_location = '';
        $this->new_event_organizer = '';
        $this->new_event_proof = null;
    }

    public function removeEvent($id)
    {
        ProfessionalEvent::destroy($id);
        $this->reloadDatabaseRelations(Auth::user()->basicInfo);
    }

    // Support Training (Step 6)
    public function addTraining()
    {
        $this->validate([
            'new_training_title' => 'required|string',
            'new_training_body' => 'required|string',
            'new_training_expiry' => 'nullable|date',
            'new_training_proof' => 'required|file|max:2048',
        ]);

        $path = $this->new_training_proof->store('trainings', 'public');
        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->trainings()->create([
            'title' => $this->new_training_title,
            'body' => $this->new_training_body,
            'expiry_date' => $this->new_training_expiry ?: null,
            'proof_path' => $path,
        ]);

        $this->refreshTrainings();

        $this->new_training_title = '';
        $this->new_training_body = '';
        $this->new_training_expiry = '';
        $this->new_training_proof = null;
    }

    public function removeTraining($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->trainings()->where('id', $id)->delete();
            $this->refreshTrainings();
        }
    }

    // Referees (Step 7)
    public function addReferee()
    {
        $this->validate([
            'new_ref_name' => 'required|string',
            'new_ref_email' => 'required|email',
            'new_ref_phone' => 'required|string',
            'new_ref_designation' => 'required|string',
            'new_ref_org' => 'required|string',
            'new_ref_relation' => 'required|string',
            'new_ref_address' => 'required|string',
        ]);

        $basicInfo = Auth::user()->basicInfo;
        if (!$basicInfo) return;

        $basicInfo->referees()->create([
            'name' => $this->new_ref_name,
            'email' => $this->new_ref_email,
            'phone' => $this->new_ref_phone,
            'designation' => $this->new_ref_designation,
            'organization' => $this->new_ref_org,
            'relation' => $this->new_ref_relation,
            'address' => $this->new_ref_address,
        ]);

        $this->refreshReferees();

        $this->new_ref_name = '';
        $this->new_ref_email = '';
        $this->new_ref_phone = '';
        $this->new_ref_designation = '';
        $this->new_ref_org = '';
        $this->new_ref_relation = '';
        $this->new_ref_address = '';
    }

    public function removeReferee($id)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $basicInfo->referees()->where('id', $id)->delete();
            $this->refreshReferees();
        }
    }

    // Refresh database helpers
    protected function refreshDegrees()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->degrees = $basicInfo->degrees()->orderBy('year', 'asc')->get()->map(fn($d) => [
                'id' => $d->id,
                'degree_level' => $d->degree_level,
                'degree_title' => $d->degree_title,
                'country' => $d->country,
                'institution' => $d->institution,
                'year' => $d->year,
                'gpa' => $d->gpa,
                'scale' => $d->scale,
                'major' => $d->major,
                'board' => $d->board,
                'division' => $d->division,
                'proof_path' => $d->proof_path,
            ])->toArray();
        } else {
            $this->degrees = [];
        }

        $options = $this->getNextDegreeLevelOptionsProperty();
        $this->new_degree_level = !empty($options) ? array_key_first($options) : null;
        $this->new_degree_country = 'Pakistan';
    }

    protected function refreshExperiences()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->experience = $basicInfo->experiences()->orderBy('start_date', 'asc')->get()->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->job_title,
                'company' => $e->organization,
                'start_date' => $e->start_date?->format('Y-m-d'),
                'end_date' => $e->end_date?->format('Y-m-d'),
                'type' => $e->experience_type,
                'sector' => $e->sector,
                'salary' => $e->salary,
                'proof_path' => $e->proof_path,
                'description' => $e->description,
            ])->toArray();
        } else {
            $this->experience = [];
        }
    }

    protected function refreshMilestones()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->milestones = $basicInfo->milestones()->orderBy('year', 'asc')->get()->map(fn($m) => [
                'id' => $m->id,
                'title' => $m->title,
                'budget' => $m->budget,
                'year' => $m->year,
                'description' => $m->description,
                'proof_path' => $m->proof_path,
            ])->toArray();
        } else {
            $this->milestones = [];
        }
    }

    protected function refreshTools()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->tools = $basicInfo->tools->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->tool_name,
                'proficiency' => $t->proficiency_level,
                'experience' => $t->experience_years,
                'fitness_cert' => $t->fitness_proof_path,
            ])->toArray();
        } else {
            $this->tools = [];
        }
    }

    protected function refreshPatents()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->patents = $basicInfo->patents()->orderBy('date_registered', 'asc')->get()->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'registry_id' => $p->registry_id,
                'country' => $p->country,
                'date' => $p->date_registered?->format('Y-m-d'),
                'licensed' => (bool)$p->is_licensed,
                'proof_path' => $p->proof_path,
            ])->toArray();
        } else {
            $this->patents = [];
        }
    }

    protected function refreshPortfolioItems()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->portfolio_items = $basicInfo->portfolioItems()->orderBy('date_completed', 'asc')->get()->map(fn($po) => [
                'id' => $po->id,
                'title' => $po->title,
                'description' => $po->description,
                'url' => $po->url,
                'date' => $po->date_completed?->format('Y-m-d'),
                'proof_path' => $po->proof_path,
            ])->toArray();
        } else {
            $this->portfolio_items = [];
        }
    }

    protected function refreshTrainings()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->trainings = $basicInfo->trainings->map(fn($tr) => [
                'id' => $tr->id,
                'title' => $tr->title,
                'body' => $tr->body,
                'expiry' => $tr->expiry_date?->format('Y-m-d'),
                'proof_path' => $tr->proof_path,
            ])->toArray();
        } else {
            $this->trainings = [];
        }
    }

    protected function refreshReferees()
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $this->referees = $basicInfo->referees->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'email' => $r->email,
                'phone' => $r->phone,
                'designation' => $r->designation,
                'org' => $r->organization,
                'relation' => $r->relation,
                'address' => $r->address,
            ])->toArray();
        } else {
            $this->referees = [];
        }
    }

    // Dynamic attribute helper
    protected function saveDynamicAttributes($key, $value)
    {
        $basicInfo = Auth::user()->basicInfo;
        if ($basicInfo) {
            $attrs = $basicInfo->dynamic_attributes ?? [];
            $attrs[$key] = $value;
            $basicInfo->update(['dynamic_attributes' => $attrs]);
        }
    }

    // Finalize basicInfo and Application in Step 8
    public function generateChallan()
    {
        $campaign = Campaign::find($this->campaign_id);
        $basicInfo = Auth::user()->basicInfo;

        if (!$campaign || !$basicInfo) {
            return;
        }

        // Lock basicInfo from further edits
        $basicInfo->update(['is_locked' => true]);

        // Generate 12-digit consumer ID
        $this->consumer_id = '1000' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $this->application_fee = $campaign->application_fee;

        // Create application record if not exists
        $application = Application::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'campaign_id' => $campaign->id,
            ],
            [
                'status' => 'SUBMITTED',
                'submission_timestamp' => now(),
            ]
        );

        // Update application status to SUBMITTED if draft
        if ($application->status === 'DRAFT') {
            $application->update([
                'status' => 'SUBMITTED',
                'submission_timestamp' => now()
            ]);
        }

        // Create Billing Ledger Entry
        BillingLedger::updateOrCreate(
            ['application_id' => $application->id],
            [
                'consumer_id' => $this->consumer_id,
                'amount' => $campaign->application_fee,
                'status' => 'PENDING',
            ]
        );

        $this->challanGenerated = true;
    }

    public function render()
    {
        return view('livewire.applicant.basic-info-wizard', [
            'campaigns' => Campaign::where('is_active', true)->get(),
            'basicInfo' => Auth::user()->basicInfo,
        ])->layout('layouts.app');
    }
}
