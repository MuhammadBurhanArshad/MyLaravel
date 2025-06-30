<?php

use Illuminate\Support\Facades\Route;

function getUsers() {
    return [
        1 => ['name' => 'Ahmed', 'phone' => '923001234567', 'city' => 'Karachi'],
        2 => ['name' => 'Fatima', 'phone' => '923451234567', 'city' => 'Lahore'],
        3 => ['name' => 'Ali', 'phone' => '923451234568', 'city' => 'Islamabad'],
        4 => ['name' => 'Ayesha', 'phone' => '923451234569', 'city' => 'Peshawar'],
        5 => ['name' => 'Usman', 'phone' => '923451234570', 'city' => 'Quetta']
    ];
}

Route::get('/', function () {
    return view('welcome');
});

Route::get('/users', function () {
    $users = [
        1 => ['name' => 'Ahmed', 'phone' => '923001234567', 'city' => 'Karachi'],
        2 => ['name' => 'Fatima', 'phone' => '923451234567', 'city' => 'Lahore'],
        3 => ['name' => 'Ali', 'phone' => '923451234568', 'city' => 'Islamabad'],
        4 => ['name' => 'Ayesha', 'phone' => '923451234569', 'city' => 'Peshawar'],
        5 => ['name' => 'Usman', 'phone' => '923451234570', 'city' => 'Quetta']
    ];
    
    return view('users', ['users' => $users]);
    
    // Passing data to the view as associative array
    // return view('users', [
    //     'name' => "Muhammad Burhan", 
    //     'city' => "Karachi"
    // ]);

    // Alternatively, passing data using with() method
    // return view('users')
    //     ->with('name', "Muhammad Burhan")
    //     ->with('city', "Karachi");

    // Also, passing data using dynamic with() method
    // return view('users')
    //     ->withName("Muhammad Burhan")
    //     ->withCity("Karachi");
});

Route::get('/users/{id}', function ($id) {
    $users = getUsers();

    if (array_key_exists($id, $users)) {
        return view('user', ['user' => $users[$id]]);
    } else {
        abort(404);
    }

    // also we can use another type of abort
    abort_if(!isset($users[$id]), 404, 'User not found');
})->name('userView');