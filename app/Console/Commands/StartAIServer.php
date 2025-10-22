<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartAIServer extends Command
{
    protected $signature = 'ai:start-server';
    protected $description = 'Start the AI recommendation server (keeps model loaded for fast responses)';

    public function handle()
    {
        $pythonBin = base_path('.venv/Scripts/python.exe');
        $scriptPath = base_path('ai_server.py');
        
        if (!file_exists($pythonBin)) {
            $this->error('Python virtualenv not found. Please run: python -m venv .venv');
            return 1;
        }
        
        if (!file_exists($scriptPath)) {
            $this->error('AI server script not found: ' . $scriptPath);
            return 1;
        }
        
        $this->info('🚀 Starting AI Recommendation Server...');
        $this->info('   Model will load (takes ~10 seconds)');
        $this->info('   Press Ctrl+C to stop');
        $this->newLine();
        
        $process = new Process([$pythonBin, $scriptPath]);
        $process->setTimeout(null); // Run indefinitely
        
        try {
            $process->run(function ($type, $buffer) {
                echo $buffer;
            });
        } catch (\Exception $e) {
            $this->error('Server error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
