<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AcceptanceNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Applicant $applicant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Your Application Has Been Accepted — Phil. Academy of Sakya',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.acceptance-notice');
    }
}
