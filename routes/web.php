<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GreenSpaceController;
use App\Http\Controllers\ExportDataController;
use App\Http\Controllers\ParticipationController;

Route::get('/', function () {
    return redirect()->route('associations.index');
});
Route::get('/home', function () {
    return view('landingpage.landing');
})->name('home');
Route::get('/team', function () {
    return view('team.team');
})->name('team');
Route::resource('associations', AssociationController::class);
Route::resource('projects', ProjectController::class);
Route::resource('greenspaces', GreenSpaceController::class);
Route::get('/export/projects', [ExportDataController::class, 'exportProjects']);
Route::post('/projects/recommend', [ProjectController::class, 'recommend']);

Route::resource('participations', ParticipationController::class);

// Additional route for updating participation status only
Route::patch('participations/{participation}/status', [ParticipationController::class, 'updateStatus'])
    ->name('participations.updateStatus');
