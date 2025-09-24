<?php
// app/Http/Controllers/ExportDataController.php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Response;

class ExportDataController extends Controller
{
    public function exportProjects()
    {
        $projects = Project::with(['association', 'greenSpace'])->get();

        return Response::json($projects);
    }
}
