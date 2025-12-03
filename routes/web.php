<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

// When I visit /tasks, run the 'index' function
Route::get('/tasks', [TaskController::class, 'index']);

// When I visit /create-task, run the 'createTestTask' function
Route::get('/create-task', [TaskController::class, 'createTestTask']);
