<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;


class Problem extends Model
{
    //
    use HasFactory, SoftDeletes, Searchable; // Laravel Scout

    #[Fillable(['user_id', 'category_id', 'title', 'slug', 'description', 'error_log', 'steps_to_reproduce', 'expected_behavior', 'actual_behavior', 'laravel_version', 'package_versions', 'project_phase', 'status',])]

    protected $casts = [
        'package_versions' => 'array',
        'is_featured' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    // ── Scout Full-Text Search ─────────────────────────────────
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'error_log' => $this->error_log,
            'laravel_version' => $this->laravel_version,
            'status' => $this->status,
            'category' => $this->category?->name,
            'tags' => $this->tags->pluck('name')->toArray(),
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
            'solved' => 'text-emerald-400 bg-emerald-400/10 ring-emerald-400/20',
            'closed' => 'text-zinc-400 bg-zinc-400/10 ring-zinc-400/20',
            'duplicate' => 'text-yellow-400 bg-yellow-400/10 ring-yellow-400/20',
            default => 'text-blue-400 bg-blue-400/10 ring-blue-400/20',
        };
    }

    // ── Boot ──────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Problem $problem) {
            $problem->slug = Str::slug($problem->title) . '-' . Str::random(6);
        });
    }

    public function updateVoteCount(): void
    {
        $this->votes_count = $this->votes()->sum('value');
        $this->saveQuietly();
    }
}
