<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GreenSpace;

class GreenSpaceActivitiesSeeder extends Seeder
{
    /**
     * Add sample activities to existing green spaces
     */
    public function run(): void
    {
        $greenSpaces = GreenSpace::all();
        
        if ($greenSpaces->isEmpty()) {
            $this->command->warn('Aucun espace vert trouvé');
            return;
        }

        $activityOptions = [
            'parc' => ['plantation', 'arrosage', 'entretien', 'nettoyage', 'événements', 'observation'],
            'jardin communautaire' => ['jardinage', 'plantation', 'entretien', 'potager', 'compostage', 'récolte', 'ateliers'],
            'forêt urbaine' => ['plantation arbres', 'observation', 'biodiversité', 'sentiers', 'sensibilisation'],
            'jardin partagé' => ['potager', 'jardinage', 'compostage', 'récolte', 'partage', 'ateliers'],
            'espace vert' => ['entretien', 'nettoyage', 'plantation', 'événements', 'sport'],
        ];

        foreach ($greenSpaces as $greenSpace) {
            // Skip if already has activities
            if ($greenSpace->activities && !empty($greenSpace->activities)) {
                $this->command->info("GreenSpace #{$greenSpace->id} a déjà des activités");
                continue;
            }

            // Determine activities based on type
            $type = strtolower($greenSpace->type ?? 'espace vert');
            
            // Find matching activity set
            $activities = null;
            foreach ($activityOptions as $key => $value) {
                if (str_contains($type, $key)) {
                    $activities = $value;
                    break;
                }
            }
            
            // Default to generic activities if no match
            if (!$activities) {
                $activities = ['plantation', 'entretien', 'nettoyage', 'événements'];
            }

            // Randomly select 3-5 activities
            $numActivities = rand(3, min(5, count($activities)));
            $selectedActivities = array_slice(
                $activities, 
                0, 
                $numActivities
            );

            $greenSpace->update(['activities' => $selectedActivities]);
            
            $this->command->info("Activités ajoutées pour: {$greenSpace->name} - " . implode(', ', $selectedActivities));
        }

        $this->command->info('✅ Activités des espaces verts initialisées');
    }
}
