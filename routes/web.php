<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GreenSpaceController;
use App\Http\Controllers\GreenSpacePlantsController;

Route::get('/', function () {
    return redirect()->route('associations.index');
});

Route::resource('associations', AssociationController::class);
Route::resource('projects', ProjectController::class);
Route::resource('greenspaces', GreenSpaceController::class);
Route::resource('greenspaces.plants', GreenSpacePlantsController::class);




