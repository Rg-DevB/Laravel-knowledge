<?php

// ============================================================
// LARAVELKNOW — ALL ELOQUENT MODELS
// ============================================================

namespace App\Models;

// ──────────────────────────────────────────────────────────────
// User.php
// ──────────────────────────────────────────────────────────────
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'avatar', 'bio', 'github_url', 'twitter_url', 'website_url',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function favorites(): MorphToMany
    {
        return $this->morphedByMany(Problem::class, 'favoritable', 'favorites');
    }

    public function reputationLogs(): HasMany
    {
        return $this->hasMany(ReputationLog::class);
    }

    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    public function addReputation(int $points, string $reason, Model $reference): void
    {
        $this->increment('reputation', $points);
        $this->reputationLogs()->create([
            'points'            => $points,
            'reason'            => $reason,
            'referenceable_id'  => $reference->id,
            'referenceable_type'=> $reference::class,
        ]);
    }

    public function reputationBadge(): string
    {
        return match (true) {
            $this->reputation >= 10000 => 'Legend',
            $this->reputation >= 5000  => 'Expert',
            $this->reputation >= 1000  => 'Contributor',
            $this->reputation >= 100   => 'Member',
            default                    => 'Newcomer',
        };
    }

    public function hasVoted(Model $model): ?int
    {
        return $this->votes()
            ->where('votable_id', $model->id)
            ->where('votable_type', $model::class)
            ->value('value');
    }

    public function hasFavorited(Model $model): bool
    {
        return \DB::table('favorites')
            ->where('user_id', $this->id)
            ->where('favoritable_id', $model->id)
            ->where('favoritable_type', $model::class)
            ->exists();
    }
}

// ──────────────────────────────────────────────────────────────
// Problem.php
// ──────────────────────────────────────────────────────────────
class Problem extends Model
{
    use HasFactory, SoftDeletes, Searchable; // Laravel Scout

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'error_log', 'steps_to_reproduce', 'expected_behavior', 'actual_behavior',
        'laravel_version', 'package_versions', 'project_phase', 'status',
    ];

    protected $casts = [
        'package_versions' => 'array',
        'is_featured'      => 'boolean',
        'is_pinned'        => 'boolean',
    ];

    // ── Scout Full-Text Search ─────────────────────────────────

    public function toSearchableArray(): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'error_log'        => $this->error_log,
            'laravel_version'  => $this->laravel_version,
            'status'           => $this->status,
            'category'         => $this->category?->name,
            'tags'             => $this->tags->pluck('name')->toArray(),
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bestSolution(): BelongsTo
    {
        return $this->belongsTo(Solution::class, 'best_solution_id');
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class)->orderByDesc('votes_count');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'problem_tag');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProblemAttachment::class);
    }

    public function followers(): HasMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeSolved(Builder $query): Builder
    {
        return $query->where('status', 'solved');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('votes_count')->orderByDesc('views');
    }

    public function scopeForLaravelVersion(Builder $query, string $version): Builder
    {
        return $query->where('laravel_version', $version);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'solved'    => 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20',
            'closed'    => 'text-zinc-400 bg-zinc-400/10 ring-zinc-400/20',
            'duplicate' => 'text-yellow-400 bg-yellow-400/10 ring-yellow-400/20',
            default     => 'text-blue-400 bg-blue-400/10 ring-blue-400/20',
        };
    }

    // ── Boot ──────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Problem $problem) {
            $problem->slug = Str::slug($problem->title) . '-' . Str::random(6);
        });
    }
}

// ──────────────────────────────────────────────────────────────
// Solution.php
// ──────────────────────────────────────────────────────────────
class Solution extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = ['problem_id', 'user_id', 'content', 'is_best', 'is_accepted'];

    public function toSearchableArray(): array
    {
        return ['id' => $this->id, 'content' => $this->content];
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function snippets(): HasMany
    {
        return $this->hasMany(CodeSnippet::class)->orderBy('sort_order');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function markAsBest(): void
    {
        \DB::transaction(function () {
            // Unmark previous best
            $this->problem->solutions()->update(['is_best' => false]);

            // Mark this as best
            $this->update(['is_best' => true]);

            // Update problem
            $this->problem->update([
                'best_solution_id' => $this->id,
                'status'           => 'solved',
            ]);

            // Award reputation to solution author
            $this->user->addReputation(25, 'best_solution', $this);
        });
    }
}

// ──────────────────────────────────────────────────────────────
// CodeSnippet.php
// ──────────────────────────────────────────────────────────────
class CodeSnippet extends Model
{
    protected $fillable = ['solution_id', 'language', 'label', 'code', 'sort_order'];

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }

    public function getHighlightedCodeAttribute(): string
    {
        // Integrate with highlight.php or use JS-side Prism.js/Shiki
        return $this->code;
    }

    // Supported languages with display labels
    public static function languages(): array
    {
        return [
            'php'        => 'PHP',
            'blade'      => 'Blade',
            'livewire'   => 'Livewire',
            'javascript' => 'JavaScript',
            'sql'        => 'SQL',
            'bash'       => 'Bash / Shell',
            'json'       => 'JSON',
            'yaml'       => 'YAML',
            'env'        => '.env',
        ];
    }
}

// ──────────────────────────────────────────────────────────────
// Tag.php
// ──────────────────────────────────────────────────────────────
class Tag extends Model
{
    use Searchable;

    protected $fillable = ['name', 'slug', 'color'];

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'problem_tag');
    }

    public function followers(): MorphMany
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    protected static function booted(): void
    {
        static::creating(fn($tag) => $tag->slug = Str::slug($tag->name));
    }
}

// ──────────────────────────────────────────────────────────────
// Category.php
// ──────────────────────────────────────────────────────────────
class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'color', 'description', 'sort_order'];

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }
}

// ──────────────────────────────────────────────────────────────
// Comment.php
// ──────────────────────────────────────────────────────────────
class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'content', 'parent_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }
}

// ──────────────────────────────────────────────────────────────
// Vote.php
// ──────────────────────────────────────────────────────────────
class Vote extends Model
{
    protected $fillable = ['user_id', 'value'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        // After creating/updating/deleting a vote, recalculate counts
        static::saved(function (Vote $vote) {
            $vote->votable->updateVoteCount();
        });

        static::deleted(function (Vote $vote) {
            $vote->votable->updateVoteCount();
        });
    }
}

// ──────────────────────────────────────────────────────────────
// ReputationLog.php
// ──────────────────────────────────────────────────────────────
class ReputationLog extends Model
{
    protected $fillable = ['user_id', 'points', 'reason', 'referenceable_id', 'referenceable_type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function reasonLabel(string $reason): string
    {
        return match ($reason) {
            'solution_posted'    => 'Posted a solution',
            'upvote_received'    => 'Solution upvoted',
            'downvote_received'  => 'Solution downvoted',
            'best_solution'      => 'Solution marked as best',
            'edit_accepted'      => 'Edit suggestion accepted',
            'problem_posted'     => 'Posted a problem',
            default              => $reason,
        };
    }
}
