<?php

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

Route::group(['prefix' => 'v1'], function () {
    //todo routes for projects
    Route::get('/todos', [\App\Http\Controllers\Api\Todo\TodoController::class, 'index']);
    Route::post('/todos', [\App\Http\Controllers\Api\Todo\TodoController::class, 'store']);
    Route::get('/todos/{slug}', [\App\Http\Controllers\Api\Todo\TodoController::class, 'show']);
    //update todo project with mark staus as completed or not completed
    Route::put('/todos/{slug}', [\App\Http\Controllers\Api\Todo\TodoController::class, 'update']);
    //delete todo project 
    Route::delete('/todos/{slug}', [\App\Http\Controllers\Api\Todo\TodoController::class, 'destroy']);
    // routes for assigning tasks to our todo projects
    Route::get('/tasks', [\App\Http\Controllers\Api\Task\TaskController::class, 'index']);
    Route::post('/tasks', [\App\Http\Controllers\Api\Task\TaskController::class, 'store']);
});
