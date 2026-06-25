<?php

namespace App\Mail;

use App\Models\Applicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaitlistNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Applicant $applicant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Update: You Have Been Waitlisted — Phil. Academy of Sakya',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.waitlist-notice');
    }
}
