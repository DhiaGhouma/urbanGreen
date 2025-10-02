<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected string $baseUrl;
    protected string $model;
    protected string $executablePath;

    public function __construct()
    {
        $this->baseUrl = 'http://localhost:11434';
        $this->model = env('OLLAMA_MODEL', 'llama3.1');
        $this->executablePath = env('OLLAMA_BIN', 'ollama');
    }

    /**
     * Check if Ollama is running and accessible
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->baseUrl}/api/tags");
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Ollama not available', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate a completion using Ollama
     * 
     * @param string $prompt The prompt to send
     * @param array $options Additional options (temperature, etc.)
     * @return array Response with 'response' and metadata
     * @throws \Exception
     */
    public function generate(string $prompt, array $options = []): array
    {
        $defaultOptions = [
            'temperature' => 0.3,
            'top_p' => 0.9,
        ];

        $payload = [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => array_merge($defaultOptions, $options),
        ];

        try {
            $response = Http::timeout(60)
                ->post("{$this->baseUrl}/api/generate", $payload);

            if (!$response->successful()) {
                throw new \Exception("Ollama API error: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Ollama generate failed', [
                'error' => $e->getMessage(),
                'model' => $this->model,
            ]);
            throw $e;
        }
    }

    /**
     * Chat with Ollama using conversation context
     * 
     * @param array $messages Array of message objects with 'role' and 'content'
     * @param array $options Additional options
     * @return array Response
     * @throws \Exception
     */
    public function chat(array $messages, array $options = []): array
    {
        $defaultOptions = [
            'temperature' => 0.3,
        ];

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => false,
            'options' => array_merge($defaultOptions, $options),
        ];

        try {
            $response = Http::timeout(60)
                ->post("{$this->baseUrl}/api/chat", $payload);

            if (!$response->successful()) {
                throw new \Exception("Ollama chat API error: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Ollama chat failed', [
                'error' => $e->getMessage(),
                'model' => $this->model,
            ]);
            throw $e;
        }
    }

    /**
     * Get list of available models
     */
    public function listModels(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");
            
            if (!$response->successful()) {
                return [];
            }

            return $response->json()['models'] ?? [];
        } catch (\Exception $e) {
            Log::warning('Failed to list Ollama models', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Check if a specific model is available locally
     */
    public function hasModel(string $modelName): bool
    {
        $models = $this->listModels();
        foreach ($models as $model) {
            if (isset($model['name']) && str_contains($model['name'], $modelName)) {
                return true;
            }
        }
        return false;
    }
}
