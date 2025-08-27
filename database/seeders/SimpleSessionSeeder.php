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
            // Créer 3 sessions consécutives par groupe sur la même journée
            $sessionDate = $baseDate->copy()->addDays($index);
            
            // Trouver un cours de la même université
            $course = Course::where('university_id', $group->university_id)->first();
            
            if (!$course) continue;
            
            // PRIORITÉ : Utiliser le site principal du groupe
            $mainSite = Site::find($group->main_site_id);
            if (!$mainSite) continue;
            
            // Trouver des salles du site principal
            $mainSiteRooms = Room::where('site_id', $mainSite->id)->get();
            
            // Créer 3 sessions consécutives : 9h-10h, 10h-11h, 11h-12h
            for ($i = 0; $i < 3; $i++) {
                $hour = 9 + $i; // 9h, 10h, 11h
                
                // Alterner entre les salles du site principal
                $room = $mainSiteRooms->get($i % $mainSiteRooms->count());
                
                if (!$room) continue;
                
                // Créer la session
                $session = CourseSession::create([
                    'academic_year_id' => $group->academic_year_id,
                    'course_id' => $course->id,
                    'site_id' => $mainSite->id, // Site principal du groupe
                    'room_id' => $room->id,
                    'start_at' => $sessionDate->copy()->setTime($hour, 0, 0),
                    'end_at' => $sessionDate->copy()->setTime($hour + 1, 0, 0),
                ]);
                
                // Lier la session au groupe
                $session->sessionGroups()->create([
                    'group_id' => $group->id,
                ]);
                
                echo "Session créée pour le groupe {$group->name} le {$sessionDate->format('d/m/Y')} à {$hour}:00 sur le site principal {$mainSite->name}\n";
            }
            
            // Créer une session sur un autre site pour tester les déplacements inter-sites
            $otherSite = Site::where('university_id', $group->university_id)
                ->where('id', '!=', $mainSite->id)
                ->first();
            
            if ($otherSite) {
                $otherSiteRoom = Room::where('site_id', $otherSite->id)->first();
                
                if ($otherSiteRoom) {
                    $session = CourseSession::create([
                        'academic_year_id' => $group->academic_year_id,
                        'course_id' => $course->id,
                        'site_id' => $otherSite->id, // Site secondaire
                        'room_id' => $otherSiteRoom->id,
                        'start_at' => $sessionDate->copy()->setTime(14, 0, 0), // 14h-15h
                        'end_at' => $sessionDate->copy()->setTime(15, 0, 0),
                    ]);
                    
                    $session->sessionGroups()->create([
                        'group_id' => $group->id,
                    ]);
                    
                    echo "Session inter-site créée pour le groupe {$group->name} le {$sessionDate->format('d/m/Y')} à 14:00 sur {$otherSite->name}\n";
                }
            }
        }
        
        echo "Sessions créées avec succès !\n";
    }
}
