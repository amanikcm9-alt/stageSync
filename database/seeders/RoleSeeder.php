<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrateur du système avec tous les droits'
            ],
            [
                'name' => 'rh',
                'description' => 'Responsable des ressources humaines'
            ],
            [
                'name' => 'encadrant',
                'description' => 'Encadrant de stage'
            ],
            [
                'name' => 'stagiaire',
                'description' => 'Stagiaire en entreprise'
            ]
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }

        $this->command->info('Rôles créés avec succès !');
        $this->command->info('- Admin');
        $this->command->info('- RH');
        $this->command->info('- Encadrant');
        $this->command->info('- Stagiaire');
    }
}
