<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\OffreStage;

class AssignOffreToStagiaire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-offre-to-stagiaire {user_id?} {offre_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigner une offre de stage à un stagiaire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $offreId = $this->argument('offre_id');
        
        // Si aucun user_id n'est fourni, lister les stagiaires
        if (!$userId) {
            $stagiaires = User::whereHas('role', function($query) {
                $query->where('name', 'stagiaire');
            })->get();
            
            $this->info('Stagiaires disponibles:');
            foreach ($stagiaires as $stagiaire) {
                $this->line("- ID: {$stagiaire->id} | Nom: {$stagiaire->name} | Email: {$stagiaire->email} | offre_stage_id: " . ($stagiaire->offre_stage_id ?? 'NULL'));
            }
            return;
        }
        
        // Si aucun offre_id n'est fourni, lister les offres
        if (!$offreId) {
            $offres = OffreStage::all();
            $this->info('Offres de stage disponibles:');
            foreach ($offres as $offre) {
                $this->line("- ID: {$offre->id} | Poste: {$offre->poste} | Entreprise: " . ($offre->entreprise->nom ?? 'N/A'));
            }
            return;
        }
        
        // Assigner l'offre au stagiaire
        $stagiaire = User::find($userId);
        $offre = OffreStage::find($offreId);
        
        if (!$stagiaire) {
            $this->error("Stagiaire avec ID {$userId} non trouvé");
            return;
        }
        
        if (!$offre) {
            $this->error("Offre avec ID {$offreId} non trouvée");
            return;
        }
        
        $stagiaire->offre_stage_id = $offreId;
        $stagiaire->save();
        
        $this->info("Offre '{$offre->poste}' assignée au stagiaire '{$stagiaire->name}' avec succès!");
    }
}
