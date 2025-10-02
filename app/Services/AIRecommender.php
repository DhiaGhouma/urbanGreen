<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AIRecommender
{
    protected OllamaService $ollamaService;
    protected string $pythonBin;
    protected string $scriptPath;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
        $this->pythonBin = env('PYTHON_BIN', 'python');
        // Use wrapper to fix Windows Python 3.12 asyncio bug with sentence-transformers
        $this->scriptPath = base_path('ai_wrapper.py');
    }

    /**
     * Recommend the best green space for a user
     * 
     * @param User $user The user to recommend for
     * @param Collection $greenSpaces Available green spaces
     * @return array Recommendation result with best_match_id, score, reason
     * @throws \Exception
     */
    public function recommend(User $user, Collection $greenSpaces): array
    {
        // Validate Ollama availability
        if (!$this->ollamaService->isAvailable()) {
            Log::warning('Ollama is not available, will use embeddings fallback');
        }

        // Prepare user data
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'preferences' => $user->preferences ?? $this->getDefaultPreferences(),
        ];

        // Prepare green spaces data
        $greenSpacesData = $greenSpaces->map(function ($gs) {
            return [
                'id' => $gs->id,
                'name' => $gs->name,
                'description' => $gs->description ?? '',
                'activities' => $gs->activities ?? [],
                'type' => $gs->type ?? '',
                'location' => $gs->location ?? '',
            ];
        })->values()->toArray();

        // Validate inputs
        if (empty($greenSpacesData)) {
            throw new \Exception('Aucun espace vert disponible');
        }

        // Call Python script via Process
        try {
            $result = $this->callPythonRecommender($userData, $greenSpacesData);
            return $result;
        } catch (\Exception $e) {
            Log::error('AI Recommendation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Call Python recommender script
     * 
     * @param array $userData User data with preferences
     * @param array $greenSpacesData Array of green spaces
     * @return array Recommendation result
     * @throws \Exception
     */
    protected function callPythonRecommender(array $userData, array $greenSpacesData): array
    {
        // Encode arguments as JSON
        $userJson = json_encode($userData, JSON_UNESCAPED_UNICODE);
        $greenSpacesJson = json_encode($greenSpacesData, JSON_UNESCAPED_UNICODE);

        // Create process with environment variables to fix Windows Python 3.12 asyncio bug
        $process = new Process([
            $this->pythonBin,
            $this->scriptPath,
            $userJson,
            $greenSpacesJson,
        ]);

        // Set environment variables for Windows Python 3.12 fix
        $env = $_ENV;
        $env['TQDM_DISABLE'] = '1';
        $env['PYTHONIOENCODING'] = 'utf-8';
        $env['PYTHONUTF8'] = '1'; // Force UTF-8 mode
        $process->setEnv($env);

        // Set timeout (120 seconds for embeddings + Ollama reasoning)
        $process->setTimeout(120);

        // Run the process
        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            $errorOutput = $process->getErrorOutput();
            Log::error('Python recommender process failed', [
                'error' => $exception->getMessage(),
                'stderr' => $errorOutput,
            ]);
            throw new \Exception('Erreur lors de l\'exécution du recommandeur IA: ' . $errorOutput);
        }

        // Get and parse output
        $output = $process->getOutput();
        
        try {
            $result = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Log::error('Failed to parse Python script output', [
                'output' => $output,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Réponse invalide du recommandeur IA');
        }

        // Check for error in result
        if (isset($result['error'])) {
            throw new \Exception($result['error']);
        }

        // Validate result structure
        if (!isset($result['best_match_id'])) {
            throw new \Exception('Résultat de recommandation invalide (best_match_id manquant)');
        }

        return $result;
    }

    /**
     * Get default preferences if user has none
     */
    protected function getDefaultPreferences(): array
    {
        return [
            'interests' => ['nature', 'environnement'],
            'preferred_activities' => ['participation'],
            'availability' => 'flexible',
            'experience_level' => 'débutant',
        ];
    }

    /**
     * Simple keyword-based fallback if AI is completely unavailable
     * Not used in normal flow, but kept as emergency backup
     */
    public function fallbackRecommendation(User $user, Collection $greenSpaces): array
    {
        $preferences = $user->preferences ?? [];
        $userInterests = $preferences['interests'] ?? [];
        $userActivities = $preferences['preferred_activities'] ?? [];

        $bestMatch = null;
        $bestScore = 0;

        foreach ($greenSpaces as $gs) {
            $score = 0;
            $activities = $gs->activities ?? [];
            $description = strtolower($gs->description ?? '');

            // Simple keyword matching
            foreach ($userInterests as $interest) {
                if (str_contains($description, strtolower($interest))) {
                    $score += 2;
                }
            }

            foreach ($userActivities as $activity) {
                if (in_array($activity, $activities)) {
                    $score += 3;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $gs;
            }
        }

        // Default to first if no match
        if (!$bestMatch) {
            $bestMatch = $greenSpaces->first();
            $bestScore = 1;
        }

        return [
            'best_match_id' => $bestMatch->id,
            'score' => min($bestScore / 10, 1.0),
            'reason' => 'Sélectionné par correspondance de mots-clés avec vos préférences',
            'engine' => 'fallback_keywords',
        ];
    }
}
