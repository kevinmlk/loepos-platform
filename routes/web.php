<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Controllers
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierController;

// Dashboard
Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

// Dossiers - index
Route::get('/dossiers', [DossierController::class, 'index'])->middleware('auth')->name('dossiers.index');
// Dossiers - show
Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->middleware('auth')->name('dossiers.show');
// Dossiers - create
// Route::get('/dossiers/create', [DossierController::class, 'create'])->middleware('auth');

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

// admin
Route::get('/admin', function () {
    if (!in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])) {
        abort(403);
    }
    return view('admin');
})->middleware('auth');

// admin - routes 
use App\Http\Controllers\AdminController;

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/organisation', [AdminController::class, 'organisatie'])->name('admin.organisatie');
    Route::get('/employees', [AdminController::class, 'medewerkers'])->name('admin.medewerkers');
    Route::get('/clients', [AdminController::class, 'clienten'])->name('admin.clienten');
});


// Login
Route::get('/login', [SessionController::class, 'create'])->name('login');
// Login action
Route::post('/login', [SessionController::class, 'store']);
// Logout action
Route::post('/logout', [SessionController::class, 'destroy']);
