<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use App\Models\User;

class ApplicationAssignedReviewerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reviewerName;
    public $candidateName;
    public $positionTitle;
    public $department;
    public $dueDate;
    public $confidentialNotes;
    public $evalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $reviewerName,
        string $candidateName,
        string $positionTitle,
        string $department,
        ?string $dueDate,
        ?string $confidentialNotes,
        string $evalUrl
    ) {
        $this->reviewerName = $reviewerName;
        $this->candidateName = $candidateName;
        $this->positionTitle = $positionTitle;
        $this->department = $department;
        $this->dueDate = $dueDate;
        $this->confidentialNotes = $confidentialNotes;
        $this->evalUrl = $evalUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confidential Candidate Dossier for Academic Review: {$this->positionTitle} ({$this->department})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application-assigned-reviewer',
        );
    }
}
