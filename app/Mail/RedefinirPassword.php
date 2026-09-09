<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RedefinirPassword extends Mailable
{
    use Queueable;

    public function __construct(public User $user, public string $url)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reposição da palavra-passe');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.redefinir-password');
    }
}