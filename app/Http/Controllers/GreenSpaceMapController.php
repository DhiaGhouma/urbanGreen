<?php

namespace App\Http\Controllers;

use App\Models\GreenSpace;
use Illuminate\Http\Request;

class GreenSpaceMapController extends Controller
{
    public function show(GreenSpace $greenSpace)
    {
        // On suppose que 'location' contient une adresse textuelle (ou des coordonnées)
        return view('greenspaces.map', compact('greenSpace'));
    }
}
