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
    public $tasks;

    public function __construct($id,$title,$tasks)
    {
        $this->taskid = $id;
        $this->title = $title;
        $this->tasks = $tasks;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->greeting($this->title)
                    ->line("Today task  = {$this->tasks}")
                    ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
