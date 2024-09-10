<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CentreInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $center;
    public $amount_paid;
    public $balance;
    public $schoolfees;
    public $total_paid;
    public function __construct($center, $schoolfees,$total_paid, $amount_paid, $balance)
    {
        $this->center = $center;
        $this->amount_paid = $amount_paid;
        $this->balance = $balance;
        $this->schoolfees = $schoolfees;
        $this->total_paid = $total_paid;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.centreInvoiceMail');
    }
}
 