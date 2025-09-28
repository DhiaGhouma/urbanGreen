<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ParticipationController;

Route::get('/', function () {
    return redirect()->route('associations.index');
});

Route::resource('associations', AssociationController::class);
Route::resource('projects', ProjectController::class);
Route::resource('participations', ParticipationController::class);

// Additional route for updating participation status only
Route::patch('participations/{participation}/status', [ParticipationController::class, 'updateStatus'])
    ->name('participations.updateStatus');