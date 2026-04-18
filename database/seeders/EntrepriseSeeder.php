<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entreprise;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer une seule entreprise principale
        Entreprise::updateOrCreate(
            ['email' => 'contact@tech-innovation.fr'], // utiliser un champ qui existe
            [
                'nom' => 'Tech Innovation Solutions',
                'description' => 'Entreprise spécialisée dans le développement de solutions technologiques innovantes',
                'secteur_activite' => 'Technologies de l\'Information',
                'adresse' => '123 Avenue de l\'Innovation',
                'ville' => 'Paris',
                'code_postal' => '75001',
                'pays' => 'France',
                'site_web' => 'https://tech-innovation.fr',
                'telephone' => '01 23 45 67 88',
                'email' => 'contact@tech-innovation.fr',
                'conditions_stage' => 'Horaires: 8h30-17h30, Tenue: Business casual, Flexibilité possible avec accord du tuteur',
                'reglement_interne' => 'Respect des consignes de sécurité, Confidentialité des informations, Utilisation professionnelle des équipements',
                'active' => true
            ]
        );

        $this->command->info('Entreprise principale créée avec succès !');
        $this->command->info('Nom : Tech Innovation Solutions');
        $this->command->info('Contact : Sophie Martin');
        $this->command->info('Règlements internes configurés pour les stagiaires');
    }
}
