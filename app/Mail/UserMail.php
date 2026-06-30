<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    private $username = "";
    private $pass = "";
    private $type = "new";

    public function __construct($username, $pass = null, $type = "new")
    {
        $this->username = $username;
        $this->pass = $pass;
        $this->type = $type;
    }

    public function envelope()
    {
        return new Envelope(subject: 'User Mail');
    }

    public function content()
    {
        return new Content(
            view: 'Emails.UsersMails',
            with: [
                'username' => $this->username,
                'pass' => $this->pass,
                'type' => $this->type
            ]
        );
    }
}
