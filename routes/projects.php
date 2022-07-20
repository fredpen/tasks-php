<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    PaymentController,
    ProjectApplicationController,
    ProjectController,
    ProjectphotoController,
    ProjectSearchController
};
// Projects
Route::group(['prefix' => 'project', 'name' => 'project'], function () {

    Route::get('all', [ProjectSearchController::class, 'all']);
    Route::get('popular', [ProjectSearchController::class, 'popular']);
    Route::get('active', [ProjectController::class, 'activeProjects']);
    Route::get('appliable', [ProjectSearchController::class, 'appliableProjects']);
    Route::get('related-to/{project_id}', [ProjectController::class, 'relatedProjects']);
    Route::post('search', [ProjectController::class, 'searchProject']);
    Route::get('/{projectId}/show', [ProjectController::class, 'show']);
    Route::get('attributes', [ProjectController::class, 'projectAttributes']);
    Route::get('{project_id}/assigned_users',  [ProjectApplicationController::class, 'assignedUsers']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('store', [ProjectController::class, 'store']);
        Route::post('favoured', [ProjectController::class, 'favouredAProject']);
        Route::post('unfavour', [ProjectController::class, 'unFavouredAProject']);
        Route::get('my_favourite_ids', [ProjectController::class, 'favouriteProjectsIds']);

        // "my_drafts", "my_cancelled_projects", "my_completed_projects", "my_running_projects", "my_favourites", "my_projects"]);
        Route::get('{searchTerm}', [ProjectController::class, 'search']);

        Route::group(['prefix' => 'media'], function () {
            Route::post('add', [ProjectphotoController::class, 'addMedia']);
            Route::post('remove', [ProjectphotoController::class, 'removeMedia']); //
        });

        Route::middleware(['projectAdminRight'])->group(function () {
            Route::put('update', [ProjectController::class, 'update']);
            Route::delete('delete', [ProjectController::class, 'delete']);
            Route::put('cancel', [ProjectController::class, 'cancel']);
            Route::get('publish/{projectId}', [ProjectController::class, 'publish']);
        });
    });
});

// payments
Route::group(['prefix' => 'project/payment'], function () {

    Route::get('verify-transaction', [PaymentController::class, 'verify']);

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::middleware(['isAdmin'])->group(function () {
            Route::get('all_transactions', [PaymentController::class, 'index']);
            Route::get('successful_transactions', [PaymentController::class, 'succesfulTransactions']);
        });

        Route::middleware(['projectAdminRight'])->group(function () {
            Route::post('initiate', [PaymentController::class, 'initiate']);
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
