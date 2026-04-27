<div align="center">

<img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
<img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" />
<img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" />
<img src="https://img.shields.io/badge/Meilisearch-1.x-FF5CAA?style=for-the-badge&logo=meilisearch&logoColor=white" />
<img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" />

<br /><br />

<h1>⌬ LaravelKnow</h1>

<p><strong>The collaborative knowledge base built exclusively for Laravel developers.</strong><br />
Document problems, share solutions, learn from the community — all in one dark-mode SaaS platform.</p>

<br />

[**Live Demo**](https://laravelknow.dev) · [**Report a Bug**](https://github.com/yourname/laravelknow/issues) · [**Request a Feature**](https://github.com/yourname/laravelknow/issues)

<br />

![LaravelKnow Screenshot](https://via.placeholder.com/900x500/0a0a0f/f43f5e?text=LaravelKnow+Screenshot)

</div>

---

## 📖 Table of Contents

- [About the Project](#-about-the-project)
- [Key Features](#-key-features)
- [Tech Stack](#-tech-stack)
- [Architecture Overview](#-architecture-overview)
- [Getting Started](#-getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Configuration](#configuration)
- [Database Schema](#-database-schema)
- [Livewire Components](#-livewire-components)
- [Reputation System](#-reputation-system)
- [Roadmap](#-roadmap)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 About the Project

**LaravelKnow** is a full-stack SaaS platform designed to solve one of the most frustrating parts of being a Laravel developer: losing hours searching for answers to problems that someone else has already solved.

Inspired by the best of **StackOverflow**, **GitHub Discussions**, **Notion**, and **Linear**, this platform gives you a structured, searchable, and community-driven knowledge base — 100% dedicated to the Laravel ecosystem.

> Built entirely with the Laravel stack. No Vue, no React, no Next.js. Pure Laravel, Livewire, Alpine.js, and Tailwind CSS.

---

## ✨ Key Features

### 🐛 Problem Management
- Rich issue creation with a **3-step guided wizard**
- Attach **error logs / stack traces**, steps to reproduce, expected vs actual behavior
- Categorize by **Laravel version**, package versions, and project phase
- Screenshot & file attachment support

### 💡 Solution System
- **Markdown editor** with live preview
- **Multi-tab code snippets** with syntax highlighting (PHP, Blade, Livewire, JS, SQL, Bash)
- Copy-to-clipboard, expand/collapse, before/after comparison view
- **Best Solution** badge selected by the problem author
- Upvote / downvote with reputation rewards

### 🔍 Smart Search (Powered by Meilisearch)
- **Instant full-text search** with typo tolerance
- Real-time duplicate detection while typing a new issue title
- Filter by category, tag, Laravel version, status, and popularity
- URL-synced filters — share any search with a bookmark

### 🏆 Reputation & Gamification
- Points earned for posting, solving, and helping
- 5 reputation badges: Newcomer → Member → Contributor → Expert → Legend
- Full activity log for every reputation change

### 🔔 Notifications
- Real-time notification bell with unread count
- Notified on: new solution, upvote, best solution selected, comment

### 🛡 Moderation Panel
- Admin dashboard with content moderation table
- User management and role assignment
- Edit suggestion review workflow (approve / reject)

### 👤 User Profiles
- Public profile with stats, reputation, social links
- Activity feed, posted problems & solutions

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| **Framework** | Laravel 12.x |
| **Frontend** | Livewire 3 · Alpine.js 3 · Blade |
| **Styling** | Tailwind CSS 3 · `@tailwindcss/typography` |
| **Search** | Laravel Scout + Meilisearch |
| **Auth** | Laravel Breeze (Livewire stack) |
| **Markdown** | `league/commonmark` with GFM extension |
| **Syntax highlighting** | highlight.js |
| **Queue** | Laravel Queues (database driver) |
| **Notifications** | Laravel Notifications (database channel) |
| **Database** | MySQL 8+ / PostgreSQL 14+ |
| **Storage** | Laravel Filesystem (local / S3) |

---

## 🏗 Architecture Overview

```
laravelknow/
├── app/
│   ├── Livewire/
│   │   ├── Home/          # Landing page
│   │   ├── Problems/      # List, Details, Create (3-step wizard)
│   │   ├── Solutions/     # Solution form with snippet editor
│   │   ├── Voting/        # +1 / -1 vote system
│   │   ├── Comments/      # Nested comment threads
│   │   ├── Notifications/ # Bell with unread count
│   │   ├── Dashboard/     # User stats + activity
│   │   ├── Profile/       # Public profile
│   │   ├── Admin/         # Moderation panel
│   │   └── Settings/      # User settings + avatar upload
│   ├── Models/            # 12 Eloquent models (polymorphic)
│   ├── Policies/          # Problem + Solution policies
│   ├── Notifications/     # 4 notification types
│   └── Observers/         # Auto-index Scout on save/delete
├── database/
│   ├── migrations/        # 11 migrations
│   └── seeders/           # Categories (18) + Tags
└── resources/views/
    ├── layouts/           # Dark sidebar layout
    ├── components/        # code-snippet, reputation-badge...
    └── livewire/          # One view per component
```

### Key Architectural Decisions

**Polymorphic relationships** — `votes`, `comments`, `favorites`, `follows` all use morphs so a single table handles Problems, Solutions, and Comments without duplication.

**Denormalized counters** — `votes_count`, `solutions_count`, `comments_count` are stored directly on parent models and updated via Vote observers. No `COUNT()` on every page load.

**Smart duplicate detection** — `CreateProblem::updatedTitle()` fires a debounced Scout search on every keystroke and surfaces similar issues before submission, reducing duplicate content.

**URL-synced filters** — `ProblemList` uses Livewire's `$queryString` for all filters. Every filter is bookmarkable and the back button works natively.

---

## 🚀 Getting Started

### Prerequisites

Make sure you have the following installed:

- **PHP** ≥ 8.3
- **Composer** ≥ 2.0
- **Node.js** ≥ 20 + npm
- **MySQL** ≥ 8.0 or **PostgreSQL** ≥ 14
- **Meilisearch** — [install guide](https://www.meilisearch.com/docs/learn/getting_started/installation)

> 💡 This project was built with **Laravel Herd**. If you use Herd with the Livewire + Built-in Auth starter kit, Breeze is already installed — skip that step.

---

### Installation

**1. Clone the repository**

```bash
git clone https://github.com/yourname/laravelknow.git
cd laravelknow
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Install additional packages**

```bash
composer require laravel/scout meilisearch/meilisearch-php http-interop/http-factory-guzzle
composer require league/commonmark
composer require spatie/laravel-permission
```

**4. Install Node dependencies**

```bash
npm install
npm install @tailwindcss/typography @tailwindcss/forms highlight.js @alpinejs/focus
```

**5. Copy environment file**

```bash
cp .env.example .env
php artisan key:generate
```

---

### Configuration

Edit your `.env` file with the following:

```env
APP_NAME=LaravelKnow
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravelknow
DB_USERNAME=root
DB_PASSWORD=your_password

# Meilisearch
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://localhost:7700

# Queue
QUEUE_CONNECTION=database
```

**6. Create the database**

```bash
mysql -u root -p -e "CREATE DATABASE laravelknow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

**7. Run migrations and seed**

```bash
php artisan migrate
php artisan db:seed
```

**8. Publish and configure Scout**

```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

**9. Start Meilisearch and index content**

```bash
# Terminal 1 — Meilisearch
./meilisearch

# Terminal 2 — Import existing data
php artisan scout:import "App\Models\Problem"
php artisan scout:import "App\Models\Solution"
```

**10. Build assets and start the server**

```bash
# Development
npm run dev

# Terminal 3 — Laravel server
php artisan serve

# Terminal 4 — Queue worker (notifications, jobs)
php artisan queue:work
```

Visit **http://localhost:8000** 🎉

---

## 🗄 Database Schema

| Table | Description |
|---|---|
| `users` | Extended with username, bio, reputation, role |
| `problems` | Core issue with full-text index |
| `solutions` | Linked to problems, supports best solution |
| `code_snippets` | Multi-language snippets per solution |
| `comments` | Polymorphic, supports nested replies |
| `votes` | Polymorphic +1/-1 on problems/solutions/comments |
| `favorites` | Polymorphic bookmark system |
| `follows` | Polymorphic — follow problems, tags, users |
| `tags` | Reusable tags with usage count |
| `categories` | 18 Laravel-specific categories |
| `reputation_logs` | Full audit trail of all rep changes |
| `edit_suggestions` | Community edit workflow |
| `problem_attachments` | File uploads per problem |
| `notifications` | Laravel's native notification table |

---

## ⚡ Livewire Components

| Component | Class | Description |
|---|---|---|
| `SearchBar` | `Search\SearchBar` | Debounced instant search with dropdown |
| `ProblemList` | `Problems\ProblemList` | Filterable, sortable, URL-synced list |
| `ProblemDetails` | `Problems\ProblemDetails` | Full issue view with solutions |
| `CreateProblem` | `Problems\CreateProblem` | 3-step wizard + duplicate detection |
| `SolutionForm` | `Solutions\SolutionForm` | Markdown editor + snippet builder |
| `VoteSystem` | `Voting\VoteSystem` | +1/-1 with reputation integration |
| `CommentThread` | `Comments\CommentThread` | Nested comments with replies |
| `NotificationBell` | `Notifications\NotificationBell` | Unread count + dropdown |
| `UserDashboard` | `Dashboard\UserDashboard` | Stats, activity, saved items |
| `UserProfile` | `Profile\UserProfile` | Public profile page |
| `ModerationPanel` | `Admin\ModerationPanel` | Admin tools and edit suggestions |
| `UserSettings` | `Settings\UserSettings` | Profile + password + avatar upload |

---

## 🏆 Reputation System

| Action | Points |
|---|---|
| Post a problem | +2 |
| Post a solution | +10 |
| Receive an upvote | +5 |
| Receive a downvote | -2 |
| Solution marked as Best | **+25** |
| Edit suggestion accepted | +5 |

| Badge | Threshold |
|---|---|
| 🆕 Newcomer | 0+ |
| 👤 Member | 100+ |
| 🤝 Contributor | 1,000+ |
| 🧠 Expert | 5,000+ |
| 🏆 Legend | 10,000+ |

---

## 🗺 Roadmap

- [x] Problem + Solution CRUD
- [x] Voting system with reputation
- [x] Full-text search with Meilisearch
- [x] Duplicate detection while typing
- [x] Notification system
- [x] Admin moderation panel
- [x] Reputation badges
- [ ] Real-time updates with Laravel Echo + Pusher
- [ ] GitHub OAuth login
- [ ] AI-powered solution suggestions (GPT integration)
- [ ] Mobile app (React Native)
- [ ] Public REST API
- [ ] Weekly digest email newsletter

---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn and build. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/amazing-feature`)
3. Commit your Changes (`git commit -m 'Add some amazing feature'`)
4. Push to the Branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please make sure your code follows **PSR-12** and includes tests.

---

## 📄 License

Distributed under the **MIT License**. See `LICENSE` for more information.

---

<div align="center">

Made with ❤️ and Laravel by **[Votre Nom](https://github.com/yourname)**

⭐ **Star this repo if it helped you!**

</div>
