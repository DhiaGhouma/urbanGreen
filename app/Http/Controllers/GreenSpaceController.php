<?php

namespace App\Http\Controllers;

use App\Models\GreenSpace;
use App\Services\EnvironmentalDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class GreenSpaceController extends Controller
{
    protected EnvironmentalDataService $envService;

    public function __construct(EnvironmentalDataService $envService)
    {
        $this->envService = $envService;
    }

    public function index(Request $request): View
    {
        $query = GreenSpace::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $greenSpaces = $query->paginate(12);
        $types = GreenSpace::select('type')->distinct()->pluck('type');
        $statuses = ['proposé', 'en cours', 'terminé'];

        return view('greenspaces.index', compact('greenSpaces', 'types', 'statuses'));
    }

    public function create(): View
    {
        return view('greenspaces.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'complexity_level' => 'required|in:débutant,intermédiaire,avancé',
            'surface' => 'nullable|numeric|min:0',
            'status' => 'required|in:proposé,en cours,terminé',
            'activities' => 'nullable|array',
        ]);

        // Geocode the location if coordinates not provided
        if (!$request->filled('latitude') || !$request->filled('longitude')) {
            $geocode = $this->envService->geocodeAddress($validated['location']);
            if ($geocode) {
                $validated['latitude'] = $geocode['latitude'];
                $validated['longitude'] = $geocode['longitude'];
            }
        }

        GreenSpace::create($validated);

        return redirect()->route('green-spaces.index')
            ->with('success', 'Espace vert créé avec succès.');
    }

    public function show(GreenSpace $greenspace): View
    {
        $greenspace->load(['projects.association', 'participations', 'plants']);

        // Get environmental data if coordinates are available
        $environmentalData = null;
        if ($greenspace->latitude && $greenspace->longitude) {
            $environmentalData = $this->envService->getComprehensiveData(
                $greenspace->latitude,
                $greenspace->longitude
            );
        }

        return view('greenspaces.show', compact('greenspace', 'environmentalData'));
    }

    public function edit(GreenSpace $greenspace): View
    {
        return view('greenspaces.edit', compact('greenspace'));
    }

    public function update(Request $request, GreenSpace $greenspace): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'complexity_level' => 'required|in:débutant,intermédiaire,avancé',
            'surface' => 'nullable|numeric|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|in:proposé,en cours,terminé',
            'activities' => 'nullable|array',
        ]);

        $greenspace->update($validated);

        return redirect()->route('green-spaces.show', $greenspace)
            ->with('success', 'Espace vert modifié avec succès.');
    }

    public function destroy(GreenSpace $greenspace): RedirectResponse
    {
        $greenspace->delete();

        return redirect()->route('green-spaces.index')
            ->with('success', 'Espace vert supprimé avec succès.');
    }

    /**
     * Get current weather for a green space
     */
    public function getWeather(GreenSpace $greenspace): JsonResponse
    {
        if (!$greenspace->latitude || !$greenspace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $weather = $this->envService->getCurrentWeather(
            $greenspace->latitude,
            $greenspace->longitude
        );

        if (!$weather) {
            return response()->json([
                'error' => 'Données météo non disponibles'
            ], 503);
        }

        return response()->json($weather);
    }

    /**
     * Get weather forecast for a green space
     */
    public function getForecast(GreenSpace $greenspace): JsonResponse
    {
        if (!$greenspace->latitude || !$greenspace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $forecast = $this->envService->getWeatherForecast(
            $greenspace->latitude,
            $greenspace->longitude
        );

        if (!$forecast) {
            return response()->json([
                'error' => 'Données de prévision non disponibles'
            ], 503);
        }

        return response()->json($forecast);
    }

    /**
     * Get air quality for a green space
     */
    public function getAirQuality(GreenSpace $greenspace): JsonResponse
    {
        if (!$greenspace->latitude || !$greenspace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $airQuality = $this->envService->getAirQuality(
            $greenspace->latitude,
            $greenspace->longitude
        );

        if (!$airQuality) {
            return response()->json([
                'error' => 'Données de qualité de l\'air non disponibles'
            ], 503);
        }

        return response()->json($airQuality);
    }

    /**
     * Get biodiversity data for a green space
     */
    public function getBiodiversity(GreenSpace $greenspace): JsonResponse
    {
        if (!$greenspace->latitude || !$greenspace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $biodiversity = $this->envService->getBiodiversityData(
            $greenspace->latitude,
            $greenspace->longitude,
            1
        );

        if (!$biodiversity) {
            return response()->json([
                'error' => 'Données de biodiversité non disponibles'
            ], 503);
        }

        return response()->json($biodiversity);
    }

    /**
     * Get species statistics for a green space
     */
    public function getSpeciesStats(GreenSpace $greenspace): JsonResponse
    {
        if (!$greenspace->latitude || !$greenspace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $stats = $this->envService->getSpeciesStats(
            $greenspace->latitude,
            $greenspace->longitude,
            1
        );

        if (!$stats) {
            return response()->json([
                'error' => 'Statistiques d\'espèces non disponibles'
            ], 503);
        }

        return response()->json($stats);
    }

    /**
     * Check if weather is suitable for activities
     */
    public function checkActivitySuitability(GreenSpace $greenspace): JsonResponse
    {
        if (!$greenspace->latitude || !$greenspace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $suitability = $this->envService->isWeatherSuitableForActivities(
            $greenspace->latitude,
            $greenspace->longitude
        );

        return response()->json($suitability);
    }

    /**
     * Geocode an address
     */
    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string'
        ]);

        $result = $this->envService->geocodeAddress($request->address);

        if (!$result) {
            return response()->json([
                'error' => 'Adresse non trouvée'
            ], 404);
        }

        return response()->json($result);
    }

    /**
     * Get comprehensive environmental dashboard data
     */
    public function getEnvironmentalDashboard(GreenSpace $greenspace): JsonResponse
    {
        if (!$greenspace->latitude || !$greenspace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $data = $this->envService->getComprehensiveData(
            $greenspace->latitude,
            $greenspace->longitude
        );

        $suitability = $this->envService->isWeatherSuitableForActivities(
            $greenspace->latitude,
            $greenspace->longitude
        );

        return response()->json([
            'green_space' => $greenspace->toAIFormat(),
            'environmental_data' => $data,
            'activity_suitability' => $suitability,
        ]);
    }
}
