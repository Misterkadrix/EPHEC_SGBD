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
            // Créer des sessions par groupe sur la même journée
            $sessionDate = $baseDate->copy()->addDays($index);
            
            // Trouver un cours de la même université
            $course = Course::where('university_id', $group->university_id)->first();
            
            if (!$course) continue;
            
            // PRIORITÉ : Utiliser le site principal du groupe
            $mainSite = Site::find($group->main_site_id);
            if (!$mainSite) continue;
            
            // Trouver des salles du site principal
            $mainSiteRooms = Room::where('site_id', $mainSite->id)->get();
            
            // Créer des sessions consécutives sur le site principal (8h-9h, 9h-10h, 10h-11h)
            // Ces sessions s'enchaînent directement car même site (déplacement 5min)
            for ($i = 0; $i < 3; $i++) {
                $hour = 8 + $i; // 8h, 9h, 10h
                
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
            // Cette session sera à 12h-13h (après la session de 10h-11h)
            // Il y aura donc 1h de déplacement entre 11h et 12h
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
                        'start_at' => $sessionDate->copy()->setTime(12, 0, 0), // 12h-13h
                        'end_at' => $sessionDate->copy()->setTime(13, 0, 0),
                    ]);
                    
                    $session->sessionGroups()->create([
                        'group_id' => $group->id,
                    ]);
                    
                    echo "Session inter-site créée pour le groupe {$group->name} le {$sessionDate->format('d/m/Y')} à 12:00 sur {$otherSite->name}\n";
                }
            }
            
            // Créer une session de retour sur le site principal à 14h-15h
            // Il y aura donc 1h de déplacement entre 13h et 14h
            if ($mainSiteRooms->count() > 0) {
                $room = $mainSiteRooms->first();
                
                $session = CourseSession::create([
                    'academic_year_id' => $group->academic_year_id,
                    'course_id' => $course->id,
                    'site_id' => $mainSite->id, // Retour au site principal
                    'room_id' => $room->id,
                    'start_at' => $sessionDate->copy()->setTime(14, 0, 0), // 14h-15h
                    'end_at' => $sessionDate->copy()->setTime(15, 0, 0),
                ]);
                
                $session->sessionGroups()->create([
                    'group_id' => $group->id,
                ]);
                
                echo "Session de retour créée pour le groupe {$group->name} le {$sessionDate->format('d/m/Y')} à 14:00 sur le site principal {$mainSite->name}\n";
            }
        }
        
        echo "Sessions créées avec succès !\n";
    }
}
