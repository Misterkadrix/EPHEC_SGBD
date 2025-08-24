# Système de Gestion des Produits

## Vue d'ensemble

Ce système permet de gérer des produits avec les fonctionnalités suivantes :
- Affichage de la liste des produits
- Création de nouveaux produits
- Validation des données côté serveur
- Interface utilisateur moderne avec Inertia.js et Vue.js

## Structure des données

Chaque produit contient :
- **name** : Nom du produit (string, max 255 caractères)
- **description** : Description détaillée (text, max 1000 caractères)
- **quantite** : Quantité en stock (integer, minimum 0)
- **created_at** : Date de création (automatique)
- **updated_at** : Date de modification (automatique)

## Routes disponibles

- `GET /products` - Liste des produits (products.index)
- `GET /products/create` - Formulaire de création (products.create)
- `POST /products` - Création d'un produit (products.store)

## Installation et configuration

### 1. Exécuter les migrations
```bash
php artisan migrate
```

### 2. Exécuter les seeders (optionnel)
```bash
php artisan db:seed
```

### 3. Vérifier les routes
```bash
php artisan route:list
```

## Utilisation

### Créer un produit
1. Naviguer vers `/products`
2. Cliquer sur "Créer un produit"
3. Remplir le formulaire avec :
   - Nom du produit
   - Description
   - Quantité
4. Cliquer sur "Créer le produit"

### Validation
Le système valide automatiquement :
- Tous les champs sont obligatoires
- Le nom ne peut pas dépasser 255 caractères
- La description ne peut pas dépasser 1000 caractères
- La quantité doit être un nombre entier positif

## Composants utilisés

- **AppLayout** : Layout principal de l'application
- **Card** : Composant pour afficher les informations des produits
- **Button** : Boutons d'action
- **Input** : Champs de saisie
- **Label** : Labels des champs

## Fonctionnalités techniques

- **Frontend** : Vue.js 3 avec TypeScript
- **Backend** : Laravel avec Inertia.js
- **Base de données** : Migration automatique avec Laravel
- **Validation** : Validation côté serveur avec messages d'erreur personnalisés
- **Messages flash** : Affichage des messages de succès/erreur

## Extensions possibles

- Modification des produits existants
- Suppression de produits
- Recherche et filtrage
- Pagination pour de grandes listes
- Images des produits
- Catégories de produits
- Gestion des stocks avancée
