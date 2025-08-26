<?php

namespace Database\Seeders;

use App\Models\Holiday;
use App\Models\University;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            // Fériés globaux (pas d'université spécifique)
            [
                'name' => 'Nouvel An',
                'date' => '2025-01-01',
                'year' => 2025,
                'university_code' => null,
            ],
            [
                'name' => 'Fête du Travail',
                'date' => '2025-05-01',
                'year' => 2025,
                'university_code' => null,
            ],
            [
                'name' => 'Fête Nationale',
                'date' => '2025-07-21',
                'year' => 2025,
                'university_code' => null,
            ],
            [
                'name' => 'Assomption',
                'date' => '2025-08-15',
                'year' => 2025,
                'university_code' => null,
            ],
            [
                'name' => 'Toussaint',
                'date' => '2025-11-01',
                'year' => 2025,
                'university_code' => null,
            ],
            [
                'name' => 'Noël',
                'date' => '2025-12-25',
                'year' => 2025,
                'university_code' => null,
            ],
            
            // Fériés spécifiques à l'université KAD
            [
                'name' => 'Journée de l\'Université KAD',
                'date' => '2025-03-15',
                'year' => 2025,
                'university_code' => 'KAD',
            ],
            [
                'name' => 'Congé Académique KAD',
                'date' => '2025-04-10',
                'year' => 2025,
                'university_code' => 'KAD',
            ],
            
            // Fériés spécifiques à EPHEC
            [
                'name' => 'Journée EPHEC',
                'date' => '2025-02-20',
                'year' => 2025,
                'university_code' => 'EPHEC',
            ],
        ];

        foreach ($holidays as $holiday) {
            $universityId = null;
            if ($holiday['university_code']) {
                $university = University::where('code', $holiday['university_code'])->first();
                $universityId = $university ? $university->id : null;
            }
            
            Holiday::create([
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'year' => $holiday['year'],
                'university_id' => $universityId,
            ]);
        }
    }
}
