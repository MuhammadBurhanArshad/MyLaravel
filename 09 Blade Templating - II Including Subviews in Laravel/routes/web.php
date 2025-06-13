<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('web')->group(function(){
    Route::redirect('/',  '/web/home');
    Route::view('/home',  'web.home')->name('home');
    Route::view('/about',  'web.about')->name('about');
    Route::view('/post',  'web.post')->name('post');
});

