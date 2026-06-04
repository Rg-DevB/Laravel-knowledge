<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Laravel\Scout\Searchable;

#[Fillable([
    'user_id', 
    'category', 
    'title', 
    'difficulty', 
    'why', 
    'junior_label', 
    'junior_code', 
    'senior_label', 
    'senior_code', 
    'pros', 
    'is_approved'
])]
class Tip extends Model
{
    use SoftDeletes, Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'why' => $this->why,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
        ];
    }

    protected $casts = [
        'pros' => 'array',
        'is_approved' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function favorites(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function updateVoteCount(): void
    {
        $this->votes_count = $this->votes()->sum('value');
        $this->saveQuietly();
    }
}
