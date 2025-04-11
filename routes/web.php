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

// Documents
Route::get('/documents', function () {
    return view('documents');
});

// Reports
Route::get('/reports', function () {
    return view('reports');
});

// Support
Route::get('/support', function () {
    return view('support');
});

// Login
Route::get('/login', function () {
    return view('login');
});
