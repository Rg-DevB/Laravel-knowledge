<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Solution;

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
