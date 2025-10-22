<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class PlantSuggestionController extends Controller
{
public function getSuggestions($latitude, $longitude)
{
    try {
        $pythonScript = base_path('SuggetionsPlants.py');
        
        // 🔍 Utilisez le chemin complet de Python
        $pythonPath = 'C:\Users\GIGABYTE I5\AppData\Local\Microsoft\WindowsApps\python.exe'; // Ajustez selon votre installation
        // OU essayez : 'C:\\Users\\GIGABYTE I5\\AppData\\Local\\Programs\\Python\\Python312\\python.exe'
        
        $command = "\"{$pythonPath}\" \"{$pythonScript}\" {$latitude} {$longitude}";
        
        $process = Process::run($command);
        
        if ($process->successful()) {
            $result = json_decode($process->output(), true);
            return response()->json($result);
        }
        
        Log::error('Python script failed', [
            'command' => $command,
            'output' => $process->output(),
            'errorOutput' => $process->errorOutput(),
        ]);
        
        return response()->json([
            'error' => 'Failed to get suggestions',
            'details' => $process->errorOutput(),
        ], 500);
        
    } catch (\Exception $e) {
        Log::error('Plant suggestion error: ' . $e->getMessage());
        return response()->json(['error' => 'Internal server error'], 500);
    }
}
}
