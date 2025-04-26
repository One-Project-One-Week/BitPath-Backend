<?php

namespace App\Http\Controllers;

use App\Models\PlanQuiz;
use App\Models\QuizQuestion;

use App\Models\Plan;
use App\Models\PlanQuiz;

use App\Models\Roadmap;
use App\Models\RoadmapSkill;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;

class QuizQuestionController extends Controller
{

    public function updateQuiz(Request $request, PlanQuiz $planQuiz)
    {
        $validator = Validator::make($request->all(), [
            'score' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $planQuiz->score = $request->input('score');
        $planQuiz->status = "completed";
        $planQuiz->save();
        return response()->json(['message' => 'Quiz score updated successfully'], 200);
    }
    public function getQuizQuestionsForSkill(Request $request) {
        $validator = Validator::make($request->all(), [
            'quiz_id' => 'required|exists:plan_quizzes,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        try {
            $user = auth()->user();
            $userId = $user->id;
            $quizId = $request->input('quiz_id');

            $quiz = PlanQuiz::findOrFail($quizId)->with('quizQuestions')->first();
            if($quiz->user_id != $userId){
                return response()->json([
                    'message' => 'You are not authorized to access this quiz'
                ], 403);
            }
            if($quiz->status == "completed"){
                return response()->json([
                    'message' => 'You have already completed this quiz'
                ], 403);
            }
            $questions = $quiz->quizQuestions;
            return response()->json([
                'status' => 200,
                'data' => $questions,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Quiz not found'
            ], 404);
        }
    }
    public function getPlanQuiz(Request $request) {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:roadmap_skills,id',
        ]);
             try {
            $user = auth()->user();
            $userId = $user->id;
            $planId = $request->input('plan_id');

            // Get the plan with relationships
            $plan = Plan::with(['planRequest.roadmapSkill'])
                ->findOrFail($planId);

                $plan_request = $plan->planRequest;


                $skillId = $plan->planRequest->roadmapSkill->id;

                $quiz = PlanQuiz::where('user_id', $userId)
                ->where('skill_id', $skillId)

                ->first();

                // $quizQuestion = QuizQuestion::where('skill_id', $skillId)->get();
                return response()->json([
                    'data' => $quiz,
                    'message' => 'Quiz questions retrieved successfully'
                ]);
            // Check if the relationships exist
            if (!$plan->planRequest || !$plan->planRequest->roadmapSkill) {
                return response()->json([
                    'message' => 'Plan is not properly configured with skills'
                ], 400);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Plan not found'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving questions: ' . $e->getMessage()
            ], 500);
        }
    }
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

    public function index($skill_id){
        $quizs = QuizQuestion::whereHas('planQuiz', function($query) use ($skill_id){
            $query->where(['skill_id' => $skill_id, 'user_id' => auth()->user()->id]);
        })->get();

        return response()->json([
            'status' => 200,
            'quizs' => $quizs,
        ], 200);
    }

    
    // public function update(Request $request){

    //     $validator = Validator::make($request->all(), [
    //         'answers' => "required|array",
    //         'skill_id' => "required",

    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 422,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }
    //     $answers = $request->input('answers');
        
    //     foreach ($answers as $answer) {
    //         $quiz_question = QuizQuestion::findOrFail($answer['question_id']);
    //         $plan_quiz_id = $quiz_question->planQuiz->id;

    //         if($quiz_question->user_answer != null){
    //             return response()->json([
    //                 'status' => 400,
    //                 'message' => 'You already answered this question',
    //             ], 400);
    //         }

    //         if($quiz_question->correct_answer == $answer['selected_option']){
    //             $quiz_question->is_correct = true;
    //         }

    //         $quiz_question->user_answer = $answer['selected_option'];
    //         $quiz_question->save();
    //     }

    //     return response()->json([
    //         'status' => 200,
    //         'planQuiz' => PlanQuiz::with('quizQuestions')->where('id', $plan_quiz_id)->get(),
    //         'message' => 'User answered question successfully'

    //     ], 200);
        

    // }
}
