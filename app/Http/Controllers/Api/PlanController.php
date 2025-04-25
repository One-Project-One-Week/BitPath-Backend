<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Task;
use App\Models\User;
use App\Models\Roadmap;
use App\Models\PlanRequest;
use Gemini\Enums\ModelType;
use App\Models\RoadmapSkill;
use Illuminate\Http\Request;
use Gemini\Data\GenerationConfig;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

use App\Http\Resources\PlanResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{

    public function index()
    {
        $plans = Plan::whereHas('planRequest', function($query){
            $query->where('user_id', Auth::id());
        })->get();

        $plan_resources = PlanResource::collection($plans);
        return response()->json([
            'status' => 200,
            'message' => 'Plans retrieved successfully',
            'plans' => $plan_resources,
        ]);
    }

    public function show($id)
    {
        $plan = Plan::where('id', $id)->with(['tasks', 'planRequest'])->get();
        return response()->json([
                'status' => 200,
                'message' => 'Plans retrieved successfully',
                'plan' => $plan,
        ], 200 );
    }

    public function regeneratePlan(Request $request, Plan $plan)
    {

        $tasks = $plan->tasks;
        $plan_request = PlanRequest::where('skill_id', $plan->skill_id)->first();

        $tasks->each(function ($task) {
            $task->delete();
        });

        $validator = Validator::make($request->all(), [
            'skill_id' => 'required|integer|exists:roadmap_skills,id',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $skill = RoadmapSkill::findOrFail($request->skill_id);
        if ($request->type == 'deadline') {
            $time = "for $request->days per day";
        } else {
            $time = "within EXACTLY $request->duration days";
        }

        $prompt = "Create a micro task planner for this roadmap in tripple backtip." .
            $skill->toJson() .
            "I want to study this EXACT roadmap" . $time .
            "1. Respond ONLY in valid JSON format
                2. The response should be an array of objects
                3. Each object should have exactly these properties:
                    - topic : 'Topic of the task',
                    - task : 'Task to be done per day'.
                    - dayNumber : 'Day number of the task start from 1 and increase one per row',
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

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }


        $plan_request->update([
            'type' => $request->type,
            'duration' => $request->duration,
            'days' => $request->days,
        ]);

        $plan->update([
            'is_finished' => false,
            'total_tasks' => $total_tasks,
            'completed_tasks' => 0,
        ]);

        foreach ($response_json as $task) {
            Task::create([
                'plan_id' => $plan->id,
                'is_finished' => false,
                'day_number' => $task['dayNumber'],
                'task' => $task['task'],
                'topic' => $task['topic'],
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Plan regenerated successfully',
            'total_tasks' => $total_tasks,
            'plan' => $response_json,
        ], 200);
    }


    public function generatePlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill_id' => 'required|integer|exists:roadmap_skills,id',
            'type' => 'required|string'
        ]);

        if ($request->type == 'deadline') {
            $validator = Validator::make($request->all(), [
                'days' => 'required|integer',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'duration' => 'required|string'
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $time = ($request->type == 'tpd') ? "for $request->duration per day." : "within EXACTLY $request->days days with 1 task per day.";
        $skill = RoadmapSkill::without('recommandResource')->select('id', 'skill', 'why', 'level', 'roadmap_id')->findOrFail($request->skill_id);
        
        $prompt = "Create a micro task planner for this skill in tripple square brackets. ONLY FOR SKILL.DO NOT INCLUDED RELATED SKILLS. " .
            "[[[" . $skill->toJson() . "]]]" .
            " I want to study this EXACT skill " . $time .
            " 1. Respond ONLY in valid JSON format
                2. The response should be an array of objects
                3. Each object should have exactly these properties:
                    - topic : 'Topic of the task',
                    - task : 'Task to be done per day'.
                    - dayNumber : 'Day number of the task start from 1 and increase one per row',
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

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $plan_request = PlanRequest::create([
            'user_id' => $user->id,
            'skill_id' => $skill->id,
            'type' => $request->type,
            'duration' => $request->duration,
            'days' => $request->days,
        ]);

        $plan = Plan::create([
            'request_id' => $plan_request->id,
            'is_finished' => false,
            'total_tasks' => $total_tasks,
            'completed_tasks' => 0,
        ]);

        foreach ($response_json as $task) {
            Task::create([
                'plan_id' => $plan->id,
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
