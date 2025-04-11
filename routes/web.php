<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', function () {
    return view('dashboard');
});

// Post processing
Route::get('/post-processing', function () {
    return view('post-processing');
});

// Login
Route::get('/login', function () {
    return view('login');
});
