<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
    }

    /**
     * Obtenir les prévisions météo pour une date et un lieu
     */
    public function getForecast(string $location, string $date)
    {
        $cacheKey = "weather_{$location}_{$date}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($location, $date) {
            try {
                // Géocoder le lieu
                $coordinates = $this->geocodeLocation($location);
                
                if (!$coordinates) {
                    return null;
                }

                // Obtenir les prévisions
                $response = Http::get("{$this->baseUrl}/forecast", [
                    'lat' => $coordinates['lat'],
                    'lon' => $coordinates['lon'],
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'fr'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->findClosestForecast($data, $date);
                }

                return null;
            } catch (\Exception $e) {
                logger()->error('Weather API Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Géocoder une adresse
     */
    protected function geocodeLocation(string $location)
    {
        $response = Http::get("http://api.openweathermap.org/geo/1.0/direct", [
            'q' => $location,
            'limit' => 1,
            'appid' => $this->apiKey
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            $data = $response->json()[0];
            return [
                'lat' => $data['lat'],
                'lon' => $data['lon']
            ];
        }

        return null;
    }

    /**
     * Trouver la prévision la plus proche de la date donnée
     */
    protected function findClosestForecast($data, $targetDate)
    {
        $targetTimestamp = strtotime($targetDate);
        $closestForecast = null;
        $minDiff = PHP_INT_MAX;

        foreach ($data['list'] as $forecast) {
            $forecastTimestamp = $forecast['dt'];
            $diff = abs($forecastTimestamp - $targetTimestamp);

            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closestForecast = $forecast;
            }
        }

        if ($closestForecast) {
            return [
                'temperature' => round($closestForecast['main']['temp']),
                'feels_like' => round($closestForecast['main']['feels_like']),
                'humidity' => $closestForecast['main']['humidity'],
                'description' => $closestForecast['weather'][0]['description'],
                'icon' => $closestForecast['weather'][0]['icon'],
                'wind_speed' => round($closestForecast['wind']['speed'] * 3.6), // m/s to km/h
                'rain' => $closestForecast['rain']['3h'] ?? 0,
                'clouds' => $closestForecast['clouds']['all'],
                'weather_main' => $closestForecast['weather'][0]['main'],
            ];
        }

        return null;
    }

    /**
     * Analyser si les conditions météo sont favorables
     */
    public function isFavorableWeather($forecast)
    {
        if (!$forecast) {
            return ['favorable' => null, 'reason' => 'Données météo non disponibles'];
        }

        // Conditions défavorables
        $unfavorableConditions = ['Thunderstorm', 'Snow', 'Extreme'];
        
        if (in_array($forecast['weather_main'], $unfavorableConditions)) {
            return [
                'favorable' => false,
                'severity' => 'high',
                'reason' => 'Conditions météorologiques dangereuses : ' . $forecast['description']
            ];
        }

        if ($forecast['rain'] > 5) {
            return [
                'favorable' => false,
                'severity' => 'medium',
                'reason' => 'Fortes pluies prévues (' . $forecast['rain'] . 'mm)'
            ];
        }

        if ($forecast['temperature'] < 5 || $forecast['temperature'] > 35) {
            return [
                'favorable' => false,
                'severity' => 'medium',
                'reason' => 'Température extrême (' . $forecast['temperature'] . '°C)'
            ];
        }

        if ($forecast['wind_speed'] > 40) {
            return [
                'favorable' => false,
                'severity' => 'medium',
                'reason' => 'Vents forts prévus (' . $forecast['wind_speed'] . ' km/h)'
            ];
        }

        // Conditions moyennement favorables
        if ($forecast['rain'] > 2 && $forecast['rain'] <= 5) {
            return [
                'favorable' => 'warning',
                'severity' => 'low',
                'reason' => 'Pluies légères possibles'
            ];
        }

        // Conditions favorables
        return [
            'favorable' => true,
            'severity' => null,
            'reason' => 'Conditions météo favorables'
        ];
    }

    /**
     * Obtenir l'icône météo
     */
    public function getWeatherIconUrl($iconCode)
    {
        return "https://openweathermap.org/img/wn/{$iconCode}@2x.png";
    }

    /**
     * Suggérer un report d'événement
     */
    public function shouldSuggestReschedule($forecast)
    {
        $analysis = $this->isFavorableWeather($forecast);
        
        return $analysis['favorable'] === false && $analysis['severity'] === 'high';
    }
}