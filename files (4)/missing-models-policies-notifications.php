<?php

// ============================================================
// app/Models/ProblemAttachment.php
// ============================================================
namespace App\Models;

class ProblemAttachment extends Model
{
    protected $fillable = ['problem_id', 'filename', 'path', 'mime_type', 'size'];

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}

// ============================================================
// app/Models/EditSuggestion.php
// ============================================================
namespace App\Models;

class EditSuggestion extends Model
{
    protected $fillable = [
        'user_id', 'original_content', 'suggested_content',
        'reason', 'status', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function editable(): MorphTo { return $this->morphTo(); }
}

// ============================================================
// app/Models/Favorite.php
// ============================================================
namespace App\Models;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'favoritable_id', 'favoritable_type'];

    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function favoritable(): MorphTo { return $this->morphTo(); }
}

// ============================================================
// app/Models/Follow.php
// ============================================================
namespace App\Models;

class Follow extends Model
{
    protected $fillable = ['user_id', 'followable_id', 'followable_type'];

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function followable(): MorphTo { return $this->morphTo(); }
}


// ============================================================
// app/Policies/ProblemPolicy.php
// ============================================================
namespace App\Policies;

class ProblemPolicy
{
    public function update(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id || in_array($user->role, ['admin', 'moderator']);
    }

    public function delete(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id || in_array($user->role, ['admin', 'moderator']);
    }

    public function markBestSolution(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id;
    }
}

// ============================================================
// app/Policies/SolutionPolicy.php
// ============================================================
namespace App\Policies;

class SolutionPolicy
{
    public function update(User $user, Solution $solution): bool
    {
        return $user->id === $solution->user_id || in_array($user->role, ['admin', 'moderator']);
    }

    public function delete(User $user, Solution $solution): bool
    {
        return $user->id === $solution->user_id
            || $user->id === $solution->problem->user_id
            || in_array($user->role, ['admin', 'moderator']);
    }
}


// ============================================================
// app/Notifications/NewSolutionNotification.php
// ============================================================
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewSolutionNotification extends Notification
{
    public function __construct(
        public readonly Solution $solution
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "{$this->solution->user->name} posted a solution to your problem: \"{$this->solution->problem->title}\"",
            'url'     => route('problems.show', $this->solution->problem->slug),
            'type'    => 'new_solution',
        ];
    }
}

// ============================================================
// app/Notifications/SolutionVotedNotification.php
// ============================================================
namespace App\Notifications;

class SolutionVotedNotification extends Notification
{
    public function __construct(
        public readonly Solution $solution,
        public readonly int $value
    ) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toDatabase(object $notifiable): array
    {
        $action = $this->value > 0 ? 'upvoted' : 'downvoted';
        return [
            'message' => "Your solution on \"{$this->solution->problem->title}\" was {$action}.",
            'url'     => route('problems.show', $this->solution->problem->slug),
            'type'    => 'vote',
        ];
    }
}

// ============================================================
// app/Notifications/BestSolutionNotification.php
// ============================================================
namespace App\Notifications;

class BestSolutionNotification extends Notification
{
    public function __construct(public readonly Solution $solution) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "🎉 Your solution was marked as Best Solution on \"{$this->solution->problem->title}\"! +25 reputation.",
            'url'     => route('problems.show', $this->solution->problem->slug),
            'type'    => 'best_solution',
        ];
    }
}

// ============================================================
// app/Notifications/NewCommentNotification.php
// ============================================================
namespace App\Notifications;

class NewCommentNotification extends Notification
{
    public function __construct(public readonly Comment $comment) {}

    public function via(object $notifiable): array { return ['database']; }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => "{$this->comment->user->name} commented on your problem.",
            'url'     => '#',
            'type'    => 'comment',
        ];
    }
}


// ============================================================
// app/Observers/SolutionObserver.php
// ============================================================
namespace App\Observers;

class SolutionObserver
{
    public function created(Solution $solution): void
    {
        // Notify problem author
        if ($solution->user_id !== $solution->problem->user_id) {
            $solution->problem->user->notify(new NewSolutionNotification($solution));
        }

        // Index in Scout
        $solution->searchable();
    }

    public function deleted(Solution $solution): void
    {
        $solution->problem->decrement('solutions_count');
        $solution->unsearchable();
    }
}

// ============================================================
// app/Observers/ProblemObserver.php
// ============================================================
namespace App\Observers;

class ProblemObserver
{
    public function created(Problem $problem): void
    {
        $problem->searchable();
    }

    public function updated(Problem $problem): void
    {
        $problem->searchable();
    }

    public function deleted(Problem $problem): void
    {
        $problem->unsearchable();
    }
}


// ============================================================
// app/Http/Middleware/RoleMiddleware.php
// ============================================================
namespace App\Http\Middleware;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}


// ============================================================
// app/Providers/AppServiceProvider.php  — additions
// ============================================================
namespace App\Providers;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register policies
        Gate::policy(Problem::class, ProblemPolicy::class);
        Gate::policy(Solution::class, SolutionPolicy::class);

        // Register observers
        Problem::observe(ProblemObserver::class);
        Solution::observe(SolutionObserver::class);

        // Register middleware alias
        // In bootstrap/app.php:
        // ->withMiddleware(function (Middleware $middleware) {
        //     $middleware->alias(['role' => RoleMiddleware::class]);
        // })

        // Markdown converter binding
        $this->app->bind(
            \League\CommonMark\MarkdownConverterInterface::class,
            function () {
                $environment = new \League\CommonMark\Environment\Environment([
                    'html_input'         => 'escape',
                    'allow_unsafe_links' => false,
                    'max_nesting_level'  => 20,
                ]);
                $environment->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
                $environment->addExtension(new \League\CommonMark\Extension\GithubFlavoredMarkdownExtension());
                $environment->addExtension(new \League\CommonMark\Extension\Table\TableExtension());
                return new \League\CommonMark\MarkdownConverter($environment);
            }
        );
    }
}


// ============================================================
// database/seeders/CategorySeeder.php
// ============================================================
namespace Database\Seeders;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Eloquent',           'slug' => 'eloquent',        'color' => '#f97316', 'icon' => 'database'],
            ['name' => 'Livewire',           'slug' => 'livewire',        'color' => '#8b5cf6', 'icon' => 'bolt'],
            ['name' => 'Blade',              'slug' => 'blade',           'color' => '#ef4444', 'icon' => 'code'],
            ['name' => 'Routing',            'slug' => 'routing',         'color' => '#3b82f6', 'icon' => 'arrows-right-left'],
            ['name' => 'Middleware',         'slug' => 'middleware',      'color' => '#a78bfa', 'icon' => 'shield'],
            ['name' => 'Queues & Jobs',      'slug' => 'queues',          'color' => '#06b6d4', 'icon' => 'queue-list'],
            ['name' => 'Notifications',      'slug' => 'notifications',   'color' => '#ec4899', 'icon' => 'bell'],
            ['name' => 'Broadcasting',       'slug' => 'broadcasting',    'color' => '#6366f1', 'icon' => 'signal'],
            ['name' => 'Sanctum',            'slug' => 'sanctum',         'color' => '#10b981', 'icon' => 'key'],
            ['name' => 'Passport',           'slug' => 'passport',        'color' => '#14b8a6', 'icon' => 'identification'],
            ['name' => 'API Resources',      'slug' => 'api-resources',   'color' => '#22c55e', 'icon' => 'server'],
            ['name' => 'Policies & Gates',   'slug' => 'policies',        'color' => '#f59e0b', 'icon' => 'lock-closed'],
            ['name' => 'Caching',            'slug' => 'caching',         'color' => '#84cc16', 'icon' => 'cpu-chip'],
            ['name' => 'Redis',              'slug' => 'redis',           'color' => '#ef4444', 'icon' => 'circle-stack'],
            ['name' => 'Horizon',            'slug' => 'horizon',         'color' => '#f97316', 'icon' => 'chart-bar'],
            ['name' => 'Deployment',         'slug' => 'deployment',      'color' => '#6366f1', 'icon' => 'rocket-launch'],
            ['name' => 'Testing',            'slug' => 'testing',         'color' => '#10b981', 'icon' => 'beaker'],
            ['name' => 'File Storage',       'slug' => 'storage',         'color' => '#8b5cf6', 'icon' => 'folder'],
        ];

        foreach ($categories as $i => $cat) {
            \App\Models\Category::create([...$cat, 'sort_order' => $i]);
        }
    }
}

// ============================================================
// database/seeders/TagSeeder.php
// ============================================================
namespace Database\Seeders;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'n+1',        'color' => '#f97316'],
            ['name' => 'migration',  'color' => '#3b82f6'],
            ['name' => 'auth',       'color' => '#10b981'],
            ['name' => 'cors',       'color' => '#ef4444'],
            ['name' => 'config',     'color' => '#8b5cf6'],
            ['name' => 'performance','color' => '#f59e0b'],
            ['name' => 'testing',    'color' => '#84cc16'],
            ['name' => 'docker',     'color' => '#06b6d4'],
            ['name' => 'forge',      'color' => '#6366f1'],
            ['name' => 'vapor',      'color' => '#a78bfa'],
            ['name' => 'relationships', 'color' => '#f97316'],
            ['name' => 'scopes',     'color' => '#3b82f6'],
            ['name' => 'validation', 'color' => '#ec4899'],
            ['name' => 'rate-limiting', 'color' => '#f43f5e'],
            ['name' => 'pagination', 'color' => '#14b8a6'],
        ];

        foreach ($tags as $tag) {
            \App\Models\Tag::create(['name' => $tag['name'], 'slug' => Str::slug($tag['name']), 'color' => $tag['color']]);
        }
    }
}

// ============================================================
// database/seeders/DatabaseSeeder.php
// ============================================================
namespace Database\Seeders;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
        ]);
    }
}
