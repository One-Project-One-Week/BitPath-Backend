<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    private function errorResponse($message, $errors = null, $statusCode = 422)
    {
        return response()->json([
            'status' => $statusCode,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }


    public function index()
    {
        $task = Task::all();
        return response()->json([
            'data'=> $task,
            'status' => 200,
        ], 200);
    }

    // public function store(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'plan_id'=>"required|exists:plans,id",
    //         'day_number'=>"required|exists:plans,day_number",
    //         'task'=>"required|exists:plans,task",
    //         'topic'=>"required|exists:plans,topic"
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 422,
    //             'message' => 'Validation failed while storing the task',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $task = new Task();
    //     $task->plan_id = $request->plan_id;
    //     $task->is_finished = true;
    //     $task->day_number = $request->day_number;
    //     $task->task = $request->task;
    //     $task->topic = $request->topic;
    //     $task->save();


    //     return response()->json([
    //         'status' => 200,
    //         'data' => $task,
    //         'message' => 'Task stored successfully'
    //     ], 200);
    // }
}
