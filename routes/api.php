<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectApplicationController;
use App\Http\Controllers\ProjectphotoController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSkillsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

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
    Route::get('regions-only', [LocationController::class, 'regionsOnly']);
    Route::get('regions_in_a_country/{countryId}', [LocationController::class, 'regions']);
    Route::get('cities_in_a_region/{regionId}',  [LocationController::class, 'cities']);
});

// Projects
Route::group(['prefix' => 'project', 'name' => 'project'], function () {

    Route::get('all', [ProjectController::class, 'index']);
    Route::get('active', [ProjectController::class, 'activeProjects']);
    Route::get('appliable', [ProjectController::class, 'appliableProjects']);
    Route::get('related-to/{project_id}', [ProjectController::class, 'relatedProjects']);
    Route::post('search', [ProjectController::class, 'searchProject']);
    Route::get('/{projectId}/show', [ProjectController::class, 'show']);
    Route::get('attributes', [ProjectController::class, 'projectAttributes']);
    Route::get('{project_id}/assigned_users',  [ProjectApplicationController::class, 'assignedUsers']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('store', [ProjectController::class, 'store']);
        Route::get('my_projects', [ProjectController::class, 'usersProject']);
        Route::get('my_drafts', [ProjectController::class, 'usersProject']);
        Route::get('my_cancelled_projects', [ProjectController::class, 'usersCancelProject']);
        Route::get('my_running_projects', [ProjectController::class, 'usersRunningProject']);
        Route::get('my_completed_projects', [ProjectController::class, 'usersCompletedProject']);
        Route::post('favoured', [ProjectController::class, 'favouredAProject']);
        Route::post('unfavour', [ProjectController::class, 'unFavouredAProject']);
        Route::get('my_favourites', [ProjectController::class, 'favouritesProjects']);
        Route::get('my_favourite_ids', [ProjectController::class, 'favouriteProjectsIds']);

        Route::middleware(['projectAdminRight'])->group(function () {
            Route::patch('update', [ProjectController::class, 'update']);
            Route::delete('delete', [ProjectController::class, 'delete']);
            Route::patch('cancel', [ProjectController::class, 'cancel']);
            Route::get('publish/{projectId}', [ProjectController::class, 'publish']);

            Route::group(['prefix' => 'media'], function () {
                Route::post('add', [ProjectphotoController::class, 'addMedia']);
                Route::post('remove', [ProjectphotoController::class, 'removeMedia']);
            });
        });
    });
});

// payments
Route::group(['prefix' => 'project/payment'], function () {

    Route::get('verify', [PaymentController::class, 'verify']);

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::middleware(['isAdmin'])->group(function () {
            Route::get('all_transactions', [PaymentController::class, 'index']);
            Route::get('successful_transactions', [PaymentController::class, 'succesfulTransactions']);
        });

        Route::middleware(['projectAdminRight'])->group(function () {
            Route::get('initiate', [PaymentController::class, 'initiate']);
            Route::get('my-transactions', [PaymentController::class, 'userPayments']);
            Route::get('my-successful-transactions', [PaymentController::class, 'userSuccesfulPayments']);
        });
    });
});

// Projects application
Route::group(['prefix' => 'project-applications'], function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/apply',  [ProjectApplicationController::class, 'apply']);
        Route::post('/accept',  [ProjectApplicationController::class, 'accept']);
        Route::post('/withdraw', [ProjectApplicationController::class, 'withdraw']);
        Route::get('/my-applications', [ProjectApplicationController::class, 'myApplications']);
        Route::get('/my-applicationIds', [ProjectApplicationController::class, 'myApplicationIds']);
    });

    Route::get('/{projectId}', [ProjectApplicationController::class, 'applications']);
});

// Notifications
Route::group(['prefix' => 'notification', 'middleware' => 'auth:sanctum'], function () {

    Route::get('all',  [ProjectApplicationController::class, 'all']);

    Route::group(['prefix' => 'admin', 'middleware' => 'isAdmin'], function () {
        Route::post('rate',  [ProjectApplicationController::class, 'rate']);
    });
});

// Projects ratings
Route::group(['prefix' => 'project/ratings'], function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('rate',  [ProjectApplicationController::class, 'rate']);
    });

    Route::get('user-ratings/{user_id}',  [ProjectApplicationController::class, 'userRatings']);
    Route::get('{project_id}',  [ProjectApplicationController::class, 'ratings']);
});


// Projects status
Route::group(['prefix' => 'project/status', 'middleware' => 'auth:sanctum'], function () {

    Route::get('completed', [ProjectApplicationController::class, 'markCompleted']);
});


///////////////////////////////////////////////////////////////////////




//     Route::get('notifications', 'AccountController@notifications')->name('notifications')->middleware('auth'); //for notifications
//     Route::get('myTasks', 'AccountController@myTasks')->name('myTasks')->middleware('auth');
//     Route::get('region/show/ajax/{id}', 'RegionController@showAjax');//country ajax to show regions
//     Route::get('city/show/ajax/{id}', 'CityController@showAjax');//country ajax to show city




//     // route group for chnaging status of projects
//     Route::name('project.')->group(function () {
//         Route::get('projectstatus/{project}/completed', 'ProjectStatusController@completed')->name('complete');
//         Route::get('projectstatus/{project}/live', 'ProjectStatusController@live')->name('live');
//         Route::get('projectstatus/{project}/cancelled', 'ProjectStatusController@cancelled')->name('cancel');
//         Route::get('projectstatus/{project}/posted', 'ProjectStatusController@posted')->name('post');
//     });

//     Route::post('/pay', 'RaveController@initialize')->name('pay');
//     Route::post('/rave/callback', 'RaveController@callback')->name('callback');
//     Route::get('/{project}/payment', 'RaveController@payment')->name('payment');



// roles durations and other creation details
// Route::group(['name' => 'createOPtions'], function () {
//     Route::get('createOPtions', 'CreateOptionsController@createOPtions')->name('createOPtions');
//     Route::get('roles', 'CreateOptionsController@roles')->name('roles');
// });
