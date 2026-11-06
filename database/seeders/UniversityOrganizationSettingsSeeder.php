<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use App\Models\SystemSetting;

class UniversityOrganizationSettingsSeeder extends Seeder
{
    /**
     * Run the institutional settings update seeder.
     * Updates default generic Hirely platform settings with institution-specific data
     * (e.g. Abdul Wali Khan University Mardan / AWKUM).
     */
    public function run(?array $customSettings = null): void
    {
        $year = date('Y');

        $settings = $customSettings ?? [
            // 🏢 Organization & University Profile
            'org_name' => 'Abdul Wali Khan University Mardan',
            'org_short_name' => 'AWKUM',
            'org_location' => 'Khyber Pakhtunkhwa, Pakistan',
            'org_office_title' => 'Office of the Registrar',
            'org_type' => 'HIGHER_EDUCATION',
            'sector_label' => 'Department / Faculty',
            'currency_symbol' => 'PKR',
            'default_application_fee' => 3000,

            // 📜 Official Letterhead Details
            'letterhead_heading' => 'Abdul Wali Khan University Mardan',
            'letterhead_subheading' => 'Khyber Pakhtunkhwa, Pakistan',
            'letterhead_office' => 'Office of the Registrar',
            'letterhead_office_font_size' => 'standard',
            'letterhead_logo' => '/images/logo-icon.svg',
            'letterhead_custom_details' => "Main Campus, Toru Road, Mardan, Khyber Pakhtunkhwa, Pakistan\nPhone: +92-937-9230611 • Fax: +92-937-9230612 • Web: www.awkum.edu.pk",
            'letterhead_layout' => 'classic_left',
            'letterhead_dossier_title_admin' => 'Official Master Application Dossier & Scrutiny Record',
            'letterhead_dossier_title_candidate' => 'Official Application Form for Employment',
            'letterhead_show_document_title' => true,
            'letterhead_show_statutory_note' => true,
            'dossier_show_photo' => true,
            'dossier_show_draft_watermark' => true,

            // 📞 Contact & Helpdesk Channels
            'org_website' => 'https://www.awkum.edu.pk',
            'support_email' => 'registrar@awkum.edu.pk',
            'support_phone' => '+92-937-9230611',
            'support_whatsapp' => '+92-300-1234567',
            'registrar_hotline' => '+92-937-9230611',
            'support_hours' => 'Monday to Friday: 08:30 AM – 04:30 PM PKT',
            'helpdesk_location' => 'Office of the Registrar, Administration Block, Main Campus, Toru Road, Mardan',

            // 🏷️ Multi-Portal Footers & Statutory Disclaimers
            'footer_copyright_text' => "© {$year} Abdul Wali Khan University Mardan (AWKUM). All rights reserved.",
            'footer_candidate_text' => 'Candidate Facilitation Desk: For application guidance or voucher verification, contact registrar@awkum.edu.pk or helpline +92-937-9230611.',
            'footer_admin_text' => 'Official Administrative Desk • Abdul Wali Khan University Mardan (AWKUM) • System Access Audited & Recorded.',
            'footer_board_text' => 'Statutory Selection Board Secretariat • Abdul Wali Khan University Mardan • Strictly Confidential Under University Act.',
            'footer_reviewer_text' => 'Academic Peer Evaluation Desk • External Referee Integrity • Blind Review Protocols Active.',
            'letter_footer_text' => 'This is an official computer-generated document issued under the seal and authority of Abdul Wali Khan University Mardan (AWKUM). Any alteration, erasure, or unauthorized tampering renders this document null and void.',
            'letter_footer_contact' => 'Office of the Registrar, AWKUM • Tel: +92-937-9230611 • Web: www.awkum.edu.pk',
            'letter_show_audit_trail' => true,
            'letter_show_verify_ref' => true,

            // ✉️ Email & SMS Gateway Configuration
            'mail_from_name' => 'AWKUM Recruitment Desk',
            'mail_from_address' => 'noreply@awkum.edu.pk',
            'mail_reply_to' => 'registrar@awkum.edu.pk',
            'sms_sender_id' => 'AWKUM-HR',
        ];

        foreach ($settings as $key => $value) {
            $group = match ($key) {
                'org_name', 'org_short_name', 'org_location', 'org_office_title',
                'org_type', 'sector_label', 'currency_symbol', 'default_application_fee',
                'letterhead_heading', 'letterhead_subheading', 'letterhead_office',
                'letterhead_logo', 'letterhead_custom_details', 'letterhead_layout',
                'letterhead_dossier_title_admin', 'letterhead_dossier_title_candidate' => 'organization',

                'org_website', 'support_email', 'support_phone', 'support_whatsapp',
                'registrar_hotline', 'support_hours', 'helpdesk_location' => 'contact',

                'mail_from_name', 'mail_from_address', 'mail_reply_to', 'sms_sender_id' => 'email',

                default => 'branding',
            };

            $type = is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : 'string');

            SystemSetting::set($key, $value, $group, $type);
        }

        Cache::flush();

        $this->command?->info("✓ Successfully updated Hirely default settings to institutional profile: " . ($settings['org_name'] ?? 'University'));
    }
}
