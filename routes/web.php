<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GreenSpaceController;
use App\Http\Controllers\ExportDataController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\ParticipationFeedbackController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GreenSpacePlantsController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GreenSpaceMapController;
use App\Http\Controllers\PlantSuggestionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProjectMessageController;


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
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
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
// AI suggestion endpoint for participations
Route::get('participations/suggest/ai', [App\Http\Controllers\ParticipationController::class, 'suggest'])
    ->name('participations.suggest')
    ->middleware('auth');

// =============================================================================
// PROTECTED ROUTES (Require Authentication)
// =============================================================================

Route::middleware('auth.custom')->group(function () {
    // Resource routes
    Route::resource('associations', AssociationController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('greenspaces', GreenSpaceController::class);
    // Green Spaces Environmental Data API Routes
    Route::prefix('greenspaces')->group(function () {
    Route::get('{greenspace}/weather', [GreenSpaceController::class, 'getWeather'])->name('greenspaces.weather');
    Route::get('{greenspace}/air-quality', [GreenSpaceController::class, 'getAirQuality'])->name('greenspaces.air-quality');
    Route::get('{greenspace}/activity-suitability', [GreenSpaceController::class, 'checkActivitySuitability'])->name('greenspaces.activity-suitability');
    Route::get('{greenspace}/forecast', [GreenSpaceController::class, 'getForecast'])->name('greenspaces.forecast');
    Route::get('{greenspace}/biodiversity', [GreenSpaceController::class, 'getBiodiversity'])->name('greenspaces.biodiversity');
    Route::get('{greenspace}/species-stats', [GreenSpaceController::class, 'getSpeciesStats'])->name('greenspaces.species-stats');
    Route::get('{greenspace}/dashboard', [GreenSpaceController::class, 'getEnvironmentalDashboard'])->name('greenspaces.dashboard');
    Route::get('/plants/{latitude}/{longitude}', [PlantSuggestionController::class, 'getSuggestions']);
    });

Route::get('greenspaces/geocode', [GreenSpaceController::class, 'geocode'])->name('greenspaces.geocode');
    Route::resource('participations', ParticipationController::class);
    Route::post('participations/{participation}/feedback', [ParticipationFeedbackController::class, 'store'])
        ->name('participations.feedback.store');
    Route::match(['put', 'patch'], 'participations/{participation}/feedback', [ParticipationFeedbackController::class, 'update'])
        ->name('participations.feedback.update');
    Route::delete('participations/{participation}/feedback', [ParticipationFeedbackController::class, 'destroy'])
        ->name('participations.feedback.destroy');

    Route::get('/greenspaces/{greenSpace}/map', [GreenSpaceMapController::class, 'show'])
    ->name('greenspaces.map.show');    

    // Additional routes
    Route::get('/export/projects', [ExportDataController::class, 'exportProjects'])->name('export.projects');
    Route::post('/projects/recommend', [ProjectController::class, 'recommend'])->name('projects.recommend');
    Route::patch('participations/{participation}/status', [ParticipationController::class, 'updateStatus'])
        ->name('participations.updateStatus');

    // =============================================================================
    // PROJECT MESSAGES ROUTES (Moved here from admin section)
    // =============================================================================
    Route::post('projects/{project}/messages', [ProjectMessageController::class, 'store'])
        ->name('projects.messages.store');

    Route::delete('projects/{project}/messages/{message}', [ProjectMessageController::class, 'destroy'])
        ->name('projects.messages.destroy');
});

// =============================================================================
// EVENTS MODULE ROUTES
// =============================================================================

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/calendar/data', [EventController::class, 'calendarData'])->name('events.calendar-data');

Route::middleware('auth.custom')->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::patch('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/cancel-registration', [EventController::class, 'cancelRegistration'])->name('events.cancel-registration');
    Route::patch('/events/{event}/registrations/{registration}/status', [EventController::class, 'updateRegistrationStatus'])->name('events.update-registration-status');
});

Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::resource('greenspaces.plants', GreenSpacePlantsController::class);

// =============================================================================
// REPORTS MODULE ROUTES (Signalements & Maintenance)
// =============================================================================

// Routes publiques pour les signalements
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// Routes protégées pour les signalements
Route::middleware('auth.custom')->group(function () {
    // CRUD des signalements
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create'); // Création
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');          // Stockage
    Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit'); // Édition
    Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('reports.update'); // Mise à jour
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy'); // Suppression

    // Ajouter une mise à jour à un signalement
    Route::post('/reports/{report}/updates', [ReportController::class, 'addUpdate'])->name('reports.update.add');


    // Assigner un signalement à une association (Admin seulement)
    Route::post('/reports/{report}/assign', [ReportController::class, 'assign'])->name('reports.assign');

    Route::post('/reports/{report}/ai-refresh', [ReportController::class, 'refreshAI'])
    ->name('reports.ai-refresh');

});

// Route SHOW (doit toujours être après toutes les routes spécifiques)
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

// =============================================================================
// ADMIN ROUTES (Protected by admin middleware)
// =============================================================================

Route::middleware(['auth.custom', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'showUser'])->name('users.show');
    Route::patch('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/lock', [App\Http\Controllers\Admin\AdminController::class, 'lockUser'])->name('users.lock');
    Route::post('/users/{user}/unlock', [App\Http\Controllers\Admin\AdminController::class, 'unlockUser'])->name('users.unlock');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'destroyUser'])->name('users.destroy');

});
