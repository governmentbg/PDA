<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackSubmitted extends Mailable
{
    use Queueable,SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $category,
        public string $description,
        public string $contactEmail,
        public string $name
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback-submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
