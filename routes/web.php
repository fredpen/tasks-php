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
