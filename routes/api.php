<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProjectApplicationController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSkillsController;
use Illuminate\Support\Facades\Route;

// auth
Route::group(['prefix' => 'auth'], function () {

    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'signup'])->name('register');

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
    Route::get('regions_in_a_country/{countryId}', [LocationController::class, 'regions']);
    Route::get('cities_in_a_region/{regionId}',  [LocationController::class, 'cities']);
});


// Projects
Route::group(['prefix' => 'project', 'name' => 'project'], function () {

    Route::get('all', [ProjectController::class, 'index'])->name('all');
    Route::get('/{projectId}/show', [ProjectController::class, 'show']);
    Route::get('attributes', [ProjectController::class, 'projectAttributes']);
    Route::get('{project_id}/assigned_users',  [ProjectApplicationController::class, 'assignedUsers']);


    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('store', [ProjectController::class, 'store']);
        Route::get('my_projects', [ProjectController::class, 'usersProject']);
        Route::post('favoured', [ProjectController::class, 'favouredAProject']);
        Route::get('my_favourites', [ProjectController::class, 'favouritesProjects']);

        Route::middleware(['projectAdminRight'])->group(function () {
            Route::patch('update', [ProjectController::class, 'update']);
            Route::delete('delete', [ProjectController::class, 'delete']);
            Route::get('publish/{projectId}', [ProjectController::class, 'publish']);
        });
    });
});

// Projects application
Route::group(['prefix' => 'project-applications', 'name' => 'project'], function () {

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/apply',  [ProjectApplicationController::class, 'apply']);
        Route::post('/accept',  [ProjectApplicationController::class, 'accept']);
        Route::post('/withdraw', [ProjectApplicationController::class, 'withdraw']);
        Route::get('/my-applications', [ProjectApplicationController::class, 'myApplications']);
    });

    Route::get('/{projectId}', [ProjectApplicationController::class, 'applications']);
});


// Projects status
// Route::group(['prefix' => 'update-project-status', 'name' => 'projectStatus'], function () {

//     Route::get('/{projectId}/{status}', 'ProjectStatusController@updateStatus')->name('update');
// });

// location controller


// Projects assign and application
// Route::group(['prefix' => 'project-assignment', 'name' => 'projectAssignment'], function () {
//     Route::get('/assigned-users/{projectId}', 'ProjectAssignmentController@projectAssignedUser')->name('projectAssignedUser');
//     Route::get('/accept/{projectId}', 'ProjectAssignmentController@accept')->name('accept');
// });

///////////////////////////////////////////////////////////////////////

// Route::group(
//     ['middleware' => ['auth', 'verified', 'isActive']], function () {
//         Route::resource('project/photos', 'ProjectphotoController');
//         Route::put('project/ajax/{id}', 'ProjectController@ajax')->name('project.ajax');
//     }
// );


// // routes for outside users interacting with projects
// Route::get('/projects/task/{task}/show', 'ProjectshowController@show')->name('project.usershow'); // task show view for users
// Route::post('/projects/{project}/apply', 'ProjectshowController@apply')->name('project.apply'); // task show view for users
// Route::get('/project/{projectAssignedUser}/accept', 'ProjectshowController@accept')->name('project.accept');





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
