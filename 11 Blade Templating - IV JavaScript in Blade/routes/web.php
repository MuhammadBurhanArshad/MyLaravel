<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return view('test');
});

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function() {
    return view('about');
})->name('about');

Route::get('/post', function() {
    return view('post');
})->name('post');
