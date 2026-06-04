<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LearningPath extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'category', 'difficulty', 'color'];

    public function tips(): BelongsToMany
    {
        return $this->belongsToMany(Tip::class)->withPivot('order')->orderBy('pivot_order');
    }
}
