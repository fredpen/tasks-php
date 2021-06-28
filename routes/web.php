<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth::routes(['verify' => true]);
Route::get('front', function (Request $request) {
    return $request;
});

Route::get('/', function () {
    return "Oops You took a wrong turn, ensure you set accept header as application/json";
});

// Route::get('/welcome', 'HomeController@welcome')->name('welcome'); // redirect after signing in


// // routes for outside users interacting with projects
// Route::get('/projects/task/{task}/show', 'ProjectshowController@show')->name('project.usershow'); // task show view for users
// Route::post('/projects/{project}/apply', 'ProjectshowController@apply')->name('project.apply'); // task show view for users
// Route::get('/project/{projectAssignedUser}/accept', 'ProjectshowController@accept')->name('project.accept');


// Route::resources([
//     'account' => 'AccountController',
//     'projects' => 'ProjectController',
//     'admin/countries' => 'CountryController',
//     'admin/regions' => 'RegionController',
//     'admin/cities' => 'CityController'
// ]);


// Route::get('notifications', 'AccountController@notifications')->name('notifications')->middleware('auth'); //for notifications
// Route::get('myTasks', 'AccountController@myTasks')->name('myTasks')->middleware('auth');
// Route::get('region/show/ajax/{id}', 'RegionController@showAjax');//country ajax to show regions
// Route::get('city/show/ajax/{id}', 'CityController@showAjax');//country ajax to show city

// Route::group(
//     ['middleware' => ['auth', 'verified', 'isActive']], function () {
//         Route::resource('project/photos', 'ProjectphotoController');
//         Route::put('project/ajax/{id}', 'ProjectController@ajax')->name('project.ajax');
//     }
// );


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

// // route group for chnaging status of projects
// Route::name('project.')->group(function () {
//     Route::get('projectstatus/{project}/completed', 'ProjectStatusController@completed')->name('complete');
//     Route::get('projectstatus/{project}/live', 'ProjectStatusController@live')->name('live');
//     Route::get('projectstatus/{project}/cancelled', 'ProjectStatusController@cancelled')->name('cancel');
//     Route::get('projectstatus/{project}/posted', 'ProjectStatusController@posted')->name('post');
// });

// Route::post('/pay', 'RaveController@initialize')->name('pay');
// Route::post('/rave/callback', 'RaveController@callback')->name('callback');
// Route::get('/{project}/payment', 'RaveController@payment')->name('payment');
