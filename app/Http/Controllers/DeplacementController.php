<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Deplacement;
use App\Models\CourseSession;
use App\Models\Group;
use App\Services\PlanningService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeplacementController extends Controller
{
    protected $planningService;

    public function __construct(PlanningService $planningService)
    {
        $this->planningService = $planningService;
    }

    /**
     * Afficher la liste des déplacements
     */
    public function index(Request $request)
    {
        $query = Deplacement::with([
            'group',
            'sessionDepart.course',
            'sessionDepart.site',
            'sessionDepart.room',
            'sessionArrivee.course',
            'sessionArrivee.site',
            'sessionArrivee.room',
            'siteDepart',
            'siteArrivee',
            'roomDepart',
            'roomArrivee'
        ]);

        // Filtres
        if ($request->filled('date')) {
            $query->forDate($request->date);
        }

        if ($request->filled('group_id')) {
            $query->forGroup($request->group_id);
        }

        if ($request->filled('inter_site') && $request->inter_site) {
            $query->interSite();
        }

        $deplacements = $query->orderBy('heure_depart', 'asc')->paginate(20);

        // Statistiques
        $stats = [
            'total' => Deplacement::count(),
            'inter_site' => Deplacement::interSite()->count(),
            'aujourd_hui' => Deplacement::forDate(Carbon::today())->count(),
        ];

        return Inertia::render('deplacements/index', [
            'deplacements' => $deplacements,
            'stats' => $stats,
            'filters' => $request->only(['date', 'group_id', 'inter_site'])
        ]);
    }

    /**
     * Afficher un déplacement spécifique
     */
    public function show(Deplacement $deplacement)
    {
        Log::info('DeplacementController: Début de show', ['deplacement_id' => $deplacement->id]);
        
        // Charger tout en une seule requête
        $deplacement = Deplacement::with([
            'group',
            'siteDepart',
            'siteArrivee',
            'roomDepart',
            'roomArrivee'
        ])->find($deplacement->id);

        // Charger les sites et salles si ils ne sont pas chargés
        if (!$deplacement->siteDepart && $deplacement->site_depart_id) {
            $deplacement->siteDepart = \App\Models\Site::find($deplacement->site_depart_id);
        }
        
        if (!$deplacement->siteArrivee && $deplacement->site_arrivee_id) {
            $deplacement->siteArrivee = \App\Models\Site::find($deplacement->site_arrivee_id);
        }
        
        if (!$deplacement->roomDepart && $deplacement->room_depart_id) {
            $deplacement->roomDepart = \App\Models\Room::find($deplacement->room_depart_id);
        }
        
        if (!$deplacement->roomArrivee && $deplacement->room_arrivee_id) {
            $deplacement->roomArrivee = \App\Models\Room::find($deplacement->room_arrivee_id);
        }

        // Charger les sessions avec leurs relations
        if ($deplacement->session_depart_id) {
            $deplacement->sessionDepart = CourseSession::with(['course', 'site', 'room'])
                ->find($deplacement->session_depart_id);
        }
        
        if ($deplacement->session_arrivee_id) {
            $deplacement->sessionArrivee = CourseSession::with(['course', 'site', 'room'])
                ->find($deplacement->session_arrivee_id);
        }

        // Debug des relations chargées
        Log::info('DeplacementController: Relations chargées', [
            'deplacement_id' => $deplacement->id,
            'sessionDepart_loaded' => $deplacement->sessionDepart ? 'OUI' : 'NON',
            'sessionArrivee_loaded' => $deplacement->sessionArrivee ? 'OUI' : 'NON',
            'sessionDepart_course' => $deplacement->sessionDepart && $deplacement->sessionDepart->course ? 'OUI' : 'NON',
            'sessionDepart_site' => $deplacement->sessionDepart && $deplacement->sessionDepart->site ? 'OUI' : 'NON',
            'sessionDepart_room' => $deplacement->sessionDepart && $deplacement->sessionDepart->room ? 'OUI' : 'NON',
        ]);

        return Inertia::render('deplacements/show', [
            'deplacement' => $deplacement
        ]);
    }



    /**
     * Trouver la session suivante pour le même groupe le même jour
     */
    private function findNextSessionForGroup(CourseSession $session)
    {
        $groupIds = $session->sessionGroups->pluck('group_id');
        
        if ($groupIds->isEmpty()) {
            return null;
        }

        return CourseSession::whereHas('sessionGroups', function ($query) use ($groupIds) {
            $query->whereIn('group_id', $groupIds);
        })
        ->where('start_at', '>', $session->end_at)
        ->whereDate('start_at', $session->start_at->toDateString())
        ->orderBy('start_at')
        ->first();
    }

    /**
     * Déterminer si un déplacement doit être créé
     */
    private function shouldCreateDeplacement(CourseSession $session1, CourseSession $session2): bool
    {
        // Vérifier qu'il n'existe pas déjà un déplacement
        $existingDeplacement = Deplacement::where('session_depart_id', $session1->id)
            ->where('session_arrivee_id', $session2->id)
            ->first();

        if ($existingDeplacement) {
            return false;
        }

        // Vérifier que les sessions ont au moins un groupe en commun
        $groupIds1 = $session1->sessionGroups->pluck('group_id');
        $groupIds2 = $session2->sessionGroups->pluck('group_id');
        
        return $groupIds1->intersect($groupIds2)->isNotEmpty();
    }

    /**
     * Générer tous les déplacements (endpoint public)
     */
    public function generateAll()
    {
        try {
            Log::info('DeplacementController: Début de génération des déplacements');

            // Récupérer toutes les sessions avec leurs relations
            $sessions = CourseSession::with([
                'sessionGroups.group',
                'course',
                'site',
                'room'
            ])->orderBy('start_at')->get();

            $deplacementsCrees = 0;
            $deplacementsInterSite = 0;

            // Parcourir toutes les sessions
            foreach ($sessions as $session) {
                // Trouver la session suivante pour le même groupe le même jour
                $nextSession = $this->findNextSessionForGroup($session);
                
                if ($nextSession && $this->shouldCreateDeplacement($session, $nextSession)) {
                    $deplacement = $this->createDeplacement($session, $nextSession);
                    
                    if ($deplacement) {
                        $deplacementsCrees++;
                        
                        // Vérifier si c'est un déplacement inter-site
                        if ($session->site_id !== $nextSession->site_id) {
                            $deplacementsInterSite++;
                        }
                    }
                }
            }

            Log::info('DeplacementController: Génération terminée', [
                'deplacements_crees' => $deplacementsCrees,
                'deplacements_inter_site' => $deplacementsInterSite
            ]);

            // Rediriger vers la page des déplacements avec un message de succès
            return redirect()->route('deplacements.index')->with('success', 
                "{$deplacementsCrees} déplacements générés ({$deplacementsInterSite} inter-sites)"
            );

        } catch (\Exception $e) {
            Log::error('DeplacementController: Erreur lors de la génération', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('deplacements.index')->with('error', 
                'Erreur lors de la génération: ' . $e->getMessage()
            );
        }
    }

    /**
     * Créer un déplacement entre deux sessions
     */
    private function createDeplacement(CourseSession $session1, CourseSession $session2)
    {
        try {
            // Calculer la durée du trajet (par défaut 60 minutes pour inter-sites)
            $dureeTrajet = 60; // minutes
            
            if ($session1->site_id === $session2->site_id) {
                $dureeTrajet = 15; // minutes pour même site
            }

            // Créer le déplacement pour chaque groupe commun
            $groupIds1 = $session1->sessionGroups->pluck('group_id');
            $groupIds2 = $session2->sessionGroups->pluck('group_id');
            $commonGroups = $groupIds1->intersect($groupIds2);

            foreach ($commonGroups as $groupId) {
                Deplacement::create([
                    'session_depart_id' => $session1->id,
                    'site_depart_id' => $session1->site_id,
                    'room_depart_id' => $session1->room_id,
                    'session_arrivee_id' => $session2->id,
                    'site_arrivee_id' => $session2->site_id,
                    'room_arrivee_id' => $session2->room_id,
                    'group_id' => $groupId,
                    'heure_depart' => $session1->end_at,
                    'heure_arrivee' => $session2->start_at,
                    'duree_trajet_minutes' => $dureeTrajet,
                ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('DeplacementController: Erreur création déplacement', [
                'session1_id' => $session1->id,
                'session2_id' => $session2->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
