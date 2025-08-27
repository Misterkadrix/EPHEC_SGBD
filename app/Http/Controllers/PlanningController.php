<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\PlanningService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class PlanningController extends Controller
{
    protected $planningService;

    public function __construct(PlanningService $planningService)
    {
        $this->planningService = $planningService;
    }

    /**
     * Afficher la page de planification
     */
    public function index()
    {
        $academicYears = AcademicYear::with('university')
            ->where('state', 'active')
            ->orderBy('name', 'desc')
            ->get();

        return Inertia::render('planning/index', [
            'academicYears' => $academicYears
        ]);
    }

    /**
     * Générer automatiquement les horaires pour une année académique
     */
    public function generateSchedule(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        try {
            $academicYear = AcademicYear::findOrFail($request->academic_year_id);
            
            Log::info('PlanningController: Début de génération des horaires', [
                'academic_year_id' => $academicYear->id,
                'academic_year_name' => $academicYear->name,
                'user_id' => auth()->id()
            ]);

            // Vérifier que l'année académique est active
            if ($academicYear->state !== 'active') {
                return back()->withErrors([
                    'error' => 'Seules les années académiques actives peuvent être planifiées'
                ]);
            }

            // Générer les horaires
            $result = $this->planningService->generateScheduleForAcademicYear($academicYear);

            if ($result['success']) {
                $message = "Planification générée avec succès ! {$result['total_generated']} sessions créées.";
                
                if (!empty($result['errors'])) {
                    $message .= " Attention: " . count($result['errors']) . " erreurs rencontrées.";
                }

                Log::info('PlanningController: Génération réussie', [
                    'academic_year_id' => $academicYear->id,
                    'sessions_generated' => $result['total_generated'],
                    'errors_count' => count($result['errors'])
                ]);

                return back()->with('success', $message);
            } else {
                Log::error('PlanningController: Échec de la génération', [
                    'academic_year_id' => $academicYear->id,
                    'error' => $result['message']
                ]);

                return back()->withErrors([
                    'error' => 'Erreur lors de la génération: ' . $result['message']
                ]);
            }

        } catch (\Exception $e) {
            Log::error('PlanningController: Exception lors de la génération', [
                'academic_year_id' => $request->academic_year_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'error' => 'Erreur inattendue: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Afficher les statistiques de planification
     */
    public function stats(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $academicYear = AcademicYear::with(['university', 'groups', 'courseSessions'])->findOrFail($request->academic_year_id);

        // Calculer les statistiques
        $stats = [
            'academic_year' => $academicYear,
            'total_groups' => $academicYear->groups->count(),
            'total_sessions' => $academicYear->courseSessions->count(),
            'sessions_by_site' => $academicYear->courseSessions->groupBy('site_id')->map->count(),
            'sessions_by_course' => $academicYear->courseSessions->groupBy('course_id')->map->count(),
        ];

        return response()->json($stats);
    }
}
