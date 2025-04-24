<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DailyReminderNotify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schedule;

class SendDailyReminderNotify extends Command
{
    protected $signature = 'app:send-daily-reminder-notify';

    protected $description = 'Send daily reminder notification this is testing';

    public function handle()
    {
    
        logger()->info('send email....');
        $users = User::all();
        $taskid = 1;
        $taskcontent= "Task is testing";
        Notification::send($users,new DailyReminderNotify($taskid,$taskcontent));

    }
}
