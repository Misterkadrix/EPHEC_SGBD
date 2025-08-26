<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Group;
use App\Models\Site;
use App\Models\University;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'university_code' => 'KAD',
                'academic_year_name' => '2024-2025',
                'name' => 'INFO-1A',
                'quantity' => 30,
                'main_site_name' => 'Campus Principal',
                'min_size' => 25,
                'max_size' => 35,
            ],
            [
                'university_code' => 'KAD',
                'academic_year_name' => '2024-2025',
                'name' => 'INFO-1B',
                'quantity' => 28,
                'main_site_name' => 'Campus Principal',
                'min_size' => 25,
                'max_size' => 35,
            ],
            [
                'university_code' => 'KAD',
                'academic_year_name' => '2024-2025',
                'name' => 'MATH-2A',
                'quantity' => 25,
                'main_site_name' => 'Campus Principal',
                'min_size' => 20,
                'max_size' => 30,
            ],
            [
                'university_code' => 'KAD',
                'academic_year_name' => '2024-2025',
                'name' => 'PROG-3A',
                'quantity' => 22,
                'main_site_name' => 'Campus Technologique',
                'min_size' => 20,
                'max_size' => 25,
            ],
            [
                'university_code' => 'EPHEC',
                'academic_year_name' => '2024-2025',
                'name' => 'BUS-1A',
                'quantity' => 35,
                'main_site_name' => 'Site Woluwe',
                'min_size' => 30,
                'max_size' => 40,
            ],
        ];

        foreach ($groups as $group) {
            $university = University::where('code', $group['university_code'])->first();
            $academicYear = AcademicYear::where('name', $group['academic_year_name'])
                ->where('university_id', $university->id)
                ->first();
            $mainSite = Site::where('name', $group['main_site_name'])->first();
            
            if ($university && $academicYear && $mainSite) {
                Group::create([
                    'university_id' => $university->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => $group['name'],
                    'quantity' => $group['quantity'],
                    'main_site_id' => $mainSite->id,
                    'min_size' => $group['min_size'],
                    'max_size' => $group['max_size'],
                ]);
            }
        }
    }
}
