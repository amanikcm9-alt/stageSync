<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyer la table sessions
        DB::table('sessions')->truncate();
        
        $this->command->info('Table sessions nettoyée avec succès !');
        $this->command->info('Le problème "page expired" devrait être résolu.');
    }
}
