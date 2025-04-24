<?php

use App\Http\Controllers\Api\JwtAuthController;
use App\Http\Controllers\Api\SocialLoginController;
use GuzzleHttp\Middleware;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\RoadmapController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\RecommandResourcesController;
use App\Http\Controllers\Api\ResourcelinksController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/roadmap', [RoadmapController::class, 'generateRoadmap'])->name('roadmap.generateRoadmap');

Route::group(['prefix' => 'auth'], function(){
    Route::post('signup', [JwtAuthController::class, 'register']);
    Route::post('signin', [JwtAuthController::class, 'login']);
    Route::get('profile', [JwtAuthController::class, 'profile'])->middleware('jwtauthmiddleware');
    Route::post('logout', [JwtAuthController::class, 'logout'])->middleware('jwtauthmiddleware');
    Route::post('refresh', [JwtAuthController::class, 'refresh']);
    Route::post('{provider}/callback', [SocialLoginController::class, 'socialLogin']);
});

Route::group(['middleware' => ['jwtauthmiddleware']], function(){
    Route::resource('/recommandresources',RecommandResourcesController::class);
    Route::resource('/resoucelinks',ResourcelinksController::class);

    Route::post('/roadmap/store', [RoadmapController::class, 'store'])->name('roadmap.store');
    Route::get('/roadmap/{roadmap}', [RoadmapController::class, 'show'])->name('roadmap.show');

    Route::post('/plan', [PlanController::class, 'generatePlan'])->name('plan.generatePlan');
});
