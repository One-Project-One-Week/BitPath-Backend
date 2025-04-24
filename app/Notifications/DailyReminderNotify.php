<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyReminderNotify extends Notification
{
    use Queueable;
    public $taskid;
    public $title;

    public function __construct($id,$title)
    {
        $this->annid = $id;
        $this->title = $title;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->greeting("Today is great day")
                    ->line($this->title)
                    ->line('Thank you for using our application!')
                    ->action('Visit Site', url('/'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
