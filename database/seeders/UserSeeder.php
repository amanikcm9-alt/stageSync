<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les rôles
        $adminRole = Role::where('name', 'admin')->first();
        $rhRole = Role::where('name', 'rh')->first();
        $encadrantRole = Role::where('name', 'encadrant')->first();
        $stagiaireRole = Role::where('name', 'stagiaire')->first();

        // Créer les utilisateurs de test
        $users = [
            [
                'nom' => 'Admin',
                'prenom' => 'User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'role_id' => 1
            ],
           
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Mettre à jour les encadrants pour les stagiaires
        $thomas = User::where('email', 't.durand@tech-innovation.fr')->first();
        $marie = User::where('email', 'm.lefebvre@tech-innovation.fr')->first();
        $lucas = User::where('email', 'l.bernard@tech-innovation.fr')->first();
        $emma = User::where('email', 'e.petit@tech-innovation.fr')->first();

        if ($thomas && $lucas) {
            $lucas->update(['encadrant_id' => $thomas->id]);
        }
        if ($marie && $emma) {
            $emma->update(['encadrant_id' => $marie->id]);
        }

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('- 1 Admin : admin@tech-innovation.fr / password');
        $this->command->info('- 1 RH : s.martin@tech-innovation.fr / password');
        $this->command->info('- 2 Encadrants : t.durand@tech-innovation.fr, m.lefebvre@tech-innovation.fr');
        $this->command->info('- 2 Stagiaires : l.bernard@tech-innovation.fr, e.petit@tech-innovation.fr');
    }
}
