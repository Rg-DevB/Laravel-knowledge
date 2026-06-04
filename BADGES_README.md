# Système de Badges

## Vue d'ensemble

Le système de badges permet de récompenser les utilisateurs pour leurs contributions et réalisations sur la plateforme.

## Badges disponibles

### Badges de participation (Bronze)
- **Premier problème** : Publier son premier problème
- **Première solution** : Publier sa première solution
- **Contributeur** : Atteindre 100 points de réputation

### Badges intermédiaires (Argent)
- **Créateur de problèmes** : Publier 10 problèmes
- **Résolveur** : Publier 25 solutions
- **Membre actif** : Atteindre 1000 points de réputation
- **Première meilleure solution** : Obtenir sa première solution marquée comme meilleure

### Badges avancés (Or)
- **Maître des problèmes** : Publier 50 problèmes
- **Expert en solutions** : Publier 100 solutions
- **Expert** : Atteindre 5000 points de réputation

### Badge ultime (Platine)
- **Légende** : Atteindre 10000 points de réputation

## Attribution automatique

Les badges sont attribués automatiquement dans les cas suivants :

1. **Lors de la publication d'un problème** : Vérifie les badges `first-problem` et `problem-creator`
2. **Lors de la publication d'une solution** : Vérifie les badges `first-solution` et `solver`
3. **Lorsqu'une solution est marquée comme meilleure** :Attribue le badge `first-best-solution`
4. **Lors de gains de réputation** : Vérifie les badges basés sur la réputation

## Utilisation dans les vues

### Afficher les badges d'un utilisateur

```blade
{{-- Liste complète des badges --}}
<x-badge-list :badges="$user->badges" />

{{-- Avec limite d'affichage --}}
<x-badge-list :badges="$user->badges" :limit="5" />

{{-- Badge individuel --}}
<x-badge :badge="$badge" size="lg" :showLabel="true" />
```

### Tailles disponibles
- `sm` : Petit (32px)
- `md` : Moyen (40px, défaut)
- `lg` : Grand (48px)

## API

### Modèle Badge

```php
// Obtenir tous les badges actifs
$badges = Badge::active()->get();

// Filtrer par type
$goldBadges = Badge::OfType('gold')->get();

// Vérifier si un utilisateur a un badge
$hasBadge = $badge->hasUser($user);
```

### Modèle User

```php
// Obtenir les badges d'un utilisateur
$badges = $user->badges;

// Attribuer un badge manuellement
$user->awardBadge($badge, 'Contexte optionnel');

// Vérifier et attribuer tous les badges éligibles
$user->checkAndAwardBadges();
```

## Seeders

Pour peupler la base de données avec les badges par défaut :

```bash
php artisan db:seed --class=BadgeSeeder
```

Ou via le seeder principal :

```bash
php artisan db:seed
```

## Migration

La migration crée deux tables :
- `badges` : Définition des badges
- `user_badges` : Table pivot liant les utilisateurs aux badges

Exécution :
```bash
php artisan migrate
```
