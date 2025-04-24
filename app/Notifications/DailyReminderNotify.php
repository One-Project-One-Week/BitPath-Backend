<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyReminderNotify extends Notification
{
    use Queueable;
    // public $annid;
    // public $title;
    // public $content;

    // public function __construct($id,$title,$content)
    // {
    //     // $this->annid = $id;
    //     // $this->title = $title;
    //     // $this->content = $content;
    // }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->greeting("Today is great day")
                    // ->line($this->title)    
                    // ->line($this->content)
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
