<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    //
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

    public function updateVoteCount(): void
    {
        $this->votes_count = $this->votes()->sum('value');
        $this->saveQuietly();
    }
}
