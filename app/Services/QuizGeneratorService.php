<?php

namespace App\Services;

use Gemini\Data\GenerationConfig;
use Gemini\Enums\ModelType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Http;

class QuizGeneratorService
{
    public static function generate()
    {
        $prompt = "";

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
            'message' => 'Quizzes generated successfully',
            'quizzes' => $response_json,
        ]);
    }
}
