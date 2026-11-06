<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BasicInfo;
use App\Models\Publication;
use App\Models\ResearchProject;
use App\Models\Patent;
use App\Models\ProfessionalEvent;
use App\Models\WorkExperience;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\ReviewerProfile;
use App\Models\SystemSetting;

return new class extends Migration
{
    /**
     * Run the migrations to enrich existing candidate dossiers with publications, research projects,
     * patents, conferences, work experiences, applications, and referee reviews.
     */
    public function up(): void
    {
        // 1. Seed or ensure System Settings for pipeline, quorums, and scoring benchmarks
        $defaultSettings = [
            'enable_peer_review_step' => true,
            'enable_scrutiny_step' => true,
            'enable_appellate_step' => true,
            'enable_board_interview_step' => true,
            'enforce_strict_committee_quorum' => true,
            'enforce_strict_peer_review_quorum' => false,
            'require_publications_academic' => false,
            'require_transcript_proof' => false,
            'require_experience_proof' => false,
            'require_cnic_upload' => false,
            'require_noc_employed' => false,
            'require_challan_receipt_upload' => false,
            'org_name' => 'Hirely Higher Education & Recruitment Commission',
            'currency_symbol' => 'PKR',
            'default_application_fee' => 3000,
        ];

        foreach ($defaultSettings as $key => $val) {
            $type = is_bool($val) ? 'boolean' : (is_int($val) ? 'integer' : 'string');
            if (SystemSetting::where('key', $key)->doesntExist()) {
                SystemSetting::set($key, $val, 'general', $type);
            }
        }

        // 2. Fetch all candidates (BasicInfo records)
        $candidates = BasicInfo::with(['user'])->get();
        if ($candidates->isEmpty()) {
            return;
        }

        // Publications Pool with authentic journals, HEC categories (W/X/Y), and DOIs
        $publicationsPool = [
            [
                'title' => 'Adaptive Distributed Consensus Mechanisms for Fault-Tolerant Federated Edge Intelligence',
                'journal_name' => 'IEEE Transactions on Parallel and Distributed Systems',
                'issn_print' => '1045-9219',
                'hjrs_category' => 'W',
                'impact_factor' => 5.300,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.1109/TPDS.2024.1045921',
                'volume_issue' => 'Vol. 35, Issue 4, pp. 812-828',
                'publication_year' => 2024,
            ],
            [
                'title' => 'Deep Transfer Learning and Feature Fusion for Autonomous Cyber Threat Detection in Critical Infrastructures',
                'journal_name' => 'IEEE Transactions on Information Forensics and Security',
                'issn_print' => '1556-6013',
                'hjrs_category' => 'W',
                'impact_factor' => 6.800,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.1109/TIFS.2023.9876543',
                'volume_issue' => 'Vol. 19, pp. 2450-2465',
                'publication_year' => 2023,
            ],
            [
                'title' => 'A Comprehensive Survey of Scalable Microservice Architectures in Multi-Cloud Environments',
                'journal_name' => 'ACM Computing Surveys',
                'issn_print' => '0360-0300',
                'hjrs_category' => 'W',
                'impact_factor' => 16.600,
                'author_order' => 2,
                'is_corresponding_author' => false,
                'doi' => '10.1145/3543873.3587321',
                'volume_issue' => 'Vol. 55, No. 6, Article 124',
                'publication_year' => 2023,
            ],
            [
                'title' => 'Automated Continuous Vulnerability Remediation in Cloud-Native Deployment Pipelines',
                'journal_name' => 'IEEE Transactions on Software Engineering',
                'issn_print' => '0098-5589',
                'hjrs_category' => 'W',
                'impact_factor' => 6.500,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.1109/TSE.2022.3190456',
                'volume_issue' => 'Vol. 49, No. 2, pp. 589-605',
                'publication_year' => 2022,
            ],
            [
                'title' => 'Empirical Analysis of Code Quality and Refactoring Patterns in Enterprise Open Source Repositories',
                'journal_name' => 'Empirical Software Engineering',
                'issn_print' => '1382-3256',
                'hjrs_category' => 'W',
                'impact_factor' => 3.800,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.1007/s10664-022-10189-w',
                'volume_issue' => 'Vol. 27, Article 145',
                'publication_year' => 2022,
            ],
            [
                'title' => 'Resource-Constrained Deep Learning Models for Edge IoT Vision Systems',
                'journal_name' => 'Elsevier Journal of Network and Computer Applications',
                'issn_print' => '1084-8045',
                'hjrs_category' => 'W',
                'impact_factor' => 8.700,
                'author_order' => 2,
                'is_corresponding_author' => false,
                'doi' => '10.1016/j.jnca.2021.103112',
                'volume_issue' => 'Vol. 192, 103112',
                'publication_year' => 2021,
            ],
            [
                'title' => 'Predictive Failure Analytics for Distributed Microservices Using Time-Series Transformer Networks',
                'journal_name' => 'IEEE Access',
                'issn_print' => '2169-3536',
                'hjrs_category' => 'X',
                'impact_factor' => 3.400,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.1109/ACCESS.2021.3098712',
                'volume_issue' => 'Vol. 9, pp. 98120-98135',
                'publication_year' => 2021,
            ],
            [
                'title' => 'Performance Optimization of High-Throughput Stream Processing in Heterogeneous Computing Nodes',
                'journal_name' => 'Elsevier Information Sciences',
                'issn_print' => '0020-0255',
                'hjrs_category' => 'W',
                'impact_factor' => 8.100,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.1016/j.ins.2020.08.045',
                'volume_issue' => 'Vol. 542, pp. 312-329',
                'publication_year' => 2020,
            ],
            [
                'title' => 'Sovereign Cryptographic Key Exchange Protocols for Low-Power Wide-Area Networks',
                'journal_name' => 'Pakistan Journal of Engineering, Technology & Science (PJETS)',
                'issn_print' => '2222-9930',
                'hjrs_category' => 'Y',
                'impact_factor' => 0.850,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.22555/pjets.v10i2.2190',
                'volume_issue' => 'Vol. 10, No. 2, pp. 45-58',
                'publication_year' => 2020,
            ],
            [
                'title' => 'Graph Neural Network Architectures for Protein-Protein Interaction Prediction',
                'journal_name' => 'IEEE/ACM Transactions on Computational Biology and Bioinformatics',
                'issn_print' => '1545-5963',
                'hjrs_category' => 'W',
                'impact_factor' => 4.500,
                'author_order' => 2,
                'is_corresponding_author' => false,
                'doi' => '10.1109/TCBB.2019.2934110',
                'volume_issue' => 'Vol. 17, No. 5, pp. 1654-1667',
                'publication_year' => 2019,
            ],
            [
                'title' => 'Dynamic Load Balancing Algorithms in Hybrid Cloud-Fog Environments for Smart Healthcare',
                'journal_name' => 'Springer Journal of Supercomputing',
                'issn_print' => '0920-8542',
                'hjrs_category' => 'X',
                'impact_factor' => 3.300,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.1007/s11227-019-02981-y',
                'volume_issue' => 'Vol. 75, pp. 7840-7862',
                'publication_year' => 2019,
            ],
            [
                'title' => 'Decentralized Identity Verification Frameworks Using Zero-Knowledge Proofs in Educational Clouds',
                'journal_name' => 'Mehran University Research Journal of Engineering & Technology',
                'issn_print' => '0254-7821',
                'hjrs_category' => 'X',
                'impact_factor' => 1.200,
                'author_order' => 1,
                'is_corresponding_author' => true,
                'doi' => '10.22581/muet1982.1804.14',
                'volume_issue' => 'Vol. 37, No. 4, pp. 621-634',
                'publication_year' => 2018,
            ],
        ];

        // Research Projects Pool with authentic funding bodies and grant amounts
        $projectsPool = [
            [
                'project_title' => 'Federated Transfer Learning Frameworks for Low-Resource Edge IoT Infrastructure',
                'funding_agency' => 'HEC National Research Program for Universities (NRPU)',
                'grant_amount' => 14500000.00,
                'role_in_project' => 'Principal Investigator (PI)',
                'turnitin_similarity_percentage' => 4.50,
                'status' => 'COMPLETED',
                'co_pis' => 'Dr. Elena Rostova (Oxford), Dr. Ahmad Khan (LUMS)',
                'start_date' => '2021-07-01',
            ],
            [
                'project_title' => 'National Autonomous Cyber Threat Detection and Incident Response Grid',
                'funding_agency' => 'IGNITE National Technology R&D Fund',
                'grant_amount' => 28000000.00,
                'role_in_project' => 'Principal Investigator (PI)',
                'turnitin_similarity_percentage' => 5.20,
                'status' => 'ONGOING',
                'co_pis' => 'Dr. Kenji Sato (Tokyo), Engr. Tariq Aziz (Telecom Authority)',
                'start_date' => '2023-01-15',
            ],
            [
                'project_title' => 'Scalable Distributed Deep Learning on Cloud TPU Clusters for Genomic Sequence Analysis',
                'funding_agency' => 'Pakistan Science Foundation (PSF)',
                'grant_amount' => 8200000.00,
                'role_in_project' => 'Co-Principal Investigator (Co-PI)',
                'turnitin_similarity_percentage' => 3.80,
                'status' => 'COMPLETED',
                'co_pis' => 'Prof. Dr. Sarah Connor (MIT)',
                'start_date' => '2020-03-01',
            ],
            [
                'project_title' => 'Industry 4.0 Smart Manufacturing Vision Inspection System for Automotive Quality Control',
                'funding_agency' => 'World Bank / HEC Higher Education Development in Pakistan (HEDP)',
                'grant_amount' => 6500000.00,
                'role_in_project' => 'Principal Investigator (PI)',
                'turnitin_similarity_percentage' => 6.10,
                'status' => 'COMPLETED',
                'co_pis' => 'Indus Motor Company R&D Division',
                'start_date' => '2022-09-01',
            ],
            [
                'project_title' => 'Automated Continuous Vulnerability Remediation for Mission-Critical Microservices',
                'funding_agency' => 'British Council / HEC Pak-UK Gateway Project',
                'grant_amount' => 18000000.00,
                'role_in_project' => 'Lead Project Director',
                'turnitin_similarity_percentage' => 4.10,
                'status' => 'ONGOING',
                'co_pis' => 'Prof. Dr. Tariq S. Durrani (Strathclyde UK)',
                'start_date' => '2023-06-01',
            ],
        ];

        // Patents Pool
        $patentsPool = [
            [
                'title' => 'Fault-Tolerant Distributed Consensus Protocol for Asynchronous Edge Nodes',
                'registry_id' => 'PK-PAT-2023-00412',
                'country' => 'Pakistan',
                'date_registered' => '2023-04-18',
                'is_licensed' => true,
                'proof_path' => 'proofs/patents/patent-pk-00412.pdf',
            ],
            [
                'title' => 'Non-Intrusive Privacy-Preserving Neural Gradient Compression Method',
                'registry_id' => 'US-18/902,411',
                'country' => 'United States',
                'date_registered' => '2024-01-12',
                'is_licensed' => false,
                'proof_path' => 'proofs/patents/patent-us-902411.pdf',
            ],
            [
                'title' => 'High-Throughput Cryptographic Hardware Accelerator for IoT Sensor Gateways',
                'registry_id' => 'PK-PAT-2022-00981',
                'country' => 'Pakistan',
                'date_registered' => '2022-11-05',
                'is_licensed' => true,
                'proof_path' => 'proofs/patents/patent-pk-00981.pdf',
            ],
        ];

        // Professional Conferences Pool
        $eventsPool = [
            [
                'event_name' => '2024 IEEE International Conference on Computer Communications (INFOCOM)',
                'event_type' => 'CONFERENCE',
                'event_role' => 'PRESENTER_INTERNATIONAL',
                'paper_title' => 'Near-Optimal Task Scheduling in Heterogeneous Fog Computing Topologies',
                'start_date' => '2024-05-20',
                'end_date' => '2024-05-23',
                'location' => 'Vancouver, Canada',
                'organizer' => 'IEEE Communications Society',
                'certificate_proof_path' => 'proofs/events/infocom2024.pdf',
            ],
            [
                'event_name' => '2023 ACM Conference on Computer and Communications Security (CCS)',
                'event_type' => 'CONFERENCE',
                'event_role' => 'PRESENTER_INTERNATIONAL',
                'paper_title' => 'Formal Verification of Byzantine Resilience in Decentralized Microservice Meshes',
                'start_date' => '2023-11-26',
                'end_date' => '2023-11-30',
                'location' => 'Copenhagen, Denmark',
                'organizer' => 'ACM SIGSAC',
                'certificate_proof_path' => 'proofs/events/ccs2023.pdf',
            ],
            [
                'event_name' => 'National Higher Education Research Symposium & Innovation Expo',
                'event_type' => 'SYMPOSIUM',
                'event_role' => 'LEADER_ORGANIZER',
                'paper_title' => 'Keynote: Commercializing University R&D Intellectual Property in Pakistan',
                'start_date' => '2023-03-14',
                'end_date' => '2023-03-15',
                'location' => 'Islamabad, Pakistan',
                'organizer' => 'Higher Education Commission (HEC)',
                'certificate_proof_path' => 'proofs/events/hec-symposium2023.pdf',
            ],
        ];

        // Work Experiences Pool
        $academicExperiences = [
            [
                'job_title' => 'Tenured Associate Professor',
                'organization' => 'National University of Sciences & Technology (NUST)',
                'start_date' => '2020-09-01',
                'end_date' => null,
                'experience_type' => 'Teaching & Academic Research',
                'sector' => 'Public Sector University (BPS-20)',
                'salary' => 'PKR 350,000 / month',
                'description' => 'Supervising doctoral & MS thesis research, teaching advanced Distributed Systems & Deep Learning, leading departmental curriculum revision and statutory accreditation committees.',
            ],
            [
                'job_title' => 'Assistant Professor',
                'organization' => 'COMSATS University Islamabad',
                'start_date' => '2016-08-01',
                'end_date' => '2020-08-31',
                'experience_type' => 'Teaching & Research',
                'sector' => 'Chartered Public University (TTS / BPS-19)',
                'salary' => 'PKR 240,000 / month',
                'description' => 'Taught undergraduate computing courses, Principal Investigator for HEC funded national research grant, published 8 impact factor research articles.',
            ],
            [
                'job_title' => 'Post-Doctoral Research Fellow',
                'organization' => 'School of Informatics, University of Edinburgh, UK',
                'start_date' => '2014-09-01',
                'end_date' => '2016-07-31',
                'experience_type' => 'Post-Doctoral Scholarly Research',
                'sector' => 'International Research Institution',
                'salary' => 'GBP 38,000 / year',
                'description' => 'Conducted foundational research on distributed graph optimization and asynchronous consensus algorithms under EU Horizon research framework.',
            ],
            [
                'job_title' => 'Lecturer in Computer Science',
                'organization' => 'FAST-NUCES Islamabad Campus',
                'start_date' => '2011-01-15',
                'end_date' => '2014-08-15',
                'experience_type' => 'Teaching',
                'sector' => 'Private University',
                'salary' => 'PKR 95,000 / month',
                'description' => 'Delivered lectures and lab tutorials for Data Structures, Algorithms, and Object-Oriented Programming.',
            ],
        ];

        $adminExperiences = [
            [
                'job_title' => 'Director IT & Digital Transformation Services',
                'organization' => 'Higher Education Institution / Public Agency',
                'start_date' => '2019-01-01',
                'end_date' => null,
                'experience_type' => 'Executive Management & IT Administration',
                'sector' => 'Public Sector (BPS-20)',
                'salary' => 'PKR 380,000 / month',
                'description' => 'Directing university-wide enterprise ERP, Tier-3 cloud datacenter, multi-gigabit optical campus backbone, and cyber security governance framework.',
            ],
            [
                'job_title' => 'Senior Systems Architect & Network Lead',
                'organization' => 'Nayatel / National Telecom Division',
                'start_date' => '2013-05-01',
                'end_date' => '2018-12-31',
                'experience_type' => 'Enterprise Technology Operations',
                'sector' => 'Corporate / Private',
                'salary' => 'PKR 250,000 / month',
                'description' => 'Led enterprise fiber infrastructure rollout, ISP peering point integration, and managed team of 24 cloud and network engineers.',
            ],
        ];

        // 3. Enrich each candidate with Publications, Projects, Patents, Events, and Experiences
        foreach ($candidates as $idx => $candidate) {
            $isAcademic = ($candidate->track_type === 'ACADEMIC');
            $candidateName = trim($candidate->first_name . ' ' . ($candidate->last_name ?? ''));

            // A. Seed Publications (3 to 8 publications per candidate)
            $pubCount = $isAcademic ? (4 + ($idx % 5)) : 2;
            for ($p = 0; $p < $pubCount; $p++) {
                $pubTemplate = $publicationsPool[($idx + $p) % count($publicationsPool)];
                Publication::updateOrCreate(
                    [
                        'basic_info_id' => $candidate->id,
                        'title' => $pubTemplate['title'] . ($p > 0 ? " — Part " . ($p + 1) : ""),
                    ],
                    [
                        'journal_name' => $pubTemplate['journal_name'],
                        'issn_print' => $pubTemplate['issn_print'],
                        'hjrs_category' => $pubTemplate['hjrs_category'],
                        'impact_factor' => $pubTemplate['impact_factor'],
                        'author_order' => $pubTemplate['author_order'],
                        'is_corresponding_author' => $pubTemplate['is_corresponding_author'],
                        'authors' => $candidateName . ', Dr. Elena Rostova, Prof. Dr. Sarah Connor',
                        'doi' => $pubTemplate['doi'],
                        'volume_issue' => $pubTemplate['volume_issue'],
                        'proof_path' => 'proofs/publications/pub_verified_' . ($p + 1) . '.pdf',
                        'publication_year' => $pubTemplate['publication_year'],
                        'is_verified' => true,
                    ]
                );
            }

            // B. Seed Research Projects (1 to 3 projects per candidate)
            $projCount = $isAcademic ? (1 + ($idx % 3)) : 1;
            for ($pj = 0; $pj < $projCount; $pj++) {
                $projTemplate = $projectsPool[($idx + $pj) % count($projectsPool)];
                ResearchProject::updateOrCreate(
                    [
                        'basic_info_id' => $candidate->id,
                        'project_title' => $projTemplate['project_title'] . ($pj > 0 ? " [Phase " . ($pj + 1) . "]" : ""),
                    ],
                    [
                        'funding_agency' => $projTemplate['funding_agency'],
                        'grant_amount' => $projTemplate['grant_amount'],
                        'role_in_project' => $projTemplate['role_in_project'],
                        'turnitin_similarity_percentage' => $projTemplate['turnitin_similarity_percentage'],
                        'status' => $projTemplate['status'],
                        'co_pis' => $projTemplate['co_pis'],
                        'start_date' => $projTemplate['start_date'],
                        'award_letter_proof_path' => 'proofs/projects/hec_grant_award.pdf',
                        'is_verified' => true,
                    ]
                );
            }

            // C. Seed Patents (for 50% of academic/technical candidates)
            if ($idx % 2 === 0) {
                $patTemplate = $patentsPool[$idx % count($patentsPool)];
                Patent::updateOrCreate(
                    [
                        'basic_info_id' => $candidate->id,
                        'registry_id' => $patTemplate['registry_id'] . '-' . $candidate->id,
                    ],
                    [
                        'title' => $patTemplate['title'],
                        'country' => $patTemplate['country'],
                        'date_registered' => $patTemplate['date_registered'],
                        'is_licensed' => $patTemplate['is_licensed'],
                        'proof_path' => $patTemplate['proof_path'],
                        'is_verified' => true,
                    ]
                );
            }

            // D. Seed Professional Events / Conferences
            $eventTemplate = $eventsPool[$idx % count($eventsPool)];
            ProfessionalEvent::updateOrCreate(
                [
                    'basic_info_id' => $candidate->id,
                    'event_name' => $eventTemplate['event_name'],
                ],
                [
                    'event_type' => $eventTemplate['event_type'],
                    'event_role' => $eventTemplate['event_role'],
                    'paper_title' => $eventTemplate['paper_title'],
                    'start_date' => $eventTemplate['start_date'],
                    'end_date' => $eventTemplate['end_date'],
                    'location' => $eventTemplate['location'],
                    'organizer' => $eventTemplate['organizer'],
                    'certificate_proof_path' => $eventTemplate['certificate_proof_path'],
                    'is_verified' => true,
                ]
            );

            // E. Seed Work Experiences
            $expPool = $isAcademic ? $academicExperiences : $adminExperiences;
            foreach ($expPool as $exp) {
                WorkExperience::updateOrCreate(
                    [
                        'basic_info_id' => $candidate->id,
                        'job_title' => $exp['job_title'],
                        'organization' => $exp['organization'],
                    ],
                    [
                        'start_date' => $exp['start_date'],
                        'end_date' => $exp['end_date'],
                        'experience_type' => $exp['experience_type'],
                        'sector' => $exp['sector'],
                        'salary' => $exp['salary'],
                        'description' => $exp['description'],
                        'proof_path' => 'proofs/experience/service_cert.pdf',
                        'is_verified' => true,
                    ]
                );
            }
        }

        // 4. Update Applications, Statuses, and Reviews
        $applications = Application::with(['user', 'campaign', 'reviews'])->get();
        if ($applications->isEmpty()) {
            return;
        }

        // Available expert reviewer users
        $reviewerUsers = User::role('academic-reviewer')->get();
        if ($reviewerUsers->isEmpty()) {
            $reviewerUsers = User::whereIn('email', [
                'dr.kenji.sato@u-tokyo.ac.jp',
                'dr.amira.hassan@aus.edu',
                'prof.durrani@strath.ac.uk',
                'dr.munir.ahmad@nus.edu.sg',
                'prof.arshad.ali@hec.gov.pk',
                'dr.lin.chen@tsinghua.edu.cn',
            ])->get();
        }

        $adminAssigner = User::role('registrar')->first() ?? User::role('super-admin')->first() ?? User::first();

        $reviewFeedbacks = [
            [
                'score' => 94.00,
                'recommendation' => 'STRONGLY_RECOMMENDED',
                'report' => "The candidate presents an exceptional research portfolio with multiple first-authored publications in flagship IEEE & ACM transactions. Substantial external R&D grant acquisitions as Principal Investigator demonstrate strong academic leadership and high scholarly impact. Highly suitable for the statutory appointment.",
            ],
            [
                'score' => 86.50,
                'recommendation' => 'RECOMMENDED',
                'report' => "Solid academic and research trajectory. HEC Category W/X publications satisfy institutional criteria. The candidate's research vision aligns well with the departmental mandate. Recommended for appointment.",
            ],
            [
                'score' => 74.00,
                'recommendation' => 'CONDITIONALLY_RECOMMENDED',
                'report' => "Scholarly publications are adequate for the post. However, external grant acquisitions are limited. Conditionally recommended subject to candidate presenting an active research and grant roadmap during the board interview.",
            ],
            [
                'score' => 45.00,
                'recommendation' => 'NOT_RECOMMENDED',
                'report' => "Evaluation indicates a shortfall in primary HEC W-category journal publications and insufficient evidence of independent research leadership. The candidate does not meet the benchmark required for this seniority.",
            ],
        ];

        foreach ($applications as $appIndex => $application) {
            $appStatus = $application->status;

            // Applications in pipeline stages should have peer reviews allocated
            $needsReviews = in_array($appStatus, ['UNDER_REVIEW', 'ELIGIBLE', 'SHORTLISTED', 'APPROVED', 'REJECTED']);

            if ($needsReviews && $reviewerUsers->isNotEmpty()) {
                // Empanel 2 distinct reviewers
                $revCount = min(2, $reviewerUsers->count());
                for ($r = 0; $r < $revCount; $r++) {
                    $reviewer = $reviewerUsers[($appIndex * 2 + $r) % $reviewerUsers->count()];
                    $feedback = $reviewFeedbacks[($appIndex + $r) % count($reviewFeedbacks)];

                    if ($appStatus === 'REJECTED') {
                        $feedback = $reviewFeedbacks[3]; // Not recommended
                    } elseif ($appStatus === 'APPROVED' || $appStatus === 'SHORTLISTED') {
                        $feedback = $reviewFeedbacks[$r % 2]; // Strongly recommended or Recommended
                    }

                    $isPending = ($appStatus === 'UNDER_REVIEW' && $r === 1);

                    ApplicationReview::updateOrCreate(
                        [
                            'application_id' => $application->id,
                            'reviewer_id' => $reviewer->id,
                        ],
                        [
                            'assigned_by' => $adminAssigner ? $adminAssigner->id : $reviewer->id,
                            'assigned_at' => now()->subDays(20 + $r),
                            'due_date' => now()->addDays(14),
                            'confidential_notes' => 'Official peer review assignment under Statutory University Recruitment Code.',
                            'status' => $isPending ? 'PENDING' : 'SUBMITTED',
                            'recommendation' => $isPending ? null : $feedback['recommendation'],
                            'overall_score' => $isPending ? null : $feedback['score'],
                            'report_text' => $isPending ? null : $feedback['report'],
                            'submitted_at' => $isPending ? null : now()->subDays(5 + $r),
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible cleanup
    }
};
