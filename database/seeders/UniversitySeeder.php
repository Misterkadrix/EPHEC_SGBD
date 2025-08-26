<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = [
            [
                'code' => 'KAD',
                'name' => 'Université KAD',
            ],
            [
                'code' => 'EPHEC',
                'name' => 'Haute École EPHEC',
            ],
            [
                'code' => 'UCL',
                'name' => 'Université Catholique de Louvain',
            ],
        ];

        foreach ($universities as $university) {
            University::create($university);
        }
    }
}
