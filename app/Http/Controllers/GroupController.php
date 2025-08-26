<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\University;
use App\Models\AcademicYear;
use App\Models\Site;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Afficher la liste des groupes
     */
    public function index()
    {
        $groups = Group::with(['university', 'academicYear', 'mainSite.university'])->get();
        
        return Inertia::render('groups/index', [
            'groups' => $groups
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $universities = University::all();
        $academicYears = AcademicYear::all();
        $sites = Site::with('university')->get();
        
        // Debug: Log des données envoyées
        \Log::info('GroupController::create - Données envoyées', [
            'universities_count' => $universities->count(),
            'academic_years_count' => $academicYears->count(),
            'sites_count' => $sites->count(),
            'universities' => $universities->toArray(),
            'academic_years' => $academicYears->toArray(),
            'sites' => $sites->toArray(),
        ]);
        
        return Inertia::render('groups/create', [
            'universities' => $universities,
            'academicYears' => $academicYears,
            'sites' => $sites
        ]);
    }

    /**
     * Stocker un nouveau groupe
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:80',
            'quantity' => 'required|integer|min:1',
            'main_site_id' => 'required|exists:sites,id',
            'min_size' => 'required|integer|min:1',
            'max_size' => 'required|integer|min:1|gte:min_size',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'academic_year_id.required' => 'L\'année académique est requise',
            'academic_year_id.exists' => 'L\'année académique sélectionnée n\'existe pas',
            'name.required' => 'Le nom du groupe est requis',
            'name.max' => 'Le nom ne peut pas dépasser 80 caractères',
            'quantity.required' => 'La quantité est requise',
            'quantity.integer' => 'La quantité doit être un nombre entier',
            'quantity.min' => 'La quantité doit être supérieure à 0',
            'main_site_id.required' => 'Le site principal est requis',
            'main_site_id.exists' => 'Le site sélectionné n\'existe pas',
            'min_size.required' => 'La taille minimale est requise',
            'min_size.integer' => 'La taille minimale doit être un nombre entier',
            'min_size.min' => 'La taille minimale doit être supérieure à 0',
            'max_size.required' => 'La taille maximale est requise',
            'max_size.integer' => 'La taille maximale doit être un nombre entier',
            'max_size.min' => 'La taille maximale doit être supérieure à 0',
            'max_size.gte' => 'La taille maximale doit être supérieure ou égale à la taille minimale',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $group = Group::create([
                'university_id' => $request->university_id,
                'academic_year_id' => $request->academic_year_id,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'main_site_id' => $request->main_site_id,
                'min_size' => $request->min_size,
                'max_size' => $request->max_size,
            ]);

            return redirect()->route('groups.index')
                ->with('success', 'Groupe créé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création du groupe: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher un groupe spécifique
     */
    public function show(Group $group)
    {
        $group->load(['university', 'academicYear', 'mainSite.university', 'sessionGroups.session']);
        
        return Inertia::render('groups/show', [
            'group' => $group
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Group $group)
    {
        $universities = University::all();
        $academicYears = AcademicYear::all();
        $sites = Site::with('university')->get();
        
        return Inertia::render('groups/edit', [
            'group' => $group,
            'universities' => $universities,
            'academicYears' => $academicYears,
            'sites' => $sites
        ]);
    }

    /**
     * Mettre à jour un groupe
     */
    public function update(Request $request, Group $group)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:80',
            'quantity' => 'required|integer|min:1',
            'main_site_id' => 'required|exists:sites,id',
            'min_size' => 'required|integer|min:1',
            'max_size' => 'required|integer|min:1|gte:min_size',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'academic_year_id.required' => 'L\'année académique est requise',
            'academic_year_id.exists' => 'L\'année académique sélectionnée n\'existe pas',
            'name.required' => 'Le nom du groupe est requis',
            'name.max' => 'Le nom ne peut pas dépasser 80 caractères',
            'quantity.required' => 'La quantité est requise',
            'quantity.integer' => 'La quantité doit être un nombre entier',
            'quantity.min' => 'La quantité doit être supérieure à 0',
            'main_site_id.required' => 'Le site principal est requis',
            'main_site_id.exists' => 'Le site sélectionné n\'existe pas',
            'min_size.required' => 'La taille minimale est requise',
            'min_size.integer' => 'La taille minimale doit être un nombre entier',
            'min_size.min' => 'La taille minimale doit être supérieure à 0',
            'max_size.required' => 'La taille maximale est requise',
            'max_size.integer' => 'La taille maximale doit être un nombre entier',
            'max_size.min' => 'La taille maximale doit être supérieure à 0',
            'max_size.gte' => 'La taille maximale doit être supérieure ou égale à la taille minimale',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $group->update([
                'university_id' => $request->university_id,
                'academic_year_id' => $request->academic_year_id,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'main_site_id' => $request->main_site_id,
                'min_size' => $request->min_size,
                'max_size' => $request->max_size,
            ]);

            return redirect()->route('groups.index')
                ->with('success', 'Groupe mis à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du groupe: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer un groupe
     */
    public function destroy(Group $group)
    {
        try {
            // Vérifier s'il y a des relations
            if ($group->sessionGroups()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer ce groupe car il est lié à des sessions']);
            }

            $group->delete();

            return redirect()->route('groups.index')
                ->with('success', 'Groupe supprimé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression du groupe: ' . $e->getMessage()]);
        }
    }
}
