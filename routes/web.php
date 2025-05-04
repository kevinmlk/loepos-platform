<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierController;

// Dashboard
Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

// Dossiers
Route::get('/dossiers', [DossierController::class, 'index'])->middleware('auth')->name('dossiers.index');
// Route::get('/dossiers/create', [DossierController::class, 'dossiers');
// })->middleware('auth');

// Documents - index
Route::get('/documents', [DocumentController::class, 'index'])->middleware('auth')->name('documents.index');
// Document - create
Route::get('/document/create', [DocumentController::class, 'create'])->middleware('auth');
// Document - store
Route::post('/document', [DocumentController::class, 'store'])->middleware('auth')->name('documents.create');

// Reports
Route::get('/reports', function () {
    return view('reports');
})->middleware('auth');

// Support
Route::get('/support', function () {
    return view('support');
})->middleware('auth');

// Login
Route::get('/login', [SessionController::class, 'create'])->name('login');
// Login action
Route::post('/login', [SessionController::class, 'store']);
// Logout action
Route::post('/logout', [SessionController::class, 'destroy']);
