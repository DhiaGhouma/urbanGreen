<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Association;
use App\Models\GreenSpace;
use App\Models\Project;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed users first
        $this->call([
            UserSeeder::class,
        ]);

        // Créer des associations
        $associations = [
            ['name' => 'Association Green Tunisia', 'email' => 'contact@greentn.org', 'phone' => '71 123 456', 'domain' => 'Environnement'],
            ['name' => 'EcoJeunes Ariana', 'email' => 'ecojeunes@ariana.tn', 'phone' => '71 654 321', 'domain' => 'Éducation environnementale'],
            ['name' => 'Sfax Nature', 'email' => 'info@sfaxnature.tn', 'phone' => '74 112 233', 'domain' => 'Biodiversité'],
            ['name' => 'Monastir Propre', 'email' => 'monastir@propre.tn', 'phone' => '73 987 654', 'domain' => 'Nettoyage et recyclage'],
            ['name' => 'Bizerte Verte', 'email' => 'bizerteverte@bz.tn', 'phone' => '72 889 900', 'domain' => 'Reboisement']
        ];

        foreach ($associations as $associationData) {
            Association::updateOrCreate(
                ['email' => $associationData['email']],
                $associationData
            );
        }

        // Espaces verts (Tunisia)
        $greenSpaces = [
            [
                'name' => 'Parc du Belvédère',
                'location' => 'Tunis',
                'description' => 'Plus grand parc de Tunis avec zones de pique-nique et zoo',
                'type' => 'parc',
                'status' => 'en cours',
                'surface' => 100000,
                'latitude' => 36.818,
                'longitude' => 10.165,
                'photos_before' => [],
                'photos_after' => [],
                'activities' => ['pique-nique', 'jogging', 'zoo', 'potager urbain', 'yoga en plein air']
            ],
            [
                'name' => 'Parc de la République',
                'location' => 'Ariana',
                'description' => 'Petit parc familial avec aire de jeux',
                'type' => 'parc',
                'status' => 'proposé',
                'surface' => 5000,
                'latitude' => 36.866,
                'longitude' => 10.193,
                'photos_before' => [],
                'photos_after' => [],
                'activities' => ['aire de jeux', 'atelier compost', 'marche douce', 'lecture publique']
            ],
            [
                'name' => 'Jardin Botanique de Sfax',
                'location' => 'Sfax',
                'description' => 'Espace dédié à la flore méditerranéenne',
                'type' => 'jardin',
                'status' => 'terminé',
                'surface' => 15000,
                'latitude' => 34.740,
                'longitude' => 10.760,
                'photos_before' => [],
                'photos_after' => [],
                'activities' => ['visite guidée', 'atelier botanique', 'photographie', 'sentier pédagogique']
            ],
            [
                'name' => 'Plage de Monastir - Zone Verte',
                'location' => 'Monastir',
                'description' => 'Zone protégée avec dunes et plantations',
                'type' => 'parc', // align with allowed types to avoid ENUM truncation
                'status' => 'proposé',
                'surface' => 20000,
                'latitude' => 35.780,
                'longitude' => 10.820,
                'photos_before' => [],
                'photos_after' => [],
                'activities' => ['nettoyage de plage', 'plantation de dunes', 'observation des oiseaux', 'randonnée côtière']
            ],
            [
                'name' => 'Forêt de Bizerte',
                'location' => 'Bizerte',
                'description' => 'Forêt naturelle à reboiser',
                'type' => 'forêt',
                'status' => 'en cours',
                'surface' => 50000,
                'latitude' => 37.280,
                'longitude' => 9.870,
                'photos_before' => [],
                'photos_after' => [],
                'activities' => ['reboisement', 'sentier nature', 'observation faune', 'atelier permaculture']
            ]
        ];

        foreach ($greenSpaces as $spaceData) {
            $existing = GreenSpace::where('name', $spaceData['name'])->first();
            if ($existing) {
                // Update activities if empty to ensure full coverage
                if (empty($existing->activities)) {
                    $existing->activities = $spaceData['activities'];
                    $existing->save();
                }
            } else {
                GreenSpace::create($spaceData);
            }
        }

        // Projets
        $associationsByEmail = Association::pluck('id', 'email');
        $greenSpacesByName = GreenSpace::pluck('id', 'name');

        $projects = [
            ['title' => 'Reboisement de la Forêt de Bizerte', 'description' => 'Plantation de 2000 arbres autochtones pour restaurer la biodiversité.', 'estimated_budget' => 20000.00, 'status' => 'en cours', 'association_email' => 'bizerteverte@bz.tn', 'green_space_name' => 'Forêt de Bizerte'],
            ['title' => 'Création d’un Jardin Botanique à Sfax', 'description' => 'Installation d’espaces thématiques pour éducation des jeunes.', 'estimated_budget' => 15000.00, 'status' => 'terminé', 'association_email' => 'info@sfaxnature.tn', 'green_space_name' => 'Jardin Botanique de Sfax'],
            ['title' => 'Nettoyage de la Plage de Monastir', 'description' => 'Campagne de nettoyage et sensibilisation des citoyens.', 'estimated_budget' => 5000.00, 'status' => 'proposé', 'association_email' => 'monastir@propre.tn', 'green_space_name' => 'Plage de Monastir - Zone Verte'],
            ['title' => 'Compostage Collectif à Ariana', 'description' => 'Mise en place de bacs à compost dans le parc.', 'estimated_budget' => 4000.00, 'status' => 'en cours', 'association_email' => 'ecojeunes@ariana.tn', 'green_space_name' => 'Parc de la République'],
            ['title' => 'Potagers Urbains au Belvédère', 'description' => 'Création de potagers collectifs pour les habitants.', 'estimated_budget' => 8000.00, 'status' => 'proposé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],

            ['title' => 'Sensibilisation au tri des déchets à Tunis', 'description' => 'Organisation d’ateliers sur le tri des déchets pour les familles.', 'estimated_budget' => 3000.00, 'status' => 'proposé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],
            ['title' => 'Reboisement du Parc de la République', 'description' => 'Planter 500 arbres pour embellir le parc.', 'estimated_budget' => 7000.00, 'status' => 'en cours', 'association_email' => 'ecojeunes@ariana.tn', 'green_space_name' => 'Parc de la République'],
            ['title' => 'Création d’espaces pédagogiques à Sfax', 'description' => 'Installer panneaux éducatifs sur la biodiversité.', 'estimated_budget' => 2500.00, 'status' => 'terminé', 'association_email' => 'info@sfaxnature.tn', 'green_space_name' => 'Jardin Botanique de Sfax'],
            ['title' => 'Nettoyage de la plage à Monastir', 'description' => 'Organisation d’une journée de nettoyage et collecte de déchets.', 'estimated_budget' => 2000.00, 'status' => 'proposé', 'association_email' => 'monastir@propre.tn', 'green_space_name' => 'Plage de Monastir - Zone Verte'],
            ['title' => 'Ateliers de jardinage urbain au Belvédère', 'description' => 'Former les habitants à cultiver des plantes locales.', 'estimated_budget' => 4500.00, 'status' => 'proposé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],
            ['title' => 'Plantation d’arbustes dans la Forêt de Bizerte', 'description' => 'Augmenter la biodiversité avec 1000 arbustes locaux.', 'estimated_budget' => 12000.00, 'status' => 'en cours', 'association_email' => 'bizerteverte@bz.tn', 'green_space_name' => 'Forêt de Bizerte'],

            ['title' => 'Éco-atelier pour enfants à Ariana', 'description' => 'Ateliers pour enseigner le recyclage et le compostage.', 'estimated_budget' => 3500.00, 'status' => 'proposé', 'association_email' => 'ecojeunes@ariana.tn', 'green_space_name' => 'Parc de la République'],
            ['title' => 'Création d’un sentier botanique à Sfax', 'description' => 'Sentier pédagogique avec plantes locales et panneaux informatifs.', 'estimated_budget' => 10000.00, 'status' => 'terminé', 'association_email' => 'info@sfaxnature.tn', 'green_space_name' => 'Jardin Botanique de Sfax'],
            ['title' => 'Plantation de fleurs méditerranéennes au Belvédère', 'description' => 'Améliorer l’esthétique et la biodiversité du parc.', 'estimated_budget' => 6000.00, 'status' => 'proposé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],
            ['title' => 'Journée de nettoyage de la plage Monastir', 'description' => 'Collecte des déchets et sensibilisation environnementale.', 'estimated_budget' => 2500.00, 'status' => 'en cours', 'association_email' => 'monastir@propre.tn', 'green_space_name' => 'Plage de Monastir - Zone Verte'],
            ['title' => 'Reforestation du Parc de la République', 'description' => 'Planter des arbres résistants au climat local.', 'estimated_budget' => 8000.00, 'status' => 'proposé', 'association_email' => 'ecojeunes@ariana.tn', 'green_space_name' => 'Parc de la République'],

            ['title' => 'Création d’un jardin sensoriel à Tunis', 'description' => 'Jardin pour personnes à mobilité réduite.', 'estimated_budget' => 9000.00, 'status' => 'proposé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],
            ['title' => 'Formation compostage à Sfax', 'description' => 'Formation pratique pour habitants et écoles.', 'estimated_budget' => 4000.00, 'status' => 'terminé', 'association_email' => 'info@sfaxnature.tn', 'green_space_name' => 'Jardin Botanique de Sfax'],
            ['title' => 'Plantation d’arbres fruitiers à Monastir', 'description' => 'Créer un verger collectif pour les habitants.', 'estimated_budget' => 7000.00, 'status' => 'en cours', 'association_email' => 'monastir@propre.tn', 'green_space_name' => 'Plage de Monastir - Zone Verte'],
            ['title' => 'Jardin communautaire à Ariana', 'description' => 'Potager partagé pour les habitants.', 'estimated_budget' => 5000.00, 'status' => 'proposé', 'association_email' => 'ecojeunes@ariana.tn', 'green_space_name' => 'Parc de la République'],
            ['title' => 'Ateliers écologiques à Belvédère', 'description' => 'Éducation à la biodiversité et aux espèces locales.', 'estimated_budget' => 3000.00, 'status' => 'terminé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],

            ['title' => 'Plantation de haies à Bizerte', 'description' => 'Créer des corridors écologiques pour la faune locale.', 'estimated_budget' => 15000.00, 'status' => 'en cours', 'association_email' => 'bizerteverte@bz.tn', 'green_space_name' => 'Forêt de Bizerte'],
            ['title' => 'Création d’un espace jeux nature à Sfax', 'description' => 'Jeux éducatifs intégrés à la nature.', 'estimated_budget' => 12000.00, 'status' => 'proposé', 'association_email' => 'info@sfaxnature.tn', 'green_space_name' => 'Jardin Botanique de Sfax'],
            ['title' => 'Nettoyage de l’Avenue Verte à Monastir', 'description' => 'Sensibilisation et ramassage des déchets.', 'estimated_budget' => 3500.00, 'status' => 'en cours', 'association_email' => 'monastir@propre.tn', 'green_space_name' => 'Plage de Monastir - Zone Verte'],
            ['title' => 'Potagers éducatifs à Ariana', 'description' => 'Jardin pédagogique pour les écoles locales.', 'estimated_budget' => 6000.00, 'status' => 'terminé', 'association_email' => 'ecojeunes@ariana.tn', 'green_space_name' => 'Parc de la République'],
            ['title' => 'Création de nichoirs à Belvédère', 'description' => 'Favoriser la biodiversité aviaire dans le parc.', 'estimated_budget' => 2500.00, 'status' => 'proposé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],
            ['title' => 'Plantation d’arbres méditerranéens à Bizerte', 'description' => 'Augmenter la couverture végétale et la biodiversité.', 'estimated_budget' => 18000.00, 'status' => 'en cours', 'association_email' => 'bizerteverte@bz.tn', 'green_space_name' => 'Forêt de Bizerte'],
            ['title' => 'Jardin communautaire sensoriel à Sfax', 'description' => 'Pour enfants et personnes âgées avec sensibilité sensorielle.', 'estimated_budget' => 9000.00, 'status' => 'proposé', 'association_email' => 'info@sfaxnature.tn', 'green_space_name' => 'Jardin Botanique de Sfax'],
            ['title' => 'Ateliers de sensibilisation à la plage Monastir', 'description' => 'Education des jeunes sur la pollution marine.', 'estimated_budget' => 4000.00, 'status' => 'en cours', 'association_email' => 'monastir@propre.tn', 'green_space_name' => 'Plage de Monastir - Zone Verte'],
            ['title' => 'Formation en permaculture à Ariana', 'description' => 'Apprentissage de méthodes de culture durable.', 'estimated_budget' => 5000.00, 'status' => 'terminé', 'association_email' => 'ecojeunes@ariana.tn', 'green_space_name' => 'Parc de la République'],
            ['title' => 'Création d’un sentier écologique Belvédère', 'description' => 'Sentier éducatif avec panneaux sur biodiversité.', 'estimated_budget' => 7000.00, 'status' => 'proposé', 'association_email' => 'contact@greentn.org', 'green_space_name' => 'Parc du Belvédère'],
        ];


        foreach ($projects as $projectData) {
            $associationId = $associationsByEmail[$projectData['association_email']] ?? null;
            $greenSpaceId = $greenSpacesByName[$projectData['green_space_name']] ?? null;

            if (!$associationId || !$greenSpaceId) {
                continue;
            }

            Project::firstOrCreate(
                ['title' => $projectData['title']],
                [
                    'description' => $projectData['description'],
                    'estimated_budget' => $projectData['estimated_budget'],
                    'status' => $projectData['status'],
                    'association_id' => $associationId,
                    'green_space_id' => $greenSpaceId,
                ]
            );
        }

        // Seed participations with preferences (varied per user)
        $this->call([
            ParticipationSeeder::class,
            ParticipationFeedbackSeeder::class,
        ]);
    }
}
