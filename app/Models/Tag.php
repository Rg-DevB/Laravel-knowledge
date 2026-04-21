<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;
use Illuminate\Support\Str;

class Tag extends Model
{
    //
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
