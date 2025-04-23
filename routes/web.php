<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\SessionController;

// Dashboard
Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

// Post processing
Route::get('/post-processing', function () {
    return view('post-processing');
})->middleware('auth');

// Documents
Route::get('/documents', function () {
    return view('documents');
})->middleware('auth');

// Reports
Route::get('/reports', function () {
    return view('reports');
})->middleware('auth');

// Support
Route::get('/support', function () {
    return view('support');
})->middleware('auth');

// Login page
Route::get('/login', [SessionController::class, 'create'])->name('login');
// Login action
Route::post('/login', [SessionController::class, 'store']);
// Logout action
Route::post('/logout', [SessionController::class, 'destroy']);
