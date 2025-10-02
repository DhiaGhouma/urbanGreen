<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Fast AI Recommender using persistent Python server
 * Much faster than subprocess because model stays loaded
 */
class FastAIRecommender
{
    protected string $serverUrl;
    
    public function __construct()
    {
        $this->serverUrl = env('AI_SERVER_URL', 'http://127.0.0.1:8765');
    }
    
    /**
     * Check if AI server is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->serverUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get green space recommendation for user
     * 
     * @param \App\Models\User $user
     * @param \Illuminate\Support\Collection $greenSpaces
     * @return array|null Recommendation or null if server unavailable
     */
    public function recommend($user, $greenSpaces): ?array
    {
        if (!$this->isAvailable()) {
            Log::warning('AI server is not available', ['url' => $this->serverUrl]);
            return null;
        }
        
        // Prepare user data
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'preferences' => $user->preferences ?? []
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
        })->toArray();
        
        try {
            $response = Http::timeout(35)
                ->post("{$this->serverUrl}/recommend", [
                    'user' => $userData,
                    'green_spaces' => $greenSpacesData
                ]);
            
            if (!$response->successful()) {
                throw new \Exception('AI server returned error: ' . $response->body());
            }
            
            $result = $response->json();
            
            // Validate result
            if (!isset($result['best_match_id'])) {
                throw new \Exception('Invalid response from AI server');
            }
            
            Log::info('AI recommendation successful', [
                'user_id' => $user->id,
                'recommended_id' => $result['best_match_id'],
                'score' => $result['score'] ?? 0,
                'engine' => $result['engine'] ?? 'unknown'
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('AI recommendation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
