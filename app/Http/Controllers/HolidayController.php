<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    /**
     * Afficher la liste des fériés
     */
    public function index()
    {
        $holidays = Holiday::with('university')->get();
        
        return Inertia::render('holidays/index', [
            'holidays' => $holidays
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $universities = University::all();
        
        return Inertia::render('holidays/create', [
            'universities' => $universities
        ]);
    }

    /**
     * Stocker un nouveau férié
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'date' => 'required|date',
            'year' => 'required|integer|min:2000|max:2100',
            'university_id' => 'nullable|exists:universities,id',
        ], [
            'name.required' => 'Le nom du férié est requis',
            'name.max' => 'Le nom ne peut pas dépasser 120 caractères',
            'date.required' => 'La date est requise',
            'date.date' => 'Format de date invalide',
            'year.required' => 'L\'année est requise',
            'year.integer' => 'L\'année doit être un nombre entier',
            'year.min' => 'L\'année doit être supérieure ou égale à 2000',
            'year.max' => 'L\'année doit être inférieure ou égale à 2100',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $holiday = Holiday::create([
                'name' => $request->name,
                'date' => $request->date,
                'year' => $request->year,
                'university_id' => $request->university_id,
            ]);

            return redirect()->route('holidays.index')
                ->with('success', 'Férié créé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création du férié: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher un férié spécifique
     */
    public function show(Holiday $holiday)
    {
        $holiday->load('university');
        
        return Inertia::render('holidays/show', [
            'holiday' => $holiday
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Holiday $holiday)
    {
        $universities = University::all();
        
        return Inertia::render('holidays/edit', [
            'holiday' => $holiday,
            'universities' => $universities
        ]);
    }

    /**
     * Mettre à jour un férié
     */
    public function update(Request $request, Holiday $holiday)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:120',
            'date' => 'required|date',
            'year' => 'required|integer|min:2000|max:2100',
            'university_id' => 'nullable|exists:universities,id',
        ], [
            'name.required' => 'Le nom du férié est requis',
            'name.max' => 'Le nom ne peut pas dépasser 120 caractères',
            'date.required' => 'La date est requise',
            'date.date' => 'Format de date invalide',
            'year.required' => 'L\'année est requise',
            'year.integer' => 'L\'année doit être un nombre entier',
            'year.min' => 'L\'année doit être supérieure ou égale à 2000',
            'year.max' => 'L\'année doit être inférieure ou égale à 2100',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $holiday->update([
                'name' => $request->name,
                'date' => $request->date,
                'year' => $request->year,
                'university_id' => $request->university_id,
            ]);

            return redirect()->route('holidays.index')
                ->with('success', 'Férié mis à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du férié: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer un férié
     */
    public function destroy(Holiday $holiday)
    {
        try {
            $holiday->delete();

            return redirect()->route('holidays.index')
                ->with('success', 'Férié supprimé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression du férié: ' . $e->getMessage()]);
        }
    }
}
