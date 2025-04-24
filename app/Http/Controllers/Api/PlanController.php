<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanRequest;
use App\Models\Roadmap;
use App\Models\Task;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ModelType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    public function generatePlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roadmap_id' => 'required|integer|exists:roadmaps,id',
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $roadmap = Roadmap::findOrFail($request->roadmap_id);
        if ($request->type == 'deadline') {
            $time = "for $request->days per day";
        } else {
            $time = "within EXACTLY $request->duration days";
        }

        $prompt = "Create a micro task planner for this roadmap in tripple backtip." .
            $roadmap->response .
            "I want to study this EXACT roadmap" . $time .
            "1. Respond ONLY in valid JSON format
                2. The response should be an array of objects
                3. Each object should have exactly these properties:
                    - topic : 'Topic of the task',
                    - task : 'Task to be done per day'.
                    - dayNumber : 'Day number of the task',
            Include all skills following a logical progression.
            Your response should be a raw JSON array with NO markdown formatting, code blocks, or explanatory text.";

        $config = new GenerationConfig(
            temperature: 0.1
        );

        $response = Gemini::generativeModel(ModelType::GEMINI_FLASH)
            ->withGenerationConfig($config)
            ->generateContent($prompt);

        $response_text = $response->text();

        $clean_json = trim($response_text);
        $clean_json = preg_replace('/^```json\s*|\s*```$/', '', $clean_json);

        $response_json = json_decode($clean_json, true);
        $total_tasks = count($response_json);
        

        /** @var User $user */
        // $user = Auth::user();
        // if (!$user) {
        //     return response()->json([
        //         'status' => 401,
        //         'message' => 'Unauthorized',
        //     ], 401);
        // }

        PlanRequest::create([
            // 'user_id' => $user->id,
            'roadmap_id' => $roadmap->id,
            'type' => $request->type,
            'duration' => $request->duration,
            'days' => $request->days,
        ]);

        $plan = Plan::create([
            'roadmap_id' => $roadmap->id,
            'is_finished' => false,
            'total_tasks' => $total_tasks,
            'completed_tasks' => 0,
        ]);

        foreach($response_json as $task){
            $plan->tasks()->create([
                'is_finished' => false,
                'day_number' => $task['dayNumber'],
                'task' => $task['task'],
                'topic' => $task['topic'],
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Plan generated successfully',
            'total_tasks' => $total_tasks,
            'plan' => $response_json,
        ]);
    }
}
