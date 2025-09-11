<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Association;
use App\Models\GreenSpace;
use App\Models\Project;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer des associations
        $associations = [
            [
                'name' => 'Jardins Urbains de Paris',
                'email' => 'contact@jardins-urbains-paris.org',
                'phone' => '01 42 33 44 55',
                'domain' => 'Jardinage urbain'
            ],
            [
                'name' => 'Eco-Citoyens 92',
                'email' => 'info@eco-citoyens92.fr',
                'phone' => '01 47 55 66 77',
                'domain' => 'Environnement'
            ],
            [
                'name' => 'Biodiversité en Ville',
                'email' => 'contact@biodiversite-ville.org',
                'phone' => '01 53 88 99 00',
                'domain' => 'Biodiversité'
            ],
            [
                'name' => 'Nature et Éducation',
                'email' => 'hello@nature-education.fr',
                'phone' => '01 44 22 33 44',
                'domain' => 'Éducation environnementale'
            ]
        ];

        foreach ($associations as $associationData) {
            Association::create($associationData);
        }

        // Créer des espaces verts
        $greenSpaces = [
            [
                'name' => 'Parc des Buttes-Chaumont',
                'location' => '19e arrondissement, Paris',
                'description' => 'Grand parc urbain avec collines et lac artificiel',
                'type' => 'parc'
            ],
            [
                'name' => 'Square du Vert-Galant',
                'location' => 'Île de la Cité, Paris',
                'description' => 'Petit jardin sur la pointe de l\'Île de la Cité',
                'type' => 'square'
            ],
            [
                'name' => 'Jardin des Tuileries',
                'location' => '1er arrondissement, Paris',
                'description' => 'Jardin historique entre le Louvre et la Place de la Concorde',
                'type' => 'jardin'
            ],
            [
                'name' => 'Parc de Bercy',
                'location' => '12e arrondissement, Paris',
                'description' => 'Parc moderne avec jardins thématiques',
                'type' => 'parc'
            ],
            [
                'name' => 'Square Saint-Julien-le-Pauvre',
                'location' => '5e arrondissement, Paris',
                'description' => 'Petit square médiéval près de Notre-Dame',
                'type' => 'square'
            ]
        ];

        foreach ($greenSpaces as $spaceData) {
            GreenSpace::create($spaceData);
        }

        // Créer des projets
        $projects = [
            [
                'title' => 'Potagers Participatifs aux Buttes-Chaumont',
                'description' => 'Création de jardins potagers participatifs dans le parc des Buttes-Chaumont pour sensibiliser les visiteurs à l\'agriculture urbaine et créer du lien social.',
                'estimated_budget' => 15000.00,
                'status' => 'en cours',
                'association_id' => 1,
                'green_space_id' => 1
            ],
            [
                'title' => 'Hôtels à Insectes - Square du Vert-Galant',
                'description' => 'Installation d\'hôtels à insectes pour favoriser la biodiversité urbaine dans ce petit espace vert emblématique de Paris.',
                'estimated_budget' => 3500.00,
                'status' => 'terminé',
                'association_id' => 3,
                'green_space_id' => 2
            ],
            [
                'title' => 'Parcours Découverte Nature - Jardin des Tuileries',
                'description' => 'Création d\'un parcours pédagogique pour découvrir la faune et la flore du jardin, destiné aux familles et aux groupes scolaires.',
                'estimated_budget' => 8000.00,
                'status' => 'proposé',
                'association_id' => 4,
                'green_space_id' => 3
            ],
            [
                'title' => 'Compostage Collectif - Parc de Bercy',
                'description' => 'Mise en place d\'un système de compostage collectif avec ateliers de sensibilisation pour les riverains.',
                'estimated_budget' => 5500.00,
                'status' => 'en cours',
                'association_id' => 2,
                'green_space_id' => 4
            ],
            [
                'title' => 'Jardin Médiéval Participatif',
                'description' => 'Restauration et animation du jardin médiéval avec plantation de variétés anciennes et organisation d\'ateliers historiques.',
                'estimated_budget' => 12000.00,
                'status' => 'proposé',
                'association_id' => 1,
                'green_space_id' => 5
            ]
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }
    }
}