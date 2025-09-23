<?php

namespace App\Http\Controllers;

use App\Models\GreenSpace;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GreenSpaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = GreenSpace::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $greenSpaces = $query->paginate(10);

        $types = GreenSpace::select('type')->distinct()->pluck('type');

        return view('greenspaces.index', compact('greenSpaces', 'types'));
    }

    public function create(): View
    {
        return view('greenspaces.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
        ]);

        GreenSpace::create($request->all());

        return redirect()->route('greenspaces.index')
            ->with('success', 'Espace vert créé avec succès.');
    }

    public function show(GreenSpace $greenSpace): View
    {
        $greenSpace->load('projects');
        return view('greenspaces.show', compact('greenSpace'));
    }

    public function edit(GreenSpace $greenSpace): View
    {
        return view('greenspaces.edit', compact('greenSpace'));
    }

    public function update(Request $request, GreenSpace $greenSpace): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
        ]);

        $greenSpace->update($request->all());

        return redirect()->route('greenspaces.index')
            ->with('success', 'Espace vert modifié avec succès.');
    }

    public function destroy(GreenSpace $greenSpace): RedirectResponse
    {
        $greenSpace->delete();

        return redirect()->route('greenspaces.index')
            ->with('success', 'Espace vert supprimé avec succès.');
    }
}
