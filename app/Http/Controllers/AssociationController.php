<?php

namespace App\Http\Controllers;

use App\Models\Association;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AssociationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Association::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('domain')) {
            $query->where('domain', $request->domain);
        }
        
        $associations = $query->paginate(10);
        
        $domains = Association::select('domain')->distinct()->pluck('domain');
        
        return view('associations.index', compact('associations', 'domains'));
    }

    public function create(): View
    {
        return view('associations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:associations',
            'phone' => 'required|string|max:20',
            'domain' => 'required|string|max:255',
        ]);

        Association::create($request->all());

        return redirect()->route('associations.index')
            ->with('success', 'Association créée avec succès.');
    }

    public function show(Association $association): View
    {
        $association->load('projects');
        return view('associations.show', compact('association'));
    }

    public function edit(Association $association): View
    {
        return view('associations.edit', compact('association'));
    }

    public function update(Request $request, Association $association): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:associations,email,' . $association->id,
            'phone' => 'required|string|max:20',
            'domain' => 'required|string|max:255',
        ]);

        $association->update($request->all());

        return redirect()->route('associations.index')
            ->with('success', 'Association modifiée avec succès.');
    }

    public function destroy(Association $association): RedirectResponse
    {
        $association->delete();

        return redirect()->route('associations.index')
            ->with('success', 'Association supprimée avec succès.');
    }
}