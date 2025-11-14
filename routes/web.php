<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home.index');

// Login Routes
Route::get('/login', [AuthController::class, 'index'])->name('login.index');