# api-stages

## Description

API REST de gestion des offres de stage universitaires, réalisée avec Laravel, SQLite, Sanctum et Pest.

L’API permet aux étudiants de consulter les offres et de les filtrer par domaine. Les entreprises se connectent avec un jeton Bearer, publient une offre, la modifient ou la suppriment. Seule l’entreprise propriétaire d’une offre peut la modifier ou la supprimer.

Le projet respecte volontairement le contrat du professeur : **4 rôles**, **9 endpoints**, aucun modèle ni endpoint `Candidature`.

## Les quatre rôles

| Rôle du professeur | Dans ce projet | Fichier principal |
|---|---|---|
| Propriétaire | l’entreprise (`User`) | `app/Models/User.php` |
| Ressource principale | l’offre de stage (`Offre`) | `app/Models/Offre.php` |
| Ressource parente | le domaine (`Domaine`) | `app/Models/Domaine.php` |
| Étiquette | la compétence (`Competence`) | `app/Models/Competence.php` |

Relations : `User::offres()` en `HasMany`, `Offre::user()` et `Offre::domaine()` en `BelongsTo`, `Offre::competences()` en `BelongsToMany`, `Domaine::offres()` en `HasMany`.

La table pivot `competence_offre` contient `competence_id`, `offre_id`, `timestamps` et un index unique composé.

`Offre` possède 6 colonnes métier : `titre`, `description`, `duree_mois`, `remuneration`, `date_debut`, `est_disponible`. `duree_mois` utilise la règle `min:1` ; `date_debut` et `est_disponible` sont déclarés dans `casts()`.

## Installation

### Bash

```bash
cd api-stages
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan test
php artisan serve
```

### PowerShell

```powershell
cd api-stages
composer install
copy .env.example .env
php artisan key:generate
if (-not (Test-Path database\database.sqlite)) { New-Item -ItemType File -Path database\database.sqlite }
php artisan migrate:fresh --seed
php artisan test
php artisan serve
```

Le serveur répond ensuite sur `http://localhost:8000`.

Le seeder crée 2 entreprises, 3 domaines, 5 compétences et 20 offres avec leurs compétences.

## Comptes de test

Les deux comptes sont créés par `DatabaseSeeder`. Le mot de passe est `password` pour les deux.

| Nom | E-mail | Mot de passe |
|---|---|---|
| Entreprise Alpha | `entreprise.alpha@example.com` | `password` |
| Entreprise Beta | `entreprise.beta@example.com` | `password` |

Les deux comptes sont des entreprises. Le deuxième compte sert à vérifier le code `403` lorsqu’une entreprise essaie de modifier une offre qui ne lui appartient pas.

## Endpoints

| # | Méthode | URI | Accès | Codes vérifiés |
|---|---|---|---|---|
| 1 | POST | `/api/login` | public | 200 avec jeton, 422 si identifiants invalides |
| 2 | POST | `/api/logout` | jeton | 200, 401 sans jeton |
| 3 | GET | `/api/offres` | public, paginé | 200, filtre `?domaine=<slug>` |
| 4 | GET | `/api/offres/{id}` | public | 200, 404 si inexistant |
| 5 | POST | `/api/offres` | jeton | 201, 401, 422 |
| 6 | PUT | `/api/offres/{id}` | jeton + Policy | 200, 401, 403, 404, 422 |
| 7 | DELETE | `/api/offres/{id}` | jeton + Policy | 200, 401, 403, 404 |
| 8 | GET | `/api/domaines` | public, paginé | 200, avec `offres_count` |
| 9 | GET | `/api/domaines/{id}` | public | 200, 404 si inexistant |

Exemples de slugs créés par le seeder : `developpement-web`, `data-intelligence-artificielle`, `cybersecurite`.

## Authentification par jeton

1. Appeler `POST /api/login` avec `email` et `password`.
2. La réponse contient `token` et `token_type: "Bearer"`.
3. Pour les routes protégées, ajouter l’en-tête :

```text
Authorization: Bearer LE_TOKEN_RECU
Accept: application/json
```

`POST /api/logout` révoque uniquement le jeton utilisé pour la requête. Ensuite, ce jeton ne fonctionne plus.

## Codes d’erreur

| Code | Signification | Exemple dans ce projet |
|---|---|---|
| 401 | Non authentifié | Créer une offre sans jeton, ou utiliser un jeton révoqué |
| 403 | Autorisé mais interdit | Modifier une offre appartenant à une autre entreprise |
| 404 | Ressource introuvable | `GET /api/offres/999999`, `GET /api/domaines/999999` |
| 422 | Validation échouée | Identifiants invalides, `duree_mois = 0`, slug de domaine inconnu |

Les messages de validation sont en français, grâce aux tableaux `messages()` des Form Requests.

## Exemple curl.exe

Connexion :

```powershell
curl.exe -H "Accept: application/json" -H "Content-Type: application/json" -d "{\"email\":\"entreprise.alpha@example.com\",\"password\":\"password\"}" http://localhost:8000/api/login
```

Lecture publique avec filtre :

```powershell
curl.exe -H "Accept: application/json" "http://localhost:8000/api/offres?domaine=developpement-web"
```

Création avec jeton (remplacez `LE_TOKEN` par le jeton reçu) :

```powershell
curl.exe -H "Accept: application/json" -H "Authorization: Bearer LE_TOKEN" -H "Content-Type: application/json" -d "{\"titre\":\"Stage example\",\"description\":\"Stage de test\",\"duree_mois\":4,\"remuneration\":800,\"date_debut\":\"2026-12-01\",\"domaine_id\":1,\"competences\":[1,2]}" http://localhost:8000/api/offres
```

## Collection Postman

Fichier : `docs/postman/api-stages.postman_collection.json`.

La collection est au format v2.1. Elle utilise deux variables de collection :

- `base_url` = `http://localhost:8000`
- `token` = vide au départ

La requête `Login` enregistre automatiquement le jeton dans `{{token}}`. Les requêtes protégées héritent de l’authentification Bearer de la collection. Aucun jeton réel n’est enregistré dans le fichier.

## Choix d’architecture

### Pourquoi la ressource principale appartient-elle à l’utilisateur, et que protège la Policy ?

Une offre de stage est publiée par une entreprise : c’est donc l’utilisateur connecté qui en est le propriétaire via `user_id`. Lors de la création, le contrôleur n’accepte pas de `user_id` envoyé par le client ; il utilise `$request->user()->offres()->create(...)`. Ainsi, une entreprise ne peut pas publier une offre au nom d’une autre.

La `OffrePolicy` protège la modification et la suppression : `update()` et `delete()` ne renvoient `true` que si l’identifiant de l’utilisateur connecté est égal à `offre.user_id`. Pour la modification, `UpdateOffreRequest::authorize()` appelle la Policy avant la validation. Pour la suppression, le contrôleur appelle `Gate::authorize('delete', $offre)` en premier. Il n’existe pas d’administrateur « passe-partout », car le contrat impose le propriétaire uniquement.

### À quel besoin réel d’un utilisateur de l’API répond le filtre ?

Un étudiant ne consulte pas toutes les offres de la même manière : il cherche souvent un stage dans un domaine précis, par exemple le développement web. Le filtre `?domaine=developpement-web` lui permet d’obtenir seulement les offres de ce domaine, sans télécharger toute la liste puis trier lui-même.

La Form Request `IndexOffreRequest` vérifie que le slug existe dans la table `domaines`. Un slug inconnu donne une erreur `422` avec un message français, au lieu d’une liste vide silencieuse.

### Quelle requête produirait un problème N+1 sans with(), et comment l’avez-vous évité ?

La liste `GET /api/offres` affiche pour chaque offre son domaine et ses compétences. Sans `with()`, Laravel ferait une requête pour la page d’offres, puis une requête supplémentaire pour le domaine de chaque offre et une autre pour les compétences de chaque offre : c’est le problème N+1.

Dans `OffreController::index()`, j’utilise :

```php
Offre::query()
    ->with(['domaine:id,name,slug', 'competences:id,name,slug'])
    ->withCount('competences')
```

`with()` charge les relations en quelques requêtes seulement, et `withCount()` évite de charger toutes les compétences rien que pour les compter. Dans `AppServiceProvider::boot()`, `Model::preventLazyLoading(! app()->isProduction())` lève une erreur en développement si une relation oubliée est consultée, ce qui rend l’erreur visible immédiatement.

Les dates sont renvoyées en ISO 8601 grâce à `toIso8601String()` dans les Resources, et les relations ne apparaissent dans le JSON que lorsqu’elles sont chargées, grâce à `whenLoaded()`.
