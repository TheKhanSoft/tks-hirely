<?php

namespace Tests\Feature;

use App\Livewire\Admin\SystemSettings;
use App\Mail\DynamicNotificationMail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\MailNotificationService;
use Database\Seeders\EmailTemplatesSeeder;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class EmailTemplatesAndTriggerGovernanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $registrar;
    protected User $applicant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SpatieRoleAndPermissionSeeder::class);
        $this->seed(EmailTemplatesSeeder::class);

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin.emails@university.edu',
        ]);
        $this->superAdmin->assignRole('super-admin');

        $this->registrar = User::factory()->create([
            'email' => 'registrar.emails@university.edu',
        ]);
        $this->registrar->assignRole('registrar');

        $this->applicant = User::factory()->create([
            'email' => 'candidate.user@gmail.com',
        ]);
        $this->applicant->assignRole('applicant');
    }

    public function test_default_email_templates_seeded_with_all_stakeholder_categories(): void
    {
        $expectedSlugs = [
            // 1. Security & Auth
            'account-created',
            'password-reset',
            'password-changed',
            'two-factor-auth-code',
            'profile-updated-security-notice',

            // 2. Candidate Lifecycle
            'candidate-application-submitted',
            'candidate-application-draft-reminder',
            'candidate-voucher-generated',
            'candidate-payment-settled',
            'candidate-document-deficiency-notice',
            'candidate-scrutiny-status-updated',
            'candidate-scrutiny-eligible',
            'candidate-scrutiny-ineligible-appeal',
            'candidate-appeal-submitted',
            'candidate-appeal-decision',
            'candidate-test-call-letter',
            'candidate-interview-call',
            'candidate-merit-list-published',
            'candidate-appointment-offer',

            // 3. Reviewers & Referees
            'reviewer-invitation',
            'reviewer-dossier-assigned',
            'reviewer-reminder',
            'reviewer-deadline-warning',
            'reviewer-report-acknowledged',
            'reviewer-evaluation-completed',

            // 4. Statutory Selection Board & Committees
            'board-meeting-scheduled',
            'board-roster-assigned',
            'board-scores-submitted-receipt',
            'candidate-final-selection-regret',
            'syndicate-approval-concurrence',

            // 5. General & Bespoke Dispatch
            'job-advertisement-announcement',
            'admin-custom-dispatch',
        ];

        $this->assertGreaterThanOrEqual(32, EmailTemplate::count());

        foreach ($expectedSlugs as $slug) {
            $template = EmailTemplate::getBySlug($slug);
            $this->assertNotNull($template, "Template with slug [{$slug}] must exist in database.");
            $this->assertTrue($template->is_trigger_enabled, "Trigger for [{$slug}] should be enabled by default.");
            $this->assertNotEmpty($template->subject, "Subject for [{$slug}] should not be empty.");
            $this->assertNotEmpty($template->body_html, "HTML body for [{$slug}] should not be empty.");
            $this->assertIsArray($template->placeholders, "Placeholders for [{$slug}] should be an array.");
        }
    }

    public function test_admin_and_registrar_can_access_email_templates_desk(): void
    {
        $this->actingAs($this->registrar);

        Livewire::test(SystemSettings::class, ['tab' => 'email-templates'])
            ->assertSet('activeTab', 'email_templates')
            ->assertSee('Official Email Templates, Automated Triggers & Custom Dispatch Desk')
            ->assertSee('Account Registration & Portal Credentials')
            ->assertSee('Password Reset Request')
            ->assertSee('Password Changed & Security Confirmation')
            ->assertSee('Application Submission Confirmation')
            ->assertSee('Recruitment Fee Challan Voucher Issued')
            ->assertSee('Statutory Scrutiny Vetting Outcome Notice')
            ->assertSee('Selection Board Interview Call Letter')
            ->assertSee('Provisional Recommendation & Statutory Appointment Offer')
            ->assertSee('Formal Invitation to Serve as External Academic Referee')
            ->assertSee('Confidential Candidate Dossier Assigned for Evaluation')
            ->assertSee('Notice of Statutory Selection Board Meeting & Agenda')
            ->assertSee('Compose Custom Email');
    }

    public function test_candidate_cannot_access_email_templates_settings(): void
    {
        $this->actingAs($this->applicant);

        Livewire::test(SystemSettings::class, ['tab' => 'email-templates'])
            ->assertForbidden();
    }

    public function test_admin_can_toggle_email_triggers_on_and_off(): void
    {
        $this->actingAs($this->registrar);
        $template = EmailTemplate::where('slug', 'candidate-voucher-generated')->first();
        $this->assertTrue($template->is_trigger_enabled);

        // Toggle trigger to false
        Livewire::test(SystemSettings::class, ['tab' => 'email-templates'])
            ->call('toggleTemplateTrigger', $template->id)
            ->assertSee('DISABLED');

        $this->assertFalse($template->fresh()->is_trigger_enabled);
        $this->assertFalse(EmailTemplate::isTriggerEnabled('candidate-voucher-generated'));

        // When trigger is disabled, attempting to dispatch skips email and logs skip status
        Mail::fake();
        $dispatchResult = MailNotificationService::sendTemplate(
            'candidate-voucher-generated',
            'test.applicant@gmail.com',
            ['{candidate_name}' => 'Ali', '{job_title}' => 'Lecturer', '{consumer_id}' => '10001']
        );

        $this->assertEquals('skipped', $dispatchResult['status']);
        Mail::assertNothingSent();

        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'test.applicant@gmail.com',
            'template_slug' => 'candidate-voucher-generated',
            'status' => 'TRIGGER_DISABLED_SKIPPED',
        ]);

        // Toggle trigger back to true
        Livewire::test(SystemSettings::class, ['tab' => 'email-templates'])
            ->call('toggleTemplateTrigger', $template->id)
            ->assertSee('ENABLED');

        $this->assertTrue($template->fresh()->is_trigger_enabled);
        $this->assertTrue(EmailTemplate::isTriggerEnabled('candidate-voucher-generated'));
    }

    public function test_admin_can_edit_template_and_restore_to_default(): void
    {
        $this->actingAs($this->registrar);
        $template = EmailTemplate::where('slug', 'candidate-application-submitted')->first();

        // 1. Open edit modal
        $component = Livewire::test(SystemSettings::class, ['tab' => 'email-templates'])
            ->call('openEditTemplateModal', $template->id)
            ->assertSet('isTemplateModalOpen', true)
            ->assertSet('template_name', $template->name)
            ->assertSet('template_subject', $template->subject);

        // 2. Modify subject and body
        $newSubject = 'Customized Subject: Your {job_title} Application Received';
        $newBody = '<p>Dear {candidate_name}, this is a customized message.</p>';

        $component->set('template_subject', $newSubject)
            ->set('template_body', $newBody)
            ->call('saveTemplate')
            ->assertSet('isTemplateModalOpen', false);

        $this->assertEquals($newSubject, $template->fresh()->subject);
        $this->assertEquals($newBody, $template->fresh()->body_html);

        // 3. Restore to statutory default
        $component->call('openEditTemplateModal', $template->id)
            ->call('resetTemplateToDefault', $template->id);

        $this->assertStringContainsString('Application Received', $template->fresh()->subject);
        $this->assertStringContainsString('Application Successfully Received', $template->fresh()->body_html);
    }

    public function test_admin_can_preview_template(): void
    {
        $this->actingAs($this->registrar);
        $template = EmailTemplate::where('slug', 'candidate-interview-call')->first();

        Livewire::test(SystemSettings::class, ['tab' => 'email-templates'])
            ->call('openPreviewModal', $template->id)
            ->assertSet('isPreviewModalOpen', true)
            ->assertSet('previewMode', 'sample')
            ->assertSee('Selection Board Interview Call Letter')
            ->assertSee('Dr. Tariq Mahmood Khan') // Dummy sample data
            ->call('setPreviewMode', 'raw')
            ->assertSet('previewMode', 'raw')
            ->call('closePreviewModal')
            ->assertSet('isPreviewModalOpen', false);
    }

    public function test_admin_can_compose_and_dispatch_custom_email_with_rich_textbox(): void
    {
        Mail::fake();
        $this->actingAs($this->registrar);

        $component = Livewire::test(SystemSettings::class, ['tab' => 'email-templates'])
            ->call('openCustomEmailModal')
            ->assertSet('isCustomEmailModalOpen', true)
            // Load from an existing template layout
            ->set('custom_selected_template_slug', 'candidate-application-submitted')
            ->call('loadTemplateForCustomEmail');

        $this->assertNotEmpty($component->get('custom_subject'));
        $this->assertNotEmpty($component->get('custom_body'));

        // Customize recipient, subject and rich text body
        $component->set('custom_recipient_email', 'referee.international@oxford.ac.uk')
            ->set('custom_recipient_name', 'Prof. Dr. Elena Rostova')
            ->set('custom_subject', 'Urgent Inquiry Regarding Faculty Review Docket')
            ->set('custom_body', '<h2>Statutory Notice</h2><p>Please review docket <strong>#42</strong>.</p>')
            ->call('sendCustomEmailAction')
            ->assertSet('isCustomEmailModalOpen', false);

        Mail::assertSent(DynamicNotificationMail::class, function ($mail) {
            return $mail->hasTo('referee.international@oxford.ac.uk')
                && str_contains($mail->mailSubject, 'Urgent Inquiry Regarding Faculty Review Docket')
                && str_contains($mail->renderedHtml, 'Prof. Dr. Elena Rostova')
                && str_contains($mail->renderedHtml, 'Statutory Notice');
        });

        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'referee.international@oxford.ac.uk',
            'recipient_name' => 'Prof. Dr. Elena Rostova',
            'template_slug' => 'admin-custom-dispatch',
            'status' => 'SENT',
            'dispatch_type' => 'MANUAL_CUSTOM',
        ]);
    }

    public function test_automated_trigger_sends_email_and_logs_in_database_when_enabled(): void
    {
        Mail::fake();

        $result = MailNotificationService::sendTemplate(
            slug: 'candidate-payment-settled',
            recipientEmail: 'applicant.verified@gmail.com',
            variables: [
                '{candidate_name}' => 'Kamran Khan',
                '{job_title}' => 'Assistant Professor - Computer Science',
                '{consumer_id}' => '1000787899',
                '{transaction_id}' => '1LINK-TXN-998822',
                '{amount_paid}' => 'PKR 3,000.00',
                '{settled_at}' => '06 Nov 2026, 02:30 PM',
                '{receipt_url}' => url('/candidate/dashboard'),
            ],
            senderUser: $this->registrar
        );

        $this->assertEquals('sent', $result['status']);

        Mail::assertSent(DynamicNotificationMail::class, function ($mail) {
            return $mail->hasTo('applicant.verified@gmail.com')
                && str_contains($mail->mailSubject, 'Payment Received & Verified')
                && str_contains($mail->renderedHtml, 'Kamran Khan')
                && str_contains($mail->renderedHtml, '1LINK-TXN-998822');
        });

        $this->assertDatabaseHas('email_logs', [
            'recipient_email' => 'applicant.verified@gmail.com',
            'template_slug' => 'candidate-payment-settled',
            'status' => 'SENT',
            'dispatch_type' => 'AUTOMATED_TRIGGER',
        ]);
    }
}
