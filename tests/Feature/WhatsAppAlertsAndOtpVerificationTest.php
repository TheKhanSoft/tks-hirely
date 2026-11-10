<?php

namespace Tests\Feature;

use App\Livewire\Admin\SystemSettings;
use App\Models\Application;
use App\Models\BasicInfo;
use App\Models\Campaign;
use App\Models\SecurityAuditLog;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\PasswordResetManager;
use App\Services\Verification\OtpVerificationService;
use App\Services\WhatsApp\WhatsAppGatewayService;
use Database\Seeders\SpatieRoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppAlertsAndOtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SpatieRoleAndPermissionSeeder::class);
        Cache::flush();
    }

    /**
     * Test 1: Feature toggles & aliases in SystemSetting model.
     */
    public function test_feature_toggles_and_aliases_operate_correctly()
    {
        // WhatsApp Business disabled by default
        SystemSetting::set('whatsapp_business_enabled', false);
        $this->assertFalse(SystemSetting::isFeatureEnabled('whatsapp_business_enabled'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('whatsapp_alerts'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('whatsapp_business'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('whatsapp_dispatch'));

        // WhatsApp Business enabled
        SystemSetting::set('whatsapp_business_enabled', true);
        $this->assertTrue(SystemSetting::isFeatureEnabled('whatsapp_business_enabled'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('whatsapp_alerts'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('whatsapp_business'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('whatsapp_dispatch'));

        // OTP Verification toggle & aliases
        SystemSetting::set('enable_otp_verification', true);
        $this->assertTrue(SystemSetting::isFeatureEnabled('enable_otp_verification'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('otp_verification'));
        $this->assertTrue(SystemSetting::isFeatureEnabled('otp'));

        SystemSetting::set('enable_otp_verification', false);
        $this->assertFalse(SystemSetting::isFeatureEnabled('enable_otp_verification'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('otp_verification'));
        $this->assertFalse(SystemSetting::isFeatureEnabled('otp'));
    }

    /**
     * Test 2: Phone number normalization across Pakistani and international formats.
     */
    public function test_phone_number_normalization()
    {
        // Local 03XX Pakistani formats normalized to 923XX digits
        $this->assertEquals('923001234567', WhatsAppGatewayService::normalizePhoneNumber('03001234567'));
        $this->assertEquals('923001234567', WhatsAppGatewayService::normalizePhoneNumber('0300-1234567'));
        $this->assertEquals('923001234567', WhatsAppGatewayService::normalizePhoneNumber('0300 1234567'));

        // 92 without plus
        $this->assertEquals('923001234567', WhatsAppGatewayService::normalizePhoneNumber('923001234567'));

        // Already with plus sign
        $this->assertEquals('923001234567', WhatsAppGatewayService::normalizePhoneNumber('+923001234567'));

        // International format
        $this->assertEquals('447700900077', WhatsAppGatewayService::normalizePhoneNumber('+447700900077'));

        // Invalid phone numbers
        $this->assertNull(WhatsAppGatewayService::normalizePhoneNumber('invalid'));
        $this->assertNull(WhatsAppGatewayService::normalizePhoneNumber('12345'));
        $this->assertNull(WhatsAppGatewayService::normalizePhoneNumber(''));
    }

    /**
     * Test 3: WhatsApp dispatch when gateway toggle is disabled.
     */
    public function test_whatsapp_dispatch_respects_disabled_feature_toggle()
    {
        SystemSetting::set('whatsapp_business_enabled', false);
        SystemSetting::set('whatsapp_provider', 'meta_cloud');

        $result = WhatsAppGatewayService::send('03001234567', 'Test message');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('disabled', strtolower($result['detail']));
    }

    /**
     * Test 4: WhatsApp provider dispatch via 'log' simulation provider.
     */
    public function test_whatsapp_dispatch_via_simulation_log_provider()
    {
        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'log');
        SystemSetting::set('whatsapp_business_phone', '+92-300-9999999');

        $result = WhatsAppGatewayService::send('03001234567', 'Institutional verification alert.');

        $this->assertTrue($result['success']);
        $this->assertEquals('log', $result['provider']);
        $this->assertNotNull($result['message_id']);

        // Verify cached simulation log
        $recent = WhatsAppGatewayService::getSimulationDispatches();
        $this->assertNotEmpty($recent);
        $this->assertEquals('923001234567', $recent[0]['to']);
        $this->assertStringContainsString('Institutional verification alert.', $recent[0]['message']);
    }

    /**
     * Test 5: WhatsApp provider dispatch via Meta Cloud API (Graph API).
     */
    public function test_whatsapp_dispatch_via_meta_cloud_api()
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [['input' => '923001234567', 'wa_id' => '923001234567']],
                'messages' => [['id' => 'wamid.HBgLMTIzNDU2Nzg5MA==']],
            ], 200),
        ]);

        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'meta_cloud');
        SystemSetting::set('whatsapp_phone_number_id', '1000999888777');
        SystemSetting::set('whatsapp_api_token', 'EAABdummy_meta_token_hirely');

        $result = WhatsAppGatewayService::send('03001234567', 'Meta Cloud API test message');

        $this->assertTrue($result['success']);
        $this->assertEquals('meta_cloud', $result['provider']);
        $this->assertEquals('wamid.HBgLMTIzNDU2Nzg5MA==', $result['message_id']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '1000999888777/messages')
                && $request->hasHeader('Authorization', 'Bearer EAABdummy_meta_token_hirely')
                && $request['to'] === '923001234567'
                && $request['text']['body'] === 'Meta Cloud API test message';
        });
    }

    /**
     * Test 6: WhatsApp provider dispatch via Twilio API.
     */
    public function test_whatsapp_dispatch_via_twilio_api()
    {
        Http::fake([
            'https://api.twilio.com/*' => Http::response([
                'sid' => 'SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                'status' => 'queued',
            ], 200),
        ]);

        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'twilio');
        SystemSetting::set('whatsapp_phone_number_id', 'ACdummy_twilio_account_sid');
        SystemSetting::set('whatsapp_api_token', 'dummy_twilio_auth_token');
        SystemSetting::set('whatsapp_business_phone', '+14155238886');

        $result = WhatsAppGatewayService::send('03001234567', 'Twilio WhatsApp dispatch test');

        $this->assertTrue($result['success']);
        $this->assertEquals('twilio', $result['provider']);
        $this->assertEquals('SMXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', $result['message_id']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'ACdummy_twilio_account_sid/Messages.json')
                && $request['To'] === 'whatsapp:923001234567'
                && $request['From'] === 'whatsapp:14155238886'
                && $request['Body'] === 'Twilio WhatsApp dispatch test';
        });
    }

    /**
     * Test 7: WhatsApp provider dispatch via Generic Webhook.
     */
    public function test_whatsapp_dispatch_via_generic_webhook()
    {
        Http::fake([
            'https://api.institution.edu.pk/whatsapp/webhook' => Http::response([
                'status' => 'success',
                'message_id' => 'WH-998877',
            ], 200),
        ]);

        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'generic_webhook');
        SystemSetting::set('whatsapp_api_url', 'https://api.institution.edu.pk/whatsapp/webhook');
        SystemSetting::set('whatsapp_api_token', 'secret_webhook_token_123');

        $result = WhatsAppGatewayService::send('03001234567', 'Generic Webhook WhatsApp notification');

        $this->assertTrue($result['success']);
        $this->assertEquals('generic_webhook', $result['provider']);
        $this->assertEquals('WH-998877', $result['message_id']);
    }

    /**
     * Test 8: Password Reset integration with WhatsApp dispatch & wa.me fallback link.
     */
    public function test_password_reset_with_whatsapp_delivery()
    {
        Mail::fake();

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $candidate = User::factory()->create([
            'email' => 'tariq.applicant@hirely.edu.pk',
            'password' => Hash::make('OldCandidatePassword123!'),
        ]);
        $candidate->assignRole('applicant');

        BasicInfo::create([
            'user_id' => $candidate->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Tariq',
            'last_name' => 'Mehmood',
            'cnic_number' => '17301-7654321-1',
            'date_of_birth' => '1988-06-15',
            'phone_number' => '0300-7654321',
        ]);

        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'log');

        $result = PasswordResetManager::executeReset(
            targetUser: $candidate,
            adminUser: $admin,
            plainPassword: 'WhatsAppReset#2026',
            method: 'whatsapp',
            alwaysSendEmail: true
        );

        // Assert database password updated
        $candidate->refresh();
        $this->assertTrue(Hash::check('WhatsAppReset#2026', $candidate->password));

        // Assert audit log recorded
        $this->assertDatabaseHas('security_audit_logs', [
            'user_id' => $admin->id,
            'action_type' => 'ADMIN_PASSWORD_RESET',
            'record_id' => $candidate->id,
        ]);

        // Assert WhatsApp data structure created with wa.me link
        $this->assertNotNull($result['whatsapp']);
        $this->assertEquals('923007654321', $result['whatsapp']['clean_phone']);
        $this->assertStringContainsString('wa.me/923007654321', $result['whatsapp']['url']);
        $this->assertStringContainsString('WhatsAppReset#2026', $result['whatsapp']['message']);

        // Assert automated WhatsApp background dispatch succeeded via simulation logger
        $this->assertNotNull($result['whatsapp']['automated_dispatch']);
        $this->assertTrue($result['whatsapp']['automated_dispatch']['success']);
    }

    /**
     * Test 9: Candidate milestone automated WhatsApp alerts.
     */
    public function test_candidate_milestone_automated_whatsapp_alerts()
    {
        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'log');

        $user = User::factory()->create(['email' => 'milestone.candidate@hirely.edu.pk']);
        BasicInfo::create([
            'user_id' => $user->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Ayesha',
            'last_name' => 'Siddiqua',
            'cnic_number' => '17301-1122334-2',
            'date_of_birth' => '1992-04-10',
            'phone_number' => '03011234567',
        ]);

        $campaign = Campaign::create([
            'title' => 'Associate Professor of Artificial Intelligence',
            'job_code' => 'CS-ASSOC-2026',
            'department' => 'Computer Science',
            'track_type' => 'ACADEMIC',
            'is_active' => true,
            'application_fee' => 3500.00,
        ]);

        $application = Application::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'tracking_number' => 'APP-TEST-9901',
            'status' => 'submitted',
            'interview_date' => now()->addDays(14)->toDateString(),
            'interview_time' => '10:30 AM',
            'interview_venue' => 'Senate Hall, Vice Chancellor Secretariat',
        ]);

        // 1. Application Submitted Alert
        $submitAlert = WhatsAppGatewayService::sendApplicationSubmittedAlert($application);
        $this->assertTrue($submitAlert['success']);
        $this->assertEquals('log', $submitAlert['provider']);

        // 2. Payment Verified Alert
        $paymentAlert = WhatsAppGatewayService::sendPaymentVerifiedAlert($application);
        $this->assertTrue($paymentAlert['success']);

        // 3. Scrutiny Deficiency Alert with Appeal guidance
        $scrutinyAlert = WhatsAppGatewayService::sendScrutinyAlert($application, 'Equivalence certificate from HEC pending verification.');
        $this->assertTrue($scrutinyAlert['success']);

        // 4. Interview Summons Alert
        $interviewAlert = WhatsAppGatewayService::sendInterviewSummonsAlert($application);
        $this->assertTrue($interviewAlert['success']);

        // Check recent simulation dispatches contain all 4 milestone alerts
        $dispatches = WhatsAppGatewayService::getSimulationDispatches();
        $this->assertGreaterThanOrEqual(4, count($dispatches));
    }

    /**
     * Test 10: Multi-Channel OTP Verification Engine (Generation, Cooldown, Verification, Lockout).
     */
    public function test_multi_channel_otp_verification_lifecycle()
    {
        SystemSetting::set('enable_otp_verification', true);
        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'log');
        SystemSetting::set('otp_max_attempts', 3);
        SystemSetting::set('otp_expiry_minutes', 10);

        $user = User::factory()->create(['email' => 'otp.tester@hirely.edu.pk']);
        BasicInfo::create([
            'user_id' => $user->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Naveed',
            'last_name' => 'Ahmed',
            'cnic_number' => '17301-5555555-1',
            'date_of_birth' => '1985-01-01',
            'phone_number' => '03029876543',
        ]);

        // 1. Generate OTP via WhatsApp
        $result = OtpVerificationService::generateAndSend($user, 'auth_2fa', 'whatsapp');
        $this->assertTrue($result['success']);
        $this->assertEquals('dispatched', $result['status']);
        $this->assertEquals('whatsapp', $result['channel']);
        $code = $result['test_code'];
        $this->assertNotEmpty($code);
        $this->assertEquals(6, strlen($code));

        // 2. Second immediate request triggers 60-second cooldown rate limiting
        $cooldownResult = OtpVerificationService::generateAndSend($user, 'auth_2fa', 'whatsapp');
        $this->assertFalse($cooldownResult['success']);
        $this->assertEquals('rate_limited', $cooldownResult['status']);

        // 3. Verify with wrong code -> decreases attempts remaining
        $wrongVerify = OtpVerificationService::verify($user, '000000', 'auth_2fa');
        $this->assertFalse($wrongVerify['success']);
        $this->assertEquals('mismatch', $wrongVerify['status']);
        $this->assertEquals(2, $wrongVerify['attempts_remaining']);

        // 4. Verify with correct code -> succeeds and invalidates code
        $validVerify = OtpVerificationService::verify($user, $code, 'auth_2fa');
        $this->assertTrue($validVerify['success']);
        $this->assertEquals('verified', $validVerify['status']);

        // User should now be marked as recently verified (15-min cache window)
        $this->assertTrue(OtpVerificationService::isRecentlyVerified($user, 'auth_2fa'));

        // 5. Trying to reuse consumed OTP code should fail
        $reuseVerify = OtpVerificationService::verify($user, $code, 'auth_2fa');
        $this->assertFalse($reuseVerify['success']);
        $this->assertEquals('expired_or_not_found', $reuseVerify['status']);
    }

    /**
     * Test 11: OTP Max Attempts lockout protection.
     */
    public function test_otp_lockout_protection_after_three_invalid_attempts()
    {
        SystemSetting::set('enable_otp_verification', true);
        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'log');
        SystemSetting::set('otp_max_attempts', 3);

        $user = User::factory()->create(['email' => 'lockout.tester@hirely.edu.pk']);
        BasicInfo::create([
            'user_id' => $user->id,
            'track_type' => 'ACADEMIC',
            'first_name' => 'Farhan',
            'last_name' => 'Ali',
            'cnic_number' => '17301-9988776-3',
            'date_of_birth' => '1995-03-21',
            'phone_number' => '03331122334',
        ]);

        $result = OtpVerificationService::generateAndSend($user, 'submission_lock', 'whatsapp');
        $this->assertTrue($result['success']);
        $validCode = $result['test_code'];

        // Attempt 1: wrong code
        $a1 = OtpVerificationService::verify($user, '111111', 'submission_lock');
        $this->assertEquals('mismatch', $a1['status']);
        $this->assertEquals(2, $a1['attempts_remaining']);

        // Attempt 2: wrong code
        $a2 = OtpVerificationService::verify($user, '222222', 'submission_lock');
        $this->assertEquals('mismatch', $a2['status']);
        $this->assertEquals(1, $a2['attempts_remaining']);

        // Attempt 3: wrong code -> locks and clears payload
        $a3 = OtpVerificationService::verify($user, '333333', 'submission_lock');
        $this->assertFalse($a3['success']);
        $this->assertEquals('locked', $a3['status']);
        $this->assertEquals(0, $a3['attempts_remaining']);

        // Now even the valid code will not work because payload was revoked
        $afterLockout = OtpVerificationService::verify($user, $validCode, 'submission_lock');
        $this->assertFalse($afterLockout['success']);
        $this->assertEquals('expired_or_not_found', $afterLockout['status']);
    }

    /**
     * Test 12: Admin SystemSettings Livewire test actions for WhatsApp and OTP.
     */
    public function test_admin_system_settings_livewire_test_actions()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        SystemSetting::set('whatsapp_business_enabled', true);
        SystemSetting::set('whatsapp_provider', 'log');

        // Test WhatsApp dispatch test console
        Livewire::actingAs($admin)
            ->test(SystemSettings::class, ['tab' => 'email'])
            ->set('test_whatsapp_recipient', '03001234567')
            ->set('test_whatsapp_message', 'Admin interactive test message')
            ->call('sendTestWhatsApp')
            ->assertSet('test_whatsapp_result.type', 'success')
            ->assertSee('WhatsApp message simulated successfully');

        // Test OTP dispatch and verify test console
        $component = Livewire::actingAs($admin)
            ->test(SystemSettings::class, ['tab' => 'security'])
            ->set('test_otp_recipient', '03001234567')
            ->set('test_otp_channel', 'whatsapp')
            ->call('sendTestOtp')
            ->assertSet('test_otp_result.type', 'success')
            ->assertSee('OTP Passcode successfully generated');

        $otpResult = $component->get('test_otp_result');
        $this->assertNotEmpty($otpResult['code']);
        $generatedCode = $otpResult['code'];

        // Verify generated test OTP
        $component->set('test_otp_code_input', $generatedCode)
            ->call('verifyTestOtp')
            ->assertSet('test_otp_verify_result.type', 'success')
            ->assertSee('Passcode Verified! Identity authenticated successfully.');
    }

    /**
     * Test 13: Unauthorized user blocked from WhatsApp & OTP administrative actions.
     */
    public function test_unauthorized_user_blocked_from_whatsapp_and_otp_actions()
    {
        $applicant = User::factory()->create();
        $applicant->assignRole('applicant');

        Livewire::actingAs($applicant)
            ->test(SystemSettings::class)
            ->assertForbidden();
    }
}
