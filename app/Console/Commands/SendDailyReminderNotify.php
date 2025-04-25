<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\Task;
use App\Models\User;
use App\Notifications\DailyReminderNotify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schedule;
use Log;

class SendDailyReminderNotify extends Command
{
    protected $signature = 'app:send-daily-reminder-notify';

    protected $description = 'Send daily reminder notification this is testing';

    public function handle()
    {

        logger()->info('send email....');
        $users = User::all(); 
        // $usernames = User::where('id')->first('name'); // get username
        $taskid = Task::get('id');
        
        $tasknumbers = Task::where('is_finished',1)->max('id')+1;
        $tasks = Task::where('id', $tasknumbers)->get('topic'); //get topic
        
        $title = "Today is great leaning day";
        Notification::send($users,new DailyReminderNotify($taskid,$title,$tasks[0]->topic));

        // Log::info("SendDailyReminderNotify ran at " . now());

    }
}
