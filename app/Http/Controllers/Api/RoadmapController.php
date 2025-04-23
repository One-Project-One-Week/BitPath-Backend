<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Roadmap;
use App\Models\User;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ModelType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoadmapController extends Controller
{
    public function generateRoadmap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->input('prompt');
        $prompt = "Create a learning roadmap for texts in the following format: {text} with the following requirements:" .
            $input .
            "1. Respond ONLY in valid JSON format
            2. The response should be an array of objects
            3. Each object should have exactly these properties:
                - 'skill': the name of the technology/skill
                - 'duration': estimated learning time
                - 'recommendedResource': specific course or resource name
                - 'why' : why should we study this
                - 'level' : level of this skill
            The JSON structure should look exactly like this:
                    [
                    {'skill': 'HTML', 'why': 'why should we study this','duration': '2 weeks','level':'level of this skill', 'recommendedResource': 'HTML Course Name'},
                    {'skill': 'CSS', 'why': 'why should we study this','duration': '1 month', ''level': 'level of this skill', 'recommendedResource': 'CSS Resource Name'}
                    ]
            Include all skills following a logical progression. Your response should be a raw JSON array with NO markdown formatting, code blocks, or explanatory text.";

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

        return response()->json([
            'status' => 200,
            'message' => 'Roadmap generated successfully',
            'prompt' => $input,
            'response' => $response_json,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prompt' => 'required|string',
            'response' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed while storing the roadmap',
                'errors' => $validator->errors()
            ], 422);
        }

        /** @var User $user */
        // $user = Auth::user();
        // if (!$user) {
        //     return response()->json([
        //         'status' => 401,
        //         'message' => 'Unauthorized',
        //     ], 401);
        // }

        Roadmap::create([
            'prompt' => $request->input('prompt'),
            'response' => json_encode($request->input('response')),
            // 'user_id' => $user->id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Roadmap stored successfully'
        ], 200);
    }
}
