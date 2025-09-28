<?php

namespace App\Http\Controllers;

use App\Models\Participation;
use App\Models\User;
use App\Models\GreenSpace;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Participation::with(['user', 'greenSpace']);

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrer par date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $participations = $query->orderBy('date', 'desc')->paginate(10);

        return view('participations.index', compact('participations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $greenSpaces = GreenSpace::orderBy('name')->get();

        return view('participations.create', compact('users', 'greenSpaces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'green_space_id' => 'required|exists:green_spaces,id',
            'date' => 'required|date|after_or_equal:today',
            'statut' => 'required|in:en_attente,confirmee,annulee,terminee'
        ]);

        Participation::create($validated);

        return redirect()->route('participations.index')
            ->with('success', 'Participation créée avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Participation $participation): View
    {
        $participation->load(['user', 'greenSpace']);
        
        return view('participations.show', compact('participation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Participation $participation): View
    {
        $users = User::orderBy('name')->get();
        $greenSpaces = GreenSpace::orderBy('name')->get();

        return view('participations.edit', compact('participation', 'users', 'greenSpaces'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Participation $participation): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'green_space_id' => 'required|exists:green_spaces,id',
            'date' => 'required|date',
            'statut' => 'required|in:en_attente,confirmee,annulee,terminee'
        ]);

        $participation->update($validated);

        return redirect()->route('participations.index')
            ->with('success', 'Participation mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participation $participation): RedirectResponse
    {
        $participation->delete();

        return redirect()->route('participations.index')
            ->with('success', 'Participation supprimée avec succès!');
    }

    /**
     * Update participation status only
     */
    public function updateStatus(Request $request, Participation $participation): RedirectResponse
    {
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,confirmee,annulee,terminee'
        ]);

        $participation->update($validated);

        return redirect()->back()
            ->with('success', 'Statut de la participation mis à jour!');
    }
}
