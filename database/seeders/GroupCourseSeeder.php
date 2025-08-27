<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Course;

class GroupCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les groupes existants
        $groups = Group::where('academic_year_id', 1)->get(); // Année 2024-2025
        
        // Récupérer les cours existants
        $courses = Course::where('university_id', 1)->get(); // Université KAD
        
        if ($groups->isEmpty() || $courses->isEmpty()) {
            $this->command->warn('Aucun groupe ou cours trouvé pour créer des associations');
            return;
        }

        // Associer chaque groupe à 2-3 cours
        foreach ($groups as $group) {
            // Sélectionner aléatoirement 2-3 cours pour ce groupe
            $selectedCourses = $courses->random(rand(2, min(3, $courses->count())));
            
            foreach ($selectedCourses as $course) {
                // Vérifier que l'association n'existe pas déjà
                if (!$group->courses()->where('course_id', $course->id)->exists()) {
                    $group->courses()->attach($course->id);
                    $this->command->info("Groupe {$group->name} associé au cours {$course->code}");
                }
            }
        }

        $this->command->info('Associations groupes-cours créées avec succès !');
    }
}
