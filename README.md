# EPHEC_SGBD
Courte description : générateur et gestionnaire d’horaires multi-universités avec contraintes (sites, salles, équipements, groupes, déplacements).

## Sommaire
- [Contexte & objectifs](#contexte--objectifs)
- [Caractéristiques principales](#caractéristiques-principales)
- [Architecture](#architecture)
- [Schéma des données](#schéma-des-données)
- [API — Aperçu](#api--aperçu)
- [Règles métier de planification](#règles-métier-de-planification)
- [Installation & Prérequis](#installation--prérequis)
- [Configuration (ENV)](#configuration-env)
- [Lancer le projet](#lancer-le-projet)
- [Jeu de données / Seed](#jeu-de-données--seed)
- [Vérifications rapides](#vérifications-rapides)
- [Limites connues](#limites-connues)
- [Licence](#licence)

## Contexte & objectifs
- Multi-universités, multi-sites, planning basé sur année académique.
- Deux interfaces : **Admin** (gestion, génération, modification) et **Groupe** (visualisation jour/semaine/mois).
- Front via **Web API** uniquement (aucun accès direct DB).

## Caractéristiques principales
- Groupes : effectif **min 20 / max 40**, site principal.
- Cours : durée **1h**, sites autorisés, matériel requis (fixe/mobile).
- Salles : capacité, association équipements.
- Génération auto d’horaires + modification **futur uniquement**.
- Déplacement inter-sites **1h** le même jour ; respect **plages par site**.
- Préférence du **site principal** pour les groupes.

## Architecture
Technos détectées :  
- **Frontend** : Vue 3 (TypeScript), [@inertiajs/vue3](https://inertiajs.com/), TailwindCSS  
- **Build tools** : Vite, Prettier, ESLint  
- **Backend** : **TODO:** (non détecté dans ce repo, à compléter)
- **DB** : **TODO:** (non détecté dans ce repo, à compléter)

Monorepo / multi-projets :  
- **Mono-repo** (frontend et potentiellement backend dans le même repo, mais seul le frontend est visible ici)

Organisation des couches :
- Controllers → Services → Repositories → Models (+ interfaces génériques)
- Deux contrôleurs min. : **Admin** et **Groupes** (noms réels : **TODO:**)
