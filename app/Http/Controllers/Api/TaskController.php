<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Task;
use App\Services\QuizGeneratorService;
use Illuminate\Http\Request;
use Psy\TabCompletion\Matcher\FunctionsMatcher;

class TaskController extends Controller
{
    public function updateTask(Task $task){

        // getting a plan of a task
        $plan = $task->plan;

        // updating a task as completed
        $task->update([
            'is_finished' => true,
        ]);

        // adding a plan completed task by one
        $plan->update([
            'completed_tasks' => $plan->completed_tasks + 1,
        ]);

        
        if($plan->completed_tasks == $plan->total_tasks){
            $plan->update([
                'is_finished' => true,
            ]);

            // generating quiz
            QuizGeneratorService::generate();
        }

        return response()->json([
            'status' => 200,
            'message' => 'Task updated successfully',
            'task' => $task,
        ]);
    }
}
