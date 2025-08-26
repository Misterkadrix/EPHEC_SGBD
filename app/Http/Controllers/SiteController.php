<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\University;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    /**
     * Afficher la liste des sites
     */
    public function index()
    {
        $sites = Site::with(['university', 'rooms', 'equipment'])->get();
        
        return Inertia::render('sites/index', [
            'sites' => $sites
        ]);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $universities = University::all();
        
        return Inertia::render('sites/create', [
            'universities' => $universities
        ]);
    }

    /**
     * Stocker un nouveau site
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:120',
            'timezone' => 'required|string|max:64',
            'day_start' => 'required|date_format:H:i',
            'day_end' => 'required|date_format:H:i|after:day_start',
            'active_days' => 'required|array|min:1',
            'active_days.*' => 'string|in:MO,TU,WE,TH,FR,SA,SU',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'name.required' => 'Le nom du site est requis',
            'name.max' => 'Le nom ne peut pas dépasser 120 caractères',
            'timezone.required' => 'Le fuseau horaire est requis',
            'timezone.max' => 'Le fuseau horaire ne peut pas dépasser 64 caractères',
            'day_start.required' => 'L\'heure de début est requise',
            'day_start.date_format' => 'Format d\'heure invalide (HH:MM)',
            'day_end.required' => 'L\'heure de fin est requise',
            'day_end.date_format' => 'Format d\'heure invalide (HH:MM)',
            'day_end.after' => 'L\'heure de fin doit être après l\'heure de début',
            'active_days.required' => 'Les jours actifs sont requis',
            'active_days.array' => 'Les jours actifs doivent être une liste',
            'active_days.min' => 'Au moins un jour actif est requis',
            'active_days.*.in' => 'Jour invalide (MO,TU,WE,TH,FR,SA,SU)',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $site = Site::create([
                'university_id' => $request->university_id,
                'name' => $request->name,
                'timezone' => $request->timezone,
                'day_start' => $request->day_start,
                'day_end' => $request->day_end,
                'active_days' => $request->active_days,
            ]);

            return redirect()->route('sites.index')
                ->with('success', 'Site créé avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la création du site: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Afficher un site spécifique
     */
    public function show(Site $site)
    {
        $site->load(['university', 'rooms', 'equipment', 'courseSessions', 'groups']);
        
        return Inertia::render('sites/show', [
            'site' => $site
        ]);
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Site $site)
    {
        $universities = University::all();
        
        return Inertia::render('sites/edit', [
            'site' => $site,
            'universities' => $universities
        ]);
    }

    /**
     * Mettre à jour un site
     */
    public function update(Request $request, Site $site)
    {
        $validator = Validator::make($request->all(), [
            'university_id' => 'required|exists:universities,id',
            'name' => 'required|string|max:120',
            'timezone' => 'required|string|max:64',
            'day_start' => 'required|date_format:H:i',
            'day_end' => 'required|date_format:H:i|after:day_start',
            'active_days' => 'required|array|min:1',
            'active_days.*' => 'string|in:MO,TU,WE,TH,FR,SA,SU',
        ], [
            'university_id.required' => 'L\'université est requise',
            'university_id.exists' => 'L\'université sélectionnée n\'existe pas',
            'name.required' => 'Le nom du site est requis',
            'name.max' => 'Le nom ne peut pas dépasser 120 caractères',
            'timezone.required' => 'Le fuseau horaire est requis',
            'timezone.max' => 'Le fuseau horaire ne peut pas dépasser 64 caractères',
            'day_start.required' => 'L\'heure de début est requise',
            'day_start.date_format' => 'Format d\'heure invalide (HH:MM)',
            'day_end.required' => 'L\'heure de fin est requise',
            'day_end.date_format' => 'Format d\'heure invalide (HH:MM)',
            'day_end.after' => 'L\'heure de fin doit être après l\'heure de début',
            'active_days.required' => 'Les jours actifs sont requis',
            'active_days.array' => 'Les jours actifs doivent être une liste',
            'active_days.min' => 'Au moins un jour actif est requis',
            'active_days.*.in' => 'Jour invalide (MO,TU,WE,TH,FR,SA,SU)',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $site->update([
                'university_id' => $request->university_id,
                'name' => $request->name,
                'timezone' => $request->timezone,
                'day_start' => $request->day_start,
                'day_end' => $request->day_end,
                'active_days' => $request->active_days,
            ]);

            return redirect()->route('sites.index')
                ->with('success', 'Site mis à jour avec succès !');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour du site: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Supprimer un site
     */
    public function destroy(Site $site)
    {
        try {
            // Supprimer en cascade les entités liées
            $deletedCounts = [];
            
            // Supprimer les sessions de cours liées
            $sessionsCount = $site->courseSessions()->count();
            if ($sessionsCount > 0) {
                // Supprimer d'abord les relations session-groupe et session-équipement
                $site->courseSessions()->each(function ($session) {
                    $session->sessionGroups()->delete();
                    $session->sessionEquipment()->delete();
                });
                $site->courseSessions()->delete();
                $deletedCounts[] = "{$sessionsCount} session(s) de cours";
            }
            
            // Supprimer les équipements liés
            $equipmentCount = $site->equipment()->count();
            if ($equipmentCount > 0) {
                $site->equipment()->delete();
                $deletedCounts[] = "{$equipmentCount} équipement(s)";
            }
            
            // Supprimer les salles liées
            $roomsCount = $site->rooms()->count();
            if ($roomsCount > 0) {
                $site->rooms()->delete();
                $deletedCounts[] = "{$roomsCount} salle(s)";
            }
            
            // Supprimer les groupes liés (qui ont ce site comme site principal)
            $groupsCount = $site->groups()->count();
            if ($groupsCount > 0) {
                // Supprimer d'abord les relations session-groupe
                $site->groups()->each(function ($group) {
                    $group->sessionGroups()->delete();
                });
                $site->groups()->delete();
                $deletedCounts[] = "{$groupsCount} groupe(s)";
            }
            
            // Maintenant supprimer le site
            $site->delete();
            
            $message = 'Site supprimé avec succès !';
            if (!empty($deletedCounts)) {
                $message .= ' Éléments supprimés en cascade : ' . implode(', ', $deletedCounts);
            }

            return redirect()->route('sites.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression du site: ' . $e->getMessage()]);
        }
    }
}
