<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return redirect()->route('associations.index');
});

Route::resource('associations', AssociationController::class);
Route::resource('projects', ProjectController::class);