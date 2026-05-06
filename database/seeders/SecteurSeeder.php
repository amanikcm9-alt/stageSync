<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Secteur;

class SecteurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        

        foreach ($secteurs as $secteur) {
            Secteur::create($secteur);
        }

        $this->command->info('Secteurs créés avec succès !');
    }
}
