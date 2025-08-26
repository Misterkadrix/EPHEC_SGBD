<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Models\Site;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class EquipmentController extends Controller
{
    /**
     * Afficher la liste des équipements
     */
    public function index()
    {
        $equipment = Equipment::with(['site.university', 'type', 'fixedRoom'])->get();
        
        return Inertia::render('equipment/index', [
            'equipment' => $equipment
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $sites = Site::with('university')->get();
        $equipmentTypes = EquipmentType::all();
        $rooms = Room::all();
        
        return Inertia::render('equipment/create', [
            'sites' => $sites,
            'equipmentTypes' => $equipmentTypes,
            'rooms' => $rooms
        ]);
    }

    /**
     * Stocker un nouvel équipement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'type_id' => 'required|exists:equipment_types,id',
            'is_mobile' => 'required|boolean',
            'fixed_room_id' => 'nullable|required_if:is_mobile,0|exists:rooms,id',
        ], [
            'site_id.required' => 'Le site est requis',
            'site_id.exists' => 'Le site sélectionné n\'existe pas',
            'type_id.required' => 'Le type d\'équipement est requis',
            'type_id.exists' => 'Le type d\'équipement sélectionné n\'existe pas',
            'is_mobile.required' => 'Le type de mobilité est requis',
            'is_mobile.boolean' => 'Le type de mobilité doit être vrai ou faux',
            'fixed_room_id.required_if' => 'La salle est requise pour un équipement fixe',
            'fixed_room_id.exists' => 'La salle sélectionnée n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $equipment = Equipment::create([
                'site_id' => $request->site_id,
                'type_id' => $request->type_id,
                'is_mobile' => $request->is_mobile,
                'fixed_room_id' => $request->is_mobile ? null : $request->fixed_room_id,
            ]);

            return redirect()->route('equipment.index')
                ->with('success', 'Équipement créé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'équipement: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher un équipement spécifique
     */
    public function show(Equipment $equipment)
    {
        $equipment->load(['site.university', 'type', 'fixedRoom', 'sessionEquipment.session']);
        
        return Inertia::render('equipment/show', [
            'equipment' => $equipment
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Equipment $equipment)
    {
        $sites = Site::with('university')->get();
        $equipmentTypes = EquipmentType::all();
        $rooms = Room::all();
        
        return Inertia::render('equipment/edit', [
            'equipment' => $equipment,
            'sites' => $sites,
            'equipmentTypes' => $equipmentTypes,
            'rooms' => $rooms
        ]);
    }

    /**
     * Mettre à jour un équipement
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'type_id' => 'required|exists:equipment_types,id',
            'is_mobile' => 'required|boolean',
            'fixed_room_id' => 'nullable|required_if:is_mobile,0|exists:rooms,id',
        ], [
            'site_id.required' => 'Le site est requis',
            'site_id.exists' => 'Le site sélectionné n\'existe pas',
            'type_id.required' => 'Le type d\'équipement est requis',
            'type_id.exists' => 'Le type d\'équipement sélectionné n\'existe pas',
            'is_mobile.required' => 'Le type de mobilité est requis',
            'is_mobile.boolean' => 'Le type de mobilité doit être vrai ou faux',
            'fixed_room_id.required_if' => 'La salle est requise pour un équipement fixe',
            'fixed_room_id.exists' => 'La salle sélectionnée n\'existe pas',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $equipment->update([
                'site_id' => $request->site_id,
                'type_id' => $request->type_id,
                'is_mobile' => $request->is_mobile,
                'fixed_room_id' => $request->is_mobile ? null : $request->fixed_room_id,
            ]);

            return redirect()->route('equipment.index')
                ->with('success', 'Équipement mis à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour de l\'équipement: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer un équipement
     */
    public function destroy(Equipment $equipment)
    {
        try {
            // Vérifier s'il y a des relations
            if ($equipment->sessionEquipment()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cet équipement car il est réservé pour des sessions']);
            }

            $equipment->delete();

            return redirect()->route('equipment.index')
                ->with('success', 'Équipement supprimé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression de l\'équipement: ' . $e->getMessage()]);
        }
    }
}
