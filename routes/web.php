<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Controllers
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\AdminController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard.index');

// Dossiers
Route::get('/dossiers', [DossierController::class, 'index'])->middleware('auth')->name('dossiers.index');
Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->middleware('auth')->name('dossiers.show');
// Route::get('/dossiers/create', [DossierController::class, 'create'])->middleware('auth');

// Documents
Route::get('/documents', [DocumentController::class, 'index'])->middleware('auth')->name('documents.index');
Route::get('/document/create', [DocumentController::class, 'create'])->middleware('auth');
Route::post('/document', [DocumentController::class, 'store'])->middleware('auth')->name('documents.create');

// Reports
Route::get('/reports', function () {
    return view('reports');
})->middleware('auth');

// Support
Route::get('/support', function () {
    return view('support');
})->middleware('auth');

// Admin
Route::get('/admin', function () {
    if (!in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])) {
        abort(403);
    }
    return view('admin');
})->middleware('auth');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/organisation', [AdminController::class, 'organisatie'])->name('admin.organisatie');
    Route::get('/employees', [AdminController::class, 'medewerkers'])->name('admin.medewerkers');
    Route::get('/clients', [AdminController::class, 'clienten'])->name('admin.clienten');
});

// Session
Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);
