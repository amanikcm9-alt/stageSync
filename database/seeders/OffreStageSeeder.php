<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OffreStage;
use App\Models\Entreprise;

class OffreStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'entreprise principale
        $entreprise = Entreprise::where('nom', 'Tech Innovation Solutions')->first();
        
        if (!$entreprise) {
            $this->command->error('Veuillez exécuter EntrepriseSeeder en premier !');
            return;
        }

        // Créer des offres de stage pour l'entreprise principale
        $offres = [
            [
                'titre' => 'Développeur Web Full Stack',
                'description' => 'Participer au développement d\'applications web modernes en utilisant les dernières technologies. Vous travaillerez sur des projets réels avec une équipe expérimentée.',
                'missions' => '- Développement de fonctionnalités web
- Maintenance d\'applications existantes
- Participation aux revues de code
- Rédaction de documentation technique
- Collaboration avec l\'équipe produit',
                'secteur' => 'Développement Web',
                'duree_semaines' => 24,
                'date_debut' => now()->addMonths(2),
                'remuneration' => 800.00,
                'lieu' => 'Paris (Hybride : 2 jours télétravail)',
                'statut' => 'publiee',
                'type_stage' => 'entreprise',
            ],
            [
                'titre' => 'Data Analyst Junior',
                'description' => 'Analyser et interpréter des données pour aider à la prise de décision. Vous apprendrez à utiliser des outils de Business Intelligence et de visualisation.',
                'missions' => '- Nettoyage et préparation des données
- Création de dashboards et rapports
- Analyse des tendances et KPIs
- Support aux équipes métier
- Présentation des résultats',
                'secteur' => 'Data Analytics',
                'duree_semaines' => 16,
                'date_debut' => now()->addMonth(),
                'remuneration' => 750.00,
                'lieu' => 'Paris (Présentiel)',
                'statut' => 'publiee',
                'type_stage' => 'entreprise',
            ],
            [
                'titre' => 'UX/UI Designer',
                'description' => 'Participer à la conception d\'interfaces utilisateur intuitives et esthétiques. Vous travaillerez sur la refonte de nos produits digitaux.',
                'missions' => '- Création de wireframes et maquettes
- Design d\'interfaces mobile et web
- Tests utilisateurs et itérations
- Collaboration avec les développeurs
- Veille sur les tendances UX',
                'secteur' => 'Design',
                'duree_semaines' => 12,
                'date_debut' => now()->addWeeks(3),
                'remuneration' => 700.00,
                'lieu' => 'Paris (Présentiel)',
                'statut' => 'publiee',
                'type_stage' => 'entreprise',
            ],
            [
                'titre' => 'Marketing Digital',
                'description' => 'Participer à la stratégie de marketing digital de l\'entreprise. Vous gérerez nos campagnes sur les réseaux sociaux et optimiserez notre présence en ligne.',
                'missions' => '- Gestion des réseaux sociaux
- Création de contenu
- Analyse des performances
- Optimisation SEO
- Participation aux campagnes marketing',
                'secteur' => 'Marketing',
                'duree_semaines' => 20,
                'date_debut' => now()->addMonths(1),
                'remuneration' => 650.00,
                'lieu' => 'Paris (Hybride : 3 jours télétravail)',
                'statut' => 'publiee',
                'type_stage' => 'entreprise',
            ],
            [
                'titre' => 'Cybersécurité',
                'description' => 'Découvrir les fondamentaux de la cybersécurité en participant à la protection des systèmes d\'information de l\'entreprise.',
                'missions' => '- Analyse des vulnérabilités
- Veille sur les menaces
- Participation aux audits de sécurité
- Rédaction de procédures
- Sensibilisation des équipes',
                'secteur' => 'Sécurité',
                'duree_semaines' => 24,
                'date_debut' => now()->addMonths(3),
                'remuneration' => 900.00,
                'lieu' => 'Paris (Présentiel)',
                'statut' => 'brouillon',
                'type_stage' => 'entreprise',
            ],
        ];

        // Récupérer l'utilisateur RH (admin par défaut)
        $rhUser = \App\Models\User::where('email', 'admin@gmail.com')->first();
        
        foreach ($offres as $offreData) {
            OffreStage::create(array_merge($offreData, [
                'entreprise_id' => $entreprise->id,
                'rh_id' => $rhUser ? $rhUser->id : 1,
            ]));
        }

        $this->command->info('Offres de stage créées avec succès pour Tech Innovation Solutions !');
        $this->command->info('- 4 offres publiées');
        $this->command->info('- 1 offre en brouillon');
        $this->command->info('- Secteurs : Développement, Data, Design, Marketing, Sécurité');
    }
}
