<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Site;
use App\Models\Room;
use App\Models\Group;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CourseSessionController extends Controller
{
    /**
     * Afficher la liste des sessions
     */
    public function index()
    {
        $sessions = CourseSession::with([
            'academicYear.university', 
            'course.university', 
            'site.university', 
            'room.site',
            'sessionGroups.group',
            'sessionEquipment.equipment.type'
        ])->get();
        
        return Inertia::render('course-sessions/index', [
            'sessions' => $sessions
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $universities = \App\Models\University::all();
        $academicYears = AcademicYear::with('university')->get();
        $courses = Course::with('university')->get();
        $sites = Site::with('university')->get();
        $rooms = Room::with('site')->get();
        $groups = Group::with('university')->get();
        $equipment = Equipment::with(['type', 'site'])->where('is_mobile', true)->get();
        
        // Debug: Log des données envoyées
        \Log::info('CourseSessionController::create - Données envoyées', [
            'universities_count' => $universities->count(),
            'academic_years_count' => $academicYears->count(),
            'courses_count' => $courses->count(),
            'sites_count' => $sites->count(),
            'rooms_count' => $rooms->count(),
        ]);
        
        return Inertia::render('course-sessions/create', [
            'universities' => $universities,
            'academicYears' => $academicYears,
            'courses' => $courses,
            'sites' => $sites,
            'rooms' => $rooms,
            'groups' => $groups,
            'equipment' => $equipment
        ]);
    }

    /**
     * Stocker une nouvelle session
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id' => 'required|exists:courses,id',
            'site_id' => 'required|exists:sites,id',
            'room_id' => 'required|exists:rooms,id',
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'end_at' => 'required|date_format:Y-m-d H:i:s|after:start_at',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
            'equipment' => 'nullable|array',
            'equipment.*' => 'exists:equipment,id',
        ], [
            'academic_year_id.required' => 'L\'année académique est requise',
            'academic_year_id.exists' => 'L\'année académique sélectionnée n\'existe pas',
            'course_id.required' => 'Le cours est requis',
            'course_id.exists' => 'Le cours sélectionné n\'existe pas',
            'site_id.required' => 'Le site est requis',
            'site_id.exists' => 'Le site sélectionné n\'existe pas',
            'room_id.required' => 'La salle est requise',
            'room_id.exists' => 'La salle sélectionnée n\'existe pas',
            'start_at.required' => 'La date et heure de début sont requises',
            'start_at.date_format' => 'Format de date invalide (YYYY-MM-DD HH:MM:SS)',
            'end_at.required' => 'La date et heure de fin sont requises',
            'end_at.date_format' => 'Format de date invalide (YYYY-MM-DD HH:MM:SS)',
            'end_at.after' => 'La date de fin doit être après la date de début',
            'groups.array' => 'Les groupes doivent être une liste',
            'groups.*.exists' => 'Un groupe sélectionné n\'existe pas',
            'equipment.array' => 'Les équipements doivent être une liste',
            'equipment.*.exists' => 'Un équipement sélectionné n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Vérifier que la salle est disponible
            $conflictingSession = CourseSession::where('room_id', $request->room_id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_at', [$request->start_at, $request->end_at])
                        ->orWhereBetween('end_at', [$request->start_at, $request->end_at])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_at', '<=', $request->start_at)
                                ->where('end_at', '>=', $request->end_at);
                        });
                })->first();

            if ($conflictingSession) {
                return back()->withErrors(['error' => 'La salle n\'est pas disponible pour ce créneau horaire'])->withInput();
            }

            $session = CourseSession::create([
                'academic_year_id' => $request->academic_year_id,
                'course_id' => $request->course_id,
                'site_id' => $request->site_id,
                'room_id' => $request->room_id,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
            ]);

            // Ajouter les groupes
            if ($request->groups) {
                foreach ($request->groups as $groupId) {
                    $session->sessionGroups()->create([
                        'group_id' => $groupId,
                    ]);
                }
            }

            // Ajouter les équipements mobiles
            if ($request->equipment) {
                foreach ($request->equipment as $equipmentId) {
                    $equipment = Equipment::find($equipmentId);
                    if ($equipment && $equipment->is_mobile) {
                        $session->sessionEquipment()->create([
                            'equipment_id' => $equipmentId,
                        ]);
                    }
                }
            }

            return redirect()->route('course-sessions.index')
                ->with('success', 'Session créée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création de la session: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher une session spécifique
     */
    public function show(CourseSession $session)
    {
        $session->load([
            'academicYear.university', 
            'course.university', 
            'site.university', 
            'room.site',
            'sessionGroups.group',
            'sessionEquipment.equipment.type'
        ]);
        
        return Inertia::render('course-sessions/show', [
            'session' => $session
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(CourseSession $session)
    {
        // Charger la session avec ses relations
        $session->load(['academicYear.university', 'course.university', 'site.university', 'room.site']);
        
        $universities = \App\Models\University::all();
        $academicYears = AcademicYear::with('university')->get();
        $courses = Course::with('university')->get();
        $sites = Site::with('university')->get();
        $rooms = Room::with('site')->get();
        $groups = Group::with('university')->get();
        $equipment = Equipment::with(['type', 'site'])->where('is_mobile', true)->get();
        
        return Inertia::render('course-sessions/edit', [
            'session' => $session,
            'universities' => $universities,
            'academicYears' => $academicYears,
            'courses' => $courses,
            'sites' => $sites,
            'rooms' => $rooms,
            'groups' => $groups,
            'equipment' => $equipment
        ]);
    }

    /**
     * Mettre à jour une session
     */
    public function update(Request $request, CourseSession $session)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id' => 'required|exists:courses,id',
            'site_id' => 'required|exists:sites,id',
            'room_id' => 'required|exists:rooms,id',
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'end_at' => 'required|date_format:Y-m-d H:i:s|after:start_at',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
            'equipment' => 'nullable|array',
            'equipment.*' => 'exists:equipment,id',
        ], [
            'academic_year_id.required' => 'L\'année académique est requise',
            'academic_year_id.exists' => 'L\'année académique sélectionnée n\'existe pas',
            'course_id.required' => 'Le cours est requis',
            'course_id.exists' => 'Le cours sélectionné n\'existe pas',
            'site_id.required' => 'Le site est requis',
            'site_id.exists' => 'Le site sélectionné n\'existe pas',
            'room_id.required' => 'La salle est requise',
            'room_id.exists' => 'La salle sélectionnée n\'existe pas',
            'start_at.required' => 'La date et heure de début sont requises',
            'start_at.date_format' => 'Format de date invalide (YYYY-MM-DD HH:MM:SS)',
            'end_at.required' => 'La date et heure de fin sont requises',
            'end_at.date_format' => 'Format de date invalide (YYYY-MM-DD HH:MM:SS)',
            'end_at.after' => 'La date de fin doit être après la date de début',
            'groups.array' => 'Les groupes doivent être une liste',
            'groups.*.exists' => 'Un groupe sélectionné n\'existe pas',
            'equipment.array' => 'Les équipements doivent être une liste',
            'equipment.*.exists' => 'Un équipement sélectionné n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Vérifier que la salle est disponible (exclure la session actuelle)
            $conflictingSession = CourseSession::where('room_id', $request->room_id)
                ->where('id', '!=', $session->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_at', [$request->start_at, $request->end_at])
                        ->orWhereBetween('end_at', [$request->start_at, $request->end_at])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_at', '<=', $request->start_at)
                                ->where('end_at', '>=', $request->end_at);
                        });
                })->first();

            if ($conflictingSession) {
                return back()->withErrors(['error' => 'La salle n\'est pas disponible pour ce créneau horaire'])->withInput();
            }

            $session->update([
                'academic_year_id' => $request->academic_year_id,
                'course_id' => $request->course_id,
                'site_id' => $request->site_id,
                'room_id' => $request->room_id,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
            ]);

            // Mettre à jour les groupes
            $session->sessionGroups()->delete();
            if ($request->groups) {
                foreach ($request->groups as $groupId) {
                    $session->sessionGroups()->create([
                        'group_id' => $groupId,
                    ]);
                }
            }

            // Mettre à jour les équipements mobiles
            $session->sessionEquipment()->delete();
            if ($request->equipment) {
                foreach ($request->equipment as $equipmentId) {
                    $equipment = Equipment::find($equipmentId);
                    if ($equipment && $equipment->is_mobile) {
                        $session->sessionEquipment()->create([
                            'equipment_id' => $equipmentId,
                        ]);
                    }
                }
            }

            return redirect()->route('course-sessions.index')
                ->with('success', 'Session mise à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour de la session: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer une session
     */
    public function destroy(CourseSession $session)
    {
        try {
            $session->delete();

            return redirect()->route('course-sessions.index')
                ->with('success', 'Session supprimée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression de la session: ' . $e->getMessage()]);
        }
    }
}
