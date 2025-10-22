<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Association;
use App\Models\GreenSpace;
use App\Services\EnvironmentalDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ProjectController extends Controller
{
    protected EnvironmentalDataService $envService;

    public function __construct(EnvironmentalDataService $envService)
    {
        $this->envService = $envService;
    }

    public function index(Request $request): View
    {
        $query = Project::with(['association', 'greenSpace']);

        if ($request->filled('association_id')) {
            $query->where('association_id', $request->association_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $projects = $query->latest()->paginate(10);

        $associations = Association::all();
        $statuses = ['proposé', 'en cours', 'terminé'];

        return view('projects.index', compact('projects', 'associations', 'statuses'));
    }

    public function create(): View
    {
        $associations = Association::all();
        $greenSpaces = GreenSpace::all();

        return view('projects.create', compact('associations', 'greenSpaces'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_budget' => 'required|numeric|min:0',
            'status' => 'required|in:proposé,en cours,terminé',
            'association_id' => 'required|exists:associations,id',
            'green_space_id' => 'required|exists:green_spaces,id',
        ]);

        $project = Project::create($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    public function show(Project $project): View
    {
        $project->load(['association', 'greenSpace', 'messages']);

        // Get weather suitability for the project's green space
        $weatherSuitability = null;
        if ($project->greenSpace->latitude && $project->greenSpace->longitude) {
            $weatherSuitability = $this->envService->isWeatherSuitableForActivities(
                $project->greenSpace->latitude,
                $project->greenSpace->longitude
            );
        }

        return view('projects.show', compact('project', 'weatherSuitability'));
    }

    public function edit(Project $project): View
    {
        $associations = Association::all();
        $greenSpaces = GreenSpace::all();

        return view('projects.edit', compact('project', 'associations', 'greenSpaces'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_budget' => 'required|numeric|min:0',
            'status' => 'required|in:proposé,en cours,terminé',
            'association_id' => 'required|exists:associations,id',
            'green_space_id' => 'required|exists:green_spaces,id',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projet modifié avec succès.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }

    /**
     * Get AI recommendations for project
     */
    public function recommend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'status' => 'nullable|string',
            'green_space_type' => 'nullable|string',
            'association_domain' => 'nullable|string',
        ]);

        try {
            $response = Http::timeout(30)->post('http://127.0.0.1:5001/recommend', $validated);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Service de recommandation non disponible'
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des recommandations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get environmental impact data for project
     */
    public function getEnvironmentalImpact(Project $project): JsonResponse
    {
        $greenSpace = $project->greenSpace;

        if (!$greenSpace->latitude || !$greenSpace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles pour cet espace vert'
            ], 400);
        }

        $data = [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'status' => $project->status,
                'budget' => $project->estimated_budget,
            ],
            'green_space' => $greenSpace->toAIFormat(),
            'weather' => $this->envService->getCurrentWeather(
                $greenSpace->latitude,
                $greenSpace->longitude
            ),
            'air_quality' => $this->envService->getAirQuality(
                $greenSpace->latitude,
                $greenSpace->longitude
            ),
            'biodiversity' => $this->envService->getBiodiversityData(
                $greenSpace->latitude,
                $greenSpace->longitude
            ),
            'activity_suitability' => $this->envService->isWeatherSuitableForActivities(
                $greenSpace->latitude,
                $greenSpace->longitude
            ),
        ];

        return response()->json($data);
    }

    /**
     * Get weather forecast for project planning
     */
    public function getProjectWeatherForecast(Project $project): JsonResponse
    {
        $greenSpace = $project->greenSpace;

        if (!$greenSpace->latitude || !$greenSpace->longitude) {
            return response()->json([
                'error' => 'Coordonnées GPS non disponibles'
            ], 400);
        }

        $forecast = $this->envService->getWeatherForecast(
            $greenSpace->latitude,
            $greenSpace->longitude
        );

        if (!$forecast) {
            return response()->json([
                'error' => 'Données de prévision non disponibles'
            ], 503);
        }

        // Analyze forecast for project planning
        $analysis = $this->analyzeWeatherForProjectPlanning($forecast);

        return response()->json([
            'forecast' => $forecast,
            'analysis' => $analysis,
            'project_id' => $project->id,
        ]);
    }

    /**
     * Analyze weather forecast for project planning
     */
    private function analyzeWeatherForProjectPlanning(array $forecast): array
    {
        $suitableDays = 0;
        $unsuitable = [];
        $recommendations = [];

        foreach ($forecast as $day) {
            $dayRating = [
                'date' => $day['date_formatted'],
                'suitable' => true,
                'reasons' => []
            ];

            foreach ($day['items'] as $item) {
                if ($item['temperature'] < 5 || $item['temperature'] > 35) {
                    $dayRating['suitable'] = false;
                    $dayRating['reasons'][] = "Température extrême";
                }

                if ($item['wind_speed'] > 30) {
                    $dayRating['suitable'] = false;
                    $dayRating['reasons'][] = "Vent trop fort";
                }

                if (stripos($item['condition'], 'pluie') !== false ||
                    stripos($item['condition'], 'orage') !== false) {
                    $dayRating['suitable'] = false;
                    $dayRating['reasons'][] = "Précipitations";
                }
            }

            if ($dayRating['suitable']) {
                $suitableDays++;
            } else {
                $unsuitable[] = $dayRating;
            }
        }

        if ($suitableDays >= 3) {
            $recommendations[] = "Conditions favorables pour planifier les activités";
        } else {
            $recommendations[] = "Peu de jours favorables - envisager de reporter";
        }

        return [
            'suitable_days_count' => $suitableDays,
            'total_days' => count($forecast),
            'unsuitable_days' => $unsuitable,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Get dashboard data for project monitoring
     */
    public function getDashboard(Project $project): JsonResponse
    {
        $greenSpace = $project->greenSpace;

        $dashboardData = [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status,
                'budget' => $project->estimated_budget,
                'created_at' => $project->created_at->format('d/m/Y'),
            ],
            'association' => [
                'id' => $project->association->id,
                'name' => $project->association->name,
                'domain' => $project->association->domain,
            ],
            'green_space' => $greenSpace->toAIFormat(),
            'statistics' => [
                'messages_count' => $project->messages()->count(),
                'days_since_creation' => $project->created_at->diffInDays(now()),
            ],
        ];

        // Add environmental data if coordinates available
        if ($greenSpace->latitude && $greenSpace->longitude) {
            $dashboardData['environmental'] = [
                'current_weather' => $this->envService->getCurrentWeather(
                    $greenSpace->latitude,
                    $greenSpace->longitude
                ),
                'air_quality' => $this->envService->getAirQuality(
                    $greenSpace->latitude,
                    $greenSpace->longitude
                ),
                'activity_suitability' => $this->envService->isWeatherSuitableForActivities(
                    $greenSpace->latitude,
                    $greenSpace->longitude
                ),
            ];
        }

        return response()->json($dashboardData);
    }
}
