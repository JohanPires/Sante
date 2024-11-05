<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/user', [UserController::class, 'getUsers']);
Route::get('/user/{id}', [UserController::class, 'getOneUser']);
Route::put('/user/{id}', [UserController::class, 'editUser']);
Route::delete('/user/{id}', [UserController::class, 'deleteUser']);
Route::post('/logout', [UserController::class, 'logout']);


Route::get('/doctor', [DoctorController::class, 'index']);
