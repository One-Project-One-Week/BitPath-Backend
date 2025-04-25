<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoadmapResource;
use App\Http\Resources\RoadmapSkillResource;
use App\Models\RecommandResource;
use App\Models\Resourcelink;
use App\Models\Roadmap;
use App\Models\RoadmapSkill;
use App\Models\User;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ModelType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Claims\JwtId;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoadmapController extends Controller
{
    use AuthorizesRequests;
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
            2. The response should be an objects containing:
                - 'title' : title related to roadmap,
                - 'skills' : array of objects containing the skills
            3. Each skills object should have exactly these properties:
                - 'skill': the name of the technology/skill
                - 'duration': estimated learning time
                - 'recommendedResource':[ {'name': latest link},{'name': latest link}]
                - 'why' : why should we study this
                - 'level' : level of this skill
            The JSON structure should look exactly like this:
                { title: 'Title RoadMap',
                    skills: [
                        {'skill': 'HTML', 'why': 'why should we study this','duration': '2 weeks','level':'level of this skill', 'recommendedResource':[{'name': 'HTML Basic Resource Name','link': 'HTML Basic Resource Link'},{'name': 'HTMLCourse Name', 'link': 'HTMLCourse Link'}]},
                        {'skill': 'CSS', 'why': 'why should we study this','duration': '1 month', 'level': 'level of this skill', 'recommendedResource': [{'name': 'CSS Basic Resource Name','link': 'CSS Basic Resource Link'},{'name': 'CSSCourse Name', 'link': 'CSSCourse Link'}]}
                    ]
                }
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

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $roadmap = Roadmap::create([
            'prompt' => $request->input('prompt'),
            'title' => $request->response['title'],
            'user_id' => $user->id,
        ]);

        foreach ($request->response['skills'] as $skill) {
            $roadmap_skill = RoadmapSkill::create([
                'roadmap_id' => $roadmap->id,
                'skill' => $skill['skill'],
                'why' => $skill['why'],
                'duration' => $skill['duration'],
                'level' => $skill['level'],
            ]);

            $resource = RecommandResource::create([
                'skill_id' => $roadmap_skill->id,
            ]);

            foreach ($skill['recommendedResource'] as $item) {
                Resourcelink::create([
                    'recommand_resource_id' => $resource->id,
                    'name' => $item['name'],
                    'link' => $item['link'],
                ]);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Roadmap stored successfully'
        ], 200);
    }

    public function updateVisibility(Request $request, Roadmap $roadmap)
    {
        $validator = Validator::make($request->all(), [
            'visibility' => 'required|string|in:public,private',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->authorize('update', $roadmap);

        $roadmap->update([
            'visibility' => $request->visibility,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Roadmap visibility updated successfully'
        ]);
    }

    public function show(Roadmap $roadmap)
    {
        $this->authorize('view', $roadmap);
        $roadmap->load('roadmapSkills');
        return response()->json([
            'status' => 200,
            'roadmap' => new RoadmapResource($roadmap),
        ]);
    }

    public function index()
    {
        $this->authorize('viewAny', Roadmap::class);

        $roadmaps = Roadmap::select('id', 'title')
            ->where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')->get();
        return response()->json([
            'status' => 200,
            'roadmap' => $roadmaps,
        ]);
    }

    public function destroy(Roadmap $roadmap)
    {
        $this->authorize('delete', $roadmap);
        $roadmap->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Roadmap deleted successfully'
        ]);
    }

    public function getRoadmapSkills(Roadmap $roadmap)
    {
        $this->authorize('view', $roadmap);

        $skills = RoadmapSkill::where('roadmap_id', $roadmap->id)->get();
        return response()->json([
            'status' => 200,
            'skills' => RoadmapSkillResource::collection($skills),
        ]);
    }

    public function getRoadmapSkill(Roadmap $roadmap, RoadmapSkill $skill)
    {
        $this->authorize('view', $roadmap);

        if ($skill->roadmap_id !== $roadmap->id) {
            return response()->json([
                'status' => 403,
                'message' => 'This skill does not belong to the specified roadmap.',
            ], 403);
        }

        return response()->json([
            'status' => 200,
            'skill' => new RoadmapSkillResource($skill),
        ]);
    }
}
