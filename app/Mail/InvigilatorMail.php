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
    public $declined;
    public $surname;
    public $other_names;
    public $center_no;
    public $center_name;
    public $mailheader;


    public function __construct($data)
    {
        $this->url = $data['url'];
        $this->declined = $data['declined'];
        $this->center_no = $data['center_no'];
        $this->surname = $data['surname'];
        $this->other_names = $data['other_names'];
        $this->center_name = $data['center_name'];
        $this->mailheader = $data['mailheader'];
    }

    public function build()
    {
        return $this->markdown('emails.invigilatoremail');
    }
}
