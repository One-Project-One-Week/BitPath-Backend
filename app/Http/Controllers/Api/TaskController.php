<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Auth;
use Psy\TabCompletion\Matcher\FunctionsMatcher;

class TaskController extends Controller
{
    public function updateTask(Task $task){
        $user = Auth::user();
        // getting a plan of a task
        $plan = $task->plan;


        // updating a task as completed
        if($task->is_finished == false){
            $task->update([
                'is_finished' => true,
            ]);
        }else{
            return response()->json([
                'statusCode' => 400,
                'message' => 'Task already completed',
            ], 400);
        }

        
        if($user->last_studied_date == null){
            $user->update([
                'last_studied_date' => now(),
            ]);
        }


        // adding a plan completed task by one
        $plan->update([
            'completed_tasks' => $plan->completed_tasks + 1,
        ]);

        if($plan->completed_tasks == $plan->total_tasks){
            $plan->update([
                'is_finished' => true,
            ]);
        }

        $last_studied_date = $user->last_studied_date;
        $current_date = now();
        $day_diff = $last_studied_date->diffInDays($current_date);

        if($day_diff > 1 && $day_diff < 2){
            $user->update([
                'current_streak' => $user->current_streak + 1,
                'last_studied_date' => now(),
            ]);
        }else if($day_diff > 2){
            $user->update([
                'current_streak' => 1,
                'last_studied_date' => now(),
            ]);
        }
        else{
            $user->update([
                'last_studied_date' => now(),
            ]);
        }

        $longest_streak = $user->longest_streak;
        $current_streak = $user->current_streak;

        if($current_streak > $longest_streak){
            $longest_streak = $current_streak;
            $user->update([
                'longest_streak' => $longest_streak,
            ]);
        }   

        return response()->json([
            'status' => 200,
            'message' => 'Task updated successfully',
            'task' => $task,
        ]);
    }
}
