<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1'], function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::resource('/tasks', TaskController::class);
        Route::post('/assignTask/{task}', [TaskController::class, 'assignTask']);
        Route::post('/commentTask/{task}', [TaskController::class, 'commentTask']);
        Route::resource('/users', UserController::class)->except(['create', 'edit']);
    });
});
