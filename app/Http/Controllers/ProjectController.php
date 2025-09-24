<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Association;
use App\Models\GreenSpace;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::with(['association', 'greenSpace']);

        if ($request->filled('association_id')) {
            $query->where('association_id', $request->association_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->paginate(10);

        $associations = Association::all();
        $statuses = ['proposé', 'en cours', 'terminé'];

        return view('projects.index', compact('projects', 'associations', 'statuses'));
    }

    public function create(): View
    {
        $associations = Association::all();
        $greenSpaces = GreenSpace::all();

        return view('projects.create', compact('associations', 'greenSpaces'));
    }
    public function recommend(Request $request)
    {
    $response = Http::post('http://127.0.0.1:5001/recommend', [
        'description' => $request->description,
        'status' => $request->status,
        'green_space_type' => $request->green_space_type,
        'association_domain' => $request->association_domain,
    ]);

    return $response->json();
    }
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_budget' => 'required|numeric|min:0',
            'status' => 'required|in:proposé,en cours,terminé',
            'association_id' => 'required|exists:associations,id',
            'green_space_id' => 'required|exists:green_spaces,id',
        ]);

        Project::create($request->all());

        return redirect()->route('projects.index')
            ->with('success', 'Projet créé avec succès.');
    }

    public function show(Project $project): View
    {
        $project->load(['association', 'greenSpace']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $associations = Association::all();
        $greenSpaces = GreenSpace::all();

        return view('projects.edit', compact('project', 'associations', 'greenSpaces'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_budget' => 'required|numeric|min:0',
            'status' => 'required|in:proposé,en cours,terminé',
            'association_id' => 'required|exists:associations,id',
            'green_space_id' => 'required|exists:green_spaces,id',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')
            ->with('success', 'Projet modifié avec succès.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }
}
