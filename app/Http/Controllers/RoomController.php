<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Site;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Afficher la liste des salles
     */
    public function index()
    {
        $rooms = Room::with(['site.university', 'fixedEquipment'])->get();
        
        return Inertia::render('rooms/index', [
            'rooms' => $rooms
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $sites = Site::with('university')->get();
        
        return Inertia::render('rooms/create', [
            'sites' => $sites
        ]);
    }

    /**
     * Stocker une nouvelle salle
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:80',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ], [
            'site_id.required' => 'Le site est requis',
            'site_id.exists' => 'Le site sélectionné n\'existe pas',
            'name.required' => 'Le nom de la salle est requis',
            'name.max' => 'Le nom ne peut pas dépasser 80 caractères',
            'capacity.required' => 'La capacité est requise',
            'capacity.integer' => 'La capacité doit être un nombre entier',
            'capacity.min' => 'La capacité doit être supérieure à 0',
            'description.max' => 'La description ne peut pas dépasser 255 caractères',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $room = Room::create([
                'site_id' => $request->site_id,
                'name' => $request->name,
                'capacity' => $request->capacity,
                'description' => $request->description,
            ]);

            return redirect()->route('rooms.index')
                ->with('success', 'Salle créée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création de la salle: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher une salle spécifique
     */
    public function show(Room $room)
    {
        $room->load(['site.university', 'fixedEquipment', 'courseSessions']);
        
        return Inertia::render('rooms/show', [
            'room' => $room
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Room $room)
    {
        $sites = Site::with('university')->get();
        
        return Inertia::render('rooms/edit', [
            'room' => $room,
            'sites' => $sites
        ]);
    }

    /**
     * Mettre à jour une salle
     */
    public function update(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'name' => 'required|string|max:80',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ], [
            'site_id.required' => 'Le site est requis',
            'site_id.exists' => 'Le site sélectionné n\'existe pas',
            'name.required' => 'Le nom de la salle est requis',
            'name.max' => 'Le nom ne peut pas dépasser 80 caractères',
            'capacity.required' => 'La capacité est requise',
            'capacity.integer' => 'La capacité doit être un nombre entier',
            'capacity.min' => 'La capacité doit être supérieure à 0',
            'description.max' => 'La description ne peut pas dépasser 255 caractères',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $room->update([
                'site_id' => $request->site_id,
                'name' => $request->name,
                'capacity' => $request->capacity,
                'description' => $request->description,
            ]);

            return redirect()->route('rooms.index')
                ->with('success', 'Salle mise à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour de la salle: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer une salle
     */
    public function destroy(Room $room)
    {
        try {
            // Vérifier s'il y a des relations
            if ($room->courseSessions()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cette salle car elle possède des sessions']);
            }
            
            if ($room->fixedEquipment()->count() > 0) {
                return back()->withErrors(['error' => 'Impossible de supprimer cette salle car elle possède des équipements fixes']);
            }

            $room->delete();

            return redirect()->route('rooms.index')
                ->with('success', 'Salle supprimée avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression de la salle: ' . $e->getMessage()]);
        }
    }
}
