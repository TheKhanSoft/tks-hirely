<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use App\Models\Campaign;
use App\Models\ReviewerProfile;
use App\Models\BasicInfo;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run Spatie role seeder
        $this->call(SpatieRoleAndPermissionSeeder::class);

        // 2. Seed Default Accounts
        $users = [
            'superadmin@university.edu' => 'super-admin',
            'registrar@university.edu' => 'registrar',
            'scrutiny1@university.edu' => 'scrutiny-committee',
            'scrutiny2@university.edu' => 'scrutiny-committee',
            'appellate1@university.edu' => 'appellate-committee',
            'appellate2@university.edu' => 'appellate-committee',
            'board1@university.edu' => 'selection-board-member',
            'board2@university.edu' => 'selection-board-member',
            'board3@university.edu' => 'selection-board-member',
            'applicant1@gmail.com' => 'applicant',
            'applicant2@gmail.com' => 'applicant',
        ];

        foreach ($users as $email => $role) {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['password' => Hash::make('password'), 'is_active' => true]
            );
            $user->assignRole($role);
        }

        // Seed basic info for internal members
        $internalStaff = [
            'scrutiny1@university.edu' => ['first_name' => 'Dr. Usman', 'last_name' => 'Qureshi'],
            'scrutiny2@university.edu' => ['first_name' => 'Engr. Fatima', 'last_name' => 'Zahra'],
            'board1@university.edu' => ['first_name' => 'Prof. Dr. Tariq', 'last_name' => 'Mansoor'],
            'board2@university.edu' => ['first_name' => 'Dr. Zainab', 'last_name' => 'Akhtar'],
            'board3@university.edu' => ['first_name' => 'Dr. Hamza', 'last_name' => 'Bilal'],
            'appellate1@university.edu' => ['first_name' => 'Barrister Bilal', 'last_name' => 'Sheikh'],
            'appellate2@university.edu' => ['first_name' => 'Dr. Ayesha', 'last_name' => 'Siddiqa'],
            'registrar@university.edu' => ['first_name' => 'Malik', 'last_name' => 'Javed Iqbal'],
        ];

        foreach ($internalStaff as $email => $info) {
            $u = User::where('email', $email)->first();
            if ($u && !$u->basicInfo) {
                BasicInfo::create([
                    'user_id' => $u->id,
                    'track_type' => 'ACADEMIC',
                    'first_name' => $info['first_name'],
                    'last_name' => $info['last_name'],
                    'cnic_number' => '37405-123456' . $u->id . '-1',
                    'nationality' => 'Pakistan',
                    'date_of_birth' => '1980-01-01',
                    'gender' => 'Male',
                    'father_name' => 'Father',
                    'marital_status' => 'Married',
                    'phone_number' => '+92-300-1234567',
                    'permanent_address' => 'Main Campus Office, Islamabad',
                    'mailing_address' => 'Main Campus Office, Islamabad',
                    'same_address' => true,
                    'is_locked' => true,
                ]);
            }
        }

        // Seed External Experts & International Specialists (Other universities, offices, countries)
        $externalExperts = [
            [
                'email' => 'dr.sarah.connor@mit.edu',
                'name' => 'Prof. Dr. Sarah Connor',
                'subject' => 'Artificial Intelligence & Systems',
                'rank' => 'Tenured Professor',
                'institution' => 'Massachusetts Institute of Technology (MIT)',
                'country' => 'United States',
                'specialization' => 'Deep Learning, Scalable Systems & Computing Architectures',
                'role' => 'selection-board-member'
            ],
            [
                'email' => 'dr.elena.rostova@oxford.ac.uk',
                'name' => 'Dr. Elena Rostova',
                'subject' => 'Cybersecurity & Cryptography',
                'rank' => 'Principal Research Fellow',
                'institution' => 'University of Oxford',
                'country' => 'United Kingdom',
                'specialization' => 'Applied Cryptography and Cyber Defense Infrastructure',
                'role' => 'selection-board-member'
            ],
            [
                'email' => 'dr.kenji.sato@u-tokyo.ac.jp',
                'name' => 'Prof. Dr. Kenji Sato',
                'subject' => 'Computer Science & Robotics',
                'rank' => 'Senior Chair Professor',
                'institution' => 'University of Tokyo',
                'country' => 'Japan',
                'specialization' => 'Robotics, Distributed Control & Embedded Real-Time Systems',
                'role' => 'academic-reviewer'
            ],
            [
                'email' => 'prof.ahmad.khan@lums.edu.pk',
                'name' => 'Prof. Dr. Ahmad Khan',
                'subject' => 'Computer Science',
                'rank' => 'Professor & Department Chair',
                'institution' => 'Lahore University of Management Sciences (LUMS)',
                'country' => 'Pakistan',
                'specialization' => 'High Performance Computing, Algorithms & Cloud Architectures',
                'role' => 'selection-board-member'
            ],
            [
                'email' => 'engr.tariq.aziz@telecom.gov.pk',
                'name' => 'Engr. Tariq Aziz',
                'subject' => 'Information Technology & Networks',
                'rank' => 'Director Technology Operations',
                'institution' => 'Federal Telecommunications Authority / Partner Office',
                'country' => 'Pakistan',
                'specialization' => 'National IT Infrastructure, Cyber Policy & Telecom Engineering',
                'role' => 'selection-board-member'
            ],
            [
                'email' => 'dr.amira.hassan@aus.edu',
                'name' => 'Dr. Amira Hassan',
                'subject' => 'Computer Engineering',
                'rank' => 'Associate Dean of Engineering',
                'institution' => 'American University of Sharjah',
                'country' => 'United Arab Emirates',
                'specialization' => 'Edge Computing, VLSI Design & AI Accelerators',
                'role' => 'academic-reviewer'
            ]
        ];

        foreach ($externalExperts as $exp) {
            $user = User::firstOrCreate(
                ['email' => $exp['email']],
                ['password' => Hash::make('password'), 'is_active' => true]
            );
            $user->assignRole($exp['role']);

            ReviewerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $exp['name'],
                    'subject' => $exp['subject'],
                    'academic_rank' => $exp['rank'],
                    'university' => $exp['institution'],
                    'country' => $exp['country'],
                    'phone' => '+1-555-0199',
                    'specialization' => $exp['specialization'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Seed Default Campaigns
        Campaign::firstOrCreate(
            ['job_code' => 'CS-FAC-2026'],
            [
                'title' => 'Assistant Professor - Computer Science',
                'track_type' => 'ACADEMIC',
                'department' => 'Department of Computer Science',
                'opening_date' => now()->subDays(5),
                'closing_date' => now()->addDays(30),
                'application_fee' => 3000.00,
                'is_active' => true,
                'scoring_rules' => [
                    'committee_constitution' => [
                        'required_total' => 5,
                        'required_internal' => 2,
                        'required_external' => 2,
                        'required_international' => 1,
                        'required_independent' => 0,
                        'title' => 'Statutory Academic Selection Committee (Faculty Track)',
                        'description' => '2 Internal Leaders/Staff, 2 External Subject Experts (Other Institutions/Offices), 1 International/Overseas Specialist.',
                        'enforce_strict_quota' => true,
                    ],
                    'gold_medal_weight' => 5.0,
                    'silver_medal_weight' => 3.0,
                    'bronze_medal_weight' => 2.0,
                    'organizer_leader_weight' => 3.0,
                    'organizer_associate_weight' => 1.5,
                    'paper_international_weight' => 1.0,
                    'paper_national_weight' => 0.5,
                    'event_attendee_weight' => 0.1,
                    'w_category_weight' => 1.0,
                    'x_category_weight' => 0.75,
                    'y_category_weight' => 0.5,
                    'z_category_weight' => 0.15,
                    'project_million_rate' => 1.0,
                    'project_max_marks' => 13.0,
                    'patent_granted_weight' => 1.0,
                    'patent_licensed_weight' => 2.0,
                ]
            ]
        );

        Campaign::firstOrCreate(
            ['job_code' => 'REG-DIR-2026'],
            [
                'title' => 'Director IT Operations',
                'track_type' => 'ADMINISTRATIVE',
                'department' => 'Information Technology Services',
                'opening_date' => now()->subDays(2),
                'closing_date' => now()->addDays(15),
                'application_fee' => 5000.00,
                'is_active' => true,
                'scoring_rules' => [
                    'experience_years_weight' => 2.0,
                    'budget_managed_threshold' => 10.0, // in Millions PKR
                ]
            ]
        );

        Campaign::firstOrCreate(
            ['job_code' => 'SUP-SEC-2026'],
            [
                'title' => 'Security Patrol Officer',
                'track_type' => 'SUPPORT',
                'department' => 'Campus Security Division',
                'opening_date' => now()->subDays(1),
                'closing_date' => now()->addDays(10),
                'application_fee' => 500.00,
                'is_active' => true,
                'scoring_rules' => [
                    'fitness_certified' => true,
                    'trade_cert_points' => 10.0,
                ]
            ]
        );
    }
}
