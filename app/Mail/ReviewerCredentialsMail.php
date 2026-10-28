<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewerCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $plainPassword;
    public $subjectField;
    public $academicRank;
    public $university;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $name,
        string $email,
        string $plainPassword,
        string $subjectField,
        string $academicRank,
        string $university,
        string $loginUrl
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->subjectField = $subjectField;
        $this->academicRank = $academicRank;
        $this->university = $university;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Empanelment as Academic Reviewer & Evaluation Portal Access - Office of the Registrar',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reviewer-credentials',
        );
    }
}
