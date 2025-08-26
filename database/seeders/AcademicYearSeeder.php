<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\University;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $academicYears = [
            [
                'university_code' => 'KAD',
                'name' => '2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'state' => 'active',
            ],
            [
                'university_code' => 'KAD',
                'name' => '2025-2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-06-30',
                'state' => 'planned',
            ],
            [
                'university_code' => 'EPHEC',
                'name' => '2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'state' => 'active',
            ],
            [
                'university_code' => 'UCL',
                'name' => '2024-2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'state' => 'active',
            ],
        ];

        foreach ($academicYears as $year) {
            $university = University::where('code', $year['university_code'])->first();
            if ($university) {
                AcademicYear::create([
                    'university_id' => $university->id,
                    'name' => $year['name'],
                    'start_date' => $year['start_date'],
                    'end_date' => $year['end_date'],
                    'state' => $year['state'],
                ]);
            }
        }
    }
}
