<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class UniversityController extends Controller
{
    /**
     * Afficher la liste des universités
     */
    public function index()
    {
        $universities = University::with(['sites', 'courses', 'academicYears'])->get();
        
        return Inertia::render('universities/index', [
            'universities' => $universities
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return Inertia::render('universities/create');
    }

    /**
     * Stocker une nouvelle université
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:16|unique:universities,code',
            'name' => 'required|string|max:120',
        ], [
            'code.required' => 'Le code de l\'université est requis',
            'code.max' => 'Le code ne peut pas dépasser 16 caractères',
            'code.unique' => 'Ce code d\'université existe déjà',
            'name.required' => 'Le nom de l\'université est requis',
            'name.max' => 'Le nom ne peut pas dépasser 120 caractères',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $university = University::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
            ]);

            return redirect()->route('universities.index')
                ->with('success', 'Université créée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'université: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher une université spécifique
     */
    public function show(University $university)
    {
        $university->load(['sites', 'courses', 'academicYears', 'holidays']);
        
        return Inertia::render('universities/show', [
            'university' => $university
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(University $university)
    {
        return Inertia::render('universities/edit', [
            'university' => $university
        ]);
    }

    /**
     * Mettre à jour une université
     */
    public function update(Request $request, University $university)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:16|unique:universities,code,' . $university->id,
            'name' => 'required|string|max:120',
        ], [
            'code.required' => 'Le code de l\'université est requis',
            'code.max' => 'Le code ne peut pas dépasser 16 caractères',
            'code.unique' => 'Ce code d\'université existe déjà',
            'name.required' => 'Le nom de l\'université est requis',
            'name.max' => 'Le nom ne peut pas dépasser 120 caractères',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $university->update([
                'code' => strtoupper($request->code),
                'name' => $request->name,
            ]);

            return redirect()->route('universities.index')
                ->with('success', 'Université mise à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour de l\'université: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer une université
     */
    public function destroy(University $university)
    {
        try {
            // Vérifier s'il y a des relations
            if ($university->sites()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cette université car elle possède des sites']);
            }
            
            if ($university->courses()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cette université car elle possède des cours']);
            }
            
            if ($university->academicYears()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cette université car elle possède des années académiques']);
            }

            $university->delete();

            return redirect()->route('universities.index')
                ->with('success', 'Université supprimée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'université: ' . $e->getMessage()]);
        }
    }
}
