<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SingleActionController;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(PageController::class)->group(function () {
    Route::get('/user/{id}', 'showUser')->name('showUser');
});

Route::get('/singleAction', SingleActionController::class);