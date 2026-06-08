<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * WelcomeCredentialsMail
 *
 * Sends a new user their login credentials (username + temporary password)
 * to their personal email. Uses SendGrid SMTP (configured in .env).
 *
 * To send:
 *   Mail::to($email)->send(new WelcomeCredentialsMail($firstName, $username, $tempPassword));
 */
class WelcomeCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $firstName;
    public string $username;
    public string $tempPassword;

    public function __construct(string $firstName, string $username, string $tempPassword)
    {
        $this->firstName    = $firstName;
        $this->username     = $username;
        $this->tempPassword = $tempPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'EncryptEd — Your Account Login Credentials',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-credentials',
        );
    }
}
