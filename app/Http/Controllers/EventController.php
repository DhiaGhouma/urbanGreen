<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Association;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        return view('events.show', compact('event', 'userRegistration'));
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
}