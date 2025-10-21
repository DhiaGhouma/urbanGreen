<?php

namespace App\Http\Controllers;

use App\Models\Participation;
use App\Models\User;
use App\Models\GreenSpace;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\FastAIRecommender;
use Illuminate\Http\JsonResponse;

class ParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Show only current user's participations (unless admin)
        $query = Participation::with(['user', 'greenSpace']);
        
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        
        // Apply filters
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        
        $participations = $query->orderBy('date', 'desc')->paginate(10);
        
        // Append query parameters to pagination links
        $participations->appends($request->query());

        return view('participations.index', compact('participations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // No need to pass users anymore - we'll use the logged-in user
        $greenSpaces = GreenSpace::orderBy('name')->get();

        return view('participations.create', compact('greenSpaces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'green_space_id' => 'required|exists:green_spaces,id',
            'date' => 'required|date|after_or_equal:today',
            'statut' => 'required|in:en_attente,confirmee,annulee,terminee'
        ]);

        // Automatically assign the logged-in user
        $validated['user_id'] = auth()->id();

        Participation::create($validated);

        return redirect()->route('participations.index')
            ->with('success', 'Votre participation a été créée avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Participation $participation): View
    {
    $participation->load(['user', 'greenSpace', 'feedback.user']);
        
        return view('participations.show', compact('participation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Participation $participation): View
    {
        // Only allow users to edit their own participations (unless admin)
        if (!auth()->user()->isAdmin() && $participation->user_id !== auth()->id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres participations.');
        }

        $greenSpaces = GreenSpace::orderBy('name')->get();

        return view('participations.edit', compact('participation', 'greenSpaces'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Participation $participation): RedirectResponse
    {
        // Only allow users to edit their own participations (unless admin)
        if (!auth()->user()->isAdmin() && $participation->user_id !== auth()->id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres participations.');
        }

        $validated = $request->validate([
            'green_space_id' => 'required|exists:green_spaces,id',
            'date' => 'required|date',
            'statut' => 'required|in:en_attente,confirmee,annulee,terminee'
        ]);

        // Don't allow regular users to change the user_id
        if (!auth()->user()->isAdmin()) {
            $validated['user_id'] = $participation->user_id; // Keep original user
        }

        $participation->update($validated);

        return redirect()->route('participations.index')
            ->with('success', 'Votre participation a été mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participation $participation): RedirectResponse
    {
        // Only allow users to delete their own participations (unless admin)
        if (!auth()->user()->isAdmin() && $participation->user_id !== auth()->id()) {
            abort(403, 'Vous ne pouvez supprimer que vos propres participations.');
        }

        $participation->delete();

        return redirect()->route('participations.index')
            ->with('success', 'Votre participation a été supprimée avec succès!');
    }

    /**
     * Update participation status only
     */
    public function updateStatus(Request $request, Participation $participation): RedirectResponse
    {
        // Only allow users to update their own participations (unless admin)
        if (!auth()->user()->isAdmin() && $participation->user_id !== auth()->id()) {
            abort(403, 'Vous ne pouvez modifier que vos propres participations.');
        }

        $validated = $request->validate([
            'statut' => 'required|in:en_attente,confirmee,annulee,terminee'
        ]);

        $participation->update($validated);

        return redirect()->back()
            ->with('success', 'Statut de la participation mis à jour avec succès!');
    }

    /**
     * Suggest the best greenspace for the current user using AI.
     * Uses FastAI server with sentence-transformers embeddings
     */
    public function suggest(Request $request, FastAIRecommender $recommender): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if AI server is available
        if (!$recommender->isAvailable()) {
            return response()->json([
                'error' => 'Le serveur IA n\'est pas disponible. Démarrez-le avec: php artisan ai:start-server'
            ], 503);
        }

        // Fetch all greenspaces with activities (regardless of status)
        $greenspaces = GreenSpace::query()
            ->whereNotNull('activities')
            ->where('activities', '!=', '')
            ->where('activities', '!=', '[]')
            ->orderBy('name')
            ->get();

        if ($greenspaces->isEmpty()) {
            return response()->json(['error' => 'Aucun espace vert disponible.'], 422);
        }

        try {
            $startTime = microtime(true);
            $result = $recommender->recommend($user, $greenspaces);
            $result['computation_time'] = round((microtime(true) - $startTime) * 1000, 2) . 'ms';
            
            \Log::info('AI Recommendation', [
                'user_id' => $user->id,
                'best_match_id' => $result['best_match_id'] ?? null,
                'score' => $result['score'] ?? null,
                'reason' => $result['reason'] ?? null,
                'engine' => $result['engine'] ?? null,
                'computation_time' => $result['computation_time'] ?? null,
                'top_rankings' => array_slice($result['rankings'] ?? [], 0, 3),
            ]);
            return response()->json($result);
        } catch (\Throwable $e) {
            \Log::error('AI Recommendation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
