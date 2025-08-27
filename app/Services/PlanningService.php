<?php

namespace App\Services;

use App\Models\CourseSession;
use App\Models\Deplacement;
use App\Models\Group;
use App\Models\Room;
use App\Models\Site;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PlanningService
{
    // Configuration des paramètres métier
    private const COURSE_DURATION_MINUTES = 60;
    private const INTER_SITE_TRAVEL_TIME_MINUTES = 60;
    private const MIN_GROUP_SIZE = 20;
    private const MAX_GROUP_SIZE = 40;
    
    /**
     * Générer automatiquement les horaires pour une année académique
     */
    public function generateScheduleForAcademicYear(AcademicYear $academicYear): array
    {
        Log::info('PlanningService: Début de génération des horaires', [
            'academic_year' => $academicYear->name,
            'university_id' => $academicYear->university_id
        ]);

        try {
            // Récupérer tous les groupes de l'année académique
            $groups = Group::where('academic_year_id', $academicYear->id)
                ->with(['university', 'mainSite', 'courses'])
                ->get();

            if ($groups->isEmpty()) {
                Log::warning('PlanningService: Aucun groupe trouvé pour cette année académique');
                return ['success' => false, 'message' => 'Aucun groupe trouvé'];
            }

            $generatedSessions = [];
            $errors = [];

            foreach ($groups as $group) {
                Log::info('PlanningService: Traitement du groupe', [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'courses_count' => $group->courses->count()
                ]);

                // Générer les sessions pour chaque cours du groupe
                foreach ($group->courses as $course) {
                    $result = $this->generateSessionsForGroupCourse($group, $course, $academicYear);
                    
                    if ($result['success']) {
                        $generatedSessions = array_merge($generatedSessions, $result['sessions']);
                    } else {
                        $errors[] = $result['message'];
                    }
                }
            }

            Log::info('PlanningService: Génération terminée', [
                'sessions_generated' => count($generatedSessions),
                'errors_count' => count($errors)
            ]);

            return [
                'success' => true,
                'sessions' => $generatedSessions,
                'errors' => $errors,
                'total_generated' => count($generatedSessions)
            ];

        } catch (\Exception $e) {
            Log::error('PlanningService: Erreur lors de la génération', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Générer les sessions pour un groupe et un cours spécifiques
     */
    private function generateSessionsForGroupCourse(Group $group, Course $course, AcademicYear $academicYear): array
    {
        Log::info('PlanningService: Génération sessions pour groupe/cours', [
            'group_id' => $group->id,
            'course_id' => $course->id,
            'group_size' => $group->quantity
        ]);

        // Vérifier la taille du groupe
        if ($group->quantity < self::MIN_GROUP_SIZE || $group->quantity > self::MAX_GROUP_SIZE) {
            return [
                'success' => false,
                'message' => "Taille du groupe invalide: {$group->quantity} (min: " . self::MIN_GROUP_SIZE . ", max: " . self::MAX_GROUP_SIZE . ")"
            ];
        }

        // Déterminer le site principal (préférence du groupe)
        $mainSite = $group->mainSite;
        if (!$mainSite) {
            return [
                'success' => false,
                'message' => "Aucun site principal défini pour le groupe {$group->name}"
            ];
        }

        // Vérifier que le site est ouvert pendant l'année académique
        if (!$this->isSiteAvailableDuringAcademicYear($mainSite, $academicYear)) {
            return [
                'success' => false,
                'message' => "Le site principal {$mainSite->name} n'est pas disponible pendant l'année académique"
            ];
        }

        // Trouver une salle disponible sur le site principal
        $availableRoom = $this->findAvailableRoom($mainSite, $group->quantity, $course);
        
        if (!$availableRoom) {
            Log::info('PlanningService: Aucune salle disponible sur le site principal, recherche de fallback');
            
            // Fallback: chercher une salle sur d'autres sites autorisés
            $fallbackRoom = $this->findFallbackRoom($group, $course, $mainSite);
            if ($fallbackRoom) {
                $availableRoom = $fallbackRoom;
                Log::info('PlanningService: Salle de fallback trouvée', [
                    'room_id' => $availableRoom->id,
                    'site_id' => $availableRoom->site_id
                ]);
            } else {
                return [
                    'success' => false,
                    'message' => "Aucune salle disponible pour le groupe {$group->name} et le cours {$course->code}"
                ];
            }
        }

        // Générer les créneaux horaires
        $timeSlots = $this->generateTimeSlots($mainSite, $academicYear);
        
        if (empty($timeSlots)) {
            return [
                'success' => false,
                'message' => "Aucun créneau horaire disponible sur le site {$mainSite->name}"
            ];
        }

        // Créer les sessions
        $sessions = [];
        foreach ($timeSlots as $timeSlot) {
            $session = $this->createSession($group, $course, $availableRoom, $timeSlot, $academicYear);
            if ($session) {
                $sessions[] = $session;
            }
        }

        return [
            'success' => true,
            'sessions' => $sessions
        ];
    }

    /**
     * Vérifier si un site est disponible pendant l'année académique
     */
    private function isSiteAvailableDuringAcademicYear(Site $site, AcademicYear $academicYear): bool
    {
        // Vérifier que le site a des jours d'ouverture
        if (empty($site->active_days)) {
            return false;
        }

        // Vérifier que le site a des horaires d'ouverture
        if (!$site->day_start || !$site->day_end) {
            return false;
        }

        return true;
    }

    /**
     * Trouver une salle disponible sur un site
     */
    private function findAvailableRoom(Site $site, int $groupSize, Course $course): ?Room
    {
        return Room::where('site_id', $site->id)
            ->where('capacity', '>=', $groupSize)
            ->orderBy('capacity', 'asc') // Prendre la plus petite salle qui convient
            ->first();
    }

    /**
     * Trouver une salle de fallback sur d'autres sites
     */
    private function findFallbackRoom(Group $group, Course $course, Site $mainSite): ?Room
    {
        // Chercher sur les sites de la même université
        $fallbackSites = Site::where('university_id', $group->university_id)
            ->where('id', '!=', $mainSite->id)
            ->get();

        foreach ($fallbackSites as $site) {
            $room = $this->findAvailableRoom($site, $group->quantity, $course);
            if ($room) {
                return $room;
            }
        }

        return null;
    }

    /**
     * Générer les créneaux horaires disponibles
     */
    private function generateTimeSlots(Site $site, AcademicYear $academicYear): array
    {
        $timeSlots = [];
        $startDate = Carbon::parse($academicYear->start_date);
        $endDate = Carbon::parse($academicYear->end_date);

        // Convertir les jours d'ouverture en numéros Carbon
        $dayMapping = [
            'MO' => Carbon::MONDAY,
            'TU' => Carbon::TUESDAY,
            'WE' => Carbon::WEDNESDAY,
            'TH' => Carbon::THURSDAY,
            'FR' => Carbon::FRIDAY,
            'SA' => Carbon::SATURDAY,
            'SU' => Carbon::SUNDAY
        ];

        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek;
            
            // Vérifier si ce jour est un jour d'ouverture
            $dayCode = array_search($dayOfWeek, $dayMapping);
            if (in_array($dayCode, $site->active_days)) {
                // Générer les créneaux pour ce jour
                $daySlots = $this->generateDayTimeSlots($site, $currentDate);
                $timeSlots = array_merge($timeSlots, $daySlots);
            }
            
            $currentDate->addDay();
        }

        return $timeSlots;
    }

    /**
     * Générer les créneaux horaires pour une journée
     */
    private function generateDayTimeSlots(Site $site, Carbon $date): array
    {
        $slots = [];
        $startTime = Carbon::parse($site->day_start);
        $endTime = Carbon::parse($site->day_end);
        
        $currentTime = $startTime->copy();
        
        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes(self::COURSE_DURATION_MINUTES);
            
            if ($slotEnd->lte($endTime)) {
                $slots[] = [
                    'start_at' => $date->copy()->setTime($currentTime->hour, $currentTime->minute),
                    'end_at' => $date->copy()->setTime($slotEnd->hour, $slotEnd->minute)
                ];
            }
            
            $currentTime->addMinutes(self::COURSE_DURATION_MINUTES);
        }
        
        return $slots;
    }

    /**
     * Créer une session de cours
     */
    private function createSession(Group $group, Course $course, Room $room, array $timeSlot, AcademicYear $academicYear): ?CourseSession
    {
        // Vérifier qu'il n'y a pas de collision
        if ($this->hasCollision($room, $timeSlot['start_at'], $timeSlot['end_at'])) {
            Log::info('PlanningService: Collision détectée, session non créée', [
                'room_id' => $room->id,
                'start_at' => $timeSlot['start_at'],
                'end_at' => $timeSlot['end_at']
            ]);
            return null;
        }

        try {
            $session = CourseSession::create([
                'academic_year_id' => $academicYear->id,
                'course_id' => $course->id,
                'site_id' => $room->site_id,
                'room_id' => $room->id,
                'start_at' => $timeSlot['start_at'],
                'end_at' => $timeSlot['end_at'],
            ]);

            // Associer le groupe à la session
            $session->sessionGroups()->create([
                'group_id' => $group->id,
            ]);

            // Associer les équipements requis
            $this->assignEquipmentToSession($session, $course, $room);

            Log::info('PlanningService: Session créée avec succès', [
                'session_id' => $session->id,
                'group_id' => $group->id,
                'course_id' => $course->id,
                'room_id' => $room->id,
                'start_at' => $timeSlot['start_at'],
                'end_at' => $timeSlot['end_at']
            ]);

            return $session;

        } catch (\Exception $e) {
            Log::error('PlanningService: Erreur lors de la création de la session', [
                'error' => $e->getMessage(),
                'group_id' => $group->id,
                'course_id' => $course->id,
                'room_id' => $room->id
            ]);
            return null;
        }
    }

    /**
     * Vérifier s'il y a une collision avec une session existante
     */
    private function hasCollision(Room $room, Carbon $startAt, Carbon $endAt): bool
    {
        return CourseSession::where('room_id', $room->id)
            ->where(function ($query) use ($startAt, $endAt) {
                $query->whereBetween('start_at', [$startAt, $endAt])
                    ->orWhereBetween('end_at', [$startAt, $endAt])
                    ->orWhere(function ($q) use ($startAt, $endAt) {
                        $q->where('start_at', '<=', $startAt)
                            ->where('end_at', '>=', $endAt);
                    });
            })
            ->exists();
    }

    /**
     * Assigner les équipements requis à une session
     */
    private function assignEquipmentToSession(CourseSession $session, Course $course, Room $room): void
    {
        // Récupérer les équipements requis pour ce cours
        $requiredEquipment = Equipment::where('course_id', $course->id)
            ->where('is_mobile', true) // Seuls les équipements mobiles peuvent être assignés
            ->get();

        foreach ($requiredEquipment as $equipment) {
            // Vérifier que l'équipement est disponible
            if ($this->isEquipmentAvailable($equipment, $session->start_at, $session->end_at)) {
                $session->sessionEquipment()->create([
                    'equipment_id' => $equipment->id,
                ]);
            }
        }
    }

    /**
     * Vérifier si un équipement est disponible pour un créneau
     */
    private function isEquipmentAvailable(Equipment $equipment, Carbon $startAt, Carbon $endAt): bool
    {
        // Vérifier qu'il n'est pas déjà utilisé dans une autre session
        return !$equipment->sessionEquipment()
            ->whereHas('session', function ($query) use ($startAt, $endAt) {
                $query->where(function ($q) use ($startAt, $endAt) {
                    $q->whereBetween('start_at', [$startAt, $endAt])
                        ->orWhereBetween('end_at', [$startAt, $endAt])
                        ->orWhere(function ($subQ) use ($startAt, $endAt) {
                            $subQ->where('start_at', '<=', $startAt)
                                ->where('end_at', '>=', $endAt);
                        });
                });
            })
            ->exists();
    }

    /**
     * Générer un planning complet avec déplacements intégrés
     */
    public function generatePlanningWithDeplacements(Group $group, Carbon $startDate, Carbon $endDate): array
    {
        $planning = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayPlanning = $this->getDayPlanning($group, $currentDate);
            if (!empty($dayPlanning)) {
                $planning[$currentDate->format('Y-m-d')] = $dayPlanning;
            }
            $currentDate->addDay();
        }

        return $planning;
    }

    /**
     * Obtenir le planning d'une journée avec déplacements
     */
    private function getDayPlanning(Group $group, Carbon $date): array
    {
        // Récupérer toutes les sessions du groupe pour cette journée
        $sessions = CourseSession::whereHas('sessionGroups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })
        ->whereDate('start_at', $date)
        ->orderBy('start_at')
        ->with(['course', 'site', 'room'])
        ->get();

        if ($sessions->isEmpty()) {
            return [];
        }

        $dayPlanning = [];
        $previousSession = null;

        foreach ($sessions as $session) {
            // Ajouter le déplacement si nécessaire
            if ($previousSession) {
                $deplacement = $this->getDeplacementBetweenSessions($previousSession, $session, $group);
                if ($deplacement) {
                    $dayPlanning[] = [
                        'type' => 'deplacement',
                        'start_time' => $deplacement->heure_depart,
                        'end_time' => $deplacement->heure_arrivee,
                        'duration' => $deplacement->duree_trajet_minutes,
                        'from_site' => $deplacement->siteDepart->name,
                        'to_site' => $deplacement->siteArrivee->name,
                        'from_room' => $deplacement->roomDepart->name,
                        'to_room' => $deplacement->roomArrivee->name,
                        'is_inter_site' => $deplacement->site_depart_id !== $deplacement->site_arrivee_id,
                        'color' => $deplacement->site_depart_id === $deplacement->site_arrivee_id ? 'blue' : 'orange',
                    ];
                }
            }

            // Ajouter la session
            $dayPlanning[] = [
                'type' => 'session',
                'start_time' => $session->start_at,
                'end_time' => $session->end_at,
                'course_code' => $session->course->code,
                'course_title' => $session->course->title,
                'site' => $session->site->name,
                'room' => $session->room->name,
                'is_main_site' => $session->site_id === $group->main_site_id,
                'color' => $session->site_id === $group->main_site_id ? 'green' : 'purple',
            ];

            $previousSession = $session;
        }

        return $dayPlanning;
    }

    /**
     * Obtenir le déplacement entre deux sessions
     */
    private function getDeplacementBetweenSessions(CourseSession $session1, CourseSession $session2, Group $group): ?Deplacement
    {
        return Deplacement::where('session_depart_id', $session1->id)
            ->where('session_arrivee_id', $session2->id)
            ->where('group_id', $group->id)
            ->with(['siteDepart', 'siteArrivee', 'roomDepart', 'roomArrivee'])
            ->first();
    }

    /**
     * Vérifier si un créneau est disponible pour une session
     */
    public function isTimeSlotAvailable(Group $group, Carbon $startTime, Carbon $endTime, int $excludeSessionId = null): bool
    {
        // Vérifier les sessions existantes
        $conflictingSessions = CourseSession::whereHas('sessionGroups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })
        ->where(function ($query) use ($startTime, $endTime) {
            $query->whereBetween('start_at', [$startTime, $endTime])
                ->orWhereBetween('end_at', [$startTime, $endTime])
                ->orWhere(function ($q) use ($startTime, $endTime) {
                    $q->where('start_at', '<=', $startTime)
                        ->where('end_at', '>=', $endTime);
                });
        });

        if ($excludeSessionId) {
            $conflictingSessions->where('id', '!=', $excludeSessionId);
        }

        return $conflictingSessions->count() === 0;
    }

    /**
     * Calculer la durée de déplacement optimale entre deux sites
     */
    public function calculateOptimalTravelTime(int $fromSiteId, int $toSiteId): int
    {
        if ($fromSiteId === $toSiteId) {
            return 5; // 5 minutes pour même site
        }
        
        return 60; // 1 heure pour changement de site
    }

    /**
     * Optimiser l'ordre des sessions pour minimiser les déplacements
     */
    public function optimizeSessionOrder(Collection $sessions, Group $group): Collection
    {
        // Priorité : sessions sur le site principal en premier
        return $sessions->sortBy(function ($session) use ($group) {
            $priority = 0;
            
            // Site principal = priorité maximale
            if ($session->site_id === $group->main_site_id) {
                $priority += 1000;
            }
            
            // Heure de début (matin = priorité)
            $priority += (24 - $session->start_at->hour) * 10;
            
            return -$priority; // Tri décroissant
        });
    }
}
