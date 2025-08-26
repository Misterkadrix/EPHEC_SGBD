<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\University;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Afficher la liste des cours
     */
    public function index()
    {
        $courses = Course::with(['university', 'courseSitePermissions.site', 'courseRequiredEquipment.equipmentType'])->get();
        
        return Inertia::render('courses/index', [
            'courses' => $courses
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $universities = University::all();
        $equipmentTypes = EquipmentType::all();
        
        return Inertia::render('courses/create', [
            'universities' => $universities,
            'equipmentTypes' => $equipmentTypes
        ]);
    }

    /**
     * Stocker un nouveau cours
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'code' => 'required|string|max:32',
            'title' => 'required|string|max:160',
            'description' => 'nullable|string',
            'equipment_types' => 'nullable|array',
            'equipment_types.*' => 'exists:equipment_types,id',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'code.required' => 'Le code du cours est requis',
            'code.max' => 'Le code ne peut pas dépasser 32 caractères',
            'title.required' => 'Le titre du cours est requis',
            'title.max' => 'Le titre ne peut pas dépasser 160 caractères',
            'equipment_types.array' => 'Les types d\'équipements doivent être une liste',
            'equipment_types.*.exists' => 'Un type d\'équipement sélectionné n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $course = Course::create([
                'university_id' => $request->university_id,
                'code' => strtoupper($request->code),
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Ajouter les équipements requis
            if ($request->equipment_types) {
                foreach ($request->equipment_types as $equipmentTypeId) {
                    $course->courseRequiredEquipment()->create([
                        'equipment_type_id' => $equipmentTypeId,
                    ]);
                }
            }

            return redirect()->route('courses.index')
                ->with('success', 'Cours créé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création du cours: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher un cours spécifique
     */
    public function show(Course $course)
    {
        $course->load([
            'university', 
            'courseSitePermissions.site', 
            'courseRequiredEquipment.equipmentType',
            'courseSessions'
        ]);
        
        return Inertia::render('courses/show', [
            'course' => $course
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Course $course)
    {
        $universities = University::all();
        $equipmentTypes = EquipmentType::all();
        
        return Inertia::render('courses/edit', [
            'course' => $course,
            'universities' => $universities,
            'equipmentTypes' => $equipmentTypes
        ]);
    }

    /**
     * Mettre à jour un cours
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'code' => 'required|string|max:32',
            'title' => 'required|string|max:160',
            'description' => 'nullable|string',
            'equipment_types' => 'nullable|array',
            'equipment_types.*' => 'exists:equipment_types,id',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'code.required' => 'Le code du cours est requis',
            'code.max' => 'Le code ne peut pas dépasser 32 caractères',
            'title.required' => 'Le titre du cours est requis',
            'title.max' => 'Le titre ne peut pas dépasser 160 caractères',
            'equipment_types.array' => 'Les types d\'équipements doivent être une liste',
            'equipment_types.*.exists' => 'Un type d\'équipement sélectionné n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $course->update([
                'university_id' => $request->university_id,
                'code' => strtoupper($request->code),
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Mettre à jour les équipements requis
            $course->courseRequiredEquipment()->delete();
            if ($request->equipment_types) {
                foreach ($request->equipment_types as $equipmentTypeId) {
                    $course->courseRequiredEquipment()->create([
                        'equipment_type_id' => $equipmentTypeId,
                    ]);
                }
            }

            return redirect()->route('courses.index')
                ->with('success', 'Cours mis à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du cours: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer un cours
     */
    public function destroy(Course $course)
    {
        try {
            // Vérifier s'il y a des relations
            if ($course->courseSessions()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer ce cours car il possède des sessions']);
            }

            $course->delete();

            return redirect()->route('courses.index')
                ->with('success', 'Cours supprimé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression du cours: ' . $e->getMessage()]);
        }
    }
}
