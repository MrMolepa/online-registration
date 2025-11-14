<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $recipient;
    public $invitation;



    public function __construct($recipient, $invitation, $url)
    {
        $this->url = $url;
        $this->invitation = $invitation;
        $this->recipient = $recipient;
    }

    public function build()
    {
        return $this->markdown('emails.invitationMail');
    }
}
