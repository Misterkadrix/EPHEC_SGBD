<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseSession;
use App\Models\Group;
use App\Models\Course;
use App\Models\Site;
use App\Models\Room;
use Carbon\Carbon;

class SimpleSessionSeeder extends Seeder
{
    public function run()
    {
        // Récupérer tous les groupes
        $groups = Group::all();
        
        // Dates de base pour septembre 2024
        $baseDate = Carbon::create(2024, 9, 2); // 2 septembre 2024
        
        foreach ($groups as $index => $group) {
            // Créer 2 sessions par groupe avec des dates et heures différentes
            for ($i = 0; $i < 2; $i++) {
                $sessionDate = $baseDate->copy()->addDays($index * 2 + $i);
                
                // Trouver un cours de la même université
                $course = Course::where('university_id', $group->university_id)->first();
                
                if (!$course) continue;
                
                // Trouver un site de la même université
                $site = Site::where('university_id', $group->university_id)->first();
                
                if (!$site) continue;
                
                // Trouver une salle du site
                $rooms = Room::where('site_id', $site->id)->get();
                $room = $rooms->get($index % $rooms->count());
                
                if (!$room) continue;
                
                // Heure unique pour chaque groupe
                $hour = 9 + ($index * 2) + $i;
                
                // Créer la session
                $session = CourseSession::create([
                    'academic_year_id' => $group->academic_year_id,
                    'course_id' => $course->id,
                    'site_id' => $site->id,
                    'room_id' => $room->id,
                    'start_at' => $sessionDate->copy()->setTime($hour, 0, 0),
                    'end_at' => $sessionDate->copy()->setTime($hour + 1, 0, 0),
                ]);
                
                // Lier la session au groupe
                $session->sessionGroups()->create([
                    'group_id' => $group->id,
                ]);
                
                echo "Session créée pour le groupe {$group->name} le {$sessionDate->format('d/m/Y')} à {$hour}:00\n";
            }
        }
        
        echo "Sessions créées avec succès !\n";
    }
}
