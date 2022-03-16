<?php

use App\Http\Controllers\Admin\ProjectApplicationController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

//acces with api/admin

// Projects
Route::prefix('project')->group(function () {
    Route::post('search-projects',  [ProjectController::class, 'deepSearch']);
    Route::get('user/{user_id}',  [ProjectController::class, 'usersProject']);
    Route::get('{searchTerm}',  [ProjectController::class, 'search']);
});

// users
Route::prefix('users')->group(function () {
    Route::get('all',  [UsersController::class, 'all']);
    Route::delete('delete/{user_id}',  [UsersController::class, 'delete']);
    Route::post('search',  [UsersController::class, 'search']);
});

// Projects applications
Route::prefix('project-application')->group(function () {
    Route::post('assign',  [ProjectApplicationController::class, 'assign']);
    Route::post('withdraw_assignment',  [ProjectApplicationController::class, 'withdraw']);
});
