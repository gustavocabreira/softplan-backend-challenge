<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CakeAvailableMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private readonly string $cake, private readonly string $email) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cake Available Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.cake-available',
            with: ['cake' => $this->cake, 'email' => $this->email],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
