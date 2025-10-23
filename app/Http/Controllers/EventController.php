<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Association;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Log;
use OpenAI;
use Carbon\Carbon;


class EventController extends Controller
{
    /**
     * Display a listing of the events with calendar view.
     */
    public function index(Request $request)
    {
        $query = Event::with(['association', 'registrations']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filter by association
        if ($request->filled('association_id')) {
            $query->where('association_id', $request->association_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date_debut', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_debut', '<=', $request->date_to);
        }

        // View mode
        $viewMode = $request->get('view', 'list');

        if ($viewMode === 'calendar') {
            // Get all events for calendar
            $events = $query->orderBy('date_debut', 'asc')->get();
        } else {
            // Paginate for list view
            $events = $query->orderBy('date_debut', 'desc')->paginate(12);
        }

        $associations = Association::orderBy('name')->get();
        $stats = [
            'total' => Event::count(),
            'upcoming' => Event::upcoming()->count(),
            'past' => Event::past()->count(),
        ];

        return view('events.index', compact('events', 'associations', 'stats', 'viewMode'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $associations = Association::orderBy('name')->get();
        return view('events.create', compact('associations'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:plantation,conference,atelier',
            'date_debut' => 'required|date|after:now',
            'date_fin' => 'nullable|date|after:date_debut',
            'lieu' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'capacite_max' => 'nullable|integer|min:1',
            'association_id' => 'required|exists:associations,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        // Set places disponibles same as capacite_max initially
        if (isset($validated['capacite_max'])) {
            $validated['places_disponibles'] = $validated['capacite_max'];
        }

        $event = Event::create($validated);

        return redirect()->route('events.show', $event)
                        ->with('success', 'Événement créé avec succès !');
    }

    /**
     * Display the specified event.
     */
public function show(Event $event)
{
    $event->load(['association', 'registrations.user']);
    
    $userRegistration = null;
    if (Auth::check()) {
        $userRegistration = $event->registrations()
                                 ->where('user_id', Auth::id())
                                 ->first();
    }

    // Obtenir la météo
    $weatherService = new WeatherService();
    $forecast = null;
    $weatherAnalysis = null;
    
    // Vérifier si l'événement est dans le futur (moins de 16 jours)
    $daysUntilEvent = now()->diffInDays($event->date_debut, false);
    
    if ($daysUntilEvent >= 0 && $daysUntilEvent <= 16) {
        $forecast = $weatherService->getForecast(
            $event->lieu . ($event->adresse ? ', ' . $event->adresse : ''),
            $event->date_debut->format('Y-m-d H:i:s')
        );
        
        if ($forecast) {
            $weatherAnalysis = $weatherService->isFavorableWeather($forecast);
            $weatherAnalysis['icon_url'] = $weatherService->getWeatherIconUrl($forecast['icon']);
            $weatherAnalysis['should_reschedule'] = $weatherService->shouldSuggestReschedule($forecast);
        }
    }

    return view('events.show', compact('event', 'userRegistration', 'forecast', 'weatherAnalysis'));
}

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        $associations = Association::orderBy('name')->get();
        return view('events.edit', compact('event', 'associations'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:plantation,conference,atelier',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'lieu' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'capacite_max' => 'nullable|integer|min:1',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'association_id' => 'required|exists:associations,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('events.show', $event)
                        ->with('success', 'Événement mis à jour avec succès !');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        // Delete image if exists
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('events.index')
                        ->with('success', 'Événement supprimé avec succès !');
    }

    /**
     * Register user for an event.
     */
    public function register(Request $request, Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')
                           ->with('error', 'Vous devez être connecté pour vous inscrire.');
        }

        // Check if event is full
        if ($event->isFull()) {
            return back()->with('error', 'Cet événement est complet.');
        }

        // Check if user is already registered
        if ($event->isUserRegistered(Auth::id())) {
            return back()->with('warning', 'Vous êtes déjà inscrit à cet événement.');
        }

        // Check if event is in the future
        if ($event->date_debut->isPast()) {
            return back()->with('error', 'Vous ne pouvez pas vous inscrire à un événement passé.');
        }

        $validated = $request->validate([
            'commentaire' => 'nullable|string|max:500',
        ]);

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'commentaire' => $validated['commentaire'] ?? null,
            'statut' => 'en_attente',
        ]);

        return back()->with('success', 'Inscription effectuée avec succès ! En attente de confirmation.');
    }

    /**
     * Cancel user registration.
     */
    public function cancelRegistration(Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        $registration = $event->registrations()
                             ->where('user_id', Auth::id())
                             ->first();

        if (!$registration) {
            return back()->with('error', 'Inscription non trouvée.');
        }

        if (!$registration->canBeCancelled()) {
            return back()->with('error', 'Cette inscription ne peut pas être annulée.');
        }

        $registration->update(['statut' => 'annulee']);

        return back()->with('success', 'Inscription annulée avec succès.');
    }

    /**
     * Update registration status (admin only).
     */
    public function updateRegistrationStatus(Request $request, Event $event, EventRegistration $registration)
    {
        $validated = $request->validate([
            'statut' => 'required|in:en_attente,confirmee,annulee',
        ]);

        $registration->update($validated);

        return back()->with('success', 'Statut de l\'inscription mis à jour avec succès.');
    }

    /**
     * Get events for calendar JSON.
     */
    public function calendarData(Request $request)
    {
        $events = Event::with('association')
                      ->where('statut', '!=', 'annule')
                      ->get()
                      ->map(function($event) {
                          return [
                              'id' => $event->id,
                              'title' => $event->titre,
                              'start' => $event->date_debut->toIso8601String(),
                              'end' => $event->date_fin ? $event->date_fin->toIso8601String() : $event->date_debut->toIso8601String(),
                              'url' => route('events.show', $event),
                              'backgroundColor' => $this->getEventColor($event->type),
                              'borderColor' => $this->getEventColor($event->type),
                              'extendedProps' => [
                                  'type' => $event->type,
                                  'lieu' => $event->lieu,
                                  'association' => $event->association->name,
                              ],
                          ];
                      });

        return response()->json($events);
    }

    /**
     * Get color for event type.
     */
    private function getEventColor(string $type): string
    {
        return match($type) {
            'plantation' => '#28a745',
            'conference' => '#007bff',
            'atelier' => '#17a2b8',
            default => '#6c757d',
        };
    }

/**
 * Generate event description using Google Gemini AI (FREE & POWERFUL)
 */
public function generateDescription(Request $request)
{
    $validated = $request->validate([
        'titre' => 'required|string',
        'type' => 'required|string',
        'lieu' => 'required|string',
        'date_debut' => 'nullable|string',
    ]);

    try {
        Log::info('=== DÉBUT GÉNÉRATION AVEC GOOGLE GEMINI ===');

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new \Exception('GEMINI_API_KEY non configurée dans .env');
        }

        // Construire le prompt
        $prompt = $this->buildPrompt($validated);
        Log::info('Prompt construit: ' . $prompt);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiKey}";

        // Appel API Gemini
        $response = \Illuminate\Support\Facades\Http::timeout(60)
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 300,
                ]
            ]);

        if ($response->failed()) {
            Log::error('Erreur API Gemini: ' . $response->body());
            $errorData = $response->json() ?: [];
            $errorMessage = $errorData['error']['message'] ?? 'Erreur inconnue';
            throw new \Exception("Erreur API Gemini: {$errorMessage}");
        }

        $data = $response->json();
        Log::info('Réponse complète Gemini: ' . json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));

        // ✅ CORRECTION : Extraction correcte du texte généré
        $description = null;
        
        // Structure standard de Gemini : candidates[0].content.parts[0].text
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $description = $data['candidates'][0]['content']['parts'][0]['text'];
        }
        // Fallback 1
        elseif (isset($data['candidates'][0]['content'][0]['text'])) {
            $description = $data['candidates'][0]['content'][0]['text'];
        }
        // Fallback 2
        elseif (isset($data['candidates'][0]['output'])) {
            $description = $data['candidates'][0]['output'];
        }

        // Nettoyage et validation
        $description = trim($description ?? '');
        
        if (empty($description)) {
            Log::warning('Aucune description extraite de la réponse Gemini');
            throw new \Exception('L\'IA n\'a pas pu générer de description. Veuillez réessayer.');
        }

        Log::info('✅ Description générée avec succès (longueur: ' . mb_strlen($description) . ')');

        return response()->json([
            'success' => true,
            'description' => $description
        ]);

    } catch (\Exception $e) {
        Log::error('=== ERREUR GÉNÉRATION IA ===');
        Log::error('Message: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());

        $message = $e->getMessage();

        // Messages d'erreur personnalisés
        if (str_contains($message, 'API key not valid') || str_contains($message, 'API_KEY_INVALID')) {
            $message = '🔑 Clé API Gemini invalide. Vérifiez votre GEMINI_API_KEY dans le fichier .env';
        } elseif (str_contains($message, 'quota')) {
            $message = '⏳ Quota Gemini atteint. Attendez quelques minutes avant de réessayer.';
        } elseif (str_contains($message, 'timeout')) {
            $message = '⏰ Délai d\'attente dépassé. Réessayez dans quelques instants.';
        }

        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}

/**
 * Build the AI prompt based on event data
 */
private function buildPrompt($data)
{
    $typeLabels = [
        'plantation' => 'plantation d\'arbres',
        'conference' => 'conférence',
        'atelier' => 'atelier pratique'
    ];
    
    $typeLabel = $typeLabels[$data['type']] ?? $data['type'];
    
    $prompt = "Rédige une description professionnelle et engageante en français (2-3 phrases maximum) pour un événement intitulé \"{$data['titre']}\" qui est un événement de type {$typeLabel} qui se tiendra à {$data['lieu']}.";
    
    if (!empty($data['date_debut'])) {
        try {
            $date = \Carbon\Carbon::parse($data['date_debut'])->locale('fr')->isoFormat('LL');
            $prompt .= " L'événement aura lieu le {$date}.";
        } catch (\Exception $e) {
            // Si parsing échoue, on continue sans la date
        }
    }
    
    $prompt .= " La description doit être informative et donner envie de participer.";
    
    return $prompt;
}
}