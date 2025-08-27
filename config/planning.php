<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paramètres de planification automatique
    |--------------------------------------------------------------------------
    |
    | Configuration des règles métier pour la génération automatique
    | des horaires de cours
    |
    */

    // Durée des cours
    'course_duration_minutes' => env('PLANNING_COURSE_DURATION', 60),

    // Temps de déplacement inter-sites
    'inter_site_travel_time_minutes' => env('PLANNING_INTER_SITE_TRAVEL', 60),

    // Contraintes de taille des groupes
    'group_size' => [
        'min' => env('PLANNING_GROUP_SIZE_MIN', 20),
        'max' => env('PLANNING_GROUP_SIZE_MAX', 40),
    ],

    // Heures de cours par jour (maximum)
    'max_courses_per_day' => env('PLANNING_MAX_COURSES_PER_DAY', 6),

    // Jours de la semaine pour les cours
    'course_days' => [
        'MO' => 'Lundi',
        'TU' => 'Mardi', 
        'WE' => 'Mercredi',
        'TH' => 'Jeudi',
        'FR' => 'Vendredi',
        // 'SA' => 'Samedi',  // Optionnel
        // 'SU' => 'Dimanche', // Optionnel
    ],

    // Heures de début et fin par défaut
    'default_hours' => [
        'start' => env('PLANNING_DAY_START', '08:00'),
        'end' => env('PLANNING_DAY_END', '18:00'),
    ],

    // Marge de sécurité pour éviter les conflits
    'safety_margin_minutes' => env('PLANNING_SAFETY_MARGIN', 15),

    // Priorités pour l'attribution des salles
    'room_priority' => [
        'capacity_match' => 10,      // Capacité parfaite
        'capacity_oversize' => 5,    // Légèrement trop grande
        'site_preference' => 8,      // Site préféré du groupe
        'equipment_availability' => 7, // Équipement disponible
    ],

    // Règles de fallback
    'fallback_rules' => [
        'allow_inter_site' => true,  // Autoriser les cours sur différents sites
        'max_sites_per_day' => 2,    // Maximum de sites différents par jour
        'prefer_same_university' => true, // Préférer la même université
    ],

    // Validation des contraintes
    'constraints' => [
        'check_room_capacity' => true,
        'check_equipment_availability' => true,
        'check_teacher_availability' => false, // À implémenter plus tard
        'check_group_preferences' => true,
        'check_site_hours' => true,
    ],

    // Logging et monitoring
    'logging' => [
        'enabled' => env('PLANNING_LOGGING', true),
        'level' => env('PLANNING_LOG_LEVEL', 'info'),
        'log_generation_steps' => true,
        'log_constraint_violations' => true,
    ],

    // Performance et optimisation
    'performance' => [
        'batch_size' => env('PLANNING_BATCH_SIZE', 100),
        'max_execution_time' => env('PLANNING_MAX_EXECUTION_TIME', 300), // 5 minutes
        'memory_limit' => env('PLANNING_MEMORY_LIMIT', '512M'),
    ],
];
