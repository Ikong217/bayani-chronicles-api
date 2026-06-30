<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OldEmailConfirm extends Mailable
{
    public $codes;
    public $encryptedId;

    public function __construct($codes, $encryptedId)
    {
        $this->codes = $codes;
        $this->encryptedId = $encryptedId;
    }

    public function build()
    {
        return $this->subject('Confirm Your Email Change')
                    ->view('Emails.old_email_confirm')
                    ->with([
                        'codes' => $this->codes,
                        'id' => $this->encryptedId,
                    ]);
    }
}
