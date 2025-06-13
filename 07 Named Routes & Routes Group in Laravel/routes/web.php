<?php

use Illuminate\Support\Facades\Route;

/**
 * Route Redirection and Naming
 */

// it will auto redirect to /home when the / will hit
Route::redirect('/', '/home'); // as third parameter we can define the HTTP code for temporary redirection or permanent useful for search engines

Route::get('/home', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/post', function () {
    return view('post');
})->name('post');


/**
 * Route Grouping
 */

 Route::prefix('page')->group(function(){
    Route::get('/about', function () {
        return "<h1>About Page</h1>";
    });
    Route::get('/gallery', function () {
        return "<h1>Gallery Page</h1>";
    });
    Route::get('/posts', function () {
        return "<h1>Posts Page</h1>";
    });
 });


 /**
  * For 404 - Page not found error
  */

  Route::fallback(function () {
    return "<h1>Page not found</h1>";
  });
