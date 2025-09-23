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
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $greenSpaces = $query->paginate(10);

        $types = GreenSpace::select('type')->distinct()->pluck('type');
        $statuses = GreenSpace::select('status')->distinct()->pluck('status');

        return view('greenspaces.index', compact('greenSpaces', 'types', 'statuses'));
    }

    public function create(): View
    {
        return view('greenspaces.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|string|max:255',
            'surface'       => 'nullable|numeric|min:0',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'status'        => 'required|string|in:proposé,en cours,terminé',
            'photos_before' => 'nullable|array',
            'photos_before.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photos_after'  => 'nullable|array',
            'photos_after.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle file uploads
        if ($request->hasFile('photos_before')) {
            $validated['photos_before'] = array_map(fn($file) => $file->store('greenspaces/photos_before', 'public'), $request->file('photos_before'));
        }
        if ($request->hasFile('photos_after')) {
            $validated['photos_after'] = array_map(fn($file) => $file->store('greenspaces/photos_after', 'public'), $request->file('photos_after'));
        }

        GreenSpace::create($validated);

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
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|string|max:255',
            'surface'       => 'nullable|numeric|min:0',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'status'        => 'required|string|in:proposé,en cours,terminé',
            'photos_before' => 'nullable|array',
            'photos_before.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photos_after'  => 'nullable|array',
            'photos_after.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photos_before')) {
            $validated['photos_before'] = array_map(fn($file) => $file->store('greenspaces/photos_before', 'public'), $request->file('photos_before'));
        }

        if ($request->hasFile('photos_after')) {
            $validated['photos_after'] = array_map(fn($file) => $file->store('greenspaces/photos_after', 'public'), $request->file('photos_after'));
        }

        $greenSpace->update($validated);

        return redirect()->route('greenspaces.index')
            ->with('success', 'Espace vert mis à jour avec succès.');
    }

    public function destroy(GreenSpace $greenSpace): RedirectResponse
    {
        $greenSpace->delete();

        return redirect()->route('greenspaces.index')
            ->with('success', 'Espace vert supprimé avec succès.');
    }
}
