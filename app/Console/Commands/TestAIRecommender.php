<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AIRecommender;
use App\Models\User;
use App\Models\GreenSpace;

class TestAIRecommender extends Command
{
    protected $signature = 'test:ai-recommender';
    protected $description = 'Test the AI recommender service';

    public function handle(AIRecommender $recommender)
    {
        $this->info('🧪 Testing AI Recommender Service...');
        $this->newLine();

        // Get a user with preferences
        $user = User::whereNotNull('preferences')->first();
        
        if (!$user) {
            $this->error('❌ No user with preferences found!');
            $this->info('Run: php artisan db:seed --class=UserPreferencesSeeder');
            return 1;
        }

        $this->info("👤 User: {$user->name}");
        $this->info("📋 Preferences: " . json_encode($user->preferences));
        $this->newLine();

        // Get green spaces
        $greenSpaces = GreenSpace::whereNotNull('activities')->limit(3)->get();
        
        if ($greenSpaces->isEmpty()) {
            $this->error('❌ No green spaces with activities found!');
            $this->info('Run: php artisan db:seed --class=GreenSpaceActivitiesSeeder');
            return 1;
        }

        $this->info("🌳 Testing with {$greenSpaces->count()} green spaces:");
        foreach ($greenSpaces as $gs) {
            $this->line("   - {$gs->name}");
        }
        $this->newLine();

        // Call recommender
        $this->info('🤖 Calling AI Recommender...');
        try {
            $result = $recommender->recommend($user, $greenSpaces);
            
            $this->newLine();
            $this->info('✅ Success!');
            $this->table(
                ['Key', 'Value'],
                [
                    ['Best Match ID', $result['best_match_id'] ?? 'N/A'],
                    ['Score', $result['score'] ?? 'N/A'],
                    ['Reason', $result['reason'] ?? 'N/A'],
                    ['Engine', $result['engine'] ?? 'N/A'],
                ]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->newLine();
            $this->line('Check logs: storage/logs/laravel.log');
            return 1;
        }
    }
}
