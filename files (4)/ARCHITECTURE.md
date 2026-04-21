# LaravelKnow — Complete Project Architecture

## 🏗 Project Structure

```
laravelknow/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ProblemController.php
│   │   │   └── SolutionController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   ├── Livewire/
│   │   ├── Search/
│   │   │   └── SearchBar.php               ← Smart suggestions, debounced
│   │   ├── Problems/
│   │   │   ├── ProblemList.php             ← Full filter system, URL sync
│   │   │   ├── ProblemDetails.php          ← Full issue view + solutions
│   │   │   └── CreateProblem.php           ← 3-step wizard, duplicate detection
│   │   ├── Solutions/
│   │   │   ├── SolutionForm.php            ← Multi-snippet editor
│   │   │   └── SolutionCard.php            ← Display + best solution badge
│   │   ├── Voting/
│   │   │   └── VoteSystem.php              ← +1/-1, reputation aware
│   │   ├── Comments/
│   │   │   └── CommentThread.php           ← Nested replies
│   │   ├── Notifications/
│   │   │   └── NotificationBell.php        ← Real-time dropdown
│   │   ├── Dashboard/
│   │   │   └── DashboardStats.php          ← Stats + activity feed
│   │   ├── Profile/
│   │   │   └── UserProfile.php             ← Public profile
│   │   └── Admin/
│   │       ├── ModerationPanel.php
│   │       ├── ProblemModeration.php
│   │       ├── UserManagement.php
│   │       └── TagManagement.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Problem.php                     ← Searchable (Scout)
│   │   ├── Solution.php                    ← Searchable (Scout)
│   │   ├── CodeSnippet.php
│   │   ├── Tag.php
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   ├── Vote.php
│   │   ├── Favorite.php
│   │   ├── Follow.php
│   │   ├── ReputationLog.php
│   │   └── EditSuggestion.php
│   ├── Notifications/
│   │   ├── NewSolutionNotification.php
│   │   ├── SolutionVotedNotification.php
│   │   └── BestSolutionNotification.php
│   ├── Policies/
│   │   ├── ProblemPolicy.php
│   │   └── SolutionPolicy.php
│   └── Observers/
│       ├── ProblemObserver.php             ← Auto-index Scout on save
│       └── SolutionObserver.php
│
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   ├── 2024_01_01_000002_create_categories_table.php
│   │   ├── 2024_01_01_000003_create_tags_table.php
│   │   ├── 2024_01_01_000004_create_problems_table.php
│   │   ├── 2024_01_01_000005_create_problem_tag_table.php
│   │   ├── 2024_01_01_000006_create_problem_attachments_table.php
│   │   ├── 2024_01_01_000007_create_solutions_table.php
│   │   ├── 2024_01_01_000008_create_code_snippets_table.php
│   │   ├── 2024_01_01_000009_create_comments_table.php
│   │   ├── 2024_01_01_000010_create_votes_table.php
│   │   ├── 2024_01_01_000011_create_favorites_table.php
│   │   ├── 2024_01_01_000012_create_follows_table.php
│   │   ├── 2024_01_01_000013_create_reputation_logs_table.php
│   │   ├── 2024_01_01_000014_create_notifications_table.php
│   │   └── 2024_01_01_000015_create_edit_suggestions_table.php
│   └── seeders/
│       ├── CategorySeeder.php
│       ├── TagSeeder.php
│       └── DatabaseSeeder.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php               ← Main dark sidebar layout
│       │   └── guest.blade.php             ← Auth pages layout
│       ├── livewire/
│       │   ├── search/search-bar.blade.php
│       │   ├── problems/
│       │   │   ├── problem-list.blade.php
│       │   │   ├── problem-details.blade.php
│       │   │   └── create-problem.blade.php
│       │   ├── solutions/
│       │   │   ├── solution-form.blade.php
│       │   │   └── solution-card.blade.php
│       │   ├── voting/vote-system.blade.php
│       │   ├── comments/comment-thread.blade.php
│       │   └── dashboard/dashboard-stats.blade.php
│       └── components/
│           ├── code-snippet.blade.php      ← Tabs, copy, expand/collapse
│           ├── reputation-badge.blade.php
│           ├── tag-pill.blade.php
│           └── problem-card.blade.php
│
└── routes/
    ├── web.php
    └── api.php
```

---

## ⚡ Quick Start

```bash
# 1. Create project
composer create-project laravel/laravel laravelknow
cd laravelknow

# 2. Install packages
composer require livewire/livewire laravel/breeze
composer require league/commonmark          # Markdown
composer require spatie/laravel-tags        # Tag management helper
composer require meilisearch/meilisearch-php  # Scout driver

npm install @tailwindcss/typography highlight.js

# 3. Install Breeze with Livewire
php artisan breeze:install livewire-functional --dark

# 4. Configure Scout (Meilisearch recommended for instant search)
# .env
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://localhost:7700

# 5. Run migrations + seed
php artisan migrate
php artisan db:seed

# 6. Index existing content
php artisan scout:import "App\Models\Problem"
php artisan scout:import "App\Models\Solution"

# 7. Build assets
npm run dev

# 8. Start server
php artisan serve
```

---

## 📦 Package List

```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^11.0",
        "livewire/livewire": "^3.0",
        "laravel/breeze": "^2.0",
        "laravel/scout": "^10.0",
        "meilisearch/meilisearch-php": "^1.0",
        "league/commonmark": "^2.4",
        "spatie/laravel-permission": "^6.0",
        "intervention/image-laravel": "^1.0",
        "barryvdh/laravel-debugbar": "^3.0"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "pestphp/pest": "^2.0",
        "pestphp/pest-plugin-laravel": "^2.0"
    }
}
```

---

## 🎨 Tailwind Config

```js
// tailwind.config.js
module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', 'system-ui', 'sans-serif'],
                mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
            },
            colors: {
                base: '#0a0a0f',
                surface: '#0d0d14',
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
    ],
}
```

---

## 🔑 Key Architectural Decisions

### Smart Duplicate Detection
`CreateProblem::updatedTitle()` fires on every keystroke (debounced 300ms via Livewire's `#[Validate]`),
runs Scout search and surfaces similar issues before the user submits.

### Denormalized Counters
`votes_count`, `solutions_count`, `comments_count` are stored on parent models for fast queries.
Updated via model observers + Vote boot events. Never do COUNT() on every page load.

### Polymorphic Voting & Comments
`votes` and `comments` use `morphs()` so one table handles Problems, Solutions, and Comments.
Same for `favorites`, `follows`, `reputation_logs`.

### Reputation System
All reputation changes go through `User::addReputation()` which atomically increments
the counter AND writes to `reputation_logs` for full audit trail.

### Scout + Meilisearch
Meilisearch gives sub-10ms search results, supports typo tolerance and ranking rules.
Configure custom ranking: `[votes_count:desc, solutions_count:desc, _text_match]`

### Livewire URL Sync
`ProblemList` uses `$queryString` for all filters — users can share/bookmark filtered URLs.
Back button works naturally.

---

## 🏆 Reputation Points Matrix

| Action                    | Points |
|---------------------------|--------|
| Post a problem            | +2     |
| Post a solution           | +10    |
| Receive upvote (solution) | +5     |
| Receive downvote          | -2     |
| Solution marked as Best   | +25    |
| Edit suggestion accepted  | +5     |

## 🎖 Reputation Badges

| Badge        | Threshold |
|--------------|-----------|
| Newcomer     | 0+        |
| Member       | 100+      |
| Contributor  | 1,000+    |
| Expert       | 5,000+    |
| Legend       | 10,000+   |

---

## 🔒 Authorization (Policies)

```php
// ProblemPolicy.php
public function update(User $user, Problem $problem): bool {
    return $user->id === $problem->user_id || $user->isAdmin();
}

public function markBestSolution(User $user, Problem $problem): bool {
    return $user->id === $problem->user_id;
}

// SolutionPolicy.php
public function delete(User $user, Solution $solution): bool {
    return $user->id === $solution->user_id
        || $user->id === $solution->problem->user_id
        || $user->isModerator();
}
```

---

## 📡 Real-time Notifications

Use Laravel Echo + Pusher/Soketi for live updates:

```php
// Events fired:
NewSolutionPosted::class   → notify problem author
SolutionUpvoted::class     → notify solution author
BestSolutionMarked::class  → notify solution author + all followers
NewCommentPosted::class    → notify parent commenters
```

Configure in `config/broadcasting.php` and listen in the layout
with Alpine.js + Echo's JavaScript client.
