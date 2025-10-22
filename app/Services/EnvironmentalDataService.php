<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class EnvironmentalDataService
{
    private string $openWeatherApiKey;
    private string $waqiApiToken;

    public function __construct()
    {
        $this->openWeatherApiKey = config('services.openweather.api_key');
        $this->waqiApiToken = config('services.waqi.token');
    }

    /**
     * Get current weather data for a location
     */
    public function getCurrentWeather(float $latitude, float $longitude): ?array
    {
        $cacheKey = "weather_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 1800, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(10)->get('https://api.openweathermap.org/data/2.5/weather', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'appid' => $this->openWeatherApiKey,
                    'units' => 'metric',
                    'lang' => 'fr'
                ]);

                if ($response->successful()) {
                    return $this->formatWeatherData($response->json());
                }

                return null;
            } catch (Exception $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Get 5-day weather forecast
     */
    public function getWeatherForecast(float $latitude, float $longitude): ?array
    {
        $cacheKey = "forecast_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 3600, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(10)->get('https://api.openweathermap.org/data/2.5/forecast', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'appid' => $this->openWeatherApiKey,
                    'units' => 'metric',
                    'lang' => 'fr'
                ]);

                if ($response->successful()) {
                    return $this->formatForecastData($response->json());
                }

                return null;
            } catch (Exception $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Get air quality index for a location
     */
    public function getAirQuality(float $latitude, float $longitude): ?array
    {
        $cacheKey = "air_quality_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 3600, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(10)->get("https://api.waqi.info/feed/geo:{$latitude};{$longitude}/", [
                    'token' => $this->waqiApiToken
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['status']) && $data['status'] === 'ok') {
                        return $this->formatAirQualityData($data['data']);
                    }
                }

                return null;
            } catch (Exception $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Get biodiversity observations from iNaturalist
     */
    public function getBiodiversityData(float $latitude, float $longitude, int $radius = 1): ?array
    {
        $cacheKey = "biodiversity_{$latitude}_{$longitude}_{$radius}";

        return Cache::remember($cacheKey, 7200, function () use ($latitude, $longitude, $radius) {
            try {
                $response = Http::timeout(10)->get('https://api.inaturalist.org/v1/observations', [
                    'lat' => $latitude,
                    'lng' => $longitude,
                    'radius' => $radius,
                    'per_page' => 50,
                    'locale' => 'fr',
                    'order' => 'desc',
                    'order_by' => 'created_at',
                    'quality_grade' => 'research'
                ]);

                if ($response->successful()) {
                    return $this->formatBiodiversityData($response->json());
                }

                return null;
            } catch (Exception $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Get species statistics for a location
     */
    public function getSpeciesStats(float $latitude, float $longitude, int $radius = 1): ?array
    {
        $cacheKey = "species_stats_{$latitude}_{$longitude}_{$radius}";

        return Cache::remember($cacheKey, 7200, function () use ($latitude, $longitude, $radius) {
            try {
                $response = Http::timeout(10)->get('https://api.inaturalist.org/v1/observations/species_counts', [
                    'lat' => $latitude,
                    'lng' => $longitude,
                    'radius' => $radius,
                    'per_page' => 20,
                    'locale' => 'fr'
                ]);

                if ($response->successful()) {
                    return $response->json()['results'] ?? [];
                }

                return null;
            } catch (Exception $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Geocode an address using Nominatim
     */
    public function geocodeAddress(string $address): ?array
    {
        $cacheKey = "geocode_" . md5($address);

        return Cache::remember($cacheKey, 86400, function () use ($address) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'GreenSpaceApp/1.0'
                    ])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $address,
                        'format' => 'json',
                        'limit' => 1,
                        'addressdetails' => 1
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data)) {
                        return [
                            'latitude' => (float) $data[0]['lat'],
                            'longitude' => (float) $data[0]['lon'],
                            'display_name' => $data[0]['display_name'],
                            'address' => $data[0]['address'] ?? []
                        ];
                    }
                }

                return null;
            } catch (Exception $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Reverse geocode coordinates to get address
     */
    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        $cacheKey = "reverse_geocode_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 86400, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'GreenSpaceApp/1.0'
                    ])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'format' => 'json',
                        'addressdetails' => 1
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'display_name' => $data['display_name'] ?? null,
                        'address' => $data['address'] ?? []
                    ];
                }

                return null;
            } catch (Exception $e) {
                report($e);
                return null;
            }
        });
    }

    /**
     * Get comprehensive environmental data for a green space
     */
    public function getComprehensiveData(float $latitude, float $longitude): array
    {
        return [
            'weather' => $this->getCurrentWeather($latitude, $longitude),
            'forecast' => $this->getWeatherForecast($latitude, $longitude),
            'air_quality' => $this->getAirQuality($latitude, $longitude),
            'biodiversity' => $this->getBiodiversityData($latitude, $longitude),
            'species_stats' => $this->getSpeciesStats($latitude, $longitude),
        ];
    }

    /**
     * Check if weather is suitable for outdoor activities
     */
    public function isWeatherSuitableForActivities(float $latitude, float $longitude): array
    {
        $weather = $this->getCurrentWeather($latitude, $longitude);

        if (!$weather) {
            return [
                'suitable' => null,
                'message' => 'Données météo non disponibles'
            ];
        }

        $temp = $weather['temperature'];
        $condition = $weather['condition'];
        $windSpeed = $weather['wind_speed'];

        $suitable = true;
        $issues = [];

        if ($temp < 5 || $temp > 35) {
            $suitable = false;
            $issues[] = "Température extrême ({$temp}°C)";
        }

        if (in_array(strtolower($condition), ['pluie', 'orage', 'neige'])) {
            $suitable = false;
            $issues[] = "Conditions météo défavorables ({$condition})";
        }

        if ($windSpeed > 30) {
            $suitable = false;
            $issues[] = "Vent trop fort ({$windSpeed} km/h)";
        }

        return [
            'suitable' => $suitable,
            'message' => $suitable
                ? 'Conditions idéales pour les activités en plein air'
                : 'Activités déconseillées : ' . implode(', ', $issues),
            'details' => $weather
        ];
    }

    /**
     * Format weather data
     */
    private function formatWeatherData(array $data): array
    {
        return [
            'temperature' => round($data['main']['temp'], 1),
            'feels_like' => round($data['main']['feels_like'], 1),
            'humidity' => $data['main']['humidity'],
            'pressure' => $data['main']['pressure'],
            'condition' => $data['weather'][0]['description'] ?? 'N/A',
            'icon' => $data['weather'][0]['icon'] ?? null,
            'wind_speed' => round($data['wind']['speed'] * 3.6, 1), // m/s to km/h
            'clouds' => $data['clouds']['all'] ?? 0,
            'sunrise' => isset($data['sys']['sunrise']) ? date('H:i', $data['sys']['sunrise']) : null,
            'sunset' => isset($data['sys']['sunset']) ? date('H:i', $data['sys']['sunset']) : null,
        ];
    }

    /**
     * Format forecast data
     */
    private function formatForecastData(array $data): array
    {
        $forecast = [];

        foreach ($data['list'] ?? [] as $item) {
            $date = date('Y-m-d', $item['dt']);
            $time = date('H:i', $item['dt']);

            if (!isset($forecast[$date])) {
                $forecast[$date] = [
                    'date' => $date,
                    'date_formatted' => date('d/m/Y', $item['dt']),
                    'items' => []
                ];
            }

            $forecast[$date]['items'][] = [
                'time' => $time,
                'temperature' => round($item['main']['temp'], 1),
                'condition' => $item['weather'][0]['description'] ?? 'N/A',
                'icon' => $item['weather'][0]['icon'] ?? null,
                'humidity' => $item['main']['humidity'],
                'wind_speed' => round($item['wind']['speed'] * 3.6, 1),
            ];
        }

        return array_values($forecast);
    }

    /**
     * Format air quality data
     */
    private function formatAirQualityData(array $data): array
    {
        $aqi = $data['aqi'] ?? 0;

        $level = match(true) {
            $aqi <= 50 => 'Bon',
            $aqi <= 100 => 'Modéré',
            $aqi <= 150 => 'Mauvais pour les groupes sensibles',
            $aqi <= 200 => 'Mauvais',
            $aqi <= 300 => 'Très mauvais',
            default => 'Dangereux'
        };

        $color = match(true) {
            $aqi <= 50 => 'success',
            $aqi <= 100 => 'info',
            $aqi <= 150 => 'warning',
            $aqi <= 200 => 'danger',
            default => 'dark'
        };

        return [
            'aqi' => $aqi,
            'level' => $level,
            'color' => $color,
            'station' => $data['city']['name'] ?? 'N/A',
            'pollutants' => [
                'pm25' => $data['iaqi']['pm25']['v'] ?? null,
                'pm10' => $data['iaqi']['pm10']['v'] ?? null,
                'o3' => $data['iaqi']['o3']['v'] ?? null,
                'no2' => $data['iaqi']['no2']['v'] ?? null,
            ],
            'updated_at' => $data['time']['s'] ?? null,
        ];
    }

    /**
     * Format biodiversity data
     */
    private function formatBiodiversityData(array $data): array
    {
        $observations = [];

        foreach ($data['results'] ?? [] as $obs) {
            $observations[] = [
                'id' => $obs['id'],
                'species' => $obs['taxon']['preferred_common_name'] ?? $obs['taxon']['name'],
                'scientific_name' => $obs['taxon']['name'],
                'category' => $obs['taxon']['iconic_taxon_name'] ?? 'Unknown',
                'image' => $obs['photos'][0]['url'] ?? null,
                'observed_at' => $obs['observed_on'] ?? null,
                'quality_grade' => $obs['quality_grade'] ?? 'casual',
                'coordinates' => [
                    'lat' => $obs['location'][0] ?? null,
                    'lng' => $obs['location'][1] ?? null,
                ]
            ];
        }

        return [
            'total_results' => $data['total_results'] ?? 0,
            'observations' => $observations,
            'categories' => $this->categorizeBiodiversity($observations),
        ];
    }

    /**
     * Categorize biodiversity observations
     */
    private function categorizeBiodiversity(array $observations): array
    {
        $categories = [];

        foreach ($observations as $obs) {
            $cat = $obs['category'];
            if (!isset($categories[$cat])) {
                $categories[$cat] = 0;
            }
            $categories[$cat]++;
        }

        return $categories;
    }
}
