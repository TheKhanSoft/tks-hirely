<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use App\Models\Campaign;

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
