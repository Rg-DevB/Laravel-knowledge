<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        static::saved(function (Vote $vote) {
            if (method_exists($vote->votable, 'updateVoteCount')) {
                $vote->votable->updateVoteCount();
            }
        });

        static::deleted(function (Vote $vote) {
            if (method_exists($vote->votable, 'updateVoteCount')) {
                $vote->votable->updateVoteCount();
            }
        });
    }
}
