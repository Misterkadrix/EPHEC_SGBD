<?php

namespace Database\Seeders;

use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class EquipmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $equipmentTypes = [
            ['label' => 'PC Lab'],
            ['label' => 'Baffles'],
            ['label' => 'Projecteur'],
            ['label' => 'Tableau Blanc Interactif'],
            ['label' => 'Microphone'],
            ['label' => 'Caméra'],
            ['label' => 'Écran de Projection'],
            ['label' => 'Système Audio'],
            ['label' => 'Ordinateur Portable'],
            ['label' => 'Tablette'],
        ];

        foreach ($equipmentTypes as $type) {
            EquipmentType::create($type);
        }
    }
}
