<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GreenSpace;
use App\Models\User;

class SmartScoringDataSeeder extends Seeder
{
    /**
     * Seed default data for Smart Scoring system
     */
    public function run(): void
    {
        $this->command->info('🌱 Mise à jour des données pour Smart Scoring...');
        
        // ═══════════════════════════════════════════════════════════
        // 1. Green Spaces : Ajouter complexity_level et coordinates
        // ═══════════════════════════════════════════════════════════
        
        // Exemple avec coordonnées GPS de Tunis/Ariana
        $greenSpacesData = [
            [
                'id' => 1,
                'complexity_level' => 'débutant',
                'latitude' => 36.8623,
                'longitude' => 10.1874
            ],
            [
                'id' => 2,
                'complexity_level' => 'débutant',
                'latitude' => 36.8665,
                'longitude' => 10.1955
            ],
            [
                'id' => 3,
                'complexity_level' => 'intermédiaire',
                'latitude' => 36.8506,
                'longitude' => 10.1967
            ],
            [
                'id' => 4,
                'complexity_level' => 'expert',
                'latitude' => 36.8890,
                'longitude' => 10.1872
            ],
            [
                'id' => 5,
                'complexity_level' => 'débutant',
                'latitude' => 36.8402,
                'longitude' => 10.1913
            ],
        ];
        
        foreach ($greenSpacesData as $data) {
            $greenSpace = GreenSpace::find($data['id']);
            if ($greenSpace) {
                $greenSpace->update([
                    'complexity_level' => $data['complexity_level'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]);
                $this->command->info("✅ {$greenSpace->name} : {$data['complexity_level']}, GPS ({$data['latitude']}, {$data['longitude']})");
            }
        }
        
        // ═══════════════════════════════════════════════════════════
        // 2. Users : Ajouter preferences complètes
        // ═══════════════════════════════════════════════════════════
        
        // Exemple pour user ID 30
        $user = User::find(30);
        if ($user) {
            $user->update([
                'preferences' => [
                    'interests' => ['écologie', 'biodiversité', 'environnement'],
                    'preferred_activities' => ['jardinage', 'agriculture urbaine', 'permaculture'],
                    'experience_level' => 'débutant',
                    'preferred_types' => ['jardin communautaire', 'parc'],
                    'max_distance' => 10, // km
                    'coordinates' => [
                        'lat' => 36.8665,
                        'lon' => 10.1955
                    ]
                ]
            ]);
            $this->command->info("✅ User {$user->name} : préférences mises à jour");
        }
        
        $this->command->newLine();
        $this->command->info('🎉 Données Smart Scoring prêtes !');
        $this->command->info('');
        $this->command->info('📝 Pour tester :');
        $this->command->info('   1. Démarrer le serveur AI : php artisan ai:start-server');
        $this->command->info('   2. Tester : php artisan test:fast-ai');
    }
}
