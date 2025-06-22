<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/users', [UserController::class, 'getUsers'])->name('usersList');
Route::get('/user/{id}', [UserController::class, 'getSingleUser'])->name('usersView');