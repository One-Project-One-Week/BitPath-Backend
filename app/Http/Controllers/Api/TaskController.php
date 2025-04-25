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

        // adding a plan completed task by one
        $plan->update([
            'completed_tasks' => $plan->completed_tasks + 1,
        ]);


        
        if($plan->completed_tasks == $plan->total_tasks){
            $plan->update([
                'is_finished' => true,
            ]);
        }

        // updating user table's streak by one
        $user = Auth::user();

        $last_studied_date = $user->last_studied_date;
        $current_date = now()->format('Y-m-d');
    
        $day_diff = $last_studied_date->diffInDays($current_date);
        // $user->update([
        //     'current_streak' => $user->current_streak + 1,
        //     'last_studied_date' => now()->format('Y-m-d'),
        // ]);

        return response()->json([
            'status' => 200,
            'message' => 'Task updated successfully',
            'task' => $day_diff,
        ]);
    }
}
