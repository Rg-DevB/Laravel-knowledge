<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReputationLog extends Model
{
    //
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
