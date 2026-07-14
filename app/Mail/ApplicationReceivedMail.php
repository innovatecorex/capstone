<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent immediately after an application is submitted, so the applicant has
 * written proof of submission and — crucially — their reference number, which
 * is the only handle they have for tracking the application.
 */
class ApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Applicant $applicant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Received — Philippine Academy of Sakya (Ref: '
                     . $this->applicant->reference_number . ')',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-received');
    }
}
