<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GreenSpaceController;
use App\Http\Controllers\ExportDataController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GreenSpacePlantsController;

// =============================================================================
// AUTHENTICATION ROUTES
// =============================================================================

// Guest routes (accessible only when not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('auth.login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:register')
        ->name('auth.register.post');
});

// Authenticated routes (accessible only when logged in)
Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('auth.dashboard');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('auth.profile.update');
    Route::patch('/password', [AuthController::class, 'changePassword'])->name('auth.password.change');
});

// =============================================================================
// PUBLIC ROUTES
// =============================================================================

Route::get('/', function () {
    return redirect()->route('associations.index');
});
Route::get('/home', function () {
    return view('landingpage.landing');
})->name('home');
Route::get('/team', function () {
    return view('team.team');
})->name('team');
// =============================================================================
// PROTECTED ROUTES (Require Authentication)
// =============================================================================

Route::middleware('auth.custom')->group(function () {
    // Resource routes
    Route::resource('associations', AssociationController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('greenspaces', GreenSpaceController::class);
    Route::resource('participations', ParticipationController::class);
    
    // Additional routes
    Route::get('/export/projects', [ExportDataController::class, 'exportProjects'])->name('export.projects');
    Route::post('/projects/recommend', [ProjectController::class, 'recommend'])->name('projects.recommend');
    Route::patch('participations/{participation}/status', [ParticipationController::class, 'updateStatus'])
        ->name('participations.updateStatus');
});

Route::resource('greenspaces.plants', GreenSpacePlantsController::class);




