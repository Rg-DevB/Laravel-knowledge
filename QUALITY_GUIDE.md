# Qualité du Code - Guide Complet

## 🧪 Tests Automatisés

### Exécuter les tests
```bash
# Tous les tests
composer test

# Tests unitaires uniquement
php artisan test --testsuite=Unit

# Tests de fonctionnalités uniquement
php artisan test --testsuite=Feature

# Test spécifique
php artisan test --filter=search_api_returns_results
```

### Structure des tests
- **Feature/** : Tests d'intégration et de bout en bout
  - `CoreFunctionalityTest.php` : Fonctionnalités principales
  - `Auth/` : Authentification
  - `Settings/` : Paramètres
- **Unit/** : Tests unitaires
  - `ModelTest.php` : Tests des modèles

### Bonnes pratiques
- Utiliser `RefreshDatabase` pour isoler les tests
- Tester les cas limites et les erreurs
- Couvrir les politiques d'autorisation
- Vérifier la protection contre les attaques (mass assignment, XSS)

## 🔍 Analyse Statique

### Laravel Pint (Formatage)
```bash
# Formater le code
composer lint

# Vérifier sans modifier
composer lint:check
```

### PHPStan (Analyse de type)
```bash
# Installation (si nécessaire)
composer require --dev phpstan/phpstan

# Exécuter l'analyse
./vendor/bin/phpstan analyse
```

Configuration dans `.phpstan.neon` :
- Niveau 5 (équilibre entre strictesse et flexibilité)
- Exclut les tests
- Ignore les erreurs dynamiques Laravel

## 📚 Documentation API

La documentation API est disponible dans `docs/api.md` :

### Points d'accès
- `GET /api/user` - Utilisateur actuel
- `GET /api/search` - Recherche avancée

### Caractéristiques
- Authentification via Sanctum
- Rate limiting automatique
- Validation des entrées
- Responses JSON structurées

## 🚀 CI/CD

### Script de vérification continue
```bash
composer ci:check
```

Ce script exécute :
1. Nettoyage de la configuration
2. Analyse statique (Pint)
3. Tous les tests

## 📊 Métriques de qualité

### Couverture de code
Les tests couvrent :
- ✅ Création de problèmes/solutions
- ✅ Système de réputation
- ✅ Attribution de badges
- ✅ Politiques d'autorisation
- ✅ Protection mass assignment
- ✅ API de recherche
- ✅ Contrôle d'accès

### Points de vigilance
- Vérifier régulièrement les nouvelles fonctionnalités
- Maintenir la couverture de tests > 80%
- Documenter les nouveaux endpoints API
- Exécuter l'analyse statique avant chaque commit

## 🛠️ Outils recommandés

### Pour le développement
- **Laravel Debugbar** : Déjà installé pour le débogage
- **IDE avec support PHP** : PhpStorm, VS Code + extensions PHP
- **Git hooks** : Automatiser lint/test avant commit

### Pour la production
- **Monitoring** : Laravel Telescope (désactivé en prod)
- **Logging** : Configuré dans `config/logging.php`
- **Error tracking** : Sentry ou Bugsnag (à ajouter)

## 📝 Checklist avant déploiement

- [ ] Tous les tests passent (`composer test`)
- [ ] Aucune erreur Pint (`composer lint:check`)
- [ ] PHPStan niveau 5 valide
- [ ] Documentation API à jour
- [ ] Variables d'environnement configurées
- [ ] Migrations exécutées
- [ ] Cache cleared (`php artisan optimize:clear`)
