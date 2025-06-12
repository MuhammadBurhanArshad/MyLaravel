<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/post/{id}', function ($id) {
    return "<h1>Post ID : $id</h1>";
});

// for optional parameter we have to add the question marks after the paramter

Route::get('/post/{id?}', function ($id = null) {
    if($id) {
        return "<h1>Post ID : $id</h1>";
    } else {
        return "<h1>No ID Provided</h1>";
    }
});


// getting more than one value on parameter


Route::get('/post/{id?}/comment/{commentId}', function(string $id = null, string $commentId = null) {
    if($id) {
        return "<h1>Post ID : $id</h1> <h2> $commentId </h2>";
    } else {
        return "<h1>No ID Provided</h1>";
    }
});


// constraints usage example

Route::get('/post/{id}', function (string $id) {
    return "User $id";
})->whereNumber('id'); // here the route constraint will be validated, and it will only work for number

Route::get('/posts/{username}', function (string $username) {
    return "User $username";
})->whereAlpha('username'); // here the route constraint will be validated, and it will only work for alphabet

Route::get('/posts/{username}', function (string $username) {
    return "User $username";
})->whereAlphaNumeric('username'); // here the route constraint will be validated, and it will only work for alphabet with numeric value

Route::get('/posts/{id}', function (string $id) {
    return "User $id";
})->whereIn('id', ['movie', 'song']); // here the route constraint will be validated, and it works for array of allowed values

Route::get('/posts/{id}', function (string $id) {
    return "User $id";
})->where('id', '[0-9]+'); // here the route constraint will be validated, and it work for the parameters and matching them with regular expression

Route::get('/posts/{id}', function (string $id) {
    return "User $id";
})->where('id', '[0-9]+')->whereAlpha('commentId'); // here the route constraint will be validated, and it work for the parameters and matching them with regular expression and commentId will be alphabet
