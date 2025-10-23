<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportUpdate;
use App\Models\Association;
use App\Models\GreenSpace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ReportController extends Controller
{
    /**
     * Affiche la liste des signalements
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'greenSpace', 'updates']);

        // Filtrer par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrer par espace vert
        if ($request->filled('green_space_id')) {
            $query->where('green_space_id', $request->green_space_id);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderBy('date_signalement', 'desc')->paginate(10);
        $greenSpaces = GreenSpace::orderBy('name')->get();

        return view('reports.index', compact('reports', 'greenSpaces'));
    }

    /**
     * Formulaire de création d'un signalement
     */
    public function create()
    {
        $greenSpaces = GreenSpace::orderBy('name')->get();
        return view('reports.create', compact('greenSpaces'));
    }

    /**
     * Enregistrer un nouveau signalement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
    'title' => 'required|string|max:255',
    'green_space_id' => 'required|exists:green_spaces,id',
    'description' => 'required|string',
    'category' => 'required|string|in:dechets,plantes_mortes,vandalisme,equipement,autre',
    'priority' => 'required|string|in:basse,normale,haute,urgente',
    'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
    'latitude' => 'nullable|numeric',
    'longitude' => 'nullable|numeric',
    'statut' => 'nullable|in:en_attente,en_cours,resolu',
]);


        $validated['user_id'] = Auth::id();
        $validated['statut'] = $validated['statut'] ?? 'en_attente';
        $validated['date_signalement'] = now();

        // Upload de photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('reports', 'public');
        }

        $report = Report::create($validated);

        // Générer la recommandation via Gemini
        $recommendedAction = $this->generateRecommendedAction($report);

        if ($recommendedAction) {
            $report->setRecommendedAction($recommendedAction);
        }

        return redirect()->route('reports.show', $report)
                 ->with('success', 'Signalement créé avec succès.');
    }

    /**
     * Afficher un signalement spécifique
     */
    public function show(Report $report)
{
    $report->load(['user', 'greenSpace', 'updates']);
    $associations = Association::all(); // Charge toutes les associations
    return view('reports.show', compact('report', 'associations'));
}


    /**
     * Formulaire d'édition d'un signalement
     */
    public function edit(Report $report)
    {
        $greenSpaces = GreenSpace::orderBy('name')->get();
        return view('reports.edit', compact('report', 'greenSpaces'));
    }

    /**
     * Mettre à jour un signalement
     */
    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'green_space_id' => 'required|exists:green_spaces,id',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'statut' => 'required|in:en_attente,en_cours,resolu',
        ]);

        // Upload nouvelle photo
        if ($request->hasFile('photo')) {
            if ($report->photo) {
                Storage::disk('public')->delete($report->photo);
            }
            $validated['photo'] = $request->file('photo')->store('reports', 'public');
        }

        $report->update($validated);

        return redirect()->route('reports.show', $report)
                         ->with('success', 'Signalement mis à jour avec succès.');
    }

    /**
     * Supprimer un signalement
     */
    public function destroy(Report $report)
    {
        if ($report->photo) {
            Storage::disk('public')->delete($report->photo);
        }

        $report->delete();

        return redirect()->route('reports.index')
                         ->with('success', 'Signalement supprimé avec succès.');
    }

    /**
     * Ajouter une mise à jour à un signalement
     */
    public function addUpdate(Request $request, Report $report)
    {
        $validated = $request->validate([
            'commentaire' => 'nullable|string',
            'statut' => 'required|in:en_attente,en_cours,resolu',
        ]);

        $validated['report_id'] = $report->id;
        $validated['date_update'] = now();

        ReportUpdate::create($validated);

        // Mettre à jour le statut du signalement
        $report->update(['statut' => $validated['statut']]);

        return redirect()->route('reports.show', $report)
                         ->with('success', 'Mise à jour ajoutée avec succès.');
    }

    /**
     * Assigner un signalement à une association ou mairie (Admin)
     */
    public function assign(Request $request, Report $report)
    {
        $validated = $request->validate([
            'association_id' => 'required|exists:associations,id',
        ]);

        $report->update([
            'association_id' => $validated['association_id'],
            'statut' => 'en_cours',
        ]);

        return redirect()->route('reports.show', $report)
                         ->with('success', 'Signalement assigné avec succès.');
    }

private function generateRecommendedAction(Report $report, array &$logs = []): ?string
{
    $logs[] = "🔹 Début génération recommandation IA pour report ID {$report->id}";

    try {
        $response = Http::withOptions([
            'verify' => false // 🔹 désactive la vérification SSL pour dev local
        ])->withHeaders([
            'x-goog-api-key' => env('GEMINI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "Voici la description du signalement : {$report->description}. Donne une recommandation d'action appropriée et courte."
                        ]
                    ]
                ]
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $recommended = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            $logs[] = "✅ Réponse Gemini: " . ($recommended ?? 'NULL');
            return $recommended;
        }

        $logs[] = "⚠️ Gemini API non réussie: " . $response->body();
        return null;

    } catch (\Exception $e) {
        $logs[] = "❌ Exception Gemini API: " . $e->getMessage();
        return null;
    }
}

public function refreshAI(Report $report)
{
    $logs = [];
    $logs[] = "🔹 refreshAI appelé pour report ID {$report->id} par user ID " . auth()->id();

    if (!auth()->user()->isAdmin() && $report->user_id !== auth()->id()) {
        $logs[] = "⚠️ Accès refusé pour refreshAI, user ID " . auth()->id();
        return response()->json([
            'recommended_action' => null,
            'logs' => $logs,
            'error' => 'Accès refusé'
        ], 403);
    }

    $recommendedAction = $this->generateRecommendedAction($report, $logs);

    if ($recommendedAction) {
        $report->setRecommendedAction($recommendedAction);
        $logs[] = "✅ Recommandation IA mise à jour pour report ID {$report->id}";

        return response()->json([
            'recommended_action' => $recommendedAction,
            'logs' => $logs
        ]);
    }

    $logs[] = "❌ Erreur IA pour report ID {$report->id}";

    return response()->json([
        'recommended_action' => 'Erreur IA, réessayez.',
        'logs' => $logs
    ], 500);
}

    
}
