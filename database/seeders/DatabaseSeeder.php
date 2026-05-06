<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            EntrepriseSeeder::class,
            // SecteurSeeder::class, // Désactivé pour garder les listes vides
            // TypeStageSeeder::class, // Désactivé pour garder les listes vides
            UserSeeder::class,
            // OffreStageSeeder::class, // Désactivé car dépend des secteurs/types
        ]);
    }
}
