<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Seed official email templates.
     */
    public function run(): void
    {
        EmailTemplate::seedDefaultTemplates();
    }
}
