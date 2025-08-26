<?php

namespace App\Services;

use App\Models\CourseSession;
use Carbon\Carbon;

class SessionValidationService
{
    /**
     * Marge de sécurité après la fin d'une session (en minutes)
     */
    private const SAFETY_MARGIN_MINUTES = 30;

    /**
     * Vérifier si une session peut être modifiée
     */
    public function canBeModified($session): array
    {
        $now = Carbon::now();
        $sessionStart = Carbon::parse($session->start_at);
        $sessionEnd = Carbon::parse($session->end_at);

        // Session terminée (après la fin + marge de sécurité)
        if ($now->isAfter($sessionEnd->copy()->addMinutes(self::SAFETY_MARGIN_MINUTES))) {
            return [
                'can_modify' => false,
                'can_delete' => false,
                'can_change_room' => false,
                'can_change_groups' => false,
                'can_change_schedule' => false,
                'reason' => 'Cette session est terminée et ne peut plus être modifiée.',
                'status' => 'past'
            ];
        }
        
        // Session en cours (entre début et fin)
        if ($now->isBetween($sessionStart, $sessionEnd)) {
            return [
                'can_modify' => false,
                'can_delete' => false,
                'can_change_room' => false,
                'can_change_groups' => false,
                'can_change_schedule' => false,
                'reason' => 'Cette session est en cours et ne peut pas être modifiée.',
                'status' => 'ongoing'
            ];
        }
        
        // Session récemment terminée (après fin mais avant marge de sécurité)
        if ($now->isAfter($sessionEnd) && $now->isBefore($sessionEnd->copy()->addMinutes(self::SAFETY_MARGIN_MINUTES))) {
            return [
                'can_modify' => false,
                'can_delete' => false,
                'can_change_room' => false,
                'can_change_groups' => false,
                'can_change_schedule' => false,
                'reason' => 'Cette session vient de se terminer et ne peut plus être modifiée.',
                'status' => 'recently_ended'
            ];
        }
        
        // Session à venir (avant le début)
        if ($now->isBefore($sessionStart)) {
            return [
                'can_modify' => true,
                'can_delete' => true,
                'can_change_room' => true,
                'can_change_groups' => true,
                'can_change_schedule' => true,
                'reason' => 'Cette session est à venir et peut être entièrement modifiée.',
                'status' => 'future'
            ];
        }
        
        // Fallback (ne devrait jamais arriver)
        return [
            'can_modify' => false,
            'can_delete' => false,
            'can_change_room' => false,
            'can_change_groups' => false,
            'can_change_schedule' => false,
            'reason' => 'Statut de session indéterminé.',
            'status' => 'unknown'
        ];
    }

    /**
     * Vérifier si une session peut être supprimée
     */
    public function canBeDeleted($session): array
    {
        $validation = $this->canBeModified($session);
        
        return [
            'can_delete' => $validation['can_delete'],
            'reason' => $validation['reason'],
            'status' => $validation['status']
        ];
    }

    /**
     * Vérifier si une session peut changer de salle
     */
    public function canChangeRoom($session): array
    {
        $validation = $this->canBeModified($session);
        
        return [
            'can_change_room' => $validation['can_change_room'],
            'reason' => $validation['reason'],
            'status' => $validation['status']
        ];
    }

    /**
     * Vérifier si une session peut changer de groupes
     */
    public function canChangeGroups($session): array
    {
        $validation = $this->canBeModified($session);
        
        return [
            'can_change_groups' => $validation['can_change_groups'],
            'reason' => $validation['reason'],
            'status' => $validation['status']
        ];
    }

    /**
     * Vérifier si une session peut changer d'horaire
     */
    public function canChangeSchedule($session): array
    {
        $validation = $this->canBeModified($session);
        
        return [
            'can_change_schedule' => $validation['can_change_schedule'],
            'reason' => $validation['reason'],
            'status' => $validation['status']
        ];
    }

    /**
     * Obtenir le statut détaillé d'une session
     */
    public function getSessionStatus($session): array
    {
        $now = Carbon::now();
        $sessionStart = Carbon::parse($session->start_at);
        $sessionEnd = Carbon::parse($session->end_at);
        $safetyThreshold = $sessionEnd->copy()->addMinutes(self::SAFETY_MARGIN_MINUTES);

        if ($now->isBefore($sessionStart)) {
            return [
                'status' => 'future',
                'label' => 'À venir',
                'description' => 'Cette session n\'a pas encore commencé',
                'can_modify' => true
            ];
        }

        if ($now->isBetween($sessionStart, $sessionEnd)) {
            return [
                'status' => 'ongoing',
                'label' => 'En cours',
                'description' => 'Cette session est actuellement en cours',
                'can_modify' => false
            ];
        }

        if ($now->isBetween($sessionEnd, $safetyThreshold)) {
            return [
                'status' => 'recently_ended',
                'label' => 'Récemment terminée',
                'description' => 'Cette session vient de se terminer',
                'can_modify' => false
            ];
        }

        return [
            'status' => 'past',
            'label' => 'Terminée',
            'description' => 'Cette session est terminée depuis plus de ' . self::SAFETY_MARGIN_MINUTES . ' minutes',
            'can_modify' => false
        ];
    }
}
