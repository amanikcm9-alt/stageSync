<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeStage;

class TypeStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach ($types as $type) {
            TypeStage::create($type);
        }

        $this->command->info('Types de stage créés avec succès !');
    }
}
