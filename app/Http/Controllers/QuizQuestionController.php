<?php

namespace App\Http\Controllers;

use App\Models\PlanQuiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizQuestionController extends Controller
{

    public function index($skill_id){
        $quizs = QuizQuestion::whereHas('planQuiz', function($query) use ($skill_id){
            $query->where(['skill_id' => $skill_id, 'user_id' => auth()->user()->id]);
        })->get();

        return response()->json([
            'status' => 200,
            'quizs' => $quizs,
        ], 200);
    }

    
    public function update(Request $request){
        // check validate
        $validator = Validator::make($request->all(), [
            'answers' => "required|array"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $answers = $request->input('answers');


        $quiz_id = $request->input('quiz_id');

        foreach ($answers as $answer) {
            $quiz_question = QuizQuestion::findOrFail($answer['question_id']);

            if($quiz_question->user_answer != null){
                return response()->json([
                    'status' => 400,
                    'message' => 'You already answered this question',
                ], 400);
            }

            if($quiz_question->correct_answer == $answer['selected_option']){
                $quiz_question->is_correct = true;
            }

            $quiz_question->user_answer = $answer['selected_option'];
            $quiz_question->save();
        }

        return response()->json([
            'status' => 200,
            'planQuiz' => PlanQuiz::with('quizQuestions')->where('id', $quiz_id)->get(),
            'message' => 'User answered question successfully'
        ], 200);
        
    }
}
