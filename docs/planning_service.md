# 🚀 Service de Planification Automatique des Horaires

## 📋 Vue d'ensemble

Le **PlanningService** est un service Laravel qui génère automatiquement les horaires de cours selon des règles métier strictes. Il respecte toutes les contraintes définies dans la checklist et optimise l'utilisation des ressources.

## 🎯 Fonctionnalités principales

### ✅ Génération automatique
- Création automatique des sessions de cours pour une année académique
- Respect des contraintes de capacité et d'équipement
- Optimisation de l'utilisation des salles et des sites

### ✅ Règles métier implémentées
- **Durée cours** : Exactement 1h (60 minutes)
- **Site principal** : Préférence du site principal du groupe
- **Déplacement inter-sites** : 1h de déplacement si changement de site
- **Plages horaires** : Respect des heures d'ouverture de chaque site
- **Fallback** : Sites alternatifs si le principal est indisponible
- **Collisions** : Aucune collision entre groupes, salles et équipements
- **Capacité** : Salle ≥ effectif du groupe
- **Équipement** : Matériel requis disponible (fixe/mobile)

## 🏗️ Architecture technique

### Structure des fichiers
```
app/
├── Services/
│   └── PlanningService.php          # Service principal
├── Contracts/
│   └── IPlanningService.php         # Interface du service
├── Http/Controllers/
│   └── PlanningController.php       # Contrôleur de planification
config/
└── planning.php                     # Configuration des paramètres
resources/js/pages/
└── planning/
    └── index.vue                    # Interface utilisateur
```

### Flux de génération
1. **Sélection** : Choix de l'année académique
2. **Récupération** : Groupes et cours de l'année
3. **Validation** : Vérification des contraintes
4. **Attribution** : Salles et créneaux horaires
5. **Création** : Sessions de cours avec relations
6. **Logging** : Traçabilité complète du processus

## ⚙️ Configuration

### Paramètres principaux
```php
// config/planning.php
'course_duration_minutes' => 60,
'inter_site_travel_time_minutes' => 60,
'group_size' => ['min' => 20, 'max' => 40],
'max_courses_per_day' => 6,
'safety_margin_minutes' => 15,
```

### Variables d'environnement
```env
PLANNING_COURSE_DURATION=60
PLANNING_INTER_SITE_TRAVEL=60
PLANNING_GROUP_SIZE_MIN=20
PLANNING_GROUP_SIZE_MAX=40
PLANNING_MAX_COURSES_PER_DAY=6
PLANNING_SAFETY_MARGIN=15
```

## 🔧 Utilisation

### Via le contrôleur
```php
use App\Services\PlanningService;

class PlanningController extends Controller
{
    public function generateSchedule(Request $request)
    {
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $result = $this->planningService->generateScheduleForAcademicYear($academicYear);
        
        if ($result['success']) {
            return back()->with('success', "{$result['total_generated']} sessions créées");
        }
        
        return back()->withErrors(['error' => $result['message']]);
    }
}
```

### Via l'interface utilisateur
1. Aller sur `/planning`
2. Sélectionner l'année académique
3. Cliquer sur "🚀 Générer les horaires"
4. Attendre la génération (avec barre de progression)
5. Voir le résultat et les éventuelles erreurs

## 📊 Logs et monitoring

### Niveaux de logging
- **INFO** : Début/fin de génération, sessions créées
- **WARNING** : Contraintes non respectées, fallbacks utilisés
- **ERROR** : Erreurs de génération, exceptions

### Exemple de logs
```log
[2025-08-26 23:30:00] local.INFO: PlanningService: Début de génération des horaires
[2025-08-26 23:30:01] local.INFO: PlanningService: Traitement du groupe G1 (25 étudiants)
[2025-08-26 23:30:02] local.INFO: PlanningService: Salle A101 attribuée (capacité: 30)
[2025-08-26 23:30:03] local.INFO: PlanningService: Session créée avec succès
[2025-08-26 23:30:10] local.INFO: PlanningService: Génération terminée (45 sessions créées)
```

## 🚨 Gestion des erreurs

### Types d'erreurs
1. **Contraintes non respectées** : Taille de groupe invalide
2. **Ressources indisponibles** : Aucune salle disponible
3. **Conflits** : Collisions d'horaires
4. **Configuration** : Sites ou salles mal configurés

### Stratégies de fallback
1. **Site principal indisponible** → Sites alternatifs de l'université
2. **Salle trop petite** → Salle plus grande sur le même site
3. **Équipement indisponible** → Création sans équipement (avec warning)

## 🔄 Optimisations futures

### Fonctionnalités à implémenter
- [ ] **Disponibilité des enseignants** : Contraintes de planning
- [ ] **Préférences de groupes** : Horaires préférés
- [ ] **Optimisation multi-critères** : Algorithme génétique
- [ ] **Planification incrémentale** : Ajout de sessions sans tout recalculer
- [ ] **Export/Import** : Formats Excel, PDF, iCal

### Améliorations techniques
- [ ] **Queue jobs** : Génération asynchrone pour gros volumes
- [ ] **Cache Redis** : Mise en cache des contraintes
- [ ] **API REST** : Endpoints pour intégration externe
- [ ] **Webhooks** : Notifications en temps réel

## 🧪 Tests et validation

### Tests unitaires
```php
// tests/Unit/Services/PlanningServiceTest.php
public function test_generate_schedule_for_academic_year()
{
    $academicYear = AcademicYear::factory()->create();
    $result = $this->planningService->generateScheduleForAcademicYear($academicYear);
    
    $this->assertTrue($result['success']);
    $this->assertGreaterThan(0, $result['total_generated']);
}
```

### Tests d'intégration
```php
// tests/Feature/PlanningControllerTest.php
public function test_generate_schedule_endpoint()
{
    $response = $this->post('/planning/generate-schedule', [
        'academic_year_id' => 1
    ]);
    
    $response->assertRedirect();
    $response->assertSessionHas('success');
}
```

## 📈 Métriques et KPIs

### Indicateurs de performance
- **Temps de génération** : < 5 minutes pour 1000 sessions
- **Taux de succès** : > 95% des sessions générées
- **Utilisation des ressources** : Salles utilisées à > 80%
- **Satisfaction des contraintes** : 100% des règles métier respectées

### Tableaux de bord
- Sessions générées par jour/semaine
- Taux d'utilisation des salles par site
- Erreurs de planification par type
- Performance du service (temps de réponse)

## 🔐 Sécurité et permissions

### Contrôles d'accès
- **Middleware** : `auth` et `verified`
- **Permissions** : Seuls les administrateurs peuvent générer
- **Validation** : Vérification des données d'entrée
- **Audit** : Traçabilité des actions de planification

### Protection des données
- **Validation** : Toutes les entrées sont validées
- **Sanitisation** : Nettoyage des données sensibles
- **Rate limiting** : Protection contre les abus
- **Logs sécurisés** : Pas d'informations sensibles dans les logs

---

## 🎉 Conclusion

Le **PlanningService** implémente complètement le point 3 de la checklist :
- ✅ Service dédié isolant la logique de génération
- ✅ Toutes les règles métier encodées
- ✅ Paramètres centralisés et configurables
- ✅ Gestion des erreurs et fallbacks
- ✅ Interface utilisateur intuitive
- ✅ Logging et monitoring complets

**Prochaine étape** : Implémentation de l'architecture en couches (Services → Repositories → Models) pour le point 2 de la checklist.
