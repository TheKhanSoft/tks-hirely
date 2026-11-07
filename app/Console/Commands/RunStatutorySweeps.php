<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Models\ApplicationReview;
use App\Models\BillingLedger;
use App\Models\SystemSetting;
use App\Services\Sms\SmsGatewayService;
use App\Services\MailNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RunStatutorySweeps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hirely:run-statutory-sweeps {--dry-run : Run sweeps without dispatching notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute automated background sweeps for 48h challan expiry, 72h reviewer nudges, and 24h deficiency appeal deadlines.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->info("🚀 Initiating Hirely Automated Statutory Sweeps" . ($dryRun ? ' [DRY-RUN MODE]' : '') . "...");

        $challanCount = $this->sweepChallanExpiries($dryRun);
        $reviewerCount = $this->sweepReviewerReminders($dryRun);
        $appealCount = $this->sweepDeficiencyAppealDeadlines($dryRun);

        $this->newLine();
        $this->info("═══════════════════════════════════════════════════════════");
        $this->info(" STATUTORY SWEEPS SUMMARY:");
        $this->line(" • 48-Hour Challan Expiry Sweeps:     {$challanCount} applications alerted");
        $this->line(" • 72-Hour Peer Reviewer Sweeps:     {$reviewerCount} reviewers nudged");
        $this->line(" • 24-Hour Deficiency Appeal Sweeps: {$appealCount} candidates notified");
        $this->info("═══════════════════════════════════════════════════════════");

        return Command::SUCCESS;
    }

    /**
     * Sweep 1: 48-Hour Fee Voucher Expiry Alert.
     */
    protected function sweepChallanExpiries(bool $dryRun): int
    {
        $this->comment("1. Running 48-Hour Challan Expiry Sweep...");

        $graceDays = (int) SystemSetting::get('challan_grace_days', 14);
        $now = Carbon::now();
        $threshold48h = Carbon::now()->addHours(48);

        // Find submitted applications with unpaid billings nearing grace period end
        $applications = Application::with(['user.basicInfo', 'campaign', 'billingLedger'])
            ->where('status', 'SUBMITTED')
            ->whereHas('billingLedger', function ($q) {
                $q->where('status', '!=', 'PAID');
            })
            ->get();

        $alerted = 0;

        foreach ($applications as $app) {
            $ledger = $app->billingLedger;
            if (!$ledger) continue;

            $submissionDate = $app->submission_timestamp ?: $app->created_at;
            $expiryDate = $ledger->expires_at ?: Carbon::parse($submissionDate)->addDays($graceDays);

            // Check if expiring within 48 hours and not expired past 24 hours
            if ($expiryDate->gte($now) && $expiryDate->lte($threshold48h)) {
                $recipientPhone = $app->user->basicInfo?->phone_number;
                $recipientEmail = $app->user->email;
                $consumerId = $ledger->consumer_id;
                $campaignTitle = $app->campaign?->title ?? 'Position';

                $this->line("   -> Flagged App #{$app->id} ({$consumerId}) - Expiry: {$expiryDate->format('Y-m-d H:i')}");

                if (!$dryRun) {
                    $smsMsg = "Hirely Portal: Your processing fee voucher (Consumer ID: {$consumerId}) for '{$campaignTitle}' expires within 48 hours. Please settle payment to prevent automatic dossier forfeiture.";
                    
                    if ($recipientPhone) {
                        SmsGatewayService::send($recipientPhone, $smsMsg);
                    }

                    if ($recipientEmail) {
                        MailNotificationService::sendTemplate(
                            'candidate-challan-expired-notice',
                            $recipientEmail,
                            [
                                'candidate_name' => $app->user->basicInfo?->first_name ?? 'Candidate',
                                'tracking_number' => $app->tracking_number,
                                'job_title' => $campaignTitle,
                                'consumer_id' => $consumerId,
                                'expiry_date' => $expiryDate->format('d M Y, h:i A'),
                                'fee_amount' => number_format($ledger->amount, 2),
                            ],
                            recipientName: $app->user->basicInfo?->first_name,
                            dispatchType: 'STATUTORY_SWEEP'
                        );
                    }
                }

                $alerted++;
            }
        }

        $this->info("   Completed. Alerted {$alerted} candidate(s).");
        return $alerted;
    }

    /**
     * Sweep 2: 72-Hour Peer Reviewer Reminder Sweep.
     */
    protected function sweepReviewerReminders(bool $dryRun): int
    {
        $this->comment("2. Running 72-Hour Peer Reviewer Nudge Sweep...");

        $now = Carbon::now();
        $threshold72h = Carbon::now()->addHours(72);

        // Find pending academic reviews assigned > 3 days ago
        $reviews = ApplicationReview::with(['reviewer', 'application.campaign', 'application.user.basicInfo'])
            ->where('status', 'PENDING')
            ->where('created_at', '<=', Carbon::now()->subDays(3))
            ->get();

        $nudged = 0;

        foreach ($reviews as $rev) {
            $reviewer = $rev->reviewer;
            if (!$reviewer) continue;

            $deadline = $rev->deadline ? Carbon::parse($rev->deadline) : Carbon::parse($rev->created_at)->addDays(14);
            $candidateName = $rev->application?->user?->basicInfo ? trim($rev->application->user->basicInfo->first_name . ' ' . $rev->application->user->basicInfo->last_name) : 'Academic Candidate';
            $campaignTitle = $rev->application?->campaign?->title ?? 'Faculty Position';

            $this->line("   -> Flagged Review #{$rev->id} (Reviewer: {$reviewer->email}) - Deadline: {$deadline->format('Y-m-d')}");

            if (!$dryRun) {
                $smsMsg = "Hirely Academic Secretariat: Statutory reminder that confidential peer evaluation for '{$campaignTitle}' ({$candidateName}) is due within 72 hours. Please log in to complete.";

                if (!empty($reviewer->phone)) {
                    SmsGatewayService::send($reviewer->phone, $smsMsg);
                }

                MailNotificationService::sendTemplate(
                    'reviewer-deadline-warning',
                    $reviewer->email,
                    [
                        'reviewer_name' => $reviewer->name ?? 'External Expert',
                        'candidate_name' => $candidateName,
                        'position_title' => $campaignTitle,
                        'review_deadline' => $deadline->format('d M Y'),
                        'portal_url' => url('/reviewer/evaluation-desk'),
                    ],
                    recipientName: $reviewer->name,
                    dispatchType: 'STATUTORY_SWEEP'
                );
            }

            $nudged++;
        }

        $this->info("   Completed. Nudged {$nudged} external reviewer(s).");
        return $nudged;
    }

    /**
     * Sweep 3: 24-Hour Deficiency / Grievance Appeal Deadline Sweep.
     */
    protected function sweepDeficiencyAppealDeadlines(bool $dryRun): int
    {
        $this->comment("3. Running 24-Hour Deficiency Appeal Deadline Sweep...");

        $now = Carbon::now();
        $threshold24h = Carbon::now()->addHours(24);

        // Standard 7-day appellate window from updated_at date
        $disqualifiedApps = Application::with(['user.basicInfo', 'campaign'])
            ->where('status', 'INELIGIBLE')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->get();

        $notified = 0;

        foreach ($disqualifiedApps as $app) {
            $appealDeadline = Carbon::parse($app->updated_at)->addDays(7);

            if ($appealDeadline->gte($now) && $appealDeadline->lte($threshold24h)) {
                $phone = $app->user->basicInfo?->phone_number;
                $email = $app->user->email;
                $campaignTitle = $app->campaign?->title ?? 'Position';
                $reason = $app->disqualification_reason ?: 'Criteria deficit';

                $this->line("   -> Flagged App #{$app->id} ({$app->tracking_number}) - Appeal Window Closes: {$appealDeadline->format('Y-m-d H:i')}");

                if (!$dryRun) {
                    $smsMsg = "Hirely Notice: Statutory grievance appeal window for {$app->tracking_number} ({$campaignTitle}) closes in less than 24 hours. Lodge your appeal online immediately.";

                    if ($phone) {
                        SmsGatewayService::send($phone, $smsMsg);
                    }

                    if ($email) {
                        MailNotificationService::sendTemplate(
                            'candidate-scrutiny-ineligible-appeal',
                            $email,
                            [
                                'candidate_name' => $app->user->basicInfo?->first_name ?? 'Candidate',
                                'tracking_number' => $app->tracking_number,
                                'job_title' => $campaignTitle,
                                'disqualification_reason' => $reason,
                                'appeal_deadline' => $appealDeadline->format('d M Y, h:i A'),
                                'portal_url' => url('/applicant/dashboard'),
                            ],
                            recipientName: $app->user->basicInfo?->first_name,
                            dispatchType: 'STATUTORY_SWEEP'
                        );
                    }
                }

                $notified++;
            }
        }

        $this->info("   Completed. Notified {$notified} candidate(s).");
        return $notified;
    }
}
