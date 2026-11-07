<?php

namespace Tests\Feature;

use App\Mail\DynamicNotificationMail;
use App\Models\Advertisement;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\BasicInfo;
use App\Models\BillingLedger;
use App\Models\Campaign;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Services\MailNotificationService;
use Database\Seeders\EmailTemplatesSeeder;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailNotificationServiceTriggersTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $candidate;
    protected User $reviewer;
    protected Campaign $campaign;
    protected Application $application;
    protected BillingLedger $ledger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SpatieRoleAndPermissionSeeder::class);
        $this->seed(EmailTemplatesSeeder::class);

        $this->superAdmin = User::factory()->create([
            'email' => 'admin.triggers@university.edu',
        ]);
        $this->superAdmin->assignRole('super-admin');
        BasicInfo::create([
            'user_id' => $this->superAdmin->id,
            'first_name' => 'Admin',
            'last_name' => 'Director',
            'cnic_number' => '17301-0000001-1',
            'date_of_birth' => '1980-01-01',
        ]);

        $this->candidate = User::factory()->create([
            'email' => 'candidate.scholar@domain.pk',
        ]);
        $this->candidate->assignRole('applicant');
        BasicInfo::create([
            'user_id' => $this->candidate->id,
            'first_name' => 'Dr. Candidate',
            'last_name' => 'Scholar',
            'cnic_number' => '17301-1234567-1',
            'date_of_birth' => '1990-05-15',
        ]);

        $this->reviewer = User::factory()->create([
            'email' => 'external.peer@partner.edu',
        ]);
        $this->reviewer->assignRole('reviewer');
        BasicInfo::create([
            'user_id' => $this->reviewer->id,
            'first_name' => 'Prof. External',
            'last_name' => 'Peer',
            'cnic_number' => '17301-9999999-1',
            'date_of_birth' => '1975-08-20',
        ]);

        $this->campaign = Campaign::create([
            'job_code' => 'CS-PROF-2026',
            'title' => 'Professor of Computer Science (BPS-21)',
            'department' => 'Computer Science',
            'application_fee' => 3000.00,
            'is_active' => true,
        ]);

        $this->application = Application::create([
            'user_id' => $this->candidate->id,
            'campaign_id' => $this->campaign->id,
            'status' => 'SUBMITTED',
            'submission_timestamp' => now(),
        ]);

        $this->ledger = BillingLedger::create([
            'application_id' => $this->application->id,
            'consumer_id' => '100098765432',
            'amount' => 3000.00,
            'status' => 'PENDING',
        ]);
    }

    public function test_auth_and_security_trigger_methods_dispatch_successfully(): void
    {
        Mail::fake();

        // 1. Account Created
        $res1 = MailNotificationService::triggerAccountCreated($this->candidate, 'TempSecret123!');
        $this->assertEquals('sent', $res1['status']);

        // 2. Password Reset
        $res2 = MailNotificationService::triggerPasswordReset($this->candidate, 'NewSecret456@', 'email');
        $this->assertEquals('sent', $res2['status']);

        // 3. Password Changed
        $res3 = MailNotificationService::triggerPasswordChanged($this->candidate);
        $this->assertEquals('sent', $res3['status']);

        // 4. Two Factor Auth Code
        $res4 = MailNotificationService::triggerTwoFactorCode($this->candidate, '982341');
        $this->assertEquals('sent', $res4['status']);

        // 5. Profile Updated Security Notice
        $res5 = MailNotificationService::triggerProfileUpdatedNotice($this->candidate, 'CNIC and Academic Degrees');
        $this->assertEquals('sent', $res5['status']);

        Mail::assertSent(DynamicNotificationMail::class, 5);
        $this->assertEquals(5, EmailLog::where('recipient_email', $this->candidate->email)->count());
    }

    public function test_candidate_lifecycle_triggers_dispatch_successfully(): void
    {
        Mail::fake();

        // 1. Voucher Generated
        $res1 = MailNotificationService::triggerVoucherGenerated($this->application);
        $this->assertEquals('sent', $res1['status']);

        // 2. Application Submitted
        $res2 = MailNotificationService::triggerApplicationSubmitted($this->application);
        $this->assertEquals('sent', $res2['status']);

        // 3. Profile Locked Confirmation
        $res3 = MailNotificationService::triggerProfileLockedConfirmation($this->application);
        $this->assertEquals('sent', $res3['status']);

        // 4. Challan Expired Notice
        $res4 = MailNotificationService::triggerChallanExpiredNotice($this->application);
        $this->assertEquals('sent', $res4['status']);

        // 5. Payment Settled
        $res5 = MailNotificationService::triggerPaymentSettled($this->application, $this->ledger);
        $this->assertEquals('sent', $res5['status']);

        // 6. Document Deficiency Notice
        $res6 = MailNotificationService::triggerDocumentDeficiencyNotice($this->application, 'Missing verified MS transcript photocopy', 5);
        $this->assertEquals('sent', $res6['status']);

        // 7. Scrutiny Eligible
        $res7 = MailNotificationService::triggerScrutinyEligible($this->application, 'HEC criteria satisfied completely.');
        $this->assertEquals('sent', $res7['status']);

        // 8. Scrutiny Ineligible
        $res8 = MailNotificationService::triggerScrutinyIneligible($this->application, 'Third division in undergraduate degree.');
        $this->assertEquals('sent', $res8['status']);

        // 9. Appeal Submitted
        $res9 = MailNotificationService::triggerAppealSubmitted($this->application, 'Equivalence certificate attached from HEC.');
        $this->assertEquals('sent', $res9['status']);

        // 10. Appeal Decision
        $res10 = MailNotificationService::triggerAppealDecision($this->application, 'ELIGIBLE', 'Appellate committee verified HEC equivalence.');
        $this->assertEquals('sent', $res10['status']);

        // 11. Screening Test Call
        $res11 = MailNotificationService::triggerScreeningTestCall($this->application, [
            'date' => 'Monday, 20 Nov 2026',
            'time' => '10:00 AM',
            'venue' => 'Main Exam Hall',
        ]);
        $this->assertEquals('sent', $res11['status']);

        // 12. Interview Call
        $res12 = MailNotificationService::triggerInterviewCall($this->application, [
            'date' => 'Wednesday, 25 Nov 2026',
            'time' => '11:00 AM',
            'venue' => 'Vice Chancellor Syndicate Hall',
        ]);
        $this->assertEquals('sent', $res12['status']);

        // 13. Merit List Published
        $res13 = MailNotificationService::triggerMeritListPublished($this->campaign, $this->application);
        $this->assertEquals('sent', $res13['status']);

        // 14. Appointment Offer
        $res14 = MailNotificationService::triggerAppointmentOffer($this->application, [
            'department' => 'Department of Computer Science',
            'ref_no' => 'AWKUM/REG/2026/891',
        ]);
        $this->assertEquals('sent', $res14['status']);

        // 15. Selection Regret
        $res15 = MailNotificationService::triggerFinalSelectionRegret($this->application, 'Position filled as per quota merit.');
        $this->assertEquals('sent', $res15['status']);

        Mail::assertSent(DynamicNotificationMail::class, 15);
    }

    public function test_reviewer_and_board_triggers_dispatch_successfully(): void
    {
        Mail::fake();

        $review = ApplicationReview::create([
            'application_id' => $this->application->id,
            'reviewer_id' => $this->reviewer->id,
            'assigned_by' => $this->superAdmin->id,
            'assigned_at' => now(),
            'due_date' => now()->addDays(14),
            'status' => 'PENDING',
        ]);

        // 1. Reviewer Invitation
        $res1 = MailNotificationService::triggerReviewerInvitation($this->reviewer, 'ReviewerPass99#', 'Artificial Intelligence');
        $this->assertEquals('sent', $res1['status']);

        // 2. Dossier Assigned
        $res2 = MailNotificationService::triggerReviewerDossierAssigned($review);
        $this->assertEquals('sent', $res2['status']);

        // 3. Reviewer Reminder
        $res3 = MailNotificationService::triggerReviewerReminder($review);
        $this->assertEquals('sent', $res3['status']);

        // 4. Deadline Warning
        $res4 = MailNotificationService::triggerReviewerDeadlineWarning($review);
        $this->assertEquals('sent', $res4['status']);

        // 5. Review Report Acknowledged
        $res5 = MailNotificationService::triggerReviewReportAcknowledged($review);
        $this->assertEquals('sent', $res5['status']);

        // 6. Honorarium Disbursement
        $res6 = MailNotificationService::triggerReviewerHonorariumCompleted($review);
        $this->assertEquals('sent', $res6['status']);

        // 7. Referee Request
        $res7 = MailNotificationService::triggerRefereeRequest([
            'name' => 'Dr. Dean Reference',
            'email' => 'dean.reference@oxford.edu',
        ], $this->application);
        $this->assertEquals('sent', $res7['status']);

        // 8. Board Roster Assigned
        $boardMember = User::factory()->create(['email' => 'board.member@university.edu']);
        $boardMember->assignRole('super-admin');
        BasicInfo::create([
            'user_id' => $boardMember->id,
            'first_name' => 'Board',
            'last_name' => 'Governor',
            'cnic_number' => '17301-8888888-1',
            'date_of_birth' => '1968-04-12',
        ]);
        $res8 = MailNotificationService::triggerBoardRosterAssigned($this->campaign, $boardMember);
        $this->assertEquals('sent', $res8['status']);

        // 9. Board Meeting Scheduled
        $res9 = MailNotificationService::triggerBoardMeetingScheduled($this->campaign, [
            'recipient_email' => $boardMember->email,
            'member_name' => $boardMember->name,
        ]);
        $this->assertEquals('sent', $res9['status']);

        // 10. Scrutiny Batch Assigned
        $res10 = MailNotificationService::triggerScrutinyBatchAssigned($this->campaign, 12, $this->superAdmin);
        $this->assertEquals('sent', $res10['status']);

        // 11. Syndicate Concurrence
        $res11 = MailNotificationService::triggerSyndicateConcurrence($this->application, 'SYN-RES-901/2026');
        $this->assertEquals('sent', $res11['status']);

        // 12. Advertisement Announcement
        $res12 = MailNotificationService::triggerAdvertisementAnnouncement([
            'title' => 'Faculty Intake 2026',
            'tracking_number' => 'ADV-01-2026',
            'deadline' => now()->addDays(30),
        ], [$this->candidate->email]);
        $this->assertEquals('sent', $res12['status']);

        Mail::assertSent(DynamicNotificationMail::class, 12);
    }

    public function test_trigger_is_safely_skipped_when_disabled_by_admin(): void
    {
        Mail::fake();

        // Disable 'candidate-voucher-generated'
        $template = EmailTemplate::getBySlug('candidate-voucher-generated');
        $this->assertNotNull($template);
        $template->update(['is_trigger_enabled' => false]);

        $res = MailNotificationService::triggerVoucherGenerated($this->application);

        $this->assertEquals('skipped', $res['status']);
        Mail::assertNothingSent();

        // Verify audit log recorded the disabled skip
        $log = EmailLog::where('template_slug', 'candidate-voucher-generated')->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('TRIGGER_DISABLED_SKIPPED', $log->status);
    }
}
