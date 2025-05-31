<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VerifyDocumentController;

// Dashboard
Route::get('/', function () {
    if (!in_array(Auth::user()->role, [User::ROLE_ADMIN, User::ROLE_EMPLOYEE, User::ROLE_SUPERADMIN])) {
        abort(403);
    }

    $controller = app(DashboardController::class);
    return $controller->index();
})->middleware('auth')->name('dashboard.index');

// Dossiers
Route::get('/dossiers', [DossierController::class, 'index'])->middleware('auth')->name('dossiers.index');
Route::get('/dossiers/{dossier}', [DossierController::class, 'show'])->middleware('auth')->name('dossiers.show');
// Route::get('/dossiers/create', [DossierController::class, 'create'])->middleware('auth');

// Tasks
Route::get('/tasks', [TaskController::class, 'index'])->middleware('auth')->name('tasks.index');

// Documents
Route::get('/documents', [DocumentController::class, 'index'])->middleware('auth')->name('documents.index');
Route::get('/document/create', [DocumentController::class, 'create'])->middleware('auth');
Route::post('/document', [DocumentController::class, 'store'])->middleware('auth')->name('documents.create');

// Uploads
Route::get('/upload/create', [UploadController::class, 'create'])->middleware('auth');
Route::post('/upload', [UploadController::class, 'store'])->middleware('auth')->name('upload.create');

// Queue
Route::get('/queue', [DocumentController::class, 'queue'])->middleware('auth')->name('documents.queue');
Route::post('/documents/process-queue', [DocumentController::class, 'processQueue'])->middleware('auth')->name('documents.processQueue');
Route::post('/documents/create-splits-from-images', [DocumentController::class, 'createSplitsFromImages'])->middleware('auth')->name('documents.create-splits-from-images');

// Document Verification
Route::get('/documents/verify', [VerifyDocumentController::class, 'show'])->middleware('auth')->name('documents.verify.show');
Route::post('/documents/verify', [VerifyDocumentController::class, 'store'])->middleware('auth')->name('documents.verify.store');

// Secure document serving
Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->middleware('auth')->name('documents.view');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->middleware('auth')->name('documents.download');

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
    return view('admin.admin');
})->middleware('auth');


// admin  routes // PREVIOUS
//Route::get('/admin/section/{type}', function ($type) {
    //$allowed = ['clients', 'employees', 'organisation'];
    //if (in_array($type, $allowed)) {
    //    return view("admin.partials.$type");
    //}
    //abort(404);
//});

use App\Models\Organization;

// admin  routes // NEW DISPLAY ORGANIZATION
Route::get('/admin/section/{type}', function ($type) {
    $allowed = ['clients', 'employees', 'organisation'];
    if (!in_array($type, $allowed)) {
        abort(404);
    }

    // Special case for organisation: load organization data
    if ($type === 'organisation') {
        $organization = Organization::find(Auth::user()->organization_id);
        return view("admin.partials.organisation", compact('organization'));
    }

    // For others: load partials without data
    return view("admin.partials.$type");
})->middleware('auth');

// admin  routes // NEW UPDATE ORGANIZATION INFORMATION
use Illuminate\Http\Request;

Route::post('/admin/organisation/update', function (Request $request) {
    $organization = Organization::find(Auth::user()->organization_id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:255',
        'website' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
        'address' => 'required|string|max:255',
        'postal_code' => 'required|string|max:20',
        'city' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'VAT' => 'nullable|string|max:255',
    ]);

    $organization->update($validated);

    return response()->json(['success' => true]);
})->middleware('auth');


use App\Models\Client;

Route::get('/admin/section/{type}', function ($type) {
    $allowed = ['clients', 'employees', 'organisation'];
    if (!in_array($type, $allowed)) {
        abort(404);
    }

    if ($type === 'organisation') {
        $organization = Organization::find(Auth::user()->organization_id);
        return view("admin.partials.organisation", compact('organization'));
    }

    if ($type === 'employees') {
        $users = User::all(); //where('role', 'epmloyee')->get(); // Yes, typo in DB is preserved
        return view("admin.partials.employees", compact('users'));
    }

    if ($type === 'clients') {
        $clients = Client::all();
        return view("admin.partials.clients", compact('clients'));
    }

    // Fallback (shouldn’t be reached)
    return view("admin.partials.$type");
})->middleware('auth');


use App\Http\Controllers\ClientController;
// Admin - Clients
Route::get('/clients', [ClientController::class, 'index']);

Route::get('/clients', function () {
    $clients = Client::with('dossiers')->get();
    return view('clients', compact('clients'));
});

// Super Admin routes

Route::get('/superdashboard', function () {
    if (!in_array(Auth::user()->role, [User::ROLE_SUPERADMIN])) {
        abort(403);
    }
    return view('superdashboard.superdashboard');
})->middleware('auth');

use App\Http\Controllers\OrganizationController;

Route::get('/organisations', [OrganizationController::class, 'index'])->middleware('auth');




// Session
Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);
