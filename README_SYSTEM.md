# Système de Gestion Universitaire - Documentation Complète

## 🏗️ Architecture du Système

Ce système de gestion universitaire permet de gérer :
- **Universités** et leurs **sites**
- **Salles** et **équipements** (fixes et mobiles)
- **Cours** avec **permissions** par site
- **Années académiques** et **groupes** d'étudiants
- **Sessions de cours** avec **réservations** d'équipements
- **Fériés** (globaux et spécifiques par université)

## 📊 Structure des Tables

### 1. **universities** - Universités
- `id` : Identifiant unique
- `code` : Code court unique (ex: KAD, EPHEC, UCL)
- `name` : Nom complet de l'université

### 2. **sites** - Sites/Campus
- `id` : Identifiant unique
- `university_id` : Référence vers l'université
- `name` : Nom du site
- `timezone` : Fuseau horaire (défaut: Europe/Brussels)
- `day_start` : Heure de début de journée (défaut: 08:00)
- `day_end` : Heure de fin de journée (défaut: 18:00)
- `active_days` : Jours actifs en JSON (défaut: ["MO","TU","WE","TH","FR"])

### 3. **rooms** - Salles/Locaux
- `id` : Identifiant unique
- `site_id` : Référence vers le site
- `name` : Nom de la salle
- `capacity` : Capacité d'accueil
- `description` : Description optionnelle

### 4. **equipment_types** - Types d'équipements
- `id` : Identifiant unique
- `label` : Libellé unique (ex: "PC Lab", "Projecteur", "Baffles")

### 5. **equipment** - Équipements
- `id` : Identifiant unique
- `site_id` : Référence vers le site
- `type_id` : Référence vers le type d'équipement
- `is_mobile` : Booléen (0 = fixe, 1 = mobile)
- `fixed_room_id` : Référence vers la salle si fixe (NULL si mobile)

### 6. **courses** - Cours
- `id` : Identifiant unique
- `university_id` : Référence vers l'université
- `code` : Code du cours (unique par université)
- `title` : Titre du cours
- `description` : Description optionnelle

### 7. **course_site_permissions** - Permissions cours ↔ sites
- `course_id` : Référence vers le cours
- `site_id` : Référence vers le site
- Clé primaire composite : (course_id, site_id)

### 8. **course_required_equipment** - Équipements requis par cours
- `course_id` : Référence vers le cours
- `equipment_type_id` : Référence vers le type d'équipement
- Clé primaire composite : (course_id, equipment_type_id)

### 9. **academic_years** - Années académiques
- `id` : Identifiant unique
- `university_id` : Référence vers l'université
- `name` : Nom de l'année (ex: "2024-2025")
- `start_date` : Date de début
- `end_date` : Date de fin
- `state` : État (planned, active, archived)

### 10. **groups** - Groupes d'étudiants
- `id` : Identifiant unique
- `university_id` : Référence vers l'université
- `academic_year_id` : Référence vers l'année académique
- `name` : Nom du groupe
- `quantity` : Taille du groupe
- `main_site_id` : Site principal du groupe
- `min_size` : Taille minimale (défaut: 20)
- `max_size` : Taille maximale (défaut: 40)

### 11. **course_sessions** - Sessions de cours
- `id` : Identifiant unique
- `academic_year_id` : Référence vers l'année académique
- `course_id` : Référence vers le cours
- `site_id` : Référence vers le site
- `room_id` : Référence vers la salle
- `start_at` : Date/heure de début (UTC, précision 6)
- `end_at` : Date/heure de fin (UTC, précision 6)

### 12. **session_groups** - Liens sessions ↔ groupes
- `session_id` : Référence vers la session
- `group_id` : Référence vers le groupe
- Clé primaire composite : (session_id, group_id)

### 13. **session_equipment** - Réservations d'équipements
- `session_id` : Référence vers la session
- `equipment_id` : Référence vers l'équipement
- Clé primaire composite : (session_id, equipment_id)

### 14. **holidays** - Fériés et fermetures
- `id` : Identifiant unique
- `name` : Nom du férié
- `date` : Date du férié
- `year` : Année
- `university_id` : Référence vers l'université (NULL = férié global)

## 🔗 Relations et Contraintes

### Contraintes de Clés Étrangères
- Toutes les tables respectent l'intégrité référentielle
- Suppression en cascade pour les relations principales
- Suppression en cascade pour les relations de session

### Contraintes Métier
- **Sessions** : Une salle ne peut avoir qu'une session par créneau (unique: room_id, start_at)
- **Équipements** : Les équipements fixes doivent avoir une salle assignée
- **Permissions** : Vérification que sites.university_id == courses.university_id
- **Réservations** : Équipements mobiles uniquement pour session_equipment

### Index de Performance
- Index sur (university_id, name) pour sites
- Index sur (site_id, name) pour rooms
- Index sur (site_id, type_id) pour equipment
- Index sur (academic_year_id, start_at) pour course_sessions
- Index sur (site_id, start_at) pour course_sessions
- Index sur (group_id, session_id) pour session_groups

## 🚀 Installation et Configuration

### 1. Exécuter les migrations
```bash
php artisan migrate
```

### 2. Exécuter les seeders
```bash
php artisan db:seed
```

### 3. Vérifier les routes
```bash
php artisan route:list
```

## 📋 Données de Test Incluses

### Universités
- **KAD** : Université KAD
- **EPHEC** : Haute École EPHEC  
- **UCL** : Université Catholique de Louvain

### Sites
- **Campus Principal** (KAD) : 08:00-18:00, L-V
- **Campus Technologique** (KAD) : 07:30-19:30, L-S
- **Site Woluwe** (EPHEC) : 08:00-17:00, L-V

### Salles
- Salles de cours standards (A101, A102)
- Laboratoire informatique (B201)
- Amphithéâtre (Tech1)
- Salle de projet (P301)

### Types d'Équipements
- PC Lab, Projecteur, Baffles, TBI, Microphone, Caméra, etc.

### Cours
- INFO101 : Introduction à l'Informatique
- MATH201 : Mathématiques Appliquées
- PROG301 : Programmation Avancée
- BUS101 : Fondements du Business
- PHYS101 : Physique Générale

### Groupes
- INFO-1A, INFO-1B (30 et 28 étudiants)
- MATH-2A (25 étudiants)
- PROG-3A (22 étudiants)
- BUS-1A (35 étudiants)

### Sessions
- Sessions réparties sur différents jours
- Cours théoriques et pratiques
- Utilisation des laboratoires

## 🔧 Fonctionnalités Techniques

### Validation des Données
- Contraintes de base de données
- Validation côté serveur Laravel
- Messages d'erreur en français

### Gestion des Fuseaux Horaire
- Stockage UTC pour les sessions
- Affichage selon le fuseau du site
- Gestion des changements d'heure

### Équipements
- Distinction fixe/mobile
- Réservation automatique des équipements fixes
- Gestion des conflits de réservation

### Sécurité
- Authentification requise pour toutes les routes
- Vérification des permissions par université
- Protection contre les accès non autorisés

## 📈 Extensions Possibles

### Fonctionnalités Avancées
- **Gestion des conflits** : Détection automatique des chevauchements
- **Notifications** : Alertes pour les réservations
- **Rapports** : Statistiques d'utilisation
- **API REST** : Interface pour applications tierces
- **Interface mobile** : Application mobile pour les étudiants

### Intégrations
- **Calendrier** : Synchronisation avec Google Calendar, Outlook
- **Email** : Notifications automatiques
- **SMS** : Alertes par SMS
- **LDAP** : Authentification avec l'annuaire universitaire

### Optimisations
- **Cache** : Mise en cache des requêtes fréquentes
- **Queue** : Traitement asynchrone des tâches lourdes
- **Monitoring** : Surveillance des performances
- **Backup** : Sauvegarde automatique des données

## 🐛 Dépannage

### Problèmes Courants
1. **Erreur de migration** : Vérifier l'ordre des migrations
2. **Conflit de clés étrangères** : Exécuter les seeders dans l'ordre
3. **Problème de fuseau horaire** : Vérifier la configuration PHP

### Logs et Debug
- Logs Laravel dans `storage/logs/`
- Debug des requêtes SQL avec `DB::enableQueryLog()`
- Validation des données avec `php artisan tinker`

## 📞 Support

Pour toute question ou problème :
1. Vérifier la documentation
2. Consulter les logs d'erreur
3. Tester avec les données de test
4. Contacter l'équipe de développement
