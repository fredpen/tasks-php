<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    Admin\GeneralController,
    AuthController,
    LocationController,
    NotificationController,
    ProjectApplicationController,
    SubTaskController,
    TasksController,
    UserController,
    UserSkillsController
};
// general
Route::group(['prefix' => 'general'], function () {

    Route::get('landing-page-details', [GeneralController::class, 'landing'])->name('landing');
});

// auth
Route::group(['prefix' => 'auth'], function () {

    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'signup'])->name('register');
    Route::post('reset-passowrd',  [AuthController::class, 'resetPassword']);
    Route::post('initiate-password-reset',  [AuthController::class, 'initiatePasswordReset']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout',  [AuthController::class, 'logout']);
    });
});

// user
Route::group(['prefix' => 'user'], function () {

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('details',  [UserController::class, 'userDetails']);
        Route::post('update-user', [UserController::class, 'updateUser']);
        Route::post('update-security-data', [UserController::class, 'updateSecurityData']);
        Route::post('set-user-security', [UserController::class, 'setSecurity']);

        Route::post('update-skills', [UserSkillsController::class, 'syncSkills']);
        Route::get('my-skills', [UserSkillsController::class, 'userSkills']);
    });

    Route::get('{id}/details',  [UserController::class, 'userDetailsWithId']);
});

// notification
Route::group(['prefix' => 'notifications', 'middleware' => 'auth:sanctum'], function () {

    Route::get('all', [NotificationController::class, 'all']);
    Route::get('unread', [NotificationController::class, 'unread']);
    Route::post('delete', [NotificationController::class, 'delete']);
    Route::post('mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::get('mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
});

// tasks
Route::group(['prefix' => 'task', 'name' => 'task'], function () {

    Route::get('all', [TasksController::class, 'index']);
    Route::get('/{taskId}/show', [TasksController::class, 'show'])->name('show');
    Route::get('all-with-subtasks', [TasksController::class, 'taskWithSubTasks']);

    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
        Route::post('store', [TasksController::class, 'store'])->name('store');
        Route::delete('/{taskId}/delete', [TasksController::class, 'delete'])->name('delete');
        Route::patch('/{taskId}/update', [TasksController::class, 'update'])->name('update');
    });
});

// subtasks
Route::group(['prefix' => 'subTask', 'name' => 'subTask'], function () {

    Route::get('all', [SubTaskController::class, 'index']);
    Route::get('/{taskId}/show',  [SubTaskController::class, 'show']);

    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
        Route::post('store',  [SubTaskController::class, 'store']);
        Route::delete('/{taskId}/delete', [SubTaskController::class, 'delete']);
        Route::patch('/{taskId}/update',  [SubTaskController::class, 'update']);
    });
});

// location
Route::group(['prefix' => 'location', 'name' => 'location'], function () {
    Route::get('countries', [LocationController::class, 'countries']);
    Route::get('countries-only', [LocationController::class, 'countriesOnly']);
    Route::get('region-detail/{region_id}', [LocationController::class, 'regionDetail']);
    Route::get('city-detail/{city_id}', [LocationController::class, 'cityDetail']);
    Route::get('regions-only', [LocationController::class, 'regionsOnly']);
    Route::get('regions_in_a_country/{countryId}', [LocationController::class, 'regions']);
    Route::get('cities_in_a_region/{regionId}',  [LocationController::class, 'cities']);
});


// Notifications
Route::group(['prefix' => 'notification', 'middleware' => 'auth:sanctum'], function () {

    Route::get('all',  [ProjectApplicationController::class, 'all']);

    Route::group(['prefix' => 'admin', 'middleware' => 'isAdmin'], function () {
        Route::post('rate',  [ProjectApplicationController::class, 'rate']);
    });
});
