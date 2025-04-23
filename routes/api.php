<?php

use App\Http\Controllers\Api\JwtAuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\RecommandResourcesController;
use App\Http\Controllers\Api\ResourcelinksController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::group(['prefix' => 'auth'], function(){

    Route::post('signup', [JwtAuthController::class, 'register']);
    Route::post('signin', [JwtAuthController::class, 'login']);
    Route::get('profile', [JwtAuthController::class, 'profile'])->middleware('jwtauthmiddleware');
    Route::post('logout', [JwtAuthController::class, 'logout'])->middleware('jwtauthmiddleware');
    Route::post('refresh', [JwtAuthController::class, 'refresh'])->middleware('jwtauthmiddleware');

    
});

Route::group(['middleware' => ['jwtauthmiddleware']], function(){
    
    Route::resource('/recommandresources',RecommandResourcesController::class);
    Route::resource('/resoucelinks',ResourcelinksController::class);

});
