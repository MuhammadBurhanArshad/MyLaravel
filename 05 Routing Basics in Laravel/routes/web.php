<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/post', function () {
    return view('post');
    // we can also call our HTML code directly here as:
    /*
    return "<h1>Post Page</h1>";
    */
});


// here is another method to direct access via route
Route::view('post', '/post');


Route::get('/post/firstpost', function() {
    return view('firstpost');
});
