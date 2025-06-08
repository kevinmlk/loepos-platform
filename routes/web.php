<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Controllers
use App\Http\Controllers\VerifyDocumentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaskController;

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
Route::get('/uploads', [UploadController::class, 'index'])->middleware('auth')->name('uploads.index');
Route::get('/upload/create', [UploadController::class, 'create'])->middleware('auth');
Route::post('/upload', [UploadController::class, 'store'])->middleware('auth')->name('upload.create');
Route::get('/uploads/{upload}', [UploadController::class, 'show'])->middleware('auth')->name('uploads.show');

// Queue
Route::get('/queue', [DocumentController::class, 'queue'])->middleware('auth')->name('documents.queue');
Route::post('/documents/process-queue', [DocumentController::class, 'processQueue'])->middleware('auth')->name('documents.processQueue');
Route::post('/documents/create-splits-from-images', [DocumentController::class, 'createSplitsFromImages'])->middleware('auth')->name('documents.create-splits-from-images');

// Document Verification
Route::get('/queue/verify', [VerifyDocumentController::class, 'show'])->middleware('auth')->name('queue.verify');
Route::post('/queue/verify', [VerifyDocumentController::class, 'store'])->middleware('auth')->name('queue.verify.store');
Route::post('/documents/{document}/reject', [VerifyDocumentController::class, 'reject'])->middleware('auth')->name('documents.reject');

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

use App\Http\Controllers\EmployeeController;

Route::get('/admin/employees/{id}', [EmployeeController::class, 'show'])->name('admin.employees.show');
Route::get('/admin/employee/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
Route::post('/admin/employee', [EmployeeController::class, 'store'])->name('admin.employees.store');
Route::patch('/admin/employee/{employee}', [EmployeeController::class, 'update'])->middleware('auth');


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

// Admin - Clients
Route::get('/admin/clients', [ClientController::class, 'index'])->middleware('auth');
Route::get('/admin/client/create',[ClientController::class, 'create'])->middleware('auth')->name('admin.clients.create');
Route::post('admin/client', [ClientController::class, 'store'])->middleware('auth');
Route::get('admin/client/{client}', [ClientController::class, 'show'])->middleware('auth')->name('admin.clients.show');
Route::patch('admin/client/{client}', [ClientController::class, 'update'])->middleware('auth');

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

Route::get('/organisations', [OrganizationController::class, 'index'])->middleware('auth')->name('organisations.index');
Route::get('/organisation/create', [OrganizationController::class, 'create'])->middleware('auth')->name('organisations.create');
Route::get('/organisations/{organization}', [OrganizationController::class, 'show'])->middleware('auth')->name('organisations.show');
Route::post('/organisation', [OrganizationController::class, 'store'])->middleware('auth');

Route::get('/organisations/{organization}/edit', [OrganizationController::class, 'edit'])->name('organisations.edit')->middleware('auth');
Route::put('/organisations/{organization}', [OrganizationController::class, 'update'])->name('organisations.update')->middleware('auth');
Route::delete('/organisations/{organization}', [OrganizationController::class, 'destroy'])->name('organisations.destroy')->middleware('auth');

// Session
Route::get('/login', [SessionController::class, 'create'])->name('login');
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);
