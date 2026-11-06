<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SystemSetting;
use App\Models\RecruitmentProcessFlow;
use App\Models\CommitteeConstitution;
use Database\Seeders\StatutoryConstitutionsAndStatusesSeeder;
use Database\Seeders\UniversityOrganizationSettingsSeeder;
use Database\Seeders\InstitutionalSettingsUpdateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HirelyDefaultSettingsAndUniversitySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_settings_defaults_are_generic_hirely_not_associated_to_any_organization(): void
    {
        SystemSetting::query()->delete();
        \Illuminate\Support\Facades\Cache::flush();

        // 1. When no settings exist, helpers return clean Hirely defaults
        $this->assertEquals('Hirely', SystemSetting::getOrgName());
        $this->assertEquals('Hirely', SystemSetting::getOrgShortName());
        $this->assertStringContainsString('Hirely', SystemSetting::getAppName());

        $letterhead = SystemSetting::getLetterheadDetails();
        $this->assertEquals('Hirely Higher Education & Recruitment Commission', $letterhead['heading']);
        $this->assertEquals('National Directorate', $letterhead['subheading']);
        $this->assertEquals('Recruitment & Statutory Selection Directorate', $letterhead['office']);
        $this->assertStringContainsString('https://hirely.app', $letterhead['custom_details']);
        $this->assertStringNotContainsString('AWKUM', $letterhead['custom_details']);
        $this->assertStringNotContainsString('Abdul Wali Khan', $letterhead['custom_details']);

        $footer = SystemSetting::getPortalFooter('admin');
        $this->assertStringContainsString('Hirely', $footer);
        $this->assertStringNotContainsString('AWKUM', $footer);
        $this->assertStringNotContainsString('Abdul Wali Khan', $footer);

        $letterFooter = SystemSetting::getLetterFooter();
        $this->assertStringContainsString('Hirely', $letterFooter['disclaimer']);
        $this->assertStringNotContainsString('AWKUM', $letterFooter['disclaimer']);
        $this->assertStringNotContainsString('Abdul Wali Khan', $letterFooter['disclaimer']);

        // 2. When seedDefaultSettings is invoked, generic Hirely defaults are persisted
        SystemSetting::seedDefaultSettings();

        $this->assertEquals('Hirely', SystemSetting::get('org_name'));
        $this->assertEquals('Hirely', SystemSetting::get('org_short_name'));
        $this->assertEquals('National Directorate', SystemSetting::get('org_location'));
        $this->assertEquals('support@hirely.app', SystemSetting::get('support_email'));
        $this->assertEquals('noreply@hirely.app', SystemSetting::get('mail_from_address'));
        $this->assertEquals('HIRELY', SystemSetting::get('sms_sender_id'));
    }

    public function test_university_organization_settings_seeder_updates_defaults_to_university_profile(): void
    {
        SystemSetting::seedDefaultSettings();
        $this->assertEquals('Hirely', SystemSetting::get('org_name'));

        // Run the seeder
        $this->seed(UniversityOrganizationSettingsSeeder::class);

        // Verify that default settings have been customized to the university
        $this->assertEquals('Abdul Wali Khan University Mardan', SystemSetting::getOrgName());
        $this->assertEquals('AWKUM', SystemSetting::getOrgShortName());
        $this->assertEquals('Khyber Pakhtunkhwa, Pakistan', SystemSetting::get('org_location'));
        $this->assertEquals('Office of the Registrar', SystemSetting::get('org_office_title'));
        $this->assertEquals('registrar@awkum.edu.pk', SystemSetting::get('support_email'));
        $this->assertEquals('+92-937-9230611', SystemSetting::get('support_phone'));
        $this->assertEquals('AWKUM-HR', SystemSetting::get('sms_sender_id'));

        $letterhead = SystemSetting::getLetterheadDetails();
        $this->assertEquals('Abdul Wali Khan University Mardan', $letterhead['heading']);
        $this->assertStringContainsString('www.awkum.edu.pk', $letterhead['custom_details']);

        $footer = SystemSetting::getPortalFooter('admin');
        $this->assertStringContainsString('AWKUM', $footer);

        $letterFooter = SystemSetting::getLetterFooter();
        $this->assertStringContainsString('AWKUM', $letterFooter['disclaimer']);
    }

    public function test_institutional_settings_update_seeder_alias_works_identically(): void
    {
        SystemSetting::seedDefaultSettings();
        $this->seed(InstitutionalSettingsUpdateSeeder::class);

        $this->assertEquals('Abdul Wali Khan University Mardan', SystemSetting::getOrgName());
        $this->assertEquals('AWKUM', SystemSetting::getOrgShortName());
    }

    public function test_seeders_only_seed_hec_and_university_related_pipelines_and_constitutions(): void
    {
        // Run seeder
        $this->seed(StatutoryConstitutionsAndStatusesSeeder::class);

        // 1. Verify Pipelines
        $pipelines = RecruitmentProcessFlow::all();
        $this->assertNotEmpty($pipelines);

        foreach ($pipelines as $pipeline) {
            $this->assertEquals('ACADEMIC', $pipeline->sector, "Pipeline [{$pipeline->slug}] must belong to ACADEMIC sector.");
            $this->assertStringNotContainsString('corporate', strtolower($pipeline->slug));
            $this->assertStringNotContainsString('healthcare', strtolower($pipeline->slug));
            $this->assertStringNotContainsString('civil-service', strtolower($pipeline->slug));
        }

        // Specific expected university & HEC pipelines
        $this->assertDatabaseHas('recruitment_process_flows', ['slug' => 'academic-statutory-pipeline']);
        $this->assertDatabaseHas('recruitment_process_flows', ['slug' => 'university-officer-statutory-pipeline']);
        $this->assertDatabaseHas('recruitment_process_flows', ['slug' => 'university-support-cadre-pipeline']);

        // Excluded pipelines
        $this->assertDatabaseMissing('recruitment_process_flows', ['slug' => 'corporate-talent-pipeline']);
        $this->assertDatabaseMissing('recruitment_process_flows', ['slug' => 'public-sector-civil-service-pipeline']);

        // 2. Verify Committee Constitutions
        $constitutions = CommitteeConstitution::all();
        $this->assertNotEmpty($constitutions);

        foreach ($constitutions as $const) {
            $this->assertEquals('ACADEMIC', $const->organization_sector, "Constitution [{$const->slug}] must belong to ACADEMIC sector.");
            $this->assertStringNotContainsString('corporate', strtolower($const->slug));
            $this->assertStringNotContainsString('healthcare', strtolower($const->slug));
            $this->assertStringNotContainsString('public-sector', strtolower($const->slug));
        }

        // Specific expected university & HEC constitutions
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'standing-scrutiny-committee']);
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'standing-expert-referees-panel']);
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'statutory-selection-asst-prof']);
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'statutory-selection-professorial']);
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'university-statutory-officer-selection-board']);
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'university-support-selection-committee']);
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'standing-syndicate-council']);
        $this->assertDatabaseHas('committee_constitutions', ['slug' => 'standing-appellate-committee']);

        // Excluded constitutions
        $this->assertDatabaseMissing('committee_constitutions', ['slug' => 'corporate-executive-panel']);
        $this->assertDatabaseMissing('committee_constitutions', ['slug' => 'healthcare-specialist-board']);
        $this->assertDatabaseMissing('committee_constitutions', ['slug' => 'public-sector-service-board']);
        $this->assertDatabaseMissing('committee_constitutions', ['slug' => 'cross-sector-independent-eval']);
    }
}
