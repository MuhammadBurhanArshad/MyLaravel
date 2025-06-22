<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/users', [UserController::class, 'getUsers'])->name('usersList');
Route::get('/user/{id}', [UserController::class, 'getSingleUser'])->name('usersView');
Route::get('/user/create', [UserController::class, 'createUser'])->name('usersCreate');
Route::get('/user/update/{id}', [UserController::class, 'updateUser'])->name('usersUpdate');
Route::get('/user/delete/{id}', [UserController::class, 'deleteUser'])->name('usersDelete');