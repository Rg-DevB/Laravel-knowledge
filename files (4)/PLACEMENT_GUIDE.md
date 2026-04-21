# LaravelKnow — Guide de placement complet de tous les fichiers

## 📁 STRUCTURE FINALE

Voici exactement où placer chaque fichier généré dans ton projet Laravel :

---

## 🟢 Fichiers du lot 1 (déjà générés)

```
all_migrations.php
→ Diviser en fichiers séparés dans : database/migrations/
  ├── 2024_01_01_000001_create_users_table.php
  ├── 2024_01_01_000002_create_categories_table.php
  ├── 2024_01_01_000003_create_tags_table.php
  ├── 2024_01_01_000004_create_problems_table.php
  ├── 2024_01_01_000005_create_problem_tag_table.php
  ├── 2024_01_01_000006_create_problem_attachments_table.php
  ├── 2024_01_01_000007_create_solutions_table.php
  ├── 2024_01_01_000008_create_code_snippets_table.php
  ├── 2024_01_01_000009_create_comments_table.php
  ├── 2024_01_01_000010_create_votes_table.php
  ├── 2024_01_01_000011_create_favorites_table.php
  ├── 2024_01_01_000012_create_follows_table.php
  ├── 2024_01_01_000013_create_reputation_logs_table.php
  ├── 2024_01_01_000014_create_notifications_table.php
  └── 2024_01_01_000015_create_edit_suggestions_table.php

all_models.php
→ Diviser en fichiers séparés dans : app/Models/
  ├── User.php
  ├── Problem.php
  ├── Solution.php
  ├── CodeSnippet.php
  ├── Tag.php
  ├── Category.php
  ├── Comment.php
  ├── Vote.php
  └── ReputationLog.php

all_components.php
→ Diviser en fichiers séparés dans : app/Livewire/
  ├── Search/SearchBar.php
  ├── Problems/ProblemList.php
  ├── Problems/CreateProblem.php
  ├── Solutions/SolutionForm.php
  ├── Voting/VoteSystem.php
  ├── Comments/CommentThread.php
  └── Dashboard/DashboardStats.php

app.blade.php           → resources/views/layouts/app.blade.php
problem-list.blade.php  → resources/views/livewire/problems/problem-list.blade.php
code-snippet.blade.php  → resources/views/components/code-snippet.blade.php
web.php                 → routes/web.php
```

---

## 🔵 Fichiers du lot 2 (ce fichier et les nouveaux)

```
LandingPage.php
→ app/Livewire/Home/LandingPage.php

landing-page.blade.php
→ resources/views/livewire/home/landing-page.blade.php

ProblemDetails.php
→ app/Livewire/Problems/ProblemDetails.php

problem-details.blade.php
→ resources/views/livewire/problems/problem-details.blade.php

create-problem.blade.php
→ resources/views/livewire/problems/create-problem.blade.php
  (la classe CreateProblem.php vient du lot 1 : app/Livewire/Problems/CreateProblem.php)

solution-form.blade.php
→ resources/views/livewire/solutions/solution-form.blade.php
  (la classe SolutionForm.php vient du lot 1)

search-and-notifications.blade.php
→ DIVISER en 3 fichiers :
  ├── resources/views/livewire/search/search-bar.blade.php         (partie vue)
  ├── app/Livewire/Notifications/NotificationBell.php              (classe PHP)
  └── resources/views/livewire/notifications/notification-bell.blade.php (vue)

comment-thread.blade.php
→ resources/views/livewire/comments/comment-thread.blade.php
  (la classe CommentThread.php vient du lot 1)

UserDashboard.php
→ app/Livewire/Dashboard/UserDashboard.php

user-dashboard.blade.php
→ resources/views/livewire/dashboard/user-dashboard.blade.php

user-profile.blade.php  (contient aussi la classe PHP)
→ DIVISER en 2 :
  ├── app/Livewire/Profile/UserProfile.php
  └── resources/views/livewire/profile/user-profile.blade.php

admin-and-settings.php (Livewire classes)
→ DIVISER en 2 :
  ├── app/Livewire/Admin/ModerationPanel.php
  └── app/Livewire/Settings/UserSettings.php

admin-and-settings.blade.php (views)
→ DIVISER en 2 :
  ├── resources/views/livewire/admin/moderation-panel.blade.php
  └── resources/views/livewire/settings/user-settings.blade.php

missing-models-policies-notifications.php
→ DIVISER en fichiers séparés :
  ├── app/Models/ProblemAttachment.php
  ├── app/Models/EditSuggestion.php
  ├── app/Models/Favorite.php
  ├── app/Models/Follow.php
  ├── app/Policies/ProblemPolicy.php
  ├── app/Policies/SolutionPolicy.php
  ├── app/Notifications/NewSolutionNotification.php
  ├── app/Notifications/SolutionVotedNotification.php
  ├── app/Notifications/BestSolutionNotification.php
  ├── app/Notifications/NewCommentNotification.php
  ├── app/Observers/SolutionObserver.php
  ├── app/Observers/ProblemObserver.php
  ├── app/Http/Middleware/RoleMiddleware.php
  ├── app/Providers/AppServiceProvider.php  (modifier l'existant)
  ├── database/seeders/CategorySeeder.php
  ├── database/seeders/TagSeeder.php
  └── database/seeders/DatabaseSeeder.php

app-js-css.txt
→ DIVISER en 2 :
  ├── resources/js/app.js     (remplacer le contenu existant)
  └── resources/css/app.css   (remplacer le contenu existant)
```

---

## ✅ Checklist complète après placement

```bash
# Après avoir placé tous les fichiers :

# 1. Autoload des nouvelles classes
composer dump-autoload

# 2. Migrations
php artisan migrate:fresh --seed

# 3. Enregistrer le middleware dans bootstrap/app.php
# Ajouter : ->withMiddleware(fn($m) => $m->alias(['role' => RoleMiddleware::class]))

# 4. Découvrir les composants Livewire
php artisan livewire:discover

# 5. Indexer dans Meilisearch
php artisan scout:import "App\Models\Problem"
php artisan scout:import "App\Models\Solution"

# 6. Builder les assets
npm install highlight.js @alpinejs/focus
npm run build

# 7. Démarrer
php artisan serve
```

---

## 📦 npm install supplémentaire nécessaire

```bash
npm install highlight.js @alpinejs/focus
```

---

## 🔧 Modifications dans bootstrap/app.php

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

---

## 📝 config/scout.php — Configuration Meilisearch

```php
'meilisearch' => [
    'host'    => env('MEILISEARCH_HOST', 'http://localhost:7700'),
    'key'     => env('MEILISEARCH_KEY'),
    'index-settings' => [
        \App\Models\Problem::class => [
            'filterableAttributes'  => ['status', 'category', 'laravel_version', 'tags'],
            'sortableAttributes'    => ['votes_count', 'views', 'created_at'],
            'rankingRules'          => ['words', 'typo', 'votes_count:desc', 'timestamp:desc', 'attribute', 'sort', 'exactness'],
        ],
    ],
],
```

---

## 📝 config/livewire.php — Ajustements recommandés

```php
'navigate' => [
    'show_progress_bar' => true,
    'progress_bar_color' => '#f43f5e',
],
'inject_assets' => true,
```

---

## ✅ PROJET 100% COMPLET

Après placement et configuration de tous les fichiers :

| Page | URL | Composant |
|------|-----|-----------|
| Landing | / | Home\LandingPage |
| Liste problèmes | /problems | Problems\ProblemList |
| Détail problème | /problems/{slug} | Problems\ProblemDetails |
| Créer problème | /problems/create | Problems\CreateProblem |
| Dashboard | /dashboard | Dashboard\UserDashboard |
| Profil | /u/{username} | Profile\UserProfile |
| Paramètres | /settings | Settings\UserSettings |
| Admin | /admin | Admin\ModerationPanel |

| Composant | Fichier |
|-----------|---------|
| SearchBar | Search\SearchBar |
| VoteSystem | Voting\VoteSystem |
| CommentThread | Comments\CommentThread |
| NotificationBell | Notifications\NotificationBell |
| CodeSnippet | components/code-snippet.blade.php |
| SolutionForm | Solutions\SolutionForm |
