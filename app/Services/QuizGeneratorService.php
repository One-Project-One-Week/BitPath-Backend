<?php

namespace App\Services;

use App\Models\PlanQuiz;
use App\Models\QuizQuestion;
use App\Models\RoadmapSkill;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ModelType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class QuizGeneratorService
{
    public static function generate(RoadmapSkill $skill)
    {
        $prompt = "Create 5 multiple-choice quiz questions based for this skill:" .
            $skill->toJson() .
            "1. Respond ONLY in valid JSON format              
            2. Each questions object should have exactly these properties:
                - 'question' : question,
                - 'options' : [a:'Hypertext Marketup Language',
                                b:'Hypertext Marketdown Language',
                                c:'Hypertext Machine Language',
                                d:'Hypertext Technology Modern Language'],
                - 'correct_answer' : a:'Hypertext Marketup Language'

            

            Include all questions following a logical progression. Your response should be a raw JSON array with NO markdown formatting, code blocks, or explanatory text.";

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
        $planQuizz = PlanQuiz::create([
            'user_id' => Auth::user()->id,
            'skill_id' => $skill->id,
        ]);

        foreach($response_json as $value) {
            // return $value;
            QuizQuestion::create([
                'quiz_id' => $planQuizz->id,
                'question' => $value['question'],
                'options' =>  json_encode($value['options']),
                'correct_answer' => $value['correct_answer'],
            ]);
        }



        return response()->json([
            'status' => 200,
            'message' => 'Quizzes generated successfully',
            'quizzes' => $response_json,
        ]);
    }
}
