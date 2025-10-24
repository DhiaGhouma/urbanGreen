<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlantSuggestionController extends Controller
{
    public function getSuggestions($latitude, $longitude)
    {
        try {
            // 🔗 URL de ton API Flask déjà en cours d’exécution
            $flaskUrl = "http://127.0.0.1:5000/suggest";

            // 📡 Requête vers Flask avec paramètres lat/lon
            $response = Http::get($flaskUrl, [
                'lat' => $latitude,
                'lon' => $longitude,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // 🧩 Adapter la réponse Flask au format attendu par ton Blade
                return response()->json([
                    'success' => true,
                    'temperature' => $data['temperature'] ?? null,
                    'season' => $data['season'] ?? 'unknown',
                    'suggestions' => [
                        [
                            'name' => $data['suggested_plant'] ?? 'Plante inconnue',
                            'type' => 'plant',
                            'ideal_temp' => ($data['temperature'] ?? '?') . '°C',
                            'confidence' => $data['confidence'] ?? null
                        ]
                    ],
                ]);
            }

            // ❌ Flask n’a pas répondu correctement
            Log::error('Erreur Flask', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur Flask',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Erreur getSuggestions: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erreur interne',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
