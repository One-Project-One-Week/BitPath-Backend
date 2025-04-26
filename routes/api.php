<?php

use App\Http\Controllers\Api\JwtAuthController;
use App\Http\Controllers\Api\SocialLoginController;
use App\Http\Controllers\Api\TasksController;
use App\Http\Controllers\QuizQuestionController;
use GuzzleHttp\Middleware;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\RoadmapController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\RecommandResourcesController;
use App\Http\Controllers\Api\ResourcelinksController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\LeaderboardController;
use App\Models\PlanRequest;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/roadmap', [RoadmapController::class, 'generateRoadmap'])->name('roadmap.generateRoadmap');

Route::get('/shared-roadmaps', [RoadmapController::class, 'getSharedRoadmaps'])->name('roadmap.getSharedRoadmaps');
Route::get('/shared-roadmaps/{roadmap}', [RoadmapController::class, 'showSharedRoadmap'])->name('roadmap.showSharedRoadmap');
Route::get('/shared-roadmaps/{roadmap}/participants', [RoadmapController::class, 'getSharedRoadmapParticipants'])->name('roadmap.getSharedRoadmapParticipants');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

Route::group(['prefix' => 'auth'], function () {
    Route::post('signup', [JwtAuthController::class, 'register']);
    Route::post('signin', [JwtAuthController::class, 'login']);
    Route::get('profile', [JwtAuthController::class, 'profile'])->middleware('jwtauthmiddleware');
    Route::post('logout', [JwtAuthController::class, 'logout'])->middleware('jwtauthmiddleware');
    Route::post('refresh', [JwtAuthController::class, 'refresh']);
    Route::post('{provider}/callback', [SocialLoginController::class, 'socialLogin']);
});

Route::group(['middleware' => ['jwtauthmiddleware']], function () {
    Route::resource('/recommandresources', RecommandResourcesController::class);
    Route::resource('/resoucelinks', ResourcelinksController::class);

    Route::get('/roadmaps', [RoadmapController::class, 'index'])->name('roadmap.index');
    Route::post('/roadmap/store', [RoadmapController::class, 'store'])->name('roadmap.store');
    Route::get('/roadmap/{roadmap}', [RoadmapController::class, 'show'])->name('roadmap.show');
    Route::delete('/roadmap/{roadmap}', [RoadmapController::class, 'destroy'])->name('roadmap.destroy');
    Route::patch('/roadmap/{roadmap}/visibility', [RoadmapController::class, 'updateVisibility'])->name('roadmap.updateVisibility');

    Route::get('/roadmap/{roadmap}/skills', [RoadmapController::class, 'getRoadmapSkills'])->name('roadmap.getRoadmapSkills');
    Route::get('/roadmap/{roadmap}/skills/{skill}', [RoadmapController::class, 'getRoadmapSkill'])->name('roadmap.getRoadmapSkill');

    Route::post('/shared-roadmaps/{roadmap}/join', [RoadmapController::class, 'joinRoadmap'])->name('roadmap.joinRoadmap');
    Route::post('/shared-roadmaps/{roadmap}/leave', [RoadmapController::class, 'destory'])->name('roadmap.leaveRoadmap');

    Route::post('/plans', [PlanController::class, 'generatePlan'])->name('plan.generatePlan');
    // this is updating task as completed if user clicked complete a task
    Route::put('/tasks/{task}', [TaskController::class, 'updateTask'])->name('task.update');
    Route::put('/plans/{plan}', [PlanController::class, 'regeneratePlan'])->name('plan.regeneratePlan');

    Route::get('plans', [PlanController::class, 'index'])->name('plan.index');
    Route::get('plans/{plan}', [PlanController::class, 'show']);

    Route::get('/quizzes/{skill}', [QuizQuestionController::class, 'index'])->name(name: 'quizquestion.index');
    Route::patch('/quizzes', [QuizQuestionController::class, 'update'])->name(name: 'quizquestion.update');
});
