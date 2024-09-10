<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvigilatorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $surname;
    public $other_names;
    public $center_no;


    public function __construct($data)
    {
        $this->url = $data['url'];
        $this->surname = $data['surname'];
        $this->other_names = $data['other_names'];
        $this->center_no = $data['center_no'];
    }

    public function build()
    {
        return $this->markdown('emails.invigilatoremail');
    }
}
