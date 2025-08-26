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
        ->orderBy('start_at')
        ->limit(5)
        ->get();

        // Universités avec leurs sites
        $universities = University::with(['sites', 'courses', 'academicYears'])->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentSessions' => $recentSessions,
            'todaySessions' => $todaySessions,
            'upcomingSessions' => $upcomingSessions,
            'universities' => $universities,
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
