<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Association;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer qu'il y a des associations
        if (Association::count() === 0) {
            $this->command->error('Aucune association trouvée. Veuillez d\'abord créer des associations.');
            return;
        }

        $associations = Association::all();

        $events = [
            [
                'titre' => 'Grande Journée de Plantation d\'Arbres',
                'description' => "Rejoignez-nous pour une journée dédiée à la reforestation urbaine. Nous planterons plus de 100 arbres dans le parc municipal.\n\nAu programme :\n- Introduction à la plantation d'arbres\n- Distribution des plants et outils\n- Plantation collective\n- Pique-nique écologique\n\nMatériel fourni. Amenez vos gants et votre bonne humeur !",
                'type' => 'plantation',
                'date_debut' => Carbon::now()->addDays(15)->setTime(9, 0),
                'date_fin' => Carbon::now()->addDays(15)->setTime(17, 0),
                'lieu' => 'Parc Municipal Central',
                'adresse' => '45 Avenue de la République, 75001 Paris',
                'capacite_max' => 50,
                'statut' => 'planifie',
            ],
            [
                'titre' => 'Conférence : Le Changement Climatique et Nos Villes',
                'description' => "Une conférence passionnante animée par des experts en environnement et urbanisme.\n\nIntervenants :\n- Dr. Marie Dubois (Climatologue)\n- Prof. Jean Martin (Urbaniste)\n- Sophie Laurent (Activiste environnementale)\n\nSujets abordés :\n- Impact du changement climatique sur les zones urbaines\n- Solutions d'adaptation\n- Le rôle des espaces verts\n- Questions-réponses avec le public",
                'type' => 'conference',
                'date_debut' => Carbon::now()->addDays(7)->setTime(18, 30),
                'date_fin' => Carbon::now()->addDays(7)->setTime(21, 0),
                'lieu' => 'Auditorium de la Mairie',
                'adresse' => '1 Place de l\'Hôtel de Ville, 75004 Paris',
                'capacite_max' => 200,
                'statut' => 'planifie',
            ],
            [
                'titre' => 'Atelier : Créer Son Jardin Urbain',
                'description' => "Apprenez à créer et entretenir votre propre jardin urbain, même dans un petit espace !\n\nCe que vous apprendrez :\n- Choisir les bonnes plantes pour votre espace\n- Techniques de jardinage en pots\n- Compostage urbain\n- Jardinage vertical\n- Entretien écologique\n\nMatériel fourni pour les exercices pratiques. Repartez avec vos premières plantes !",
                'type' => 'atelier',
                'date_debut' => Carbon::now()->addDays(10)->setTime(14, 0),
                'date_fin' => Carbon::now()->addDays(10)->setTime(17, 0),
                'lieu' => 'Centre Communautaire des Lilas',
                'adresse' => '23 Rue des Lilas, 75020 Paris',
                'capacite_max' => 25,
                'statut' => 'planifie',
            ],
            [
                'titre' => 'Plantation d\'un Verger Participatif',
                'description' => "Participez à la création d'un verger communautaire ! Nous planterons des arbres fruitiers qui seront accessibles à tous les habitants du quartier.\n\nTypes d'arbres :\n- Pommiers\n- Poiriers\n- Cerisiers\n- Pruniers\n\nActivités :\n- Préparation du terrain\n- Plantation des arbres\n- Installation de panneaux informatifs\n- Inauguration du verger",
                'type' => 'plantation',
                'date_debut' => Carbon::now()->addDays(20)->setTime(10, 0),
                'date_fin' => Carbon::now()->addDays(20)->setTime(16, 0),
                'lieu' => 'Jardin Partagé du Quartier',
                'adresse' => '78 Boulevard Voltaire, 75011 Paris',
                'capacite_max' => 40,
                'statut' => 'planifie',
            ],
            [
                'titre' => 'Atelier : Fabriquer un Hôtel à Insectes',
                'description' => "Découvrez l'importance de la biodiversité urbaine et apprenez à construire un hôtel à insectes pour votre jardin ou balcon.\n\nAu programme :\n- Comprendre le rôle des insectes pollinisateurs\n- Conception d'un hôtel à insectes\n- Construction étape par étape\n- Installation et entretien\n\nMatériaux fournis. Vous repartirez avec votre hôtel à insectes !",
                'type' => 'atelier',
                'date_debut' => Carbon::now()->addDays(12)->setTime(14, 30),
                'date_fin' => Carbon::now()->addDays(12)->setTime(17, 30),
                'lieu' => 'Maison de la Nature',
                'adresse' => '12 Rue de la Butte, 75019 Paris',
                'capacite_max' => 20,
                'statut' => 'planifie',
            ],
            [
                'titre' => 'Conférence : Agriculture Urbaine et Permaculture',
                'description' => "Explorez les possibilités de l'agriculture urbaine et les principes de la permaculture appliqués en ville.\n\nThèmes abordés :\n- Introduction à la permaculture\n- L'agriculture urbaine dans le monde\n- Projets réussis en France\n- Comment démarrer votre projet\n- Aspects légaux et réglementaires\n\nSuivi d'un échange avec des porteurs de projets locaux.",
                'type' => 'conference',
                'date_debut' => Carbon::now()->addDays(25)->setTime(19, 0),
                'date_fin' => Carbon::now()->addDays(25)->setTime(21, 30),
                'lieu' => 'Bibliothèque Municipale',
                'adresse' => '34 Rue de la Bibliothèque, 75005 Paris',
                'capacite_max' => 80,
                'statut' => 'planifie',
            ],
            [
                'titre' => 'Événement Passé : Festival de la Biodiversité',
                'description' => "Un événement mémorable célébrant la biodiversité urbaine avec des stands, des ateliers et des animations pour toute la famille.",
                'type' => 'atelier',
                'date_debut' => Carbon::now()->subDays(30)->setTime(10, 0),
                'date_fin' => Carbon::now()->subDays(30)->setTime(18, 0),
                'lieu' => 'Parc des Buttes-Chaumont',
                'adresse' => '1 Rue Botzaris, 75019 Paris',
                'capacite_max' => 500,
                'statut' => 'termine',
            ],
        ];

        $createdCount = 0;

        foreach ($events as $eventData) {
            $association = $associations->random();
            
            $event = Event::create(array_merge($eventData, [
                'association_id' => $association->id,
            ]));

            $this->command->info("✓ Événement créé : {$event->titre}");
            $createdCount++;
        }

        $this->command->info("\n✅ {$createdCount} événements créés avec succès !");
    }
}