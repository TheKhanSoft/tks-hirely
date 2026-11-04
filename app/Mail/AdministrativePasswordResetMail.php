<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class AdministrativePasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientName;
    public $recipientEmail;
    public $plainPassword;
    public $method;
    public $adminName;
    public $includePasswordInEmail;
    public $loginUrl;
    public $resetTimestamp;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $user,
        string $plainPassword,
        string $method = 'email',
        ?string $adminName = null,
        bool $includePasswordInEmail = true
    ) {
        $this->recipientName = $user->display_name;
        $this->recipientEmail = $user->email;
        $this->plainPassword = $plainPassword;
        $this->method = $method;
        $this->adminName = $adminName ?: 'Office of the Registrar';
        $this->includePasswordInEmail = $includePasswordInEmail;
        $this->loginUrl = url('/login');
        $this->resetTimestamp = now()->format('d M Y, h:i A T');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ($this->includePasswordInEmail || $this->method === 'email')
            ? 'Your New Hirely Portal Credentials - Office of the Registrar'
            : 'Security Notice: Administrative Password Reset - Hirely';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-password-reset',
        );
    }
}
