<?php

use App\Http\Controllers\Admin\ProjectApplicationController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

//acces with api/admin

// Projects
Route::prefix('project')->group(function () {
    Route::get('drafts',  [ProjectController::class, 'drafts']);
    Route::get('published',  [ProjectController::class, 'published']);
    Route::get('started',  [ProjectController::class, 'started']);
    Route::get('completed',  [ProjectController::class, 'completed']);
    Route::get('cancelled',  [ProjectController::class, 'cancelled']);
    Route::get('deleted',  [ProjectController::class, 'deleted']);
    Route::get('user/{user_id}',  [ProjectController::class, 'usersProject']);
});

// users
Route::prefix('users')->group(function () {
    Route::get('all',  [UsersController::class, 'all']);
});

// Projects applications
Route::prefix('project-application')->group(function () {
    Route::post('assign',  [ProjectApplicationController::class, 'assign']);
    Route::post('withdraw_assignment',  [ProjectApplicationController::class, 'withdraw']);
});


// Route::group(
//     ['middleware' => ['auth', 'isAdmin'] ], function () {
//         Route::get('/admin', 'AdminController@index')->name('admin.home');
//         Route::get('/admin/projects/all', 'AdminProjectController@showallProjects')->name('project.all');
//         Route::get('/admin/projects/ongoing', 'AdminProjectController@showongoingProjects')->name('project.ongoing');
//         Route::get('/admin/projects/completed', 'AdminProjectController@showcompletedProjects')->name('project.completed');
//         Route::get('/admin/projects/posted', 'AdminProjectController@showpostedProjects')->name('project.posted');
//         Route::get('/admin/projects/created', 'AdminProjectController@showcreatedProjects')->name('project.created');
//         Route::get('/admin/projects/cancelled', 'AdminProjectController@showcancelledProjects')->name('project.cancelled');
//         Route::get('/admin/projects/{project}/adminShow', 'AdminProjectController@adminShow')->name('project.adminShow');
//         Route::post('/admin/project/assign', 'AdminProjectController@assign')->name('project.assign');
//         Route::post('/admin/project/reassign', 'AdminProjectController@reassign')->name('project.reassign');


//         Route::resources([
//             'admin/users' => 'AdminUsersController',
//             'admin/tasks' => 'TasksController',
//             'admin/subtasks' => 'SubTaskController'
//         ]);
//     }
// );
