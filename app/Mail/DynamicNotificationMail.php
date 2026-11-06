<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailSubject;
    public string $renderedHtml;
    public ?string $plainText;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $renderedHtml, ?string $plainText = null)
    {
        $this->mailSubject = $subject;
        $this->renderedHtml = $renderedHtml;
        $this->plainText = $plainText;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->renderedHtml,
        );
    }
}
