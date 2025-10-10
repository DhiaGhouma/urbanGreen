<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FastAIRecommender;
use App\Models\User;
use App\Models\GreenSpace;

class TestFastAI extends Command
{
    protected $signature = 'test:fast-ai';
    protected $description = 'Test the fast AI recommender with HTTP server';

    public function handle()
    {
        $this->info('🧪 Testing Fast AI Recommender...');
        $this->newLine();
        
        $recommender = new FastAIRecommender();
        
        // Check server availability
        $this->info('📡 Checking AI server...');
        if (!$recommender->isAvailable()) {
            $this->error('❌ AI server is not running!');
            $this->newLine();
            $this->info('💡 Start it with: php artisan ai:start-server');
            $this->info('   Or manually: .venv\Scripts\python.exe ai_server.py');
            return 1;
        }
        $this->info('✅ AI server is running!');
        $this->newLine();
        
        // Get test user
        $user = User::whereNotNull('preferences')->first();
        if (!$user) {
            $this->error('No user with preferences found');
            return 1;
        }
        
        $this->info("👤 User: {$user->name}");
        $this->info("📋 Preferences: " . json_encode($user->preferences, JSON_UNESCAPED_UNICODE));
        $this->newLine();
        
        // Get green spaces
        $greenSpaces = GreenSpace::take(3)->get();
        $this->info("🌳 Testing with {$greenSpaces->count()} green spaces:");
        foreach ($greenSpaces as $gs) {
            $this->info("   - {$gs->name}");
        }
        $this->newLine();
        
        // Call recommender
        $this->info('🤖 Calling Fast AI Recommender...');
        
        $startTime = microtime(true);
        
        try {
            $result = $recommender->recommend($user, $greenSpaces);
            
            $elapsed = round((microtime(true) - $startTime) * 1000);
            
            $this->newLine();
            $this->info("✅ Success! ({$elapsed}ms)");
            
            $this->table(
                ['Key', 'Value'],
                [
                    ['Best Match ID', $result['best_match_id']],
                    ['Score', $result['score']],
                    ['Reason', $result['reason']],
                    ['Engine', $result['engine']],
                    ['Response Time', "{$elapsed}ms"],
                ]
            );
            
            // Show the recommended green space
            $recommended = $greenSpaces->find($result['best_match_id']);
            if ($recommended) {
                $this->newLine();
                $this->info("🎯 Recommended: {$recommended->name}");
                $this->info("   Activities: " . implode(', ', $recommended->activities ?? []));
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->info('Check logs: storage/logs/laravel.log');
            return 1;
        }
    }
}
