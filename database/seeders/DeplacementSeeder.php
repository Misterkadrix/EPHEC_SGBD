<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Deplacement;
use App\Models\CourseSession;
use App\Models\Group;
use App\Models\Site;
use Carbon\Carbon;

class DeplacementSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌍 Création des déplacements inter-sites...');

        // Supprimer les déplacements existants
        Deplacement::query()->delete();
        $this->command->info('🗑️ Anciens déplacements supprimés');

        // Créer des déplacements simples entre sessions existantes
        $this->createSimpleDeplacements();
    }

    private function createSimpleDeplacements()
    {
        $this->command->info('🚶 Création de déplacements simples...');
        
        // Récupérer quelques sessions existantes
        $sessions = CourseSession::with(['course', 'site', 'room'])
            ->orderBy('start_at')
            ->limit(10)
            ->get();
        
        if ($sessions->count() < 2) {
            $this->command->error('❌ Pas assez de sessions pour créer des déplacements');
            return;
        }

        $deplacementsCrees = 0;
        $deplacementsInterSite = 0;

        // Créer des déplacements entre sessions consécutives
        for ($i = 0; $i < $sessions->count() - 1; $i++) {
            $session1 = $sessions[$i];
            $session2 = $sessions[$i + 1];

            // Vérifier que les sessions sont le même jour
            if ($session1->start_at->toDateString() !== $session2->start_at->toDateString()) {
                continue;
            }

            // Créer un déplacement pour le groupe A
            $existingDeplacement = Deplacement::where('session_depart_id', $session1->id)
                ->where('session_arrivee_id', $session2->id)
                ->where('group_id', 1)
                ->first();

            if ($existingDeplacement) {
                continue;
            }

            // Créer le déplacement
            $dureeTrajet = 60; // 1 heure par défaut
            
            // Si c'est le même site, durée plus courte
            if ($session1->site_id === $session2->site_id) {
                $dureeTrajet = 15; // 15 minutes pour même site
            }

            Deplacement::create([
                'session_depart_id' => $session1->id,
                'site_depart_id' => $session1->site_id,
                'room_depart_id' => $session1->room_id,
                'session_arrivee_id' => $session2->id,
                'site_arrivee_id' => $session2->site_id,
                'room_arrivee_id' => $session2->room_id,
                'group_id' => 1, // Groupe A
                'heure_depart' => $session1->end_at,
                'heure_arrivee' => $session2->start_at,
                'duree_trajet_minutes' => $dureeTrajet,
            ]);

            $deplacementsCrees++;
            
            if ($session1->site_id !== $session2->site_id) {
                $deplacementsInterSite++;
            }

            // Limiter le nombre de déplacements
            if ($deplacementsCrees >= 15) {
                break;
            }
        }

        $this->command->info("✅ {$deplacementsCrees} déplacements créés ({$deplacementsInterSite} inter-sites)");
        
        // Afficher quelques exemples
        $this->command->info('📋 Exemples de déplacements créés :');
        $exemples = Deplacement::with([
            'group', 
            'sessionDepart.course', 
            'sessionArrivee.course',
            'siteDepart',
            'siteArrivee'
        ])->limit(5)->get();

        foreach ($exemples as $deplacement) {
            $type = $deplacement->site_depart_id === $deplacement->site_arrivee_id ? 'Même site' : 'Inter-site';
            $this->command->info("• {$deplacement->group->name}: {$deplacement->sessionDepart->course->code} → {$deplacement->sessionArrivee->course->code} ({$type})");
        }
    }
}
