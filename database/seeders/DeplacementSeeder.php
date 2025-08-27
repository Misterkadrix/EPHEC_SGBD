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
        $this->command->info('🚶 Création de déplacements selon la logique métier...');
        
        // Récupérer toutes les sessions groupées par groupe et par jour
        $sessions = CourseSession::with(['course', 'site', 'room', 'sessionGroups.group'])
            ->orderBy('start_at')
            ->get();
        
        if ($sessions->count() < 2) {
            $this->command->error('❌ Pas assez de sessions pour créer des déplacements');
            return;
        }

        $deplacementsCrees = 0;
        $deplacementsInterSite = 0;

        // Grouper les sessions par groupe et par jour
        $sessionsByGroupAndDay = [];
        foreach ($sessions as $session) {
            foreach ($session->sessionGroups as $sessionGroup) {
                $groupId = $sessionGroup->group_id;
                $dayKey = $session->start_at->toDateString();
                $key = "{$groupId}_{$dayKey}";
                
                if (!isset($sessionsByGroupAndDay[$key])) {
                    $sessionsByGroupAndDay[$key] = [];
                }
                
                $sessionsByGroupAndDay[$key][] = $session;
            }
        }

        // Créer des déplacements pour chaque groupe et chaque jour
        foreach ($sessionsByGroupAndDay as $key => $daySessions) {
            // Trier les sessions par heure de début
            usort($daySessions, function($a, $b) {
                return $a->start_at->compare($b->start_at);
            });
            
            // Créer des déplacements entre sessions consécutives
            for ($i = 0; $i < count($daySessions) - 1; $i++) {
                $session1 = $daySessions[$i];
                $session2 = $daySessions[$i + 1];
                
                // Extraire le group_id de la clé
                $groupId = (int) explode('_', $key)[0];
                
                // Vérifier qu'il n'existe pas déjà un déplacement
                $existingDeplacement = Deplacement::where('session_depart_id', $session1->id)
                    ->where('session_arrivee_id', $session2->id)
                    ->where('group_id', $groupId)
                    ->first();

                if ($existingDeplacement) {
                    continue;
                }

                // Déterminer la durée du trajet
                $dureeTrajet = 60; // 1 heure par défaut (sites différents)
                $heureDepart = $session1->end_at;
                $heureArrivee = $session2->start_at;
                
                // Si c'est le même site, durée plus courte (5 minutes)
                if ($session1->site_id === $session2->site_id) {
                    $dureeTrajet = 5; // 5 minutes pour même site
                    // Les cours s'enchaînent directement (pas de décalage)
                    $heureDepart = $session1->end_at;
                    $heureArrivee = $session2->start_at;
                } else {
                    // Sites différents : il faut 1h de déplacement
                    // Le cours suivant commence 1h après la fin du cours précédent
                    $heureDepart = $session1->end_at;
                    $heureArrivee = $session1->end_at->copy()->addHour();
                }

                Deplacement::create([
                    'session_depart_id' => $session1->id,
                    'site_depart_id' => $session1->site_id,
                    'room_depart_id' => $session1->room_id,
                    'session_arrivee_id' => $session2->id,
                    'site_arrivee_id' => $session2->site_id,
                    'room_arrivee_id' => $session2->room_id,
                    'group_id' => $groupId,
                    'heure_depart' => $heureDepart,
                    'heure_arrivee' => $heureArrivee,
                    'duree_trajet_minutes' => $dureeTrajet,
                ]);

                $deplacementsCrees++;
                
                if ($session1->site_id !== $session2->site_id) {
                    $deplacementsInterSite++;
                }

                $this->command->info("✅ Déplacement créé: {$session1->course->code} ({$session1->site->name}) → {$session2->course->code} ({$session2->site->name}) - Durée: {$dureeTrajet}min");
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
            $type = $deplacement->site_depart_id === $deplacement->site_arrivee_id ? 'Même site (5min)' : 'Inter-site (1h)';
            $this->command->info("• {$deplacement->group->name}: {$deplacement->sessionDepart->course->code} → {$deplacement->sessionArrivee->course->code} ({$type})");
        }
    }
}
