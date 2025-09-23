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
            ['name' => 'Jardins Urbains de Paris', 'email' => 'contact@jardins-urbains-paris.org', 'phone' => '01 42 33 44 55', 'domain' => 'Jardinage urbain'],
            ['name' => 'Eco-Citoyens 92', 'email' => 'info@eco-citoyens92.fr', 'phone' => '01 47 55 66 77', 'domain' => 'Environnement'],
            ['name' => 'Biodiversité en Ville', 'email' => 'contact@biodiversite-ville.org', 'phone' => '01 53 88 99 00', 'domain' => 'Biodiversité'],
            ['name' => 'Nature et Éducation', 'email' => 'hello@nature-education.fr', 'phone' => '01 44 22 33 44', 'domain' => 'Éducation environnementale']
        ];

        foreach ($associations as $associationData) {
            Association::create($associationData);
        }

        // Créer des espaces verts
        $greenSpaces = [
            ['name' => 'Parc des Buttes-Chaumont', 'location' => '19e arrondissement, Paris', 'description' => 'Grand parc urbain avec collines et lac artificiel', 'type' => 'parc', 'status' => 'en cours', 'surface' => 24000, 'latitude' => 48.880, 'longitude' => 2.382, 'photos_before' => [], 'photos_after' => []],
            ['name' => 'Square du Vert-Galant', 'location' => 'Île de la Cité, Paris', 'description' => 'Petit jardin sur la pointe de l\'Île de la Cité', 'type' => 'square', 'status' => 'proposé', 'surface' => 500, 'latitude' => 48.857, 'longitude' => 2.341, 'photos_before' => [], 'photos_after' => []],
            ['name' => 'Jardin des Tuileries', 'location' => '1er arrondissement, Paris', 'description' => 'Jardin historique entre le Louvre et la Place de la Concorde', 'type' => 'jardin', 'status' => 'terminé', 'surface' => 25000, 'latitude' => 48.863, 'longitude' => 2.327, 'photos_before' => [], 'photos_after' => []],
            ['name' => 'Parc de Bercy', 'location' => '12e arrondissement, Paris', 'description' => 'Parc moderne avec jardins thématiques', 'type' => 'parc', 'status' => 'en cours', 'surface' => 14000, 'latitude' => 48.837, 'longitude' => 2.384, 'photos_before' => [], 'photos_after' => []],
            ['name' => 'Square Saint-Julien-le-Pauvre', 'location' => '5e arrondissement, Paris', 'description' => 'Petit square médiéval près de Notre-Dame', 'type' => 'square', 'status' => 'proposé', 'surface' => 350, 'latitude' => 48.853, 'longitude' => 2.348, 'photos_before' => [], 'photos_after' => []]
        ];

        foreach ($greenSpaces as $spaceData) {
            GreenSpace::create($spaceData);
        }

        // Créer des projets
        $projects = [
            ['title' => 'Potagers Participatifs aux Buttes-Chaumont', 'description' => 'Création de jardins potagers participatifs...', 'estimated_budget' => 15000.00, 'status' => 'en cours', 'association_id' => 1, 'green_space_id' => 1],
            ['title' => 'Hôtels à Insectes - Square du Vert-Galant', 'description' => 'Installation d\'hôtels à insectes...', 'estimated_budget' => 3500.00, 'status' => 'terminé', 'association_id' => 3, 'green_space_id' => 2],
            ['title' => 'Parcours Découverte Nature - Jardin des Tuileries', 'description' => 'Création d\'un parcours pédagogique...', 'estimated_budget' => 8000.00, 'status' => 'proposé', 'association_id' => 4, 'green_space_id' => 3],
            ['title' => 'Compostage Collectif - Parc de Bercy', 'description' => 'Mise en place d\'un système de compostage collectif...', 'estimated_budget' => 5500.00, 'status' => 'en cours', 'association_id' => 2, 'green_space_id' => 4],
            ['title' => 'Jardin Médiéval Participatif', 'description' => 'Restauration et animation du jardin médiéval...', 'estimated_budget' => 12000.00, 'status' => 'proposé', 'association_id' => 1, 'green_space_id' => 5],
            ['title' => 'Jardin japonaise Participatif', 'description' => 'Restauration et animation du jardin médiéval...', 'estimated_budget' => 10000.00, 'status' => 'proposé', 'association_id' => 1, 'green_space_id' => 6]

        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }
    }
}
