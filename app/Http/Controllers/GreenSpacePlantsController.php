<?php

namespace App\Http\Controllers;

use App\Models\GreenSpace;
use App\Models\GreenSpacePlant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GreenSpacePlantsController extends Controller
{
    // Liste toutes les plantes pour un espace vert donné
    public function index(GreenSpace $greenspace): View
    {
        $plants = $greenspace->plants()->paginate(10);
        return view('plants.index', compact('greenspace', 'plants'));
    }

    // Formulaire de création d'une plante
    public function create(GreenSpace $greenspace): View
    {
        return view('plants.create', compact('greenspace'));
    }

    // Enregistrer une nouvelle plante
    public function store(Request $request, GreenSpace $greenspace): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'species'     => 'nullable|string|max:255',
            'quantity'    => 'nullable|integer|min:1',
            'planted_at'  => 'nullable|date',
            'maintenance' => 'nullable|string|max:255',
            'status'      => 'required|string|in:en vie,malade,abattu',
            'notes'       => 'nullable|string',
        ]);

        $greenspace->plants()->create($validated);

        return redirect()->route('greenspaces.plants.index', $greenspace->id)
                         ->with('success', 'Plante ajoutée avec succès.');
    }

    // Formulaire d'édition d'une plante
    public function edit(GreenSpace $greenspace, GreenSpacePlant $plant): View
    {
        return view('plants.edit', compact('greenspace', 'plant'));
    }

    // Mettre à jour une plante
    public function update(Request $request, GreenSpace $greenspace, GreenSpacePlant $plant): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'species'     => 'nullable|string|max:255',
            'quantity'    => 'nullable|integer|min:1',
            'planted_at'  => 'nullable|date',
            'maintenance' => 'nullable|string|max:255',
            'status'      => 'required|string|in:en vie,malade,abattu',
            'notes'       => 'nullable|string',
        ]);

        $plant->update($validated);

        return redirect()->route('greenspaces.plants.index', $greenspace->id)
                         ->with('success', 'Plante mise à jour avec succès.');
    }

    // Supprimer une plante
    public function destroy(GreenSpace $greenspace, GreenSpacePlant $plant): RedirectResponse
    {
        $plant->delete();

        return redirect()->route('greenspaces.plants.index', $greenspace->id)
                         ->with('success', 'Plante supprimée avec succès.');
    }

    public function show(GreenSpace $greenspace, GreenSpacePlant $plant)
{
    return view('plants.show', compact('greenspace', 'plant'));
}
}
