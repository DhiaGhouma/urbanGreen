<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GreenSpaceController;

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
