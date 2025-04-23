<?php

use App\Http\Controllers\Api\JwtAuthController;
use App\Http\Controllers\Api\SocialLoginController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['prefix' => 'auth'], function(){

    Route::post('signup', [JwtAuthController::class, 'register']);
    Route::post('signin', [JwtAuthController::class, 'login']);
    Route::get('profile', [JwtAuthController::class, 'profile'])->middleware('jwtauthmiddleware');
    Route::post('logout', [JwtAuthController::class, 'logout'])->middleware('jwtauthmiddleware');
    Route::post('refresh', [JwtAuthController::class, 'refresh']);
    Route::post('{provider}/callback', [SocialLoginController::class, 'socialLogin']);
});
