<?php

namespace App\Http\Controllers;

use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class EquipmentTypeController extends Controller
{
    /**
     * Afficher la liste des types d'équipements
     */
    public function index()
    {
        $equipmentTypes = EquipmentType::with(['equipment', 'courseRequiredEquipment'])->get();
        
        return Inertia::render('equipment-types/index', [
            'equipmentTypes' => $equipmentTypes
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return Inertia::render('equipment-types/create');
    }

    /**
     * Stocker un nouveau type d'équipement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:80|unique:equipment_types,label',
        ], [
            'label.required' => 'Le libellé est requis',
            'label.max' => 'Le libellé ne peut pas dépasser 80 caractères',
            'label.unique' => 'Ce libellé existe déjà',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $equipmentType = EquipmentType::create([
                'label' => $request->label,
            ]);

            return redirect()->route('equipment-types.index')
                ->with('success', 'Type d\'équipement créé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création du type d\'équipement: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher un type d'équipement spécifique
     */
    public function show(EquipmentType $equipmentType)
    {
        $equipmentType->load(['equipment.site', 'courseRequiredEquipment.course']);
        
        return Inertia::render('equipment-types/show', [
            'equipmentType' => $equipmentType
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(EquipmentType $equipmentType)
    {
        return Inertia::render('equipment-types/edit', [
            'equipmentType' => $equipmentType
        ]);
    }

    /**
     * Mettre à jour un type d'équipement
     */
    public function update(Request $request, EquipmentType $equipmentType)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:80|unique:equipment_types,label,' . $equipmentType->id,
        ], [
            'label.required' => 'Le libellé est requis',
            'label.max' => 'Le libellé ne peut pas dépasser 80 caractères',
            'label.unique' => 'Ce libellé existe déjà',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $equipmentType->update([
                'label' => $request->label,
            ]);

            return redirect()->route('equipment-types.index')
                ->with('success', 'Type d\'équipement mis à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du type d\'équipement: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer un type d'équipement
     */
    public function destroy(EquipmentType $equipmentType)
    {
        try {
            // Vérifier s'il y a des relations
            if ($equipmentType->equipment()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer ce type d\'équipement car il est utilisé par des équipements']);
            }
            
            if ($equipmentType->courseRequiredEquipment()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer ce type d\'équipement car il est requis par des cours']);
            }

            $equipmentType->delete();

            return redirect()->route('equipment-types.index')
                ->with('success', 'Type d\'équipement supprimé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression du type d\'équipement: ' . $e->getMessage()]);
        }
    }
}
