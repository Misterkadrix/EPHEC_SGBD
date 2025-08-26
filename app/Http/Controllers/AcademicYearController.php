<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    /**
     * Afficher la liste des années académiques
     */
    public function index()
    {
        $academicYears = AcademicYear::with(['university', 'groups', 'courseSessions'])->get();
        
        return Inertia::render('academic-years/index', [
            'academicYears' => $academicYears
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $universities = University::all();
        
        return Inertia::render('academic-years/create', [
            'universities' => $universities
        ]);
    }

    /**
     * Stocker une nouvelle année académique
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:32',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'state' => 'required|in:planned,active,archived',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'name.required' => 'Le nom de l\'année académique est requis',
            'name.max' => 'Le nom ne peut pas dépasser 32 caractères',
            'start_date.required' => 'La date de début est requise',
            'start_date.date' => 'Format de date invalide',
            'end_date.required' => 'La date de fin est requise',
            'end_date.date' => 'Format de date invalide',
            'end_date.after' => 'La date de fin doit être après la date de début',
            'state.required' => 'L\'état est requis',
            'state.in' => 'L\'état doit être planned, active ou archived',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $academicYear = AcademicYear::create([
                'university_id' => $request->university_id,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'state' => $request->state,
            ]);

            return redirect()->route('academic-years.index')
                ->with('success', 'Année académique créée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'année académique: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher une année académique spécifique
     */
    public function show(AcademicYear $academicYear)
    {
        $academicYear->load(['university', 'groups', 'courseSessions.course', 'courseSessions.site', 'courseSessions.room']);
        
        return Inertia::render('academic-years/show', [
            'academicYear' => $academicYear
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(AcademicYear $academicYear)
    {
        $universities = University::all();
        
        return Inertia::render('academic-years/edit', [
            'academicYear' => $academicYear,
            'universities' => $universities
        ]);
    }

    /**
     * Mettre à jour une année académique
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:32',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'state' => 'required|in:planned,active,archived',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'name.required' => 'Le nom de l\'année académique est requis',
            'name.max' => 'Le nom ne peut pas dépasser 32 caractères',
            'start_date.required' => 'La date de début est requise',
            'start_date.date' => 'Format de date invalide',
            'end_date.required' => 'La date de fin est requise',
            'end_date.date' => 'Format de date invalide',
            'end_date.after' => 'La date de fin doit être après la date de début',
            'state.required' => 'L\'état est requis',
            'state.in' => 'L\'état doit être planned, active ou archived',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $academicYear->update([
                'university_id' => $request->university_id,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'state' => $request->state,
            ]);

            return redirect()->route('academic-years.index')
                ->with('success', 'Année académique mise à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour de l\'année académique: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer une année académique
     */
    public function destroy(AcademicYear $academicYear)
    {
        try {
            // Vérifier s'il y a des relations
            if ($academicYear->groups()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cette année académique car elle possède des groupes']);
            }
            
            if ($academicYear->courseSessions()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cette année académique car elle possède des sessions']);
            }

            $academicYear->delete();

            return redirect()->route('academic-years.index')
                ->with('success', 'Année académique supprimée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'année académique: ' . $e->getMessage()]);
        }
    }
}
