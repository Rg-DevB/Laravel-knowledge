<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Solution extends Model
{
    //
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

    public function updateVoteCount(): void
    {
        $this->votes_count = $this->votes()->sum('value');
        $this->saveQuietly();
    }
}
