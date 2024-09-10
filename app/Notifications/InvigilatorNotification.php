<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvigilatorNotification extends Notification
{
    use Queueable;

    protected $url;
    protected $other_names;
    protected $surname;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Notification Message.')
            ->action('View Details', $this->data)
            ->line('Thank you for using our application!');
    }
}
