<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Models\Site;
use App\Models\Room;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Group;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord principal
     */
    public function index()
    {
        // Statistiques générales
        $stats = [
            'universities_count' => University::count(),
            'sites_count' => Site::count(),
            'rooms_count' => Room::count(),
            'courses_count' => Course::count(),
            'sessions_count' => CourseSession::count(),
            'groups_count' => Group::count(),
            'equipment_count' => Equipment::count(),
        ];

        // Dernières sessions
        $recentSessions = CourseSession::with([
            'course.university', 
            'site.university', 
            'room.site',
            'sessionGroups.group'
        ])
        ->orderBy('start_at', 'desc')
        ->limit(5)
        ->get();

        // Sessions aujourd'hui
        $todaySessions = CourseSession::with([
            'course.university', 
            'site.university', 
            'room.site',
            'sessionGroups.group'
        ])
        ->whereDate('start_at', today())
        ->orderBy('start_at')
        ->get();

        // Prochaines sessions
        $upcomingSessions = CourseSession::with([
            'course.university', 
            'site.university', 
            'room.site',
            'sessionGroups.group'
        ])
        ->where('start_at', '>', now())
        ->limit(5)
        ->get();

        // Universités avec leurs sites
        $universities = University::with(['sites', 'courses', 'academicYears'])->get();

        // Données pour le calendrier
        try {
            // Récupérer les sessions avec leurs relations
            $sessions = CourseSession::with([
                'course.university',
                'room.site.university',
                'sessionGroups.group.university'
            ])->get();

            // Récupérer les déplacements avec leurs relations
            $deplacements = \App\Models\Deplacement::with([
                'group.university',
                'sessionDepart.course.university',
                'sessionDepart.site.university',
                'sessionDepart.room.site.university',
                'sessionArrivee.course.university',
                'sessionArrivee.site.university',
                'sessionArrivee.room.site.university'
            ])->get();

            $calendarData = [
                'universities' => University::select('id', 'name', 'code')->get(),
                'groups' => Group::with('university')->get(),
                'sessions' => $sessions->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'type' => 'session',
                        'start_at' => $session->start_at,
                        'end_at' => $session->end_at,
                        'course' => [
                            'id' => $session->course->id,
                            'title' => $session->course->title,
                            'code' => $session->course->code,
                            'university_id' => $session->course->university_id,
                        ],
                        'room' => [
                            'id' => $session->room->id,
                            'name' => $session->room->name,
                        ],
                        'site' => [
                            'id' => $session->room->site->id ?? null,
                            'name' => $session->room->site->name ?? null,
                        ],
                        'groups' => $session->sessionGroups->map(function ($sessionGroup) {
                            return [
                                'id' => $sessionGroup->group->id,
                                'name' => $sessionGroup->group->name,
                                'quantity' => $sessionGroup->group->quantity,
                                'university_id' => $sessionGroup->group->university_id,
                            ];
                        })->toArray(),
                    ];
                }),
                'deplacements' => $deplacements->map(function ($deplacement) {
                    return [
                        'id' => $deplacement->id,
                        'type' => 'deplacement',
                        'start_at' => $deplacement->heure_depart,
                        'end_at' => $deplacement->heure_arrivee,
                        'duree_trajet_minutes' => $deplacement->duree_trajet_minutes,
                        'group' => [
                            'id' => $deplacement->group->id,
                            'name' => $deplacement->group->name,
                            'university_id' => $deplacement->group->university_id,
                        ],
                        'depart' => [
                            'course' => [
                                'id' => $deplacement->sessionDepart->course->id ?? null,
                                'title' => $deplacement->sessionDepart->course->title ?? null,
                                'code' => $deplacement->sessionDepart->course->code ?? null,
                            ],
                            'site' => [
                                'id' => $deplacement->sessionDepart->site->id ?? null,
                                'name' => $deplacement->sessionDepart->site->name ?? null,
                            ],
                            'room' => [
                                'id' => $deplacement->sessionDepart->room->id ?? null,
                                'name' => $deplacement->sessionDepart->room->name ?? null,
                            ],
                        ],
                        'arrivee' => [
                            'course' => [
                                'id' => $deplacement->sessionArrivee->course->id ?? null,
                                'title' => $deplacement->sessionArrivee->course->title ?? null,
                                'code' => $deplacement->sessionArrivee->course->code ?? null,
                            ],
                            'site' => [
                                'id' => $deplacement->sessionArrivee->site->id ?? null,
                                'name' => $deplacement->sessionArrivee->site->name ?? null,
                            ],
                            'room' => [
                                'id' => $deplacement->sessionArrivee->room->id ?? null,
                                'name' => $deplacement->sessionArrivee->room->name ?? null,
                            ],
                        ],
                    ];
                }),
            ];
        } catch (\Exception $e) {
            \Log::error('Erreur dans DashboardController: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            $calendarData = [
                'universities' => [],
                'groups' => [],
                'sessions' => [],
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentSessions' => $recentSessions,
            'todaySessions' => $todaySessions,
            'upcomingSessions' => $upcomingSessions,
            'universities' => $universities,
            'calendarData' => $calendarData,
        ]);
    }

    /**
     * Afficher les statistiques détaillées
     */
    public function stats()
    {
        // Statistiques par université
        $universityStats = University::withCount(['sites', 'courses', 'academicYears', 'holidays'])->get();

        // Statistiques par site
        $siteStats = Site::withCount(['rooms', 'equipment', 'courseSessions', 'groups'])->get();

        // Sessions par mois (6 derniers mois)
        $monthlySessions = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlySessions[] = [
                'month' => $date->format('M Y'),
                'count' => CourseSession::whereYear('start_at', $date->year)
                    ->whereMonth('start_at', $date->month)
                    ->count()
            ];
        }

        // Top des salles les plus utilisées
        $topRooms = Room::withCount('courseSessions')
            ->orderBy('course_sessions_count', 'desc')
            ->limit(10)
            ->get();

        // Top des cours les plus programmés
        $topCourses = Course::withCount('courseSessions')
            ->orderBy('course_sessions_count', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('stats', [
            'universityStats' => $universityStats,
            'siteStats' => $siteStats,
            'monthlySessions' => $monthlySessions,
            'topRooms' => $topRooms,
            'topCourses' => $topCourses,
        ]);
    }
}
