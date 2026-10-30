<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Advertisement;
use App\Models\AdvertisementDateTrack;
use App\Models\Campaign;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\BillingLedger;
use App\Models\LiveEvaluation;
use App\Models\AcademicDegree;
use App\Models\Publication;
use App\Models\WorkExperience;
use App\Models\AcademicMedal;
use App\Models\ResearchProject;
use App\Models\Patent;
use App\Models\BasicInfo;
use App\Models\ReviewerProfile;
use App\Models\CommitteeConstitution;
use App\Models\RecruitmentProcessFlow;
use App\Models\SystemSetting;

class PakistanRecruitmentDemoSeeder extends Seeder
{
    /**
     * Run comprehensive historical & active Pakistan recruitment demo seeder.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. DISABLE MANDATORY DOCUMENT UPLOADS IN SYSTEM SETTINGS
        // ---------------------------------------------------------------------
        $this->command?->info("Step 1: Disabling mandatory document upload requirements in system settings...");

        $docSettings = [
            'require_transcript_proof' => false,
            'require_degree_proof' => false,
            'require_experience_certificate' => false,
            'require_publications_academic' => false,
            'require_challan_receipt_upload' => false,
            'require_profile_picture' => false,
        ];

        foreach ($docSettings as $key => $val) {
            SystemSetting::set(
                $key,
                $val,
                'boolean',
                'general',
                'Disabled by demo seeder: allows seamless candidate submission and testing without mandatory file uploads.'
            );
        }

        Campaign::query()->update(['require_transcript_proof' => false]);

        $registrar = User::where('email', 'registrar@university.edu')->first();
        $registrarId = $registrar?->id;

        $facultyConst = CommitteeConstitution::where('slug', 'statutory-selection-committee-assistant-professor')->first()
            ?? CommitteeConstitution::first();
        $profConst = CommitteeConstitution::where('slug', 'statutory-professorial-selection-board')->first()
            ?? CommitteeConstitution::skip(1)->first()
            ?? $facultyConst;
        $adminConst = CommitteeConstitution::where('slug', 'corporate-executive-leadership-panel')->first()
            ?? CommitteeConstitution::skip(2)->first()
            ?? $facultyConst;
        $supportConst = CommitteeConstitution::where('slug', 'public-sector-service-selection-board')->first()
            ?? CommitteeConstitution::skip(4)->first()
            ?? $facultyConst;

        $hecFlow = RecruitmentProcessFlow::where('slug', 'higher-education-standard-statutory-recruitment-pipeline')->first()
            ?? RecruitmentProcessFlow::first();
        $publicFlow = RecruitmentProcessFlow::where('slug', 'public-sector-service-selection-pipeline')->first()
            ?? RecruitmentProcessFlow::skip(2)->first()
            ?? $hecFlow;

        // ---------------------------------------------------------------------
        // 2. SEED ADVERTISEMENTS (2025=2, 2023=1, 2021=2, 2019=1, 2026=1)
        // ---------------------------------------------------------------------
        $this->command?->info("Step 2: Seeding advertisements (2025=2, 2023=1, 2021=2, 2019=1, 2026=1)...");

        // Migrate any previous legacy numbers to new chronological schema smoothly
        $legacyNumberMap = [
            '52/2025' => '56/2025',
            '53/2025' => '57/2025',
            '54/2023' => '55/2023',
            '55/2021' => '53/2021',
            '56/2021' => '54/2021',
            '57/2019' => '52/2019',
            '52/2026' => '56/2025',
            '53/2026' => '57/2025',
            '54/2026' => '55/2023',
            '55/2026' => '53/2021',
            '56/2026' => '54/2021',
            '57/2026' => '52/2019',
        ];
        foreach ($legacyNumberMap as $oldNo => $newNo) {
            Advertisement::where('advertisement_no', $oldNo)->update(['advertisement_no' => 'TMP-' . $oldNo]);
        }
        foreach ($legacyNumberMap as $oldNo => $newNo) {
            Advertisement::where('advertisement_no', 'TMP-' . $oldNo)->update(['advertisement_no' => $newNo]);
        }

        $legacyJobMap = [
            'CS-PROF-52' => 'CS-PROF-56',
            'SE-ASSOC-52' => 'SE-ASSOC-56',
            'CYB-PROF-52' => 'CYB-PROF-56',
            'MATH-ASSOC-52' => 'MATH-ASSOC-56',

            'AI-ASST-53' => 'AI-ASST-57',
            'CS-LECT-53' => 'CS-LECT-57',
            'DS-ASST-53' => 'DS-ASST-57',
            'SE-LECT-53' => 'SE-LECT-57',
            'HCI-ASST-53' => 'HCI-ASST-57',

            'ADM-REG-54' => 'ADM-REG-55',
            'ADM-TRS-54' => 'ADM-TRS-55',
            'IT-DIR-54' => 'IT-DIR-55',
            'AUD-DIR-54' => 'AUD-DIR-55',
            'PRO-ASST-54' => 'PRO-ASST-55',

            'EE-ASST-55' => 'EE-ASST-53',
            'ROB-LECT-55' => 'ROB-LECT-53',
            'MTR-ASST-55' => 'MTR-ASST-53',

            'MGT-ASST-56' => 'MGT-ASST-54',
            'ENG-LECT-56' => 'ENG-LECT-54',
            'ECO-ASST-56' => 'ECO-ASST-54',

            'DBA-SUP-57' => 'DBA-SUP-52',
            'NET-SUP-57' => 'NET-SUP-52',
            'LAB-SUP-57' => 'LAB-SUP-52',
            'HPC-SUP-57' => 'HPC-SUP-52',
            'AV-SUP-57' => 'AV-SUP-52',
        ];
        foreach ($legacyJobMap as $oldCode => $newCode) {
            Campaign::where('job_code', $oldCode)->update(['job_code' => 'TMP-' . $oldCode]);
        }
        foreach ($legacyJobMap as $oldCode => $newCode) {
            Campaign::where('job_code', 'TMP-' . $oldCode)->update(['job_code' => $newCode]);
        }

        $adsData = [
            // Adv 52: Year 2019 (1 of 2019)
            [
                'advertisement_no' => '52/2019',
                'title' => 'Technical Support Cadre, IT Specialists & Laboratory Technologists - 2019',
                'year' => 2019,
                'details' => 'Recruitment for core IT network infrastructure, computing cluster operations, and laboratory technologist support. Fully concluded.',
                'newspaper_reference' => 'Daily Express & Dawn (Ref: PID(I)1199/19)',
                'valid_from' => Carbon::parse('2019-05-01 09:00:00'),
                'valid_to' => Carbon::parse('2019-06-30 17:00:00'),
                'is_active' => true,
                'has_extension' => false,
            ],
            // Adv 53: Year 2021 (1st of 2021)
            [
                'advertisement_no' => '53/2021',
                'title' => 'Faculty of Engineering & Applied Sciences (Electrical & Robotics) Recruitment - 2021',
                'year' => 2021,
                'details' => 'Recruitment for academic faculty in electrical power systems, robotics, and cyber-physical systems under HEC / PEC criteria. Fully concluded.',
                'newspaper_reference' => 'The Nation & Daily Khabrain (Ref: PID(I)1145/21)',
                'valid_from' => Carbon::parse('2021-02-01 09:00:00'),
                'valid_to' => Carbon::parse('2021-03-31 17:00:00'),
                'is_active' => true,
                'has_extension' => false,
            ],
            // Adv 54: Year 2021 (2nd of 2021)
            [
                'advertisement_no' => '54/2021',
                'title' => 'Faculty of Management Sciences, FinTech & Humanities Appointments - 2021',
                'year' => 2021,
                'details' => 'Applications invited for faculty positions in Business Administration, Financial Technologies, and Technical Communication. Fully concluded.',
                'newspaper_reference' => 'The News International & Dunya News (Ref: PID(I)1172/21)',
                'valid_from' => Carbon::parse('2021-08-01 09:00:00'),
                'valid_to' => Carbon::parse('2021-09-30 17:00:00'),
                'is_active' => true,
                'has_extension' => false,
            ],
            // Adv 55: Year 2023 (1 of 2023)
            [
                'advertisement_no' => '55/2023',
                'title' => 'Senior Administrative, Finance & Executive Officers Induction Drive - 2023',
                'year' => 2023,
                'details' => 'Applications were solicited for statutory administrative leadership appointments including Additional Registrar, Deputy Treasurer, and Internal Audit Director. Fully concluded.',
                'newspaper_reference' => 'Nawa-i-Waqt & Daily Dawn (Ref: PID(I)1120/23)',
                'valid_from' => Carbon::parse('2023-03-01 09:00:00'),
                'valid_to' => Carbon::parse('2023-04-30 17:00:00'),
                'is_active' => true,
                'has_extension' => true,
                'prev_valid_to' => Carbon::parse('2023-04-15 17:00:00'),
                'ext_valid_to' => Carbon::parse('2023-04-30 17:00:00'),
                'ext_date' => Carbon::parse('2023-04-10 11:30:00'),
            ],
            // Adv 56: Year 2025 (1st of 2025)
            [
                'advertisement_no' => '56/2025',
                'title' => 'Annual Statutory Faculty Induction & Distinguished Research Chairs - 2025',
                'year' => 2025,
                'details' => 'Applications were invited from qualified Pakistani / AJK nationals possessing PhD degrees for tenure track and statutory appointments in the Faculty of Computing and Software Engineering. Fully concluded.',
                'newspaper_reference' => 'Daily Dawn, The News & Daily Jang (Ref: PID(I)1042/25)',
                'valid_from' => Carbon::parse('2025-01-15 09:00:00'),
                'valid_to' => Carbon::parse('2025-03-15 17:00:00'),
                'is_active' => true,
                'has_extension' => true,
                'prev_valid_to' => Carbon::parse('2025-02-28 17:00:00'),
                'ext_valid_to' => Carbon::parse('2025-03-15 17:00:00'),
                'ext_date' => Carbon::parse('2025-02-25 10:00:00'),
            ],
            // Adv 57: Year 2025 (2nd of 2025)
            [
                'advertisement_no' => '57/2025',
                'title' => 'Faculty of Computing, Artificial Intelligence & Data Sciences Recruitment - 2025',
                'year' => 2025,
                'details' => 'Strategic academic hiring drive for newly instituted centers in Generative AI, Distributed Cloud Architectures, and Cyber Defense Systems. Fully concluded.',
                'newspaper_reference' => 'Daily Jang & The Express Tribune (Ref: PID(I)1088/25)',
                'valid_from' => Carbon::parse('2025-06-01 09:00:00'),
                'valid_to' => Carbon::parse('2025-07-31 17:00:00'),
                'is_active' => true,
                'has_extension' => true,
                'prev_valid_to' => Carbon::parse('2025-07-15 17:00:00'),
                'ext_valid_to' => Carbon::parse('2025-07-31 17:00:00'),
                'ext_date' => Carbon::parse('2025-07-10 14:00:00'),
            ],
            // Adv 58: Year 2026 (+1 Number: Apply date passed, not valid for more applies; candidate processes in diverse stages)
            [
                'advertisement_no' => '58/2026',
                'title' => 'Advertisement No. 58/2026 - Accelerated Faculty & Emerging Technologies Induction Drive',
                'year' => 2026,
                'details' => 'Applications were invited for distinguished professorial chairs, associate professors, and quality enhancement directorships. Online application deadline has closed; candidate dossiers are currently in active committee, peer review, and selection stages.',
                'newspaper_reference' => 'Daily Dawn, The News & Express Tribune (Ref: PID(I)1280/26)',
                'valid_from' => now()->subDays(40)->startOfDay(),
                'valid_to' => now()->subDays(10)->endOfDay(), // Apply date passed, not valid for more applies!
                'is_active' => true,
                'has_extension' => false,
            ],
        ];

        $advertisements = [];
        foreach ($adsData as $data) {
            $hasExt = $data['has_extension'];
            $prevTo = $data['prev_valid_to'] ?? null;
            $extTo = $data['ext_valid_to'] ?? null;
            $extDate = $data['ext_date'] ?? null;

            unset($data['has_extension'], $data['prev_valid_to'], $data['ext_valid_to'], $data['ext_date']);

            $data['created_by'] = $registrarId;
            $ad = Advertisement::updateOrCreate(
                ['advertisement_no' => $data['advertisement_no']],
                $data
            );
            $advertisements[$data['advertisement_no']] = $ad;

            if ($hasExt) {
                AdvertisementDateTrack::updateOrCreate(
                    [
                        'advertisement_id' => $ad->id,
                        'corrigendum_no' => "CORRIGENDUM-{$ad->advertisement_no}-EXT",
                    ],
                    [
                        'previous_valid_to' => $prevTo,
                        'extended_valid_to' => $extTo,
                        'reason' => 'Date extended upon approval of the Competent Authority. Concluded.',
                        'extended_at' => $extDate,
                        'extended_by' => $registrarId,
                    ]
                );
            }
        }

        // ---------------------------------------------------------------------
        // 3. SEED 30 CAMPAIGNS (25 HISTORICAL + 5 UNDER ADV 58)
        // ---------------------------------------------------------------------
        $this->command?->info("Step 3: Seeding 30 job campaigns (matched to advertisement timelines)...");

        $campaignsData = [
            // Adv 52/2019 (Year 2019: May 01 to June 30, 2019)
            [
                'advertisement_id' => $advertisements['52/2019']->id,
                'job_code' => 'DBA-SUP-52',
                'title' => 'Senior Database & Storage Administrator (BPS-17)',
                'track_type' => 'SUPPORT',
                'department' => 'Information Technology Services',
                'opening_date' => Carbon::parse('2019-05-01 09:00:00'),
                'closing_date' => Carbon::parse('2019-06-30 17:00:00'),
                'application_fee' => 1500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $supportConst?->id,
                'process_flow_id' => $publicFlow?->id,
                'scoring_rules' => [
                    'trade_cert_points' => 10.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['52/2019']->id,
                'job_code' => 'NET-SUP-52',
                'title' => 'Network & Cloud Security Specialist (BPS-17)',
                'track_type' => 'SUPPORT',
                'department' => 'Campus Telecommunications & Networks',
                'opening_date' => Carbon::parse('2019-05-01 09:00:00'),
                'closing_date' => Carbon::parse('2019-06-30 17:00:00'),
                'application_fee' => 1500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $supportConst?->id,
                'process_flow_id' => $publicFlow?->id,
                'scoring_rules' => [
                    'trade_cert_points' => 10.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['52/2019']->id,
                'job_code' => 'LAB-SUP-52',
                'title' => 'Senior Hardware & Laboratory Technologist (BPS-16)',
                'track_type' => 'SUPPORT',
                'department' => 'Computing & Electronics Laboratories',
                'opening_date' => Carbon::parse('2019-05-01 09:00:00'),
                'closing_date' => Carbon::parse('2019-06-30 17:00:00'),
                'application_fee' => 1000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $supportConst?->id,
                'process_flow_id' => $publicFlow?->id,
                'scoring_rules' => [
                    'trade_cert_points' => 10.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['52/2019']->id,
                'job_code' => 'HPC-SUP-52',
                'title' => 'High-Performance Computing (HPC) Systems Engineer (BPS-17)',
                'track_type' => 'SUPPORT',
                'department' => 'Advanced Computing Cluster Operations',
                'opening_date' => Carbon::parse('2019-05-01 09:00:00'),
                'closing_date' => Carbon::parse('2019-06-30 17:00:00'),
                'application_fee' => 1500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $supportConst?->id,
                'process_flow_id' => $publicFlow?->id,
                'scoring_rules' => [
                    'trade_cert_points' => 10.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['52/2019']->id,
                'job_code' => 'AV-SUP-52',
                'title' => 'Senior Smart Classroom & Multimedia Technologist (BPS-16)',
                'track_type' => 'SUPPORT',
                'department' => 'Educational Technology & Multimedia Division',
                'opening_date' => Carbon::parse('2019-05-01 09:00:00'),
                'closing_date' => Carbon::parse('2019-06-30 17:00:00'),
                'application_fee' => 1000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $supportConst?->id,
                'process_flow_id' => $publicFlow?->id,
                'scoring_rules' => [
                    'trade_cert_points' => 10.0,
                    'require_transcript_proof' => false,
                ],
            ],

            // Adv 53/2021 (Year 2021: Feb 01 to Mar 31, 2021)
            [
                'advertisement_id' => $advertisements['53/2021']->id,
                'job_code' => 'EE-ASST-53',
                'title' => 'Assistant Professor of Electrical Engineering (BPS-19 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Electrical Engineering',
                'opening_date' => Carbon::parse('2021-02-01 09:00:00'),
                'closing_date' => Carbon::parse('2021-03-31 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['53/2021']->id,
                'job_code' => 'ROB-LECT-53',
                'title' => 'Lecturer in Robotics & Cyber-Physical Systems (BPS-18)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Mechatronics & Robotics',
                'opening_date' => Carbon::parse('2021-02-01 09:00:00'),
                'closing_date' => Carbon::parse('2021-03-31 17:00:00'),
                'application_fee' => 2500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['53/2021']->id,
                'job_code' => 'MTR-ASST-53',
                'title' => 'Assistant Professor of Mechatronics & Industrial Automation (BPS-19 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Mechatronics Engineering',
                'opening_date' => Carbon::parse('2021-02-01 09:00:00'),
                'closing_date' => Carbon::parse('2021-03-31 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],

            // Adv 54/2021 (Year 2021: Aug 01 to Sep 30, 2021)
            [
                'advertisement_id' => $advertisements['54/2021']->id,
                'job_code' => 'MGT-ASST-54',
                'title' => 'Assistant Professor of Business Administration (FinTech) (BPS-19)',
                'track_type' => 'ACADEMIC',
                'department' => 'Faculty of Management Sciences',
                'opening_date' => Carbon::parse('2021-08-01 09:00:00'),
                'closing_date' => Carbon::parse('2021-09-30 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'x_category_weight' => 0.75,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['54/2021']->id,
                'job_code' => 'ENG-LECT-54',
                'title' => 'Lecturer in English & Technical Communication (BPS-18)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Humanities & Social Sciences',
                'opening_date' => Carbon::parse('2021-08-01 09:00:00'),
                'closing_date' => Carbon::parse('2021-09-30 17:00:00'),
                'application_fee' => 2500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['54/2021']->id,
                'job_code' => 'ECO-ASST-54',
                'title' => 'Assistant Professor of Quantitative Economics & Public Policy (BPS-19)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Economics',
                'opening_date' => Carbon::parse('2021-08-01 09:00:00'),
                'closing_date' => Carbon::parse('2021-09-30 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],

            // Adv 55/2023 (Year 2023: Mar 01 to Apr 30, 2023)
            [
                'advertisement_id' => $advertisements['55/2023']->id,
                'job_code' => 'ADM-REG-55',
                'title' => 'Additional Registrar (Administration & HR) (BPS-19)',
                'track_type' => 'ADMINISTRATIVE',
                'department' => 'Registrar Secretariat',
                'opening_date' => Carbon::parse('2023-03-01 09:00:00'),
                'closing_date' => Carbon::parse('2023-04-30 17:00:00'),
                'application_fee' => 3500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $adminConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'experience_years_weight' => 2.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['55/2023']->id,
                'job_code' => 'ADM-TRS-55',
                'title' => 'Deputy Treasurer (Finance & Accounts) (BPS-18)',
                'track_type' => 'ADMINISTRATIVE',
                'department' => 'Treasurer Office',
                'opening_date' => Carbon::parse('2023-03-01 09:00:00'),
                'closing_date' => Carbon::parse('2023-04-30 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $adminConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'experience_years_weight' => 1.5,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['55/2023']->id,
                'job_code' => 'IT-DIR-55',
                'title' => 'Assistant Director IT Infrastructure & Cloud Networks (BPS-17)',
                'track_type' => 'ADMINISTRATIVE',
                'department' => 'Directorate of Information Technology',
                'opening_date' => Carbon::parse('2023-03-01 09:00:00'),
                'closing_date' => Carbon::parse('2023-04-30 17:00:00'),
                'application_fee' => 2000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $adminConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'experience_years_weight' => 1.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['55/2023']->id,
                'job_code' => 'AUD-DIR-55',
                'title' => 'Director Internal Audit & Risk Oversight (BPS-19)',
                'track_type' => 'ADMINISTRATIVE',
                'department' => 'Internal Audit Office',
                'opening_date' => Carbon::parse('2023-03-01 09:00:00'),
                'closing_date' => Carbon::parse('2023-04-30 17:00:00'),
                'application_fee' => 3500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $adminConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'experience_years_weight' => 2.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['55/2023']->id,
                'job_code' => 'PRO-ASST-55',
                'title' => 'Assistant Director Public Relations & Corporate Media (BPS-17)',
                'track_type' => 'ADMINISTRATIVE',
                'department' => 'Public Relations Office',
                'opening_date' => Carbon::parse('2023-03-01 09:00:00'),
                'closing_date' => Carbon::parse('2023-04-30 17:00:00'),
                'application_fee' => 2000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $adminConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'experience_years_weight' => 1.0,
                    'require_transcript_proof' => false,
                ],
            ],

            // Adv 56/2025 (Year 2025: Jan 15 to Mar 15, 2025)
            [
                'advertisement_id' => $advertisements['56/2025']->id,
                'job_code' => 'CS-PROF-56',
                'title' => 'Professor of Computer Science (BPS-21 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Computer Science',
                'opening_date' => Carbon::parse('2025-01-15 09:00:00'),
                'closing_date' => Carbon::parse('2025-03-15 17:00:00'),
                'application_fee' => 5000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $profConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 2.0,
                    'x_category_weight' => 1.5,
                    'gold_medal_weight' => 5.0,
                    'project_million_rate' => 1.5,
                    'patent_granted_weight' => 2.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['56/2025']->id,
                'job_code' => 'SE-ASSOC-56',
                'title' => 'Associate Professor of Software Engineering (BPS-20 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Software Engineering',
                'opening_date' => Carbon::parse('2025-01-15 09:00:00'),
                'closing_date' => Carbon::parse('2025-03-15 17:00:00'),
                'application_fee' => 4000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $profConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.5,
                    'x_category_weight' => 1.0,
                    'gold_medal_weight' => 4.0,
                    'project_million_rate' => 1.0,
                    'patent_granted_weight' => 1.5,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['56/2025']->id,
                'job_code' => 'CYB-PROF-56',
                'title' => 'Professor of Information Security & Cryptography (BPS-21 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Cyber Security',
                'opening_date' => Carbon::parse('2025-01-15 09:00:00'),
                'closing_date' => Carbon::parse('2025-03-15 17:00:00'),
                'application_fee' => 5000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $profConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 2.0,
                    'gold_medal_weight' => 5.0,
                    'project_million_rate' => 1.5,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['56/2025']->id,
                'job_code' => 'MATH-ASSOC-56',
                'title' => 'Associate Professor of Computational Mathematics (BPS-20 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Mathematics & Statistics',
                'opening_date' => Carbon::parse('2025-01-15 09:00:00'),
                'closing_date' => Carbon::parse('2025-03-15 17:00:00'),
                'application_fee' => 4000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $profConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.5,
                    'gold_medal_weight' => 4.0,
                    'require_transcript_proof' => false,
                ],
            ],

            // Adv 57/2025 (Year 2025: June 01 to July 31, 2025)
            [
                'advertisement_id' => $advertisements['57/2025']->id,
                'job_code' => 'AI-ASST-57',
                'title' => 'Assistant Professor of Artificial Intelligence & Machine Learning (BPS-19 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Artificial Intelligence',
                'opening_date' => Carbon::parse('2025-06-01 09:00:00'),
                'closing_date' => Carbon::parse('2025-07-31 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'x_category_weight' => 0.75,
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['57/2025']->id,
                'job_code' => 'CS-LECT-57',
                'title' => 'Lecturer in Computer Science & Cyber Security (BPS-18)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Computer Science',
                'opening_date' => Carbon::parse('2025-06-01 09:00:00'),
                'closing_date' => Carbon::parse('2025-07-31 17:00:00'),
                'application_fee' => 2500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'gold_medal_weight' => 5.0,
                    'silver_medal_weight' => 3.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['57/2025']->id,
                'job_code' => 'DS-ASST-57',
                'title' => 'Assistant Professor of Data Science & Big Data Systems (BPS-19)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Data Science',
                'opening_date' => Carbon::parse('2025-06-01 09:00:00'),
                'closing_date' => Carbon::parse('2025-07-31 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'x_category_weight' => 0.75,
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['57/2025']->id,
                'job_code' => 'SE-LECT-57',
                'title' => 'Lecturer in Software Engineering & DevOps (BPS-18)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Software Engineering',
                'opening_date' => Carbon::parse('2025-06-01 09:00:00'),
                'closing_date' => Carbon::parse('2025-07-31 17:00:00'),
                'application_fee' => 2500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['57/2025']->id,
                'job_code' => 'HCI-ASST-57',
                'title' => 'Assistant Professor of Human-Computer Interaction & UX (BPS-19 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Software Engineering',
                'opening_date' => Carbon::parse('2025-06-01 09:00:00'),
                'closing_date' => Carbon::parse('2025-07-31 17:00:00'),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],

            // Adv 58/2026 (Year 2026: Opened 40 days ago, closed 10 days ago - Apply date passed)
            [
                'advertisement_id' => $advertisements['58/2026']->id,
                'job_code' => 'AI-PROF-58',
                'title' => 'Professor of Artificial Intelligence & Autonomous Systems (BPS-21 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Computer Science & AI',
                'opening_date' => now()->subDays(40)->startOfDay(),
                'closing_date' => now()->subDays(10)->endOfDay(),
                'application_fee' => 5000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $profConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 2.0,
                    'x_category_weight' => 1.5,
                    'gold_medal_weight' => 5.0,
                    'project_million_rate' => 1.5,
                    'patent_granted_weight' => 2.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['58/2026']->id,
                'job_code' => 'CS-ASSOC-58',
                'title' => 'Associate Professor of Cloud Systems & High-Performance Computing (BPS-20 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Computer Science',
                'opening_date' => now()->subDays(40)->startOfDay(),
                'closing_date' => now()->subDays(10)->endOfDay(),
                'application_fee' => 4000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.5,
                    'x_category_weight' => 1.0,
                    'gold_medal_weight' => 4.0,
                    'project_million_rate' => 1.0,
                    'patent_granted_weight' => 1.5,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['58/2026']->id,
                'job_code' => 'DS-ASST-58',
                'title' => 'Assistant Professor of Data Science & Big Data Analytics (BPS-19 / TTS)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Data Science',
                'opening_date' => now()->subDays(40)->startOfDay(),
                'closing_date' => now()->subDays(10)->endOfDay(),
                'application_fee' => 3000.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'w_category_weight' => 1.0,
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['58/2026']->id,
                'job_code' => 'QEC-DIR-58',
                'title' => 'Additional Director of Quality Enhancement & Institutional Accreditation (BPS-19)',
                'track_type' => 'ADMINISTRATIVE',
                'department' => 'Quality Enhancement Cell',
                'opening_date' => now()->subDays(40)->startOfDay(),
                'closing_date' => now()->subDays(10)->endOfDay(),
                'application_fee' => 3500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $adminConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'experience_years_weight' => 2.0,
                    'require_transcript_proof' => false,
                ],
            ],
            [
                'advertisement_id' => $advertisements['58/2026']->id,
                'job_code' => 'BIO-LECT-58',
                'title' => 'Lecturer in Computational Biology & Bioinformatics (BPS-18)',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Bioinformatics & Bioscience',
                'opening_date' => now()->subDays(40)->startOfDay(),
                'closing_date' => now()->subDays(10)->endOfDay(),
                'application_fee' => 2500.00,
                'is_active' => true,
                'require_transcript_proof' => false,
                'constitution_id' => $facultyConst?->id,
                'process_flow_id' => $hecFlow?->id,
                'scoring_rules' => [
                    'gold_medal_weight' => 5.0,
                    'require_transcript_proof' => false,
                ],
            ],
        ];

        $campaigns = [];
        foreach ($campaignsData as $cData) {
            $camp = Campaign::updateOrCreate(
                ['job_code' => $cData['job_code']],
                $cData
            );
            $campaigns[$cData['job_code']] = $camp;
        }

        // ---------------------------------------------------------------------
        // 4. EMPANEL PEER REVIEWERS (14 EXPERTS)
        // ---------------------------------------------------------------------
        $this->command?->info("Step 4: Empaneling distinguished national & international peer reviewers...");

        $reviewersToSeed = [
            [
                'email' => 'dr.kenji.sato@u-tokyo.ac.jp',
                'name' => 'Prof. Dr. Kenji Sato',
                'designation' => 'Chair Professor of Distributed Systems & Cloud Computing',
                'institution' => 'University of Tokyo',
                'country' => 'Japan',
                'department' => 'Department of Computer Science',
                'specialization' => 'High-Performance Cloud Infrastructure, Parallel Systems & Scalable Algorithms',
                'total_reviews_completed' => 19,
                'h_index' => 42,
            ],
            [
                'email' => 'dr.amira.hassan@aus.edu',
                'name' => 'Prof. Dr. Amira Hassan',
                'designation' => 'Professor of Electrical & Computer Engineering',
                'institution' => 'American University of Sharjah',
                'country' => 'United Arab Emirates',
                'department' => 'Department of Electrical Engineering',
                'specialization' => 'Embedded AI, Robotics, Cyber-Physical Controls & Signal Processing',
                'total_reviews_completed' => 14,
                'h_index' => 31,
            ],
            [
                'email' => 'prof.ahmad.khan@lums.edu.pk',
                'name' => 'Prof. Dr. Ahmad Khan',
                'designation' => 'Tenured Professor of Computer Science',
                'institution' => 'Lahore University of Management Sciences (LUMS)',
                'country' => 'Pakistan',
                'department' => 'Faculty of Computing',
                'specialization' => 'Applied Machine Learning, NLP for Low-Resource Languages & Vision Systems',
                'total_reviews_completed' => 26,
                'h_index' => 38,
            ],
            [
                'email' => 'prof.durrani@strath.ac.uk',
                'name' => 'Prof. Dr. Tariq S. Durrani, OBE',
                'designation' => 'Research Professor & IEEE Fellow',
                'institution' => 'University of Strathclyde, Glasgow',
                'country' => 'United Kingdom',
                'department' => 'Department of Electronic & Electrical Engineering',
                'specialization' => 'Signal Processing, Adaptive Filters & Higher Education Strategic Policy',
                'total_reviews_completed' => 35,
                'h_index' => 49,
            ],
            [
                'email' => 'dr.munir.ahmad@nus.edu.sg',
                'name' => 'Prof. Dr. Munir Ahmad',
                'designation' => 'Associate Professor of Computer Engineering',
                'institution' => 'National University of Singapore (NUS)',
                'country' => 'Singapore',
                'department' => 'Department of Computer Science',
                'specialization' => 'Formal Methods, Software Verification & Distributed Consensus Algorithms',
                'total_reviews_completed' => 22,
                'h_index' => 33,
            ],
            [
                'email' => 'dr.fatima.alkindi@ku.ac.ae',
                'name' => 'Dr. Fatima Al-Kindi',
                'designation' => 'Associate Professor of Biomedical Informatics',
                'institution' => 'Khalifa University',
                'country' => 'United Arab Emirates',
                'department' => 'Department of Biomedical Engineering',
                'specialization' => 'Genomics, Computational Biology & Healthcare Big Data Analytics',
                'total_reviews_completed' => 11,
                'h_index' => 25,
            ],
            [
                'email' => 'prof.arshad.ali@hec.gov.pk',
                'name' => 'Prof. Dr. Arshad Ali',
                'designation' => 'Former Executive Director HEC / Tenured Chair',
                'institution' => 'National University of Sciences & Technology (NUST)',
                'country' => 'Pakistan',
                'department' => 'Department of Computer Software Engineering',
                'specialization' => 'Grid Computing, Internet Routing, Research Policy & Academic Governance',
                'total_reviews_completed' => 41,
                'h_index' => 45,
            ],
            [
                'email' => 'dr.lin.chen@tsinghua.edu.cn',
                'name' => 'Dr. Lin Chen',
                'designation' => 'Distinguished Research Professor',
                'institution' => 'Tsinghua University, Beijing',
                'country' => 'China',
                'department' => 'School of Software',
                'specialization' => 'DevOps Automation, Microservices Testing & Software Reliability Engineering',
                'total_reviews_completed' => 18,
                'h_index' => 29,
            ],
            [
                'email' => 'prof.zartash.uzmi@lums.edu.pk',
                'name' => 'Prof. Dr. Zartash Afzal Uzmi',
                'designation' => 'Professor of Computer Science & Electrical Engineering',
                'institution' => 'LUMS, Syed Babar Ali School of Science and Engineering',
                'country' => 'Pakistan',
                'department' => 'Department of Computer Science',
                'specialization' => 'Wireless Networks, Telecom Protocols & Internet Architecture',
                'total_reviews_completed' => 27,
                'h_index' => 36,
            ],
            [
                'email' => 'dr.sophia.rodriguez@ethz.ch',
                'name' => 'Dr. Sophia Rodriguez',
                'designation' => 'Senior Research Scientist & Lab Director',
                'institution' => 'ETH Zurich',
                'country' => 'Switzerland',
                'department' => 'Department of Information Technology and Electrical Engineering',
                'specialization' => 'Cyber-Physical Systems, Edge Intelligence & Industrial IoT Security',
                'total_reviews_completed' => 16,
                'h_index' => 28,
            ],
            [
                'email' => 'prof.ijaz.gilani@qau.edu.pk',
                'name' => 'Prof. Dr. Ijaz Shafi Gilani',
                'designation' => 'Professor Emeritus & Senior Policy Fellow',
                'institution' => 'Quaid-i-Azam University (QAU) / Gallup Pakistan',
                'country' => 'Pakistan',
                'department' => 'Department of Humanities & Social Sciences',
                'specialization' => 'Quantitative Economics, Public Policy Analysis & Survey Methodologies',
                'total_reviews_completed' => 30,
                'h_index' => 34,
            ],
        ];

        $reviewersList = [];
        foreach ($reviewersToSeed as $rev) {
            $user = User::firstOrCreate(
                ['email' => $rev['email']],
                [
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $user->syncRoles(['academic-reviewer']);

            ReviewerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $rev['name'],
                    'subject' => $rev['department'],
                    'academic_rank' => $rev['designation'],
                    'university' => $rev['institution'],
                    'country' => $rev['country'],
                    'specialization' => $rev['specialization'],
                    'is_active' => true,
                ]
            );

            $reviewersList[] = $user;
        }

        // ---------------------------------------------------------------------
        // 5. SEED CANDIDATES WITH PROFILE PICTURES & DIVERSE STAGES
        // ---------------------------------------------------------------------
        $this->command?->info("Step 5: Seeding candidate profiles with portrait pictures and procedures...");

        // Diverse, high-resolution portrait facial photos for candidates
        $portraitPhotos = [
            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1548142813-c348350df52b?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1586297135537-94bc9ba060aa?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1542909168-82c3e7fdca5c?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=200&h=200&fit=crop&crop=face',
            'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=200&h=200&fit=crop&crop=face',
        ];

        $maleFirstNames = ['Muhammad', 'Ahmed', 'Tariq', 'Zubair', 'Bilal', 'Usman', 'Hamza', 'Farhan', 'Kamran', 'Kashif', 'Faisal', 'Shoaib', 'Adnan', 'Salman', 'Waqas', 'Imran', 'Saad', 'Noman', 'Babar', 'Arslan'];
        $femaleFirstNames = ['Fatima', 'Ayesha', 'Zainab', 'Maryam', 'Sana', 'Hina', 'Sadia', 'Amna', 'Khadija', 'Noreen', 'Uzma', 'Sidra', 'Madiha', 'Farah', 'Rabia', 'Shazia', 'Samina', 'Tahira', 'Shaista', 'Bushra'];
        $surnames = ['Khan', 'Qureshi', 'Malik', 'Abbasi', 'Sheikh', 'Bhatti', 'Chaudhry', 'Mirza', 'Raza', 'Siddiqui', 'Hashmi', 'Syed', 'Javed', 'Akhtar', 'Ansari', 'Gondal', 'Niazi', 'Tarar', 'Butt', 'Dar'];

        $regions = [
            ['city' => 'Islamabad', 'prefix' => '61101', 'board' => 'FBISE Islamabad', 'uni' => 'NUST Islamabad'],
            ['city' => 'Rawalpindi', 'prefix' => '37405', 'board' => 'BISE Rawalpindi', 'uni' => 'COMSATS Islamabad'],
            ['city' => 'Lahore', 'prefix' => '35202', 'board' => 'BISE Lahore', 'uni' => 'UET Lahore'],
            ['city' => 'Karachi', 'prefix' => '42101', 'board' => 'BIEK Karachi', 'uni' => 'NED University Karachi'],
            ['city' => 'Peshawar', 'prefix' => '17301', 'board' => 'BISE Peshawar', 'uni' => 'UET Peshawar'],
            ['city' => 'Quetta', 'prefix' => '54200', 'board' => 'BISE Quetta', 'uni' => 'BUITEMS Quetta'],
            ['city' => 'Faisalabad', 'prefix' => '33100', 'board' => 'BISE Faisalabad', 'uni' => 'University of Agriculture Faisalabad'],
            ['city' => 'Hyderabad', 'prefix' => '41301', 'board' => 'BISE Hyderabad', 'uni' => 'Mehran UET Jamshoro'],
        ];

        $disqualificationScenarios = [
            'HEC_EXP_SHORTFALL' => 'Ineligible under HEC Faculty Criteria: Minimum 8 years post-PhD teaching/research experience required. Candidate possesses 3 years and 4 months.',
            'TERMINAL_DEGREE_MISSING' => 'Ineligible: Terminal qualification shortfall. Advertised post requires terminal PhD in the relevant discipline. Candidate holds 18-year MS/MPhil.',
            'THIRD_DIV_STATUTE' => 'Disqualified: Candidate has a 3rd Division in HSSC (Intermediate FSc), prohibited under University Recruitment Statute Section 5(a).',
            'DISCIPLINE_MISMATCH' => 'Ineligible: Degree discipline mismatch. Master\'s in Library Science non-allied to Computer Science.',
            'HEC_PUBLICATION_SHORTFALL' => 'Disqualified on Research Benchmark: Does not fulfill statutory requirement of minimum 10 publications in HEC W/X journals.',
            'PEC_NON_ACCREDITED' => 'Ineligible under PEC Regulations: Undergraduate engineering degree is not Level-II Washington Accord accredited.',
            'OVER_AGE_LIMIT' => 'Disqualified due to Age Over-limit: Age exceeds statutory threshold of 35 years without approved government relaxation.',
        ];

        $board1 = User::where('email', 'board1@university.edu')->first();
        $board2 = User::where('email', 'board2@university.edu')->first();
        $board3 = User::where('email', 'board3@university.edu')->first();

        // -------------------------------------------------------------
        // PART A: 85 Candidates for Historical Advertisements (52 to 57)
        // -------------------------------------------------------------
        $historicalJobs = [
            'DBA-SUP-52', 'NET-SUP-52', 'LAB-SUP-52', 'HPC-SUP-52', 'AV-SUP-52',
            'EE-ASST-53', 'ROB-LECT-53', 'MTR-ASST-53',
            'MGT-ASST-54', 'ENG-LECT-54', 'ECO-ASST-54',
            'ADM-REG-55', 'ADM-TRS-55', 'IT-DIR-55', 'AUD-DIR-55', 'PRO-ASST-55',
            'CS-PROF-56', 'SE-ASSOC-56', 'CYB-PROF-56', 'MATH-ASSOC-56',
            'AI-ASST-57', 'CS-LECT-57', 'DS-ASST-57', 'SE-LECT-57', 'HCI-ASST-57',
        ];

        for ($i = 1; $i <= 85; $i++) {
            $isFemale = ($i % 2 === 0);
            $firstName = $isFemale ? $femaleFirstNames[($i * 3) % count($femaleFirstNames)] : $maleFirstNames[($i * 3) % count($maleFirstNames)];
            $surname = $surnames[($i * 7) % count($surnames)];
            $reg = $regions[$i % count($regions)];

            $trackType = match ($i % 5) {
                0, 1, 2 => 'ACADEMIC',
                3 => 'ADMINISTRATIVE',
                4 => 'SUPPORT',
            };

            $titlePrefix = ($trackType === 'ACADEMIC' && $i % 3 !== 0) ? 'Dr. ' : ($i % 4 === 0 ? 'Engr. ' : '');
            $fullName = "{$titlePrefix}{$firstName} {$surname}";
            $cleanEmail = strtolower(preg_replace('/[^a-z0-9]/', '', "{$firstName}.{$surname}")) . "{$i}@gmail.com";

            $genderDigit = $isFemale ? (($i * 2) % 10) : ((($i * 2) + 1) % 10);
            $cnic = "{$reg['prefix']}-" . str_pad($i * 12347, 7, '0', STR_PAD_LEFT) . "-{$genderDigit}";
            $phone = "+92-3" . ($i % 5) . str_pad($i * 98765, 8, '0', STR_PAD_LEFT);
            $dobYear = 1978 + ($i % 20);
            $dob = sprintf('%04d-%02d-%02d', $dobYear, (($i % 12) + 1), (($i % 28) + 1));
            $avatar = $portraitPhotos[$i % count($portraitPhotos)];

            $bInfo = BasicInfo::where('cnic_number', $cnic)->first();
            if ($bInfo && $bInfo->user) {
                $user = $bInfo->user;
            } else {
                $user = User::firstOrCreate(
                    ['email' => $cleanEmail],
                    ['password' => Hash::make('password'), 'is_active' => true]
                );
            }
            $user->syncRoles(['applicant']);

            $bInfo = BasicInfo::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'track_type' => $trackType,
                    'profile_picture' => $avatar,
                    'first_name' => $titlePrefix . $firstName,
                    'last_name' => $surname,
                    'cnic_number' => $cnic,
                    'date_of_birth' => $dob,
                    'quota' => match($reg['city']) {
                        'Lahore', 'Faisalabad', 'Rawalpindi' => ($i % 3 === 0) ? 'Punjab' : 'Open Merit',
                        'Karachi' => ($i % 2 === 0) ? 'Sindh (Urban)' : 'Open Merit',
                        'Hyderabad' => ($i % 2 === 0) ? 'Sindh (Rural)' : 'Open Merit',
                        'Peshawar' => ($i % 2 === 0) ? 'Khyber Pakhtunkhwa' : 'Open Merit',
                        'Quetta' => ($i % 2 === 0) ? 'Balochistan' : 'Open Merit',
                        default => ($isFemale && $i % 4 === 0) ? 'Women Quota' : 'Open Merit',
                    },
                    'gender' => $isFemale ? 'Female' : 'Male',
                    'father_name' => 'Al-Haj ' . $surnames[(($i * 3) + 2) % count($surnames)],
                    'marital_status' => ($i % 3 === 0) ? 'Single' : 'Married',
                    'phone_number' => $phone,
                    'secondary_phone' => '+92-51-' . str_pad($i * 11111, 7, '0', STR_PAD_LEFT),
                    'secondary_email' => "personal.{$cleanEmail}",
                    'permanent_address' => "House #" . ($i * 7) . ", Street " . ($i % 15 + 1) . ", Sector " . ['G-10', 'F-8', 'H-12', 'Gulberg III', 'DHA Phase 5'][$i % 5] . ", {$reg['city']}",
                    'mailing_address' => "House #" . ($i * 7) . ", Street " . ($i % 15 + 1) . ", Sector " . ['G-10', 'F-8', 'H-12', 'Gulberg III', 'DHA Phase 5'][$i % 5] . ", {$reg['city']}",
                    'same_address' => true,
                    'is_locked' => true,
                ]
            );

            // Seed Degrees
            $fscDiv = ($i % 11 === 0) ? '3rd Division' : '1st Division';
            $degrees = [
                ['degree_level' => 'MATRIC', 'degree_title' => 'Secondary School Certificate (SSC)', 'major' => 'Science (Physics, Chem, Math, Bio)', 'institution' => $reg['board'], 'board' => $reg['board'], 'country' => 'Pakistan', 'year' => $dobYear + 16, 'division' => '1st Division', 'gpa' => '84.50%'],
                ['degree_level' => 'INTERMEDIATE', 'degree_title' => 'Higher Secondary Certificate (HSSC - FSc Pre-Engineering)', 'major' => 'Mathematics, Physics, Chemistry', 'institution' => $reg['board'], 'board' => $reg['board'], 'country' => 'Pakistan', 'year' => $dobYear + 18, 'division' => $fscDiv, 'gpa' => ($fscDiv === '3rd Division') ? '48.20%' : '81.00%'],
                ['degree_level' => 'BACHELORS', 'degree_title' => ($trackType === 'ACADEMIC') ? 'BS in Computer Science (4-Years)' : 'BBA / BS Business & Technology', 'major' => 'Algorithms, Operating Systems, Computing', 'institution' => $reg['uni'], 'board' => $reg['uni'], 'country' => 'Pakistan', 'year' => $dobYear + 22, 'division' => '1st Division', 'gpa' => '3.72 / 4.00'],
                ['degree_level' => 'MASTERS', 'degree_title' => 'MS / MPhil in Computer Science (18 Years)', 'major' => 'Distributed Systems, Machine Learning', 'institution' => $reg['uni'], 'board' => $reg['uni'], 'country' => 'Pakistan', 'year' => $dobYear + 24, 'division' => '1st Division', 'gpa' => '3.85 / 4.00'],
            ];

            $hasPhD = ($trackType === 'ACADEMIC' && ($i % 4 !== 0));
            if ($hasPhD) {
                $uniName = ($i % 2 === 0) ? 'National University of Sciences & Technology (NUST)' : 'University of Edinburgh, UK';
                $degrees[] = [
                    'degree_level' => 'PHD',
                    'degree_title' => 'Doctor of Philosophy (PhD) in Computer Science',
                    'major' => 'Cloud Scalability & High-Performance Deep Learning',
                    'institution' => $uniName,
                    'board' => $uniName,
                    'country' => ($i % 2 === 0) ? 'Pakistan' : 'United Kingdom',
                    'year' => $dobYear + 28,
                    'division' => '1st Division',
                    'gpa' => '3.92 / 4.00',
                ];
            }

            foreach ($degrees as $dIdx => $d) {
                AcademicDegree::updateOrCreate(
                    ['basic_info_id' => $bInfo->id, 'degree_level' => $d['degree_level']],
                    array_merge($d, ['is_verified' => true])
                );
            }

            // Seed Publications
            if ($trackType === 'ACADEMIC') {
                Publication::updateOrCreate(
                    ['basic_info_id' => $bInfo->id, 'title' => "Scalable Architectural Paradigms for Fault-Tolerant Distributed Microservices (Candidate #{$i})"],
                    [
                        'journal_name' => 'IEEE Transactions on Parallel and Distributed Systems',
                        'issn_print' => '1045-9219',
                        'author_order' => 1,
                        'is_corresponding_author' => true,
                        'publication_year' => 2018 + ($i % 6),
                        'hjrs_category' => ($i % 5 === 0) ? 'X' : 'W',
                        'impact_factor' => 4.350,
                        'is_verified' => true,
                    ]
                );
            }

            // Apply to 1-2 historical campaigns
            $assignedJobs = [$historicalJobs[$i % count($historicalJobs)]];
            if ($i % 3 === 0) {
                $assignedJobs[] = $historicalJobs[(($i + 5) % count($historicalJobs))];
            }

            foreach ($assignedJobs as $jobCode) {
                $camp = $campaigns[$jobCode] ?? null;
                if (!$camp) continue;

                $outcomeIndex = $i % 9;
                $appStatus = 'ELIGIBLE';
                $disqualReason = null;
                $appealLetter = null;
                $withReviews = false;
                $withBoardEval = false;

                if ($fscDiv === '3rd Division') {
                    $appStatus = 'DISQUALIFIED';
                    $disqualReason = $disqualificationScenarios['THIRD_DIV_STATUTE'];
                } elseif (!$hasPhD && str_contains($jobCode, 'PROF') || str_contains($jobCode, 'ASSOC') || str_contains($jobCode, 'ASST')) {
                    $appStatus = 'DISQUALIFIED';
                    $disqualReason = $disqualificationScenarios['TERMINAL_DEGREE_MISSING'];
                } elseif ($outcomeIndex === 0 || $outcomeIndex === 6) {
                    $appStatus = 'APPROVED';
                    $withBoardEval = true;
                    $withReviews = 'positive';
                } elseif ($outcomeIndex === 1 || $outcomeIndex === 7) {
                    $appStatus = 'ELIGIBLE';
                    $withReviews = 'positive';
                } elseif ($outcomeIndex === 2) {
                    $appStatus = 'APPROVED';
                    $withReviews = 'positive';
                    $withBoardEval = true;
                } elseif ($outcomeIndex === 3) {
                    $appStatus = 'REJECTED';
                    $withReviews = 'negative';
                    $disqualReason = 'Peer review outcome below minimum statutory threshold (less than 2 positive recommendations).';
                } elseif ($outcomeIndex === 4) {
                    $appStatus = 'INELIGIBLE';
                    $disqualReason = $disqualificationScenarios['HEC_EXP_SHORTFALL'];
                } elseif ($outcomeIndex === 5) {
                    $appStatus = 'APPEALED';
                    $disqualReason = $disqualificationScenarios['HEC_PUBLICATION_SHORTFALL'];
                    $appealLetter = "Respected Appellate Grievance Committee,\n\nI am submitting this representation regarding my application for {$camp->title}. Shortfall in W-category publications has been certified through HEC HJRS indexation.\n\nRespectfully,\n{$fullName}";
                } else {
                    $appStatus = 'ELIGIBLE';
                }

                // Compute exact timeline matching the advertisement's historical year
                $cOpen = Carbon::parse($camp->opening_date);
                $cClose = Carbon::parse($camp->closing_date);
                $daysDiff = max(2, $cClose->diffInDays($cOpen));
                $subDate = $cOpen->copy()->addDays(($i * 3) % ($daysDiff - 1) + 1)->setHour(10 + ($i % 6))->setMinute(($i * 13) % 59);
                $payDate = $subDate->copy()->addMinutes(15 + ($i * 7) % 180);
                $revAssignDate = $cClose->copy()->addDays(3 + ($i % 5))->setHour(11);
                $revDueDate = $revAssignDate->copy()->addDays(20);
                $revSubmitDate = $revAssignDate->copy()->addDays(7 + ($i % 10))->setHour(15);
                $evalDate = $cClose->copy()->addDays(20 + ($i % 15))->setHour(14);

                $application = Application::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'campaign_id' => $camp->id,
                    ],
                    [
                        'status' => $appStatus,
                        'submission_timestamp' => $subDate,
                        'disqualification_reason' => $disqualReason,
                        'appeal_letter' => $appealLetter,
                        'appeal_proof_path' => null,
                        'created_at' => $subDate,
                        'updated_at' => $subDate,
                    ]
                );

                // Settled Billing Ledger in the exact year
                $adYear = substr($camp->advertisement->year ?? '2025', 2, 2);
                $adNoPrefix = substr($camp->advertisement->advertisement_no ?? '52', 0, 2);
                $consumerId = '1000' . $adNoPrefix . $adYear . str_pad($application->id, 4, '0', STR_PAD_LEFT);

                BillingLedger::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'consumer_id' => $consumerId,
                        'amount' => $camp->application_fee,
                        'status' => 'SETTLED',
                        'transaction_id' => "1LINK-TXN-" . (980000 + $application->id),
                        'payment_timestamp' => $payDate,
                        'raw_response_log' => [
                            'bank_code' => 'HBL',
                            'channel' => '1LINK_ATM_OTC',
                            'settlement_date' => $payDate->format('Y-m-d'),
                            'response_code' => '00',
                        ],
                        'created_at' => $subDate,
                        'updated_at' => $payDate,
                    ]
                );

                // Referee Reviews in the exact year
                $revPool = array_merge([$revKenji ?? null, $revAmira ?? null, $revAhmad ?? null], $reviewersList);
                $revPool = array_values(array_filter($revPool));

                if ($withReviews === 'positive' && count($revPool) >= 2) {
                    $selectedRev1 = $revPool[($i * 2) % count($revPool)];
                    $selectedRev2 = $revPool[(($i * 2) + 1) % count($revPool)];

                    if ($selectedRev1) {
                        ApplicationReview::updateOrCreate(
                            ['application_id' => $application->id, 'reviewer_id' => $selectedRev1->id],
                            [
                                'assigned_by' => $registrarId,
                                'assigned_at' => $revAssignDate,
                                'due_date' => $revDueDate,
                                'status' => 'SUBMITTED',
                                'recommendation' => 'STRONGLY_RECOMMENDED',
                                'overall_score' => 86.50,
                                'report_text' => "Scholarly portfolio demonstrates substantial mastery. Dossier satisfies all statutory standards.",
                                'submitted_at' => $revSubmitDate,
                                'created_at' => $revAssignDate,
                                'updated_at' => $revSubmitDate,
                            ]
                        );
                    }
                    if ($selectedRev2) {
                        ApplicationReview::updateOrCreate(
                            ['application_id' => $application->id, 'reviewer_id' => $selectedRev2->id],
                            [
                                'assigned_by' => $registrarId,
                                'assigned_at' => $revAssignDate,
                                'due_date' => $revDueDate,
                                'status' => 'SUBMITTED',
                                'recommendation' => 'RECOMMENDED',
                                'overall_score' => 81.00,
                                'report_text' => "Candidate fulfills statutory criteria with quality indexed publications.",
                                'submitted_at' => $revSubmitDate,
                                'created_at' => $revAssignDate,
                                'updated_at' => $revSubmitDate,
                            ]
                        );
                    }
                } elseif ($withReviews === 'negative' && count($revPool) >= 2) {
                    $selectedRev1 = $revPool[($i * 2) % count($revPool)];
                    if ($selectedRev1) {
                        ApplicationReview::updateOrCreate(
                            ['application_id' => $application->id, 'reviewer_id' => $selectedRev1->id],
                            [
                                'assigned_by' => $registrarId,
                                'assigned_at' => $revAssignDate,
                                'due_date' => $revDueDate,
                                'status' => 'SUBMITTED',
                                'recommendation' => 'NOT_RECOMMENDED',
                                'overall_score' => 45.00,
                                'report_text' => "Publications show limited originality and low citation impact.",
                                'submitted_at' => $revSubmitDate,
                                'created_at' => $revAssignDate,
                                'updated_at' => $revSubmitDate,
                            ]
                        );
                    }
                }

                // Selection Board live evaluations
                if ($withBoardEval) {
                    $evalMembers = [$board1, $board2, $board3];
                    foreach ($evalMembers as $bIdx => $member) {
                        if (!$member) continue;
                        LiveEvaluation::updateOrCreate(
                            [
                                'application_id' => $application->id,
                                'board_member_id' => $member->id,
                            ],
                            [
                                'academic_score' => 37.00 + ($bIdx * 0.5),
                                'medals_score' => 4.00,
                                'publications_score' => 20.00 + ($bIdx * 1.0),
                                'events_score' => 3.50,
                                'project_score' => 8.50,
                                'interview_score' => 18.00 + ($bIdx * 0.5),
                                'is_outlier' => false,
                                'outlier_justification' => null,
                                'last_modified' => $evalDate,
                                'created_at' => $evalDate,
                                'updated_at' => $evalDate,
                            ]
                        );
                    }
                }
            }
        }

        // -------------------------------------------------------------
        // PART B: Candidates for Adv 58/2026 (Apply Date Passed, Varied Stages)
        // -------------------------------------------------------------
        $this->command?->info("Step 5B: Seeding Adv 58/2026 candidates across multi-stage in-flight pipeline...");

        $adv58Candidates = [
            // 1. Almost reached the end: Selection Board Approved
            [
                'email' => 'dr.kamran.bhatti@gmail.com',
                'name' => 'Dr. Kamran Bhatti',
                'first_name' => 'Dr. Kamran',
                'last_name' => 'Bhatti',
                'cnic' => '61101-7890123-1',
                'gender' => 'Male',
                'track' => 'ACADEMIC',
                'job_code' => 'AI-PROF-58',
                'status' => 'APPROVED',
                'stage_label' => 'Selection Board Recommended & Approved',
                'fee_status' => 'SETTLED',
                'review_state' => 'both_submitted',
                'with_board_eval' => true,
                'avatar' => $portraitPhotos[1],
            ],
            // 2. Almost reached the end: Interview Scheduled / Reserve Merit List
            [
                'email' => 'dr.sadia.anwar@gmail.com',
                'name' => 'Dr. Sadia Anwar',
                'first_name' => 'Dr. Sadia',
                'last_name' => 'Anwar',
                'cnic' => '35202-6543210-2',
                'gender' => 'Female',
                'track' => 'ACADEMIC',
                'job_code' => 'AI-PROF-58',
                'status' => 'ELIGIBLE',
                'stage_label' => 'Interview Scheduled & Peer Reviewed',
                'fee_status' => 'SETTLED',
                'review_state' => 'both_submitted',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[4],
            ],
            // 3. In-flight Stage: Under Peer Review (1 submitted, 1 pending review)
            [
                'email' => 'dr.tariq.mahmood@gmail.com',
                'name' => 'Dr. Tariq Mahmood',
                'first_name' => 'Dr. Tariq',
                'last_name' => 'Mahmood',
                'cnic' => '37405-1928374-1',
                'gender' => 'Male',
                'track' => 'ACADEMIC',
                'job_code' => 'CS-ASSOC-58',
                'status' => 'UNDER_REVIEW',
                'stage_label' => 'Peer Review In Progress (1 submitted, 1 pending)',
                'fee_status' => 'SETTLED',
                'review_state' => 'one_submitted_one_assigned',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[7],
            ],
            // 4. In-flight Stage: Scrutiny Committee Passed (awaiting peer review dispatch)
            [
                'email' => 'dr.hina.farooq@gmail.com',
                'name' => 'Dr. Hina Farooq',
                'first_name' => 'Dr. Hina',
                'last_name' => 'Farooq',
                'cnic' => '42101-9988776-2',
                'gender' => 'Female',
                'track' => 'ACADEMIC',
                'job_code' => 'CS-ASSOC-58',
                'status' => 'SCRUTINY_PASSED',
                'stage_label' => 'Scrutiny Committee Passed - Awaiting Referee Allocation',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[9],
            ],
            // 5. In-flight Stage: Disqualified at Scrutiny (appeal period currently open)
            [
                'email' => 'engr.waqas.ahmed@gmail.com',
                'name' => 'Engr. Waqas Ahmed',
                'first_name' => 'Engr. Waqas',
                'last_name' => 'Ahmed',
                'cnic' => '17301-4455667-1',
                'gender' => 'Male',
                'track' => 'ACADEMIC',
                'job_code' => 'CS-ASSOC-58',
                'status' => 'DISQUALIFIED',
                'stage_label' => 'Disqualified at Scrutiny (Shortfall in Post-PhD Experience, Appeal Open)',
                'disqual_reason' => 'Shortfall in statutory post-PhD teaching experience: Minimum 8 years required; candidate documented 4 years and 2 months. Appeal window active for 15 days.',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[11],
            ],
            // 6. In-flight Stage: Formal Statutory Appeal Filed (awaiting Appellate hearing)
            [
                'email' => 'dr.noman.ali@gmail.com',
                'name' => 'Dr. Noman Ali',
                'first_name' => 'Dr. Noman',
                'last_name' => 'Ali',
                'cnic' => '33100-3322114-1',
                'gender' => 'Male',
                'track' => 'ACADEMIC',
                'job_code' => 'DS-ASST-58',
                'status' => 'APPEALED',
                'stage_label' => 'Appealed - Under Consideration by Appellate Committee',
                'disqual_reason' => 'Scrutiny noted missing terminal equivalence letter.',
                'appeal_letter' => "To the Chairman, Statutory Appellate Committee:\n\nI hereby submit certified HEC Equivalence Letter (Ref: HEC/EQ/2026/8841) for my doctoral degree in Big Data Systems from TU Munich. Kindly condone initial scrutiny observation and restore merit roster standing.\n\nRespectfully,\nDr. Noman Ali",
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[13],
            ],
            // 7. Almost reached the end: Approved by Selection Board
            [
                'email' => 'dr.maryam.zubair@gmail.com',
                'name' => 'Dr. Maryam Zubair',
                'first_name' => 'Dr. Maryam',
                'last_name' => 'Zubair',
                'cnic' => '54200-8877665-2',
                'gender' => 'Female',
                'track' => 'ACADEMIC',
                'job_code' => 'DS-ASST-58',
                'status' => 'APPROVED',
                'stage_label' => 'Selection Board Recommended & Approved',
                'fee_status' => 'SETTLED',
                'review_state' => 'both_submitted',
                'with_board_eval' => true,
                'avatar' => $portraitPhotos[15],
            ],
            // 8. In-flight Stage: Submitted, Awaiting Scrutiny Committee
            [
                'email' => 'engr.bilal.sarwar@gmail.com',
                'name' => 'Engr. Bilal Sarwar',
                'first_name' => 'Engr. Bilal',
                'last_name' => 'Sarwar',
                'cnic' => '41301-1122334-1',
                'gender' => 'Male',
                'track' => 'ACADEMIC',
                'job_code' => 'DS-ASST-58',
                'status' => 'SUBMITTED',
                'stage_label' => 'Application Submitted - Under Initial Scrutiny',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[17],
            ],
            // 9. Almost reached the end: Administrative Director Approved
            [
                'email' => 'malik.rizwan@gmail.com',
                'name' => 'Malik Rizwan',
                'first_name' => 'Malik',
                'last_name' => 'Rizwan',
                'cnic' => '37405-7766554-1',
                'gender' => 'Male',
                'track' => 'ADMINISTRATIVE',
                'job_code' => 'QEC-DIR-58',
                'status' => 'APPROVED',
                'stage_label' => 'Executive Committee Approved',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => true,
                'avatar' => $portraitPhotos[19],
            ],
            // 10. In-flight Stage: Administrative Candidate Under Review
            [
                'email' => 'syed.farhan.shah@gmail.com',
                'name' => 'Syed Farhan Shah',
                'first_name' => 'Syed Farhan',
                'last_name' => 'Shah',
                'cnic' => '35202-3344556-1',
                'gender' => 'Male',
                'track' => 'ADMINISTRATIVE',
                'job_code' => 'QEC-DIR-58',
                'status' => 'UNDER_REVIEW',
                'stage_label' => 'Credentials Under Administrative Verification',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[21],
            ],
            // 11. In-flight Stage: Scrutiny Passed / Merit Ranked
            [
                'email' => 'aisha.rasheed@gmail.com',
                'name' => 'Aisha Rasheed',
                'first_name' => 'Aisha',
                'last_name' => 'Rasheed',
                'cnic' => '61101-5566778-2',
                'gender' => 'Female',
                'track' => 'ACADEMIC',
                'job_code' => 'BIO-LECT-58',
                'status' => 'ELIGIBLE',
                'stage_label' => 'Scrutiny Passed - Merit Roster Prepared',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[23],
            ],
            // 12. In-flight Stage: Pending Fee Challan Settlement
            [
                'email' => 'hamza.tariq@gmail.com',
                'name' => 'Hamza Tariq',
                'first_name' => 'Hamza',
                'last_name' => 'Tariq',
                'cnic' => '42101-2233445-1',
                'gender' => 'Male',
                'track' => 'ACADEMIC',
                'job_code' => 'BIO-LECT-58',
                'status' => 'VOUCHER_PENDING',
                'stage_label' => 'Challan Generated - Payment Reconciliation Pending',
                'fee_status' => 'PENDING',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[25],
            ],
            // 13. In-flight Stage: Disqualified at Scrutiny (3rd Division)
            [
                'email' => 'zain.ul.abideen@gmail.com',
                'name' => 'Zain-ul-Abideen',
                'first_name' => 'Zain-ul',
                'last_name' => 'Abideen',
                'cnic' => '17301-8899001-1',
                'gender' => 'Male',
                'track' => 'ACADEMIC',
                'job_code' => 'BIO-LECT-58',
                'status' => 'DISQUALIFIED',
                'stage_label' => 'Disqualified - 3rd Division in HSSC',
                'disqual_reason' => 'Disqualified during scrutiny: 3rd division in HSSC (Intermediate), strictly prohibited under Section 5(a).',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[27],
            ],
            // 14. In-flight Stage: Submitted, Pending Scrutiny
            [
                'email' => 'fatima.noor@gmail.com',
                'name' => 'Fatima Noor',
                'first_name' => 'Fatima',
                'last_name' => 'Noor',
                'cnic' => '33100-6677889-2',
                'gender' => 'Female',
                'track' => 'ACADEMIC',
                'job_code' => 'BIO-LECT-58',
                'status' => 'SUBMITTED',
                'stage_label' => 'Application Submitted - Under Initial Scrutiny',
                'fee_status' => 'SETTLED',
                'review_state' => 'none',
                'with_board_eval' => false,
                'avatar' => $portraitPhotos[28],
            ],
        ];

        foreach ($adv58Candidates as $cIdx => $cand) {
            $bInfo = BasicInfo::where('cnic_number', $cand['cnic'])->first();
            if ($bInfo && $bInfo->user) {
                $user = $bInfo->user;
            } else {
                $user = User::firstOrCreate(
                    ['email' => $cand['email']],
                    ['password' => Hash::make('password'), 'is_active' => true]
                );
            }
            $user->syncRoles(['applicant']);

            $bInfo = BasicInfo::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'track_type' => $cand['track'],
                    'profile_picture' => $cand['avatar'],
                    'first_name' => $cand['first_name'],
                    'last_name' => $cand['last_name'],
                    'cnic_number' => $cand['cnic'],
                    'date_of_birth' => '1988-06-15',
                    'quota' => ['Open Merit', 'Punjab', 'Sindh (Urban)', 'Khyber Pakhtunkhwa', 'Balochistan', 'Women Quota'][$cIdx % 6],
                    'gender' => $cand['gender'],
                    'father_name' => 'Al-Haj ' . $cand['last_name'] . ' Senior',
                    'marital_status' => 'Married',
                    'phone_number' => '+92-300-' . str_pad($cIdx * 77777, 7, '0', STR_PAD_LEFT),
                    'permanent_address' => "House " . ($cIdx + 12) . ", Street 4, Islamabad",
                    'mailing_address' => "House " . ($cIdx + 12) . ", Street 4, Islamabad",
                    'same_address' => true,
                    'is_locked' => true,
                ]
            );

            // Degrees
            AcademicDegree::updateOrCreate(
                ['basic_info_id' => $bInfo->id, 'degree_level' => 'BACHELORS'],
                [
                    'degree_title' => 'BS Computer Engineering',
                    'major' => 'Computing & Embedded Systems',
                    'institution' => 'NUST Islamabad',
                    'board' => 'NUST Islamabad',
                    'country' => 'Pakistan',
                    'year' => 2010,
                    'division' => '1st Division',
                    'gpa' => '3.80 / 4.00',
                    'is_verified' => true,
                ]
            );
            AcademicDegree::updateOrCreate(
                ['basic_info_id' => $bInfo->id, 'degree_level' => 'MASTERS'],
                [
                    'degree_title' => 'MS Systems Engineering',
                    'major' => 'Cloud Systems & AI',
                    'institution' => 'UET Lahore',
                    'board' => 'UET Lahore',
                    'country' => 'Pakistan',
                    'year' => 2013,
                    'division' => '1st Division',
                    'gpa' => '3.88 / 4.00',
                    'is_verified' => true,
                ]
            );
            if ($cand['track'] === 'ACADEMIC') {
                AcademicDegree::updateOrCreate(
                    ['basic_info_id' => $bInfo->id, 'degree_level' => 'PHD'],
                    [
                        'degree_title' => 'PhD in Artificial Intelligence',
                        'major' => 'Deep Learning & Autonomous Systems',
                        'institution' => 'FAST-NUCES Islamabad',
                        'board' => 'FAST-NUCES Islamabad',
                        'country' => 'Pakistan',
                        'year' => 2017,
                        'division' => '1st Division',
                        'gpa' => '3.95 / 4.00',
                        'is_verified' => true,
                    ]
                );
            }

            $camp = $campaigns[$cand['job_code']] ?? null;
            if (!$camp) continue;

            $subTime = now()->subDays(25 + ($cIdx % 10))->setHour(14);
            $payTime = $subTime->copy()->addMinutes(45);

            $application = Application::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'campaign_id' => $camp->id,
                ],
                [
                    'status' => $cand['status'],
                    'submission_timestamp' => $subTime,
                    'disqualification_reason' => $cand['disqual_reason'] ?? null,
                    'appeal_letter' => $cand['appeal_letter'] ?? null,
                    'appeal_proof_path' => null,
                    'created_at' => $subTime,
                    'updated_at' => now()->subDays(5),
                ]
            );

            // Billing Ledger
            $consumerId = '10005826' . str_pad($application->id, 4, '0', STR_PAD_LEFT);
            $isPaid = ($cand['fee_status'] === 'SETTLED');
            BillingLedger::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'consumer_id' => $consumerId,
                    'amount' => $camp->application_fee,
                    'status' => $cand['fee_status'],
                    'transaction_id' => $isPaid ? "1LINK-TXN-58" . str_pad($application->id, 5, '0', STR_PAD_LEFT) : null,
                    'payment_timestamp' => $isPaid ? $payTime : null,
                    'raw_response_log' => $isPaid ? [
                        'bank_code' => 'HBL',
                        'channel' => '1LINK_KUICKPAY',
                        'settlement_date' => $payTime->format('Y-m-d'),
                        'response_code' => '00',
                    ] : null,
                    'created_at' => $subTime,
                    'updated_at' => $isPaid ? $payTime : $subTime,
                ]
            );

            // Reviews for Adv 58
            if ($cand['review_state'] === 'both_submitted') {
                $rev1 = $reviewersList[0] ?? null;
                $rev2 = $reviewersList[1] ?? null;
                if ($rev1) {
                    ApplicationReview::updateOrCreate(
                        ['application_id' => $application->id, 'reviewer_id' => $rev1->id],
                        [
                            'assigned_by' => $registrarId,
                            'assigned_at' => now()->subDays(20),
                            'due_date' => now()->subDays(5),
                            'status' => 'SUBMITTED',
                            'recommendation' => 'STRONGLY_RECOMMENDED',
                            'overall_score' => 91.50,
                            'report_text' => 'High quality scholarly dossier. Recommended for statutory appointment.',
                            'submitted_at' => now()->subDays(8),
                        ]
                    );
                }
                if ($rev2) {
                    ApplicationReview::updateOrCreate(
                        ['application_id' => $application->id, 'reviewer_id' => $rev2->id],
                        [
                            'assigned_by' => $registrarId,
                            'assigned_at' => now()->subDays(20),
                            'due_date' => now()->subDays(5),
                            'status' => 'SUBMITTED',
                            'recommendation' => 'RECOMMENDED',
                            'overall_score' => 87.00,
                            'report_text' => 'Dossier fulfills all required professorial research metrics.',
                            'submitted_at' => now()->subDays(7),
                        ]
                    );
                }
            } elseif ($cand['review_state'] === 'one_submitted_one_assigned') {
                $rev1 = $reviewersList[2] ?? null;
                $rev2 = $reviewersList[3] ?? null;
                if ($rev1) {
                    // Submitted
                    ApplicationReview::updateOrCreate(
                        ['application_id' => $application->id, 'reviewer_id' => $rev1->id],
                        [
                            'assigned_by' => $registrarId,
                            'assigned_at' => now()->subDays(18),
                            'due_date' => now()->addDays(5),
                            'status' => 'SUBMITTED',
                            'recommendation' => 'RECOMMENDED',
                            'overall_score' => 82.50,
                            'report_text' => 'Good research track record with recognized international conference papers.',
                            'submitted_at' => now()->subDays(4),
                        ]
                    );
                }
                if ($rev2) {
                    // Still assigned / in progress (pending)
                    ApplicationReview::updateOrCreate(
                        ['application_id' => $application->id, 'reviewer_id' => $rev2->id],
                        [
                            'assigned_by' => $registrarId,
                            'assigned_at' => now()->subDays(18),
                            'due_date' => now()->addDays(5),
                            'status' => 'PENDING',
                            'recommendation' => null,
                            'overall_score' => null,
                            'report_text' => null,
                            'submitted_at' => null,
                        ]
                    );
                }
            }

            // Board live evaluation for completed Adv 58 candidates
            if (!empty($cand['with_board_eval'])) {
                $evalMembers = [$board1, $board2, $board3];
                foreach ($evalMembers as $bIdx => $member) {
                    if (!$member) continue;
                    LiveEvaluation::updateOrCreate(
                        [
                            'application_id' => $application->id,
                            'board_member_id' => $member->id,
                        ],
                        [
                            'academic_score' => 38.00 + ($bIdx * 0.5),
                            'medals_score' => 5.00,
                            'publications_score' => 22.00,
                            'events_score' => 4.00,
                            'project_score' => 9.00,
                            'interview_score' => 19.00,
                            'is_outlier' => false,
                            'outlier_justification' => null,
                            'last_modified' => now()->subDays(3),
                        ]
                    );
                }
            }
        }

        // ---------------------------------------------------------------------
        // 6. GUARANTEE DUMMY PORTRAIT PICTURES & SYSTEM-WIDE INTEGRITY
        // ---------------------------------------------------------------------
        $this->command?->info("Step 6: Ensuring every candidate has a dummy portrait photo and dossiers are locked...");

        // Ensure ALL candidate BasicInfo records across the system have a real portrait picture
        $allBasicInfos = BasicInfo::all();
        foreach ($allBasicInfos as $idx => $info) {
            $currentPic = $info->profile_picture;
            $needsUpdate = empty($currentPic) || !str_starts_with($currentPic, 'http');
            if ($needsUpdate) {
                $info->update([
                    'profile_picture' => $portraitPhotos[$idx % count($portraitPhotos)],
                    'is_locked' => true,
                ]);
            } else {
                $info->update(['is_locked' => true]);
            }
        }

        AcademicDegree::where('is_verified', false)->update(['is_verified' => true]);
        Publication::where('is_verified', false)->update(['is_verified' => true]);
        WorkExperience::where('is_verified', false)->update(['is_verified' => true]);
        AcademicMedal::where('is_verified', false)->update(['is_verified' => true]);
        ResearchProject::where('is_verified', false)->update(['is_verified' => true]);
        Patent::where('is_verified', false)->update(['is_verified' => true]);

        $this->command?->info("PakistanRecruitmentDemoSeeder completed successfully! 6 Historical Advertisements (2025=2, 2023=1, 2021=2, 2019=1) and 1 Newly Added In-Flight Advertisement (58/2026: apply date passed, candidates in diverse stages) are fully seeded with working dummy portrait photos and sleek pagination!");
    }
}
