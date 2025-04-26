<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizQuestionController extends Controller
{
    public function update(Request $request, QuizQuestion $quizQuestion){

        // check if user has already answered this question
        // if($quizQuestion->user_answer){
        //     return response()->json([
        //         "message"=> "You have already answered this question",
        //         "status"=> 403,
        //     ],403);
        // }
        
        // check validate
        $validator = Validator::make($request->all(), [
            'user_answer' => 'required|string',
            'answer' => "required|array"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if($quizQuestion->correct_answer == $request->user_answer){
            $quizQuestion->is_correct = true;
        }

        $quizQuestion->user_answer = $request->user_answer;
        $quizQuestion->save();

        return response()->json([
            'status' => 200,
            'data' => $quizQuestion,
            'message' => 'User answered question successfully'
        ]);

        
    }
}
