<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\University;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'university_code' => 'KAD',
                'code' => 'INFO101',
                'title' => 'Introduction à l\'Informatique',
                'description' => 'Cours de base pour comprendre les concepts fondamentaux de l\'informatique',
            ],
            [
                'university_code' => 'KAD',
                'code' => 'MATH201',
                'title' => 'Mathématiques Appliquées',
                'description' => 'Applications des mathématiques dans le domaine technologique',
            ],
            [
                'university_code' => 'KAD',
                'code' => 'PROG301',
                'title' => 'Programmation Avancée',
                'description' => 'Techniques avancées de programmation et bonnes pratiques',
            ],
            [
                'university_code' => 'EPHEC',
                'code' => 'BUS101',
                'title' => 'Fondements du Business',
                'description' => 'Introduction aux concepts de base du monde des affaires',
            ],
            [
                'university_code' => 'UCL',
                'code' => 'PHYS101',
                'title' => 'Physique Générale',
                'description' => 'Cours de physique pour étudiants en sciences',
            ],
        ];

        foreach ($courses as $course) {
            $university = University::where('code', $course['university_code'])->first();
            if ($university) {
                Course::create([
                    'university_id' => $university->id,
                    'code' => $course['code'],
                    'title' => $course['title'],
                    'description' => $course['description'],
                ]);
            }
        }
    }
}
