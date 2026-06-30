<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserChangeCredential extends Mailable
{
    use Queueable, SerializesModels;

    private $title = "";
    private $body = "";

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function envelope()
    {
        return new Envelope(
            subject: $this->title
        );
    }

    public function content()
    {
        return new Content(
            view: 'Emails.UserCredential',
            with: [
                'title' => $this->title,
                'body' => $this->body
            ]
        );
    }
}
