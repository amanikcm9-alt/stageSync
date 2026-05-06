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

        // Créer 5 utilisateurs par rôle
        $users = [
<<<<<<< HEAD
            [
                'nom' => 'Admin',
                'prenom' => 'User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'role_id' => 1
            ],
           
=======
            // 5 Admins
            ['nom' => 'Admin', 'prenom' => 'Principal', 'email' => 'admin1@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 1],
            ['nom' => 'Admin', 'prenom' => 'Système', 'email' => 'admin2@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 1],
            ['nom' => 'Admin', 'prenom' => 'Réseau', 'email' => 'admin3@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 1],
            ['nom' => 'Admin', 'prenom' => 'Base', 'email' => 'admin4@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 1],
            ['nom' => 'Admin', 'prenom' => 'Sécurité', 'email' => 'admin5@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 1],
            
            // 5 RH
            ['nom' => 'Martin', 'prenom' => 'Sophie', 'email' => 's.martin@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 2],
            ['nom' => 'Dubois', 'prenom' => 'Pierre', 'email' => 'p.dubois@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 2],
            ['nom' => 'Robert', 'prenom' => 'Isabelle', 'email' => 'i.robert@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 2],
            ['nom' => 'Richard', 'prenom' => 'Nicolas', 'email' => 'n.richard@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 2],
            ['nom' => 'Petit', 'prenom' => 'Camille', 'email' => 'c.petit@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 2],
            
            // 5 Encadrants
            ['nom' => 'Durand', 'prenom' => 'Thomas', 'email' => 't.durand@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 3],
            ['nom' => 'Lefebvre', 'prenom' => 'Marie', 'email' => 'm.lefebvre@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 3],
            ['nom' => 'Moreau', 'prenom' => 'Jean', 'email' => 'j.moreau@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 3],
            ['nom' => 'Laurent', 'prenom' => 'Sophie', 'email' => 's.laurent@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 3],
            ['nom' => 'Garcia', 'prenom' => 'David', 'email' => 'd.garcia@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 3],
            
            // 5 Stagiaires
            ['nom' => 'Bernard', 'prenom' => 'Lucas', 'email' => 'l.bernard@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 4],
            ['nom' => 'Petit', 'prenom' => 'Emma', 'email' => 'e.petit@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 4],
            ['nom' => 'Rousseau', 'prenom' => 'Léa', 'email' => 'l.rousseau@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 4],
            ['nom' => 'Muller', 'prenom' => 'Hugo', 'email' => 'h.muller@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 4],
            ['nom' => 'Lemoine', 'prenom' => 'Chloé', 'email' => 'c.lemoine@tech-innovation.fr', 'password' => Hash::make('password'), 'role_id' => 4]
>>>>>>> 0796fcd31ef0870ffca50c5d831cc797299e7912
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Assigner les encadrants aux stagiaires (répartition équilibrée)
        $encadrants = [
            User::where('email', 't.durand@tech-innovation.fr')->first(),
            User::where('email', 'm.lefebvre@tech-innovation.fr')->first(),
            User::where('email', 'j.moreau@tech-innovation.fr')->first(),
            User::where('email', 's.laurent@tech-innovation.fr')->first(),
            User::where('email', 'd.garcia@tech-innovation.fr')->first()
        ];

        $stagiaires = [
            User::where('email', 'l.bernard@tech-innovation.fr')->first(),
            User::where('email', 'e.petit@tech-innovation.fr')->first(),
            User::where('email', 'l.rousseau@tech-innovation.fr')->first(),
            User::where('email', 'h.muller@tech-innovation.fr')->first(),
            User::where('email', 'c.lemoine@tech-innovation.fr')->first()
        ];

        // Assigner chaque stagiaire à un encadrant différent
        for ($i = 0; $i < 5; $i++) {
            if ($encadrants[$i] && $stagiaires[$i]) {
                $stagiaires[$i]->update(['encadrant_id' => $encadrants[$i]->id]);
            }
        }

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('');
        $this->command->info('=== ADMINS (5) ===');
        $this->command->info('- admin1@tech-innovation.fr / password');
        $this->command->info('- admin2@tech-innovation.fr / password');
        $this->command->info('- admin3@tech-innovation.fr / password');
        $this->command->info('- admin4@tech-innovation.fr / password');
        $this->command->info('- admin5@tech-innovation.fr / password');
        $this->command->info('');
        $this->command->info('=== RH (5) ===');
        $this->command->info('- s.martin@tech-innovation.fr / password');
        $this->command->info('- p.dubois@tech-innovation.fr / password');
        $this->command->info('- i.robert@tech-innovation.fr / password');
        $this->command->info('- n.richard@tech-innovation.fr / password');
        $this->command->info('- c.petit@tech-innovation.fr / password');
        $this->command->info('');
        $this->command->info('=== ENCADRANTS (5) ===');
        $this->command->info('- t.durand@tech-innovation.fr / password (encadrant de l.bernard@tech-innovation.fr)');
        $this->command->info('- m.lefebvre@tech-innovation.fr / password (encadrant de e.petit@tech-innovation.fr)');
        $this->command->info('- j.moreau@tech-innovation.fr / password (encadrant de l.rousseau@tech-innovation.fr)');
        $this->command->info('- s.laurent@tech-innovation.fr / password (encadrant de h.muller@tech-innovation.fr)');
        $this->command->info('- d.garcia@tech-innovation.fr / password (encadrant de c.lemoine@tech-innovation.fr)');
        $this->command->info('');
        $this->command->info('=== STAGIAIRES (5) ===');
        $this->command->info('- l.bernard@tech-innovation.fr / password');
        $this->command->info('- e.petit@tech-innovation.fr / password');
        $this->command->info('- l.rousseau@tech-innovation.fr / password');
        $this->command->info('- h.muller@tech-innovation.fr / password');
        $this->command->info('- c.lemoine@tech-innovation.fr / password');
    }
}
